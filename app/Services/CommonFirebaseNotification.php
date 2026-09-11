<?php

namespace App\Services;

use App\Models\NotificationRecord;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\SendMulticastMessage;
use Kreait\Firebase\Messaging\AndroidConfig;

class CommonFirebaseNotification 
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('firebase_credentials.json'));
        $this->messaging = $factory->createMessaging();
    }

    public function sendCommonNotification($tokens, string $title, string $body, array $data = [], $userIds = null)
    {
        $tokens  = is_array($tokens) ? $tokens : [$tokens];
        $userIds = is_array($userIds) ? $userIds : ($userIds ? [$userIds] : []);
        
        $channelId = 'normal_v6';     
        if (isset($data['payload'])) {         
            $decoded = json_decode($data['payload'], true);         
            if (isset($decoded['android_channel_id'])) {             
                $channelId = $decoded['android_channel_id'];         
            }     
        }

        $sound = ($channelId == 'emergency_v6') ? 'emergency_alert' : 'default';

        // Prepare Firebase Notification
        // $notification = Notification::create($title, $body);
        $payloadData = array_merge(
            array_map('strval', $data), // 🔥 convert all to string
            [
                'title' => (string) $title,
                'body'  => (string) $body,
            ]
        );

        // Split into 500-token chunks
        $chunks = array_chunk($tokens, 500);

        $successCount = 0; //get notification success count
        $failureCount = 0; //get notification fail count

        $sentTokens = [];
        $failedTokens = [];
        
        // $channelId = $data['android_channel_id'] ?? 'normal_v5';
        // $sound = ($channelId == 'emergency_v5') ? 'emergency_alert' : 'default';
        
        foreach ($chunks as $index => $batch) {
            $androidConfig = AndroidConfig::fromArray([             'priority' => 'high', 
                'notification' => [                 
                    'channel_id' => $channelId,                 
                    'sound' => $sound,                 
                    'notification_priority' => 'PRIORITY_MAX', 
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',             
                ],         
            ]);
            // Log::info($batch);
            // $message = CloudMessage::new()
            //     ->withNotification($notification)
            //     ->withData($data);
            $message = CloudMessage::new()
                        ->withNotification(Notification::create($title, $body))
                        ->withData($payloadData)
                        ->withAndroidConfig($androidConfig);


            // $report = $this->messaging->sendMulticast($message, $batch);
            try {
                $report = $this->messaging->sendMulticast($message, $batch);
            } catch (\Exception $e) {
                Log::error("🔥 FIREBASE SEND ERROR:", ['error' => $e->getMessage()]);
                continue;
            }
            $items = $report->getItems();

            foreach ($items as $i => $result) {
                $token = $batch[$i];

                if ($result->isSuccess()) {
                    $successCount++;
                    $sentTokens[] = $token;
                } else {
                    $failureCount++;
                    $failedTokens[] = $token;

                    $error = $result->error();
                    $errorMessage = 'Unknown error';
                    if (is_object($error) && method_exists($error, 'getMessage')) {
                        $errorMessage = $error->getMessage();
                    } elseif (is_array($error) && isset($error['message'])) {
                        $errorMessage = $error['message'];
                    } elseif (is_string($error) && $error !== '') {
                        $errorMessage = $error;
                    }

                    Log::error("❌ FAILED", [
                        'token' => $token,
                        'error' => $errorMessage,
                    ]);
                }
            }
        }

        //NOW SAVE TO DATABASE
        $successUsers = [];
        $failedUsers  = [];

        foreach ($userIds as $uid) {
            $user = User::find($uid);
            if (!$user) continue;

            $token = $user->device_token ?? null;

            if ($token && in_array($token, $sentTokens)) {
                $successUsers[] = [
                    'user_id'   => $uid,
                    'name'      => $user->name,
                    'email'     => $user->email,
                    'mobile'    => $user->mobile,
                    'token'     => $token,
                    'status'    => 'success'
                ];
            }

            if ($token && in_array($token, $failedTokens)) {
                $failedUsers[] = [
                    'user_id'   => $uid,
                    'name'      => $user->name,
                    'email'     => $user->email,
                    'mobile'    => $user->mobile,
                    'token'     => $token,
                    'status'    => 'failed'
                ];
            }
        }

        $payload = [
            'title'         => $title,
            'body'          => $body,
            'data'          => $data,
            'success_users' => $successUsers,
            'failed_users'  => $failedUsers,
            'total_success' => $successCount,
            'total_failed'  => $failureCount,
            'created_at'    => now()
        ];

        // Store in DB as ONE ROW
        NotificationRecord::create([
            'date'      => date('Y-m-d'),
            'payload'   => json_encode($payload)
        ]);

        return "Notifications processed successfully! ✅ Success: {$successCount}, ❌ Failed: {$failureCount}";
    }
}