<?php

namespace App\Http\Controllers\admin\settings;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:general_setting'])->only(['index','removeFile','update']);
    }

    public function index()
    {
        $setting = Setting::all();
        $timezones = \DateTimeZone::listIdentifiers();
        return view('admin.settings.general-setting.index',compact('timezones'));
    }

    public function removeFile(Request $request)
    {
        $removeFile = deleteSettingFile($request->key,'settings');
        return $removeFile;
    }

    public function update(Request $request)
    {
        $request->validate([
            'review_driver_phone'   => 'required_if:review_login_enabled,on|nullable|digits_between:8,15',
            'review_customer_phone' => 'required_if:review_login_enabled,on|nullable|digits_between:8,15',
            'review_otp'            => 'required_if:review_login_enabled,on|nullable|digits_between:4,6',

            // Website contact details (read by App\Support\SiteData)
            'business_email'   => 'nullable|email|max:150',
            'business_mobile'  => ['nullable', 'regex:/^(\+?91[\s-]?)?[6-9]\d{4}[\s-]?\d{5}$/'],
            'business_address' => 'nullable|string|max:300',
            'social_instagram' => 'nullable|url:http,https|max:255',
            'social_facebook'  => 'nullable|url:http,https|max:255',
            'social_youtube'   => 'nullable|url:http,https|max:255',
            'social_twitter'   => 'nullable|url:http,https|max:255',
            'social_linkedin'  => 'nullable|url:http,https|max:255',
        ], [
            'business_mobile.regex' => 'Business Mobile must be a valid 10 digit Indian mobile number.',
            'review_driver_phone.required_if'   => 'AutoBazaar app test number is required when Review Login is enabled.',
            'review_customer_phone.required_if' => 'FairPrice app test number is required when Review Login is enabled.',
            'review_otp.required_if'            => 'Fixed OTP is required when Review Login is enabled.',
            'url' => 'The :attribute must be a full link starting with https://',
        ], [
            // friendly names for the error messages
            'business_email'   => 'Business Email',
            'business_address' => 'Business Address',
            'social_instagram' => 'Instagram link',
            'social_facebook'  => 'Facebook link',
            'social_youtube'   => 'YouTube link',
            'social_twitter'   => 'X (Twitter) link',
            'social_linkedin'  => 'LinkedIn link',
        ]);

        $inputs = $request->except([
            '_token', 'admin_logo', 'web_logo', 'fav_icon',
            'review_login_enabled', 'review_driver_phone', 'review_customer_phone', 'review_otp',
        ]);

        $this->saveReviewLogin($request);

        // The generic loop below only saves filled boxes, so an emptied box would keep its
        // old value forever. For the website contact fields, empty means "go back to the
        // built-in value", so the saved row is removed.
        foreach (array_merge(['business_email', 'business_mobile', 'business_address'], array_keys(\App\Support\SiteData::SOCIAL_NETWORKS)) as $contactKey) {
            if ($request->exists($contactKey) && trim((string) $request->input($contactKey)) === '') {
                Setting::where('key', $contactKey)->delete();
            }
        }
        // file uploads using helper
        $files = ['admin_logo', 'web_logo', 'fav_icon'];
        foreach ($files as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $path = uploadedAsset($request, $fileKey, $fileKey, 'settings');
                if ($path) {
                    Setting::updateOrInsert(
                        ['key' => $fileKey],
                        [
                            'value'      => $path,
                            'type'       => 'file',
                            'status_id'  => 1,
                            'created_by' => Auth::user()->id,
                            'ip_address' => $request->ip(),
                        ]
                    );
                }
            }
            unset($inputs[$fileKey]);
        }
        // group socials
        $socialPlatforms = ['google', 'facebook', 'twitter', 'github', 'linkedin', 'apple'];
        foreach ($socialPlatforms as $platform) {
            $checked = $request->has("{$platform}_login"); // checkbox present & checked
            $data = [
                'client_id'     => $request->input("{$platform}_client_id"),
                'client_secret' => Setting::encryptSecret($request->input("{$platform}_client_secret")),
                'redirect'      => $request->input("{$platform}_redirect"),
            ];
            if ($checked) {
                if (!empty(array_filter($data))) { // only save if at least one value
                    Setting::updateOrInsert(
                        ['key' => $platform],
                        [
                            'value'      => json_encode($data), // or serialize($data)
                            'type'       => 'social',
                            'status_id'  => 1,
                            'created_by' => Auth::user()->id,
                            'ip_address' => $request->ip(),
                        ]
                    );
                }
            } else {
                Setting::where('key', $platform)->update([
                    'status_id'     => 0,
                    'created_by'    => Auth::user()->id,
                    'updated_at'    => now(),
                    'ip_address'    => $request->ip(),
                ]);
            }

            // remove from normal inputs
            unset($inputs["{$platform}_client_id"], $inputs["{$platform}_client_secret"], $inputs["{$platform}_redirect"], $inputs["{$platform}_login"]);
        }
        // save other normal inputs
        foreach ($inputs as $key => $value) {
            if(!empty($value)){
                Setting::updateOrInsert(
                    ['key' => $key],
                    [
                        'value'      => $value,
                        'type'       => 'text',
                        'status_id'  => 1,
                        'created_by' => Auth::user()->id,
                        'ip_address' => $request->ip(),
                    ]
                );
            } 
        }
        // updateOrInsert bypasses model events, so drop the cached settings here.
        Setting::flushCache();

        return back()->with('success', 'General Settings saved successfully!');
    }

    /**
     * App store reviewer login (fixed phone + OTP). Off = the numbers behave like any other user.
     */
    private function saveReviewLogin(Request $request): void
    {
        $existing = Setting::where('key', 'review_login')->first();
        $enabled  = $request->has('review_login_enabled');

        if (!$enabled && !$existing) {
            // First time it is switched off: store the row so "off" is remembered.
            $existing = null;
        }

        $current = $existing ? (json_decode((string) $existing->value, true) ?: []) : [];

        $value = $enabled ? [
            'driver_phone'   => (string) $request->input('review_driver_phone'),
            'customer_phone' => (string) $request->input('review_customer_phone'),
            'otp'            => (string) $request->input('review_otp'),
        ] : $current;

        Setting::updateOrInsert(
            ['key' => 'review_login'],
            [
                'value'      => json_encode($value),
                'type'       => 'review_login',
                'status_id'  => $enabled ? 1 : 0,
                'created_by' => Auth::user()->id,
                'ip_address' => $request->ip(),
                'updated_at' => now(),
            ]
        );
    }
}
