<?php
use GuzzleHttp\Client;
use App\Models\Setting;
use App\Mail\OTPMailSend;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
if (!function_exists('areActiveRoutes')) {
    # return active class
    function areActiveRoutes(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
        return '';
    }
}

if (!function_exists('areActiveRoutesList')) {
    # return active class
    function areActiveRoutesList(array $routes, $output = "active open")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
        return '';
    }
}

if(!function_exists('uploadedAsset')){
    #upload the files
    function uploadedAsset($request,$upload_file_name,$file_name,$path)
    {
        if($request->hasFile($upload_file_name)){
            //create folder in public directory
            // if (!file_exists(public_path('uploads/'.$path))) {
            //     mkdir(public_path('uploads/'.$path));
            // }
            $filepath = 'uploads/'.$path;
            $fullPath = public_path('uploads/'.$path);

            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true); // mode + recursive only mkdir ku podanum
            }

            $destinationPath = public_path('uploads/'.$path); //set the storeage path
            
            $file = $request->file($upload_file_name);

            $filename = $file->getClientOriginalName(); //get image name

            $extension = $file->getClientOriginalExtension(); //get image type

            $fileName = $file_name."." .$extension;

            // $fileName = time().rand(100,999) . "." .$extension; // to create alternative name in a image

            $file->move($destinationPath, $fileName); //move the file

            return $filepath.'/'.$fileName;
        }
    }
}

if(!function_exists('uploadedAssetFile')){
    function uploadedAssetFile($file, $file_name, $path)
    {
        $filepath = 'uploads/'.$path;
        $fullPath = public_path($filepath);

        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        $extension = $file->getClientOriginalExtension();
        $finalName = $file_name.".".$extension;

        $file->move($fullPath, $finalName);

        return $filepath.'/'.$finalName;
    }
}

if (!function_exists('cacheClear')) {
    # clear server cache
    function cacheClear()
    {
        try {
            Artisan::call('cache:forget spatie.permission.cache');
        } catch (\Throwable $th) {
            //throw $th;
        }

        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
    }
}

if (!function_exists('writeToEnvFile')) {
    # write To Env File
    function
    writeToEnvFile($type, $val)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            //$val = '"' . trim($val) . '"';
            if (is_numeric(strpos(file_get_contents($path), $type)) && strpos(file_get_contents($path), $type) >= 0) {
                file_put_contents($path, str_replace(
                    // $type . '="' . env($type) . '"',
                    $type . '=' . env($type) ,
                    $type . '=' . $val,
                    file_get_contents($path)
                ));
            } else {
                file_put_contents($path, file_get_contents($path) . "\r\n" . $type . '=' . $val);
            }
        }
        cacheClear();
    }
}

