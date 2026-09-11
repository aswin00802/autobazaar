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
        // \Log::info($request);exit;
        $inputs = $request->except(['_token', 'admin_logo', 'web_logo', 'fav_icon']);
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
                'client_secret' => $request->input("{$platform}_client_secret"),
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
        return back()->with('success', 'General Settings saved successfully!');
    }
}
