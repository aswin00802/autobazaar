<?php

namespace App\Http\Controllers\Api\fairprice\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\RideRequest;
use App\Models\User;
use App\Services\RideCommunicationService;
use Illuminate\Http\Request;

class RideCommunicationController extends Controller
{
    public function __construct(
        private RideCommunicationService $communicationService
    ) {}

    public function customerConversation(Request $request, int $rideId)
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();
        $ride = $this->getRideForCustomer($rideId, (int) $customer->id);
        $conversation = $this->communicationService->getOrSyncConversationForCustomer($ride, $customer);

        $unread = $conversation->messages()
            ->where('sender_type', 'driver')
            ->whereNull('customer_read_at')
            ->count();

        return response()->json([
            'status' => true,
            'ride_id' => (int) $ride->id,
            'conversation' => [
                'id' => (int) $conversation->id,
                'status' => $conversation->status,
                'unread_count' => (int) $unread,
                'closed_at' => optional($conversation->closed_at)->toIso8601String(),
            ],
        ]);
    }

    public function customerMessages(Request $request, int $rideId)
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();
        $ride = $this->getRideForCustomer($rideId, (int) $customer->id);
        $conversation = $this->communicationService->getOrSyncConversationForCustomer($ride, $customer);

        $perPage = (int) min(50, max(1, (int) $request->query('limit', 20)));
        $paginator = $this->communicationService->listMessages($conversation, $perPage);

        $messages = collect($paginator->items())
            ->map(fn ($message) => $this->communicationService->formatMessage($message, 'customer'))
            ->values();

        return response()->json([
            'status' => true,
            'ride_id' => (int) $ride->id,
            'conversation_id' => (int) $conversation->id,
            'messages' => $messages,
            'next_cursor' => optional($paginator->nextCursor())->encode(),
        ]);
    }

    public function customerSendMessage(Request $request, int $rideId)
    {
        $validated = $request->validate([
            'message_type' => 'required|in:text,voice_note,image,video',
            'text' => 'nullable|string|max:500',
            // 'voice_note' => 'nullable|file|mimetypes:audio/m4a,audio/mp4,audio/mpeg,audio/aac,audio/ogg,audio/wav|max:5120',
            'voice_note' => 'nullable|file|max:5120',
            // 'image' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:5120',
            'image' => 'nullable|file|max:5120',
            // 'video' => 'nullable|file|mimes:mp4,mov,quicktime|max:20480',
            'video' => 'nullable|file|max:20480',
            'duration_sec' => 'nullable|integer|min:1|max:300',
        ]);


        if (($validated['message_type'] ?? null) === 'text' && empty(trim((string) ($validated['text'] ?? '')))) {
            return response()->json(['status' => false, 'message' => 'text is required for text message'], 422);
        }

        $mediaFile = $this->resolveMediaFile($request, $validated['message_type']);
        if ($mediaFile === false) {
            return response()->json([
                'status' => false,
                'message' => $validated['message_type'] . ' file is required',
            ], 422);
        }

        /** @var Customer $customer */
        $customer = auth('customer')->user();
        $ride = $this->getRideForCustomer($rideId, (int) $customer->id);
        $conversation = $this->communicationService->getOrSyncConversationForCustomer($ride, $customer);

        $message = $this->communicationService->sendCustomerMessage(
            $ride,
            $conversation,
            $customer,
            $validated,
            $mediaFile
        );

        return response()->json([
            'status' => true,
            'message' => 'Message sent',
            'ride_id' => (int) $ride->id,
            'data' => $this->communicationService->formatMessage($message, 'customer'),
        ]);
    }

    public function customerReadMessages(Request $request, int $rideId)
    {
        $validated = $request->validate([
            'message_ids' => 'required|array|min:1',
            'message_ids.*' => 'integer',
        ]);

        /** @var Customer $customer */
        $customer = auth('customer')->user();
        $ride = $this->getRideForCustomer($rideId, (int) $customer->id);
        $conversation = $this->communicationService->getOrSyncConversationForCustomer($ride, $customer);

        $updated = $this->communicationService->markRead($conversation, 'customer', $validated['message_ids']);

        return response()->json([
            'status' => true,
            'updated_count' => (int) $updated,
        ]);
    }

    public function driverConversation(Request $request, int $rideId)
    {
        /** @var User $driver */
        $driver = auth()->user();
        $ride = $this->getRideForDriver($rideId, (int) $driver->id);
        $conversation = $this->communicationService->getOrSyncConversationForDriver($ride, $driver);

        $unread = $conversation->messages()
            ->where('sender_type', 'customer')
            ->whereNull('driver_read_at')
            ->count();

        return response()->json([
            'status' => true,
            'ride_id' => (int) $ride->id,
            'conversation' => [
                'id' => (int) $conversation->id,
                'status' => $conversation->status,
                'unread_count' => (int) $unread,
                'closed_at' => optional($conversation->closed_at)->toIso8601String(),
            ],
        ]);
    }

    public function driverMessages(Request $request, int $rideId)
    {
        /** @var User $driver */
        $driver = auth()->user();
        $ride = $this->getRideForDriver($rideId, (int) $driver->id);
        $conversation = $this->communicationService->getOrSyncConversationForDriver($ride, $driver);

        $perPage = (int) min(50, max(1, (int) $request->query('limit', 20)));
        $paginator = $this->communicationService->listMessages($conversation, $perPage);

        $messages = collect($paginator->items())
            ->map(fn ($message) => $this->communicationService->formatMessage($message, 'driver'))
            ->values();

        return response()->json([
            'status' => true,
            'ride_id' => (int) $ride->id,
            'conversation_id' => (int) $conversation->id,
            'messages' => $messages,
            'next_cursor' => optional($paginator->nextCursor())->encode(),
        ]);
    }

    public function driverSendMessage(Request $request, int $rideId)
    {
        $validated = $request->validate([
            'message_type' => 'required|in:text,voice_note,image,video',
            'text' => 'nullable|string|max:500',

            // 'voice_note' => 'nullable|file|mimetypes:audio/m4a,audio/mp4,audio/mpeg,audio/aac,audio/ogg,audio/wav|max:5120',
            'voice_note' => 'nullable|file|max:5120',

            // 'image' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:5120',
            'image' => 'nullable|file|max:5120',

            // 'video' => 'nullable|file|mimes:mp4,mov,quicktime|max:20480',
            'video' => 'nullable|file|max:20480',

            'duration_sec' => 'nullable|integer|min:1|max:300',
        ]);


        if (($validated['message_type'] ?? null) === 'text' && empty(trim((string) ($validated['text'] ?? '')))) {
            return response()->json(['status' => false, 'message' => 'text is required for text message'], 422);
        }

        $mediaFile = $this->resolveMediaFile($request, $validated['message_type']);
        if ($mediaFile === false) {
            return response()->json([
                'status' => false,
                'message' => $validated['message_type'] . ' file is required',
            ], 422);
        }

        /** @var User $driver */
        $driver = auth()->user();
        $ride = $this->getRideForDriver($rideId, (int) $driver->id);
        $conversation = $this->communicationService->getOrSyncConversationForDriver($ride, $driver);

        $message = $this->communicationService->sendDriverMessage(
            $ride,
            $conversation,
            $driver,
            $validated,
            $mediaFile
        );

        return response()->json([
            'status' => true,
            'message' => 'Message sent',
            'ride_id' => (int) $ride->id,
            'data' => $this->communicationService->formatMessage($message, 'driver'),
        ]);
    }

    public function driverReadMessages(Request $request, int $rideId)
    {
        $validated = $request->validate([
            'message_ids' => 'required|array|min:1',
            'message_ids.*' => 'integer',
        ]);

        /** @var User $driver */
        $driver = auth()->user();
        $ride = $this->getRideForDriver($rideId, (int) $driver->id);
        $conversation = $this->communicationService->getOrSyncConversationForDriver($ride, $driver);

        $updated = $this->communicationService->markRead($conversation, 'driver', $validated['message_ids']);

        return response()->json([
            'status' => true,
            'updated_count' => (int) $updated,
        ]);
    }

    private function getRideForCustomer(int $rideId, int $customerId): RideRequest
    {
        $ride = RideRequest::where('id', $rideId)
            ->where('customer_id', $customerId)
            ->first();

        if (!$ride) {
            abort(response()->json(['status' => false, 'message' => 'Ride not found'], 404));
        }

        return $ride;
    }

    private function getRideForDriver(int $rideId, int $driverId): RideRequest
    {
        $ride = RideRequest::where('id', $rideId)
            ->where('driver_id', $driverId)
            ->first();

        if (!$ride) {
            abort(response()->json(['status' => false, 'message' => 'Ride not found'], 404));
        }

        return $ride;
    }

    /**
     * @return \Illuminate\Http\UploadedFile|null|false null for text, false if required file missing
     */
    private function resolveMediaFile(Request $request, string $messageType)
    {
        if ($messageType === 'text') {
            return null;
        }

        $field = match ($messageType) {
            'voice_note' => 'voice_note',
            'image' => 'image',
            'video' => 'video',
            default => null,
        };

        if (!$field || !$request->hasFile($field)) {
            return false;
        }

        return $request->file($field);
    }
}

