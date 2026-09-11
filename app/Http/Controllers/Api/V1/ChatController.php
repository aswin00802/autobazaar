<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Models\Chat;
use Illuminate\Http\Request;
use App\Models\DriverRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message'       => 'nullable|string',
            'image'         => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'video'         => 'nullable|file|mimes:mp4,avi,mov|max:10240',
            'reply_to_id'   => 'nullable|exists:chats,id',
        ]);

        $chat = new Chat();
        $chat->user_id = auth()->id();
        $chat->message = $request->message;
        $chat->reply_to_id = $request->reply_to_id;

        // Handle file uploads
        if ($request->hasFile('image')) {
            $chat->image = $request->file('image')->store('chat_images', 'public');
        }

        if ($request->hasFile('video')) {
            $chat->video = $request->file('video')->store('chat_videos', 'public');
        }

        // Determine message type
        if ($chat->image && $chat->video) {
            $chat->message_type = 'image_video';
        } elseif ($chat->image) {
            $chat->message_type = 'image';
        } elseif ($chat->video) {
            $chat->message_type = 'video';
        } elseif ($chat->message) {
            $chat->message_type = 'text';
        } else {
            $chat->message_type = 'system';
        }

        // Store current date and time separately
        $now = Carbon::now();
        $chat->message_date = $now->toDateString(); // e.g., 2025-06-05
        $chat->message_time = $now->toTimeString(); // e.g., 16:42:00

        $chat->save();

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => $chat
        ]);
    }
public function getMessages(Request $request)
{
    $authUserId = Auth::id(); // Get currently authenticated user's ID

    $messages = Chat::with([
            'user', 
            'replyTo' => function($query) {
                $query->with('user'); // Also load user of the replied message
            }
        ])
        ->get();

    $transformedMessages = $messages->map(function ($message) use ($authUserId) {
        return [
            'id'            => $message->id,
            'user_id'       => $message->user_id,
            'user'          => $message->user->name,
            'message'       => $message->message,
            'image'         => $message->image,
            'video'         => $message->video,
            'message_type'  => $message->message_type,
            'message_time'  => $message->message_time,
            'message_date'  => $message->message_date,
            //'is_deleted'  => $message->is_deleted,
            'created_at'    => $message->created_at,
            //'created_at'  => Carbon::parse($message->created_at)->format('h:i A'),
            'status'        => $message->user_id == $authUserId ? 1 : 0,
            'reply_to'      => $message->replyTo ? [
                // 'id'         => $message->replyTo->id,
                // 'user_id'    => $message->replyTo->user_id,
                // 'user'       => $message->replyTo->user,
                'message'       => $message->replyTo->message,
                'image'         => $message->replyTo->image,
                'video'         => $message->replyTo->video,
                //'message_type' => $message->replyTo->message_type,
                //'created_at' => $message->replyTo->created_at
            ] : null
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Messages retrieved successfully.',
        'data' => $transformedMessages
    ]);
}


public function updateMessage(Request $request, $id)
{
    $chat = Chat::findOrFail($id);

    // Ensure the authenticated user owns the message
    if ($chat->user_id !== auth()->id()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 403);
    }

    $request->validate([
        'message'   => 'nullable|string',
        'image'     => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        'video'     => 'nullable|file|mimes:mp4,avi,mov|max:10240',
    ]);

    // Update fields
    if ($request->has('message')) {
        $chat->message = $request->message;
    }

    if ($request->hasFile('image')) {
        $chat->image = $request->file('image')->store('chat_images', 'public');
    }

    if ($request->hasFile('video')) {
        $chat->video = $request->file('video')->store('chat_videos', 'public');
    }

    // Update message type
    if ($chat->image && $chat->video) {
        $chat->message_type = 'image_video';
    } elseif ($chat->image) {
        $chat->message_type = 'image';
    } elseif ($chat->video) {
        $chat->message_type = 'video';
    } elseif ($chat->message) {
        $chat->message_type = 'text';
    }

    $chat->save();

    return response()->json([
        'success'   => true,
        'message'   => 'Message updated successfully.',
        'data'      => $chat
    ]);
}
public function deleteMessage($id)
{
    $chat = Chat::findOrFail($id);

    if ($chat->user_id !== auth()->id()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 403);
    }

    $chat->is_deleted = true;
    $chat->save();

    return response()->json([
        'success' => true,
        'message' => 'Message deleted successfully.',
    ]);
}


public function saveDriverRequest(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized user',
        ], 401);
    }

    // Check if user already has a driver request
    $existingRequest = DriverRequest::where('user_id', $user->id)->first();

    if ($existingRequest) {
        // If exists, update status to 0
        $existingRequest->status = 0;
        $existingRequest->save();

        return response()->json([
            'success'   => true,
            'message'   => 'Existing driver request status reset to 0',
            'data'      => $existingRequest,
        ]);
    }

    // Else, create new driver request
    $driverRequest          = new DriverRequest();
    $driverRequest->user_id = $user->id;
    $driverRequest->name    = $user->name;
    $driverRequest->email   = $user->email;
    $driverRequest->number  = $user->phone_number ?? null;
    $driverRequest->area_id = $user->auto_area_id ?? null;
    //$driverRequest->status = 0; // default status
    $driverRequest->save();

    return response()->json([
        'success'   => true,
        'message'   => 'Driver request submitted successfully',
        'data'      => $driverRequest
    ]);
}
}