if (!function_exists('removeAsset')) {
    #files delete the folder
    function
    removeAsset($MODEL, $condition , $value , $path, $removefield)
    {
        $query = $MODEL::query();
        $results = $query->where($condition,$value)->first();
        if($results){
            if(file_exists(public_path('uploads').'/'.$path.'/'.$results->value)){
                unlink(public_path('uploads').'/'.$path.'/'.$results->value);
                $query->update([$removefield => NULL]);
            }
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('deleteSettingFile')) {
    function deleteSettingFile($key, $path)
    {
        $setting = Setting::where('key', $key)->first();

        if ($setting && $key) {
            $filePath = public_path($setting->value);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            Setting::where('key', $key)->update(['value' => null]);
            Setting::flushCache();

            return true;
        }

        return false;
    }
}

if (!function_exists('getSetting')) {
    /**
     * Reads from the cached settings map (one query per cache window, not one per call).
     * Same return values as before: social settings only when active.
     */
    function getSetting($key, $default = null) {
        $setting = Setting::cachedRow((string) $key);
        if (!$setting) {
            return $default;
        }
        if ($setting['type'] === 'social') {
            // for socials, only return if active
            return $setting['status_id'] == 1 ? $setting['value'] : $default;
        }
        return $setting['value'] ?? $default;
    }
}

if (!function_exists('safeApiMessage')) {
    /**
     * Message that is safe to send to a mobile app for a caught exception.
     * Business messages ("Ride not found", "Razorpay is not enabled") pass through;
     * database, PHP and filesystem errors are logged and replaced, so SQL, table
     * names and file paths never leave the server. The response shape is unchanged.
     */
    function safeApiMessage(\Throwable $e, string $fallback = 'Something went wrong. Please try again.'): string
    {
        $internal = $e instanceof \PDOException
            || $e instanceof \Error
            || $e instanceof \ErrorException
            || $e instanceof \Illuminate\Database\QueryException
            || $e instanceof \Illuminate\Contracts\Container\BindingResolutionException;

        $message = (string) $e->getMessage();

        // Belt and braces: anything that looks like SQL or a server path is internal too.
        if (!$internal && preg_match('/SQLSTATE|select\s.+\sfrom\s|insert\s+into|\bvendor[\/\\\\]|\.php\b|[A-Za-z]:\\\\|\/var\/|\/home\//i', $message)) {
            $internal = true;
        }

        if ($internal || $message === '') {
            \Illuminate\Support\Facades\Log::error('api.internal_error', [
                'exception' => get_class($e),
                'message'   => $message,
                'file'      => $e->getFile() . ':' . $e->getLine(),
                'url'       => request()?->fullUrl(),
            ]);

            return $fallback;
        }

        return $message;
    }
}

if (!function_exists('isReviewLogin')) {
    /**
     * App store reviewers sign in with a fixed phone + OTP because they cannot
     * receive our SMS. It is controlled from Admin > Settings > General so it can
     * be switched off outside review windows. With no saved setting it behaves
     * exactly as the app always has.
     *
     * @param string $app 'driver' (AutoBazaar app) or 'customer' (FairPrice app)
     */
    function isReviewLogin(string $app, $phone, $otp): bool
    {
        $expectedOtp = reviewLoginOtp($app, $phone);

        return $expectedOtp !== null && hash_equals($expectedOtp, (string) $otp);
    }
}

if (!function_exists('reviewLoginOtp')) {
    /**
     * The fixed OTP for a reviewer phone number, or null when the number is not a
     * reviewer number or review login is switched off.
     */
    function reviewLoginOtp(string $app, $phone): ?string
    {
        $defaults = [
            'driver_phone'   => '9094262603',
            'customer_phone' => '8939345008',
            'otp'            => '2203',
        ];

        $row = Setting::cachedRow('review_login');

        if ($row) {
            if ((int) $row['status_id'] !== 1) {
                return null;
            }
            $config = array_merge($defaults, array_filter(json_decode((string) $row['value'], true) ?: []));
        } else {
            $config = $defaults;
        }

        $expectedPhone = (string) ($config[$app . '_phone'] ?? '');

        if ($expectedPhone === '' || !hash_equals($expectedPhone, (string) $phone)) {
            return null;
        }

        return (string) $config['otp'];
    }
}

// if (!function_exists('getSetting')) {
//     # return system settings value
//     function getSetting($key, $default = null)
//     {
//         try {
//             $setting = SystemSetting::where('entity',$key)->first();
//             return $setting == null ? $default : $setting->value;
//         } catch (\Throwable $th) {
//             return $default;
//         }
//     }
// }

if (!function_exists('extractPlaceholders')) {
    function extractPlaceholders($text)
    {
        // Match all {{ something }}
        // preg_match_all('/\{\{\s*(.*?)\s*\}\}/', $text, $matches);

        // $matches[1] will have only the inside content
        // Example: "otp_code", "user_name"
        // return $matches[1] ?? [];

        // Match all {{ $variable }}
        preg_match_all('/\{\{\s*\$([a-zA-Z0-9_]+)\s*\}\}/', $text, $matches);

        // $matches[1] => variable names without the $
        // Example: otp_code, user_name
        return $matches[1] ?? [];
    }
}

//send otp notifications common
if (!function_exists('sendOtpNotification')) {
    function sendOtpNotification($templatekey,$templatevalue,$user, $otp, $expiry = 5,array $extraData = [])
    {
        try {
            $template = EmailTemplate::where($templatekey, $templatevalue)->first();

            if (!$template) {
                return [
                    'status'  => false,
                    'message' => "The template not found: {$templatevalue}"
                ];
            }

            // Common data
            $common = [
                'user_name'       => $user->name ?? 'User',
                'app_name'        => config('app.name'),
                'otp_code'        => $otp,
                'otp_expiry_time' => $expiry,
            ];

            $allData = array_merge($common, $extraData);

            // Detect placeholders
            $placeholders = array_unique(array_merge(
                extractPlaceholders($template->subject ?? ''),
                extractPlaceholders($template->body ?? '')
            ));

            $filteredData = [];
            foreach ($placeholders as $ph) {
                $filteredData[$ph] = $allData[$ph] ?? '';
            }

            $subject = Blade::render($template->subject ?? '', $filteredData);
            $body    = Blade::render($template->body ?? '', $filteredData);

            // Send Email
            if (!empty($user->email)) {
                // $subject = Blade::render($template->subject ?? '', $filteredData);
                // $body    = Blade::render($template->body ?? '', $filteredData);

                Mail::to($user->email)->send(new OTPMailSend($subject, $body));
            }

            // Send SMS
            if (!empty($user->mobile)) {
                // $smsTemplate = "Your OTP is {{$otp_code}}. It is valid for {{$otp_expiry_time}} minutes - {{$app_name}}.";
                // $smsMessage  = Blade::render($smsTemplate, $filteredData);

                // SMS::send($user->mobile, $smsMessage);
                $smsTemplate = "Your OTP is 9985. It is valid for 1 minutes - Laravel.";
                $url = 'http://site.ping4sms.com/api/smsapi';
                $params = [
                    'key'        => '1dfbed55da0ae9f9cf4ffd1d350abd06',
                    'route'      => 2,
                    'sender'     => 'PNGOTP',
                    'number'     => $user->mobile,
                    'sms'        => $smsTemplate,
                    'templateid' => '1507165967974501361'
                ];
                $client = new Client();
                $response = $client->post($url, [
                    'form_params' => $params
                ]);
                // \Log::info('Ping4SMS API Response Status : ' . $response->getStatusCode());
                // \Log::info('Ping4SMS API Response Body : ' . $response->getBody()->getContents());
            }

            return [
                'status'  => true,
                'message' => 'OTP sent successfully'
            ];

        } catch (\Throwable $e) {
            return [
                'status'  => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ];
        }
    }
}
