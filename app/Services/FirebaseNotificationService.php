<?php

namespace App\Services;

use App\Models\User;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\SendMulticastMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('firebase_credentials.json'));
        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification()
    {
        // Fetch all device tokens, filter out null values, and flatten the array
        $deviceTokens = User::whereNotNull('device_token')->pluck('device_token')->toArray();
      
        if (empty($deviceTokens)) {
            return 'No device tokens found.';
        }
    
        $notification = [
            'title' => 'New Auto in Town! Don’t Miss Out!',
            'body' => 'Don’t wait—this new auto won’t be available for long!',
        ];

        // $notification = Notification::create(
        //     'New Auto in Town! Don’t Miss Out!',
        //     'Don’t wait—this new auto won’t be available for long!'
        // );
    
        // Firebase requires tokens for multicast message in batches of up to 500
        $chunks = array_chunk($deviceTokens, 500);
        foreach ($chunks as $tokens) {
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData(['click_action' => 'FLUTTER_NOTIFICATION_CLICK']); // Optional, if needed for custom actions
    
            $report = $this->messaging->sendMulticast($message, $tokens);
    
            // Handle successes
            $successfulMessages = $report->successes();
            if (!empty($successfulMessages)) {
                foreach ($successfulMessages as $success) {
                    Log::info('Notification sent successfully', [
                        'token' => $success->target(),
                        'message_id' => $success->result()['name'],
                    ]);
                }
            }
    
            // Handle failures
            $failures = $report->failures();
            if (!empty($failures)) {
                foreach ($failures as $failure) {
                    Log::error('Failed to send notification', [
                        'token' => $failure->target(),
                        'error' => $failure->error()->getMessage(),
                    ]);
                }
            }
        }
    
        return 'Notifications sent successfully!';
    }
    
}
