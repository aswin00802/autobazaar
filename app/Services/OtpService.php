<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use App\Models\EmailTemplate;

class OtpService
{
    public function send($templatekey,$templatevalue,$identifier, $expiry = 1, $cooldown = 60)
    {
        //check mobileno or not
        // $mobileNo = is_numeric($identifier) ? $identifier : null;
        
        // Check if blocked
        $blockedOtp = Otp::where('identifier', $identifier)
            ->whereNotNull('blocked_at')
            ->latest()
            ->first();
        
        if ($blockedOtp && $blockedOtp->blocked_at->addMinutes(10) > Carbon::now()) {
            return ['status' => false, 'message' => 'Too many wrong attempts. Try again after 10 minutes.'];
        }

        // Rate limit
        $recentOtp = Otp::where('identifier', $identifier)
            ->where('created_at', '>=', Carbon::now()->subSeconds($cooldown))
            ->latest()
            ->first();

        if ($recentOtp) {
            return ['status' => false, 'message' => "OTP already sent. Please wait {$cooldown} seconds."];
        }
        //check Email Template Create or Not
        // if(!isset($mobileNo)){
            $template = EmailTemplate::where($templatekey, $templatevalue)->first();
            if(!$template){
            return [
                    'status'  => false,
                    'message' => "The template not found: {$templatevalue}",
                ]; 
            }
        // }
        // Generate new OTP
        $otp = rand(1000, 9999);

        // Invalidate old OTPs
        Otp::where('identifier', $identifier)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // Save OTP
        $otpModel =Otp::create([
            'identifier' => $identifier,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes($expiry),
        ]);
        
        // Call email/sms template system
        $notificationStatus = sendOtpNotification($templatekey,$templatevalue,(object)[
            'email' => filter_var($identifier, FILTER_VALIDATE_EMAIL) ? $identifier : null,
            'mobile' => is_numeric($identifier) ? $identifier : null,
            'name' => 'User'
        ], $otp, $expiry);

        //check result
        if (is_array($notificationStatus) && ($notificationStatus['status'] ?? false) === true) {
            return ['status' => true, 'message' => 'OTP sent successfully'];
        }
        // optional: mark OTP invalid if notification failed
        $otpModel->update(['is_used' => true]);
        
        return [
            'status'  => false,
            'message' => $notificationStatus['message'] ?? 'Failed to send OTP'
        ];

    }

    public function verify($identifier, $otpInput, $maxAttempts = 5,array $extraData = [])
    {
        $otpData = Otp::where('identifier', $identifier)
            ->where('is_used', false)
            ->where('expires_at', '>=', Carbon::now())
            ->latest()
            ->first();
        
        if (!$otpData || $otpData->otp != $otpInput) {
            if ($otpData) {
                $otpData->increment('attempts');
                if ($otpData->attempts >= $maxAttempts) {
                    $otpData->update(['blocked_at' => Carbon::now()]);
                }
            }
            return ['status' => false, 'message' => 'Invalid or expired OTP'];
        }

        $otpData->update(['is_used' => true]);

        // Create/find user (for login case only)
        // $user = User::firstOrCreate(
        //     [
        //         'email' => filter_var($identifier, FILTER_VALIDATE_EMAIL) ? $identifier : null,
        //         'mobile' => is_numeric($identifier) ? $identifier : null
        //     ],
        //     // ['name' => 'Guest User']
        // );

        $user = User::where(function ($query) use ($identifier) {
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $query->where('email', $identifier);
            } elseif (is_numeric($identifier)) {
                $query->where('mobile', $identifier);
            }
        })->first();

        if (!$user) {
            return [
                'status'  => false,
                'message' => 'User not found'
            ];
        }
        $user->otp          = $otpInput;
        $user->otp_status   = 'verified';
        $user->last_login   =  now();
        if (isset($extraData['fcm_token'])) {
            $user->fcm_token = $extraData['fcm_token'];
        }

        if (isset($extraData['device_id'])) {
            $user->device_id = $extraData['device_id'];
        }
        $user->save();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'status'    => true,
            'message'   => 'OTP verified successfully',
            'token'     => $token,
            'user'      => $user,
            'show_mode_selection' => empty($user->selected_mode),
            'selected_mode' => $user->selected_mode
        ];
    }
}