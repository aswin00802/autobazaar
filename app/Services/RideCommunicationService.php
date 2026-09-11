<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\RideConversation;
use App\Models\RideMessage;
use App\Models\RideRequest;
use App\Models\User;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RideCommunicationService
{
    public const ALLOWED_ACTIVE_STATUSES = ['accepted', 'arrived', 'started'];

    public function syncConversationForRide(RideRequest $ride): ?RideConversation
    {
        if (empty($ride->customer_id) || empty($ride->driver_id)) {
            return null;
        }

        $conversation = RideConversation::firstOrCreate(
            ['ride_id' => $ride->id],
            [
                'customer_id' => $ride->customer_id,
                'driver_id' => $ride->driver_id,
                'status' => 'open',
            ]
        );

        $updates = [];
        if ((int) $conversation->customer_id !== (int) $ride->customer_id) {
            $updates['customer_id'] = $ride->customer_id;
        }
        if ((int) $conversation->driver_id !== (int) $ride->driver_id) {
            $updates['driver_id'] = $ride->driver_id;
        }

        if (in_array($ride->status, self::ALLOWED_ACTIVE_STATUSES, true)) {
            if ($conversation->status !== 'open') {
                $updates['status'] = 'open';
                $updates['closed_at'] = null;
            }
        } elseif (in_array($ride->status, ['completed', 'cancelled'], true) && $conversation->status !== 'closed') {
            $updates['status'] = 'closed';
            $updates['closed_at'] = now();
        }

        if (!empty($updates)) {
            $conversation->update($updates);
        }

        return $conversation->fresh();
    }

    public function closeConversation(RideRequest $ride): ?RideConversation
    {
        $conversation = RideConversation::where('ride_id', $ride->id)->first();
        if (!$conversation) {
            return null;
        }

        if ($conversation->status !== 'closed') {
            $conversation->update([
                'status' => 'closed',
                'closed_at' => now(),
            ]);
        }

        $this->notifyConversationClosed($ride, $conversation);

        return $conversation->fresh();
    }

    public function getOrSyncConversationForCustomer(RideRequest $ride, Customer $customer): RideConversation
    {
        if ((int) $ride->customer_id !== (int) $customer->id) {
            abort(response()->json(['status' => false, 'message' => 'Unauthorized ride access'], 403));
        }

        $conversation = $this->syncConversationForRide($ride);
        if (!$conversation) {
            abort(response()->json(['status' => false, 'message' => 'Driver is not assigned yet'], 409));
        }

        return $conversation;
    }

    public function getOrSyncConversationForDriver(RideRequest $ride, User $driver): RideConversation
    {
        if ((int) $ride->driver_id !== (int) $driver->id) {
            abort(response()->json(['status' => false, 'message' => 'Unauthorized ride access'], 403));
        }

        $conversation = $this->syncConversationForRide($ride);
        if (!$conversation) {
            abort(response()->json(['status' => false, 'message' => 'Conversation not available for this ride'], 409));
        }

        return $conversation;
    }

    public function listMessages(RideConversation $conversation, int $perPage = 20): CursorPaginator
    {
        return RideMessage::where('conversation_id', $conversation->id)
            ->where('is_deleted', false)
            ->orderBy('id')
            ->cursorPaginate($perPage);
    }

    public function sendCustomerMessage(
        RideRequest $ride,
        RideConversation $conversation,
        Customer $customer,
        array $payload,
        ?UploadedFile $mediaFile
    ): RideMessage {
        $this->guardSendAllowed($ride, $conversation);

        $message = $this->createMessage(
            conversation: $conversation,
            ride: $ride,
            senderType: 'customer',
            senderId: (int) $customer->id,
            payload: $payload,
            mediaFile: $mediaFile
        );

        $this->notifyNewMessage($ride, $conversation, $message, 'driver');

        return $message;
    }

    public function sendDriverMessage(
        RideRequest $ride,
        RideConversation $conversation,
        User $driver,
        array $payload,
        ?UploadedFile $mediaFile
    ): RideMessage {
        $this->guardSendAllowed($ride, $conversation);

        $message = $this->createMessage(
            conversation: $conversation,
            ride: $ride,
            senderType: 'driver',
            senderId: (int) $driver->id,
            payload: $payload,
            mediaFile: $mediaFile
        );

        $this->notifyNewMessage($ride, $conversation, $message, 'customer');

        return $message;
    }

    public function markRead(RideConversation $conversation, string $viewerType, array $messageIds): int
    {
        if (empty($messageIds)) {
            return 0;
        }

        $column = $viewerType === 'customer' ? 'customer_read_at' : 'driver_read_at';

        return RideMessage::where('conversation_id', $conversation->id)
            ->whereIn('id', $messageIds)
            ->whereNull($column)
            ->update([$column => now()]);
    }

    public function formatMessage(RideMessage $message, string $viewerType): array
    {
        return [
            'id' => (int) $message->id,
            'ride_id' => (int) $message->ride_id,
            'message_type' => $message->message_type,
            'text' => $message->text,
            'media_url' => $message->media_path ? Storage::disk('public')->url($message->media_path) : null,
            'duration_sec' => $message->duration_sec === null ? null : (int) $message->duration_sec,
            'media_mime' => $message->media_mime,
            'sender_type' => $message->sender_type,
            'sender_id' => (int) $message->sender_id,
            'created_at' => optional($message->created_at)->toIso8601String(),
            'read_at' => $viewerType === 'customer'
                ? optional($message->customer_read_at)->toIso8601String()
                : optional($message->driver_read_at)->toIso8601String(),
        ];
    }

    private function guardSendAllowed(RideRequest $ride, RideConversation $conversation): void
    {
        $this->syncConversationForRide($ride);

        if (!in_array($ride->status, self::ALLOWED_ACTIVE_STATUSES, true) || $conversation->status !== 'open') {
            abort(response()->json([
                'status' => false,
                'message' => 'Conversation is closed for this ride state',
            ], 409));
        }
    }

    private function createMessage(
        RideConversation $conversation,
        RideRequest $ride,
        string $senderType,
        int $senderId,
        array $payload,
        ?UploadedFile $mediaFile
    ): RideMessage {
        $messageType = $payload['message_type'];
        $text = $messageType === 'text' ? trim((string) ($payload['text'] ?? '')) : null;

        $mediaPath = null;
        $mediaMime = null;
        $mediaSize = null;
        $durationSec = null;

        if (in_array($messageType, ['voice_note', 'image', 'video'], true)) {
            if (!$mediaFile) {
                abort(response()->json([
                    'status' => false,
                    'message' => $messageType . ' file is required',
                ], 422));
            }

            $storageFolder = match ($messageType) {
                'voice_note' => 'ride_chat_voice_notes',
                'image' => 'ride_chat_images',
                'video' => 'ride_chat_videos',
            };

            $mediaPath = $mediaFile->store($storageFolder, 'public');
            $mediaMime = $mediaFile->getMimeType();
            $mediaSize = $mediaFile->getSize();

            if ($messageType === 'voice_note' || $messageType === 'video') {
                $durationSec = isset($payload['duration_sec']) ? (int) $payload['duration_sec'] : null;
            }
        }

        return RideMessage::create([
            'conversation_id' => $conversation->id,
            'ride_id' => $ride->id,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message_type' => $messageType,
            'text' => $text ?: null,
            'media_path' => $mediaPath,
            'media_mime' => $mediaMime,
            'media_size_bytes' => $mediaSize,
            'duration_sec' => $durationSec,
            'customer_read_at' => $senderType === 'customer' ? now() : null,
            'driver_read_at' => $senderType === 'driver' ? now() : null,
        ]);
    }

    private function messagePreview(RideMessage $message): string
    {
        return match ($message->message_type) {
            'text' => mb_substr((string) $message->text, 0, 120),
            'voice_note' => 'Voice note',
            'image' => 'Photo',
            'video' => 'Video',
            default => 'New message',
        };
    }

    private function notifyNewMessage(
        RideRequest $ride,
        RideConversation $conversation,
        RideMessage $message,
        string $recipientType
    ): void {
        $extra = [
            'conversation_id' => (string) $conversation->id,
            'message_id' => (string) $message->id,
            'message_type' => $message->message_type,
            'sender_type' => $message->sender_type,
            'sent_at' => optional($message->created_at)->toIso8601String(),
            'preview' => $this->messagePreview($message),
        ];

        if ($recipientType === 'customer') {
            app(FairPriceRideFlowService::class)->notifyCustomer(
                $ride,
                'New Message',
                $this->messagePreview($message),
                'chat_message_new',
                $extra
            );

            return;
        }

        app(FairPriceRideFlowService::class)->notifyDriver(
            $ride,
            'New Message',
            $this->messagePreview($message),
            'chat_message_new',
            $extra
        );
    }

    private function notifyConversationClosed(RideRequest $ride, RideConversation $conversation): void
    {
        $extra = [
            'conversation_id' => (string) $conversation->id,
            'closed_at' => optional($conversation->closed_at)->toIso8601String(),
        ];

        app(FairPriceRideFlowService::class)->notifyCustomer(
            $ride,
            'Chat Closed',
            'This ride conversation is now closed.',
            'chat_conversation_closed',
            $extra
        );

        app(FairPriceRideFlowService::class)->notifyDriver(
            $ride,
            'Chat Closed',
            'This ride conversation is now closed.',
            'chat_conversation_closed',
            $extra
        );
    }
}

