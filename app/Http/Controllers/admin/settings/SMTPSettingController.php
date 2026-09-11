<?php

namespace App\Http\Controllers\admin\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SMTPSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:smtp_setting'])->only(['index','update']);
    }

    public function index()
    {
        return view('admin.settings.smtp-setting.index');
    }

    public function update(Request $request)
    {
        foreach ($request->types as $key => $type) {
            writeToEnvFile($type, $request[$type]);
        }
        return back()->with('success', 'SMTP Settings saved successfully!');
    }
}
