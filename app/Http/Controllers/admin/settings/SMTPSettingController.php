<?php

namespace App\Http\Controllers\admin\settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SMTPSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:smtp_setting'])->only(['index','update']);
    }

    public function index()
    {
        $row = Setting::where('key', 'smtp')->first();

        if ($row) {
            $smtp = Setting::decodeCredentials($row->value, ['password']);
        } else {
            // Nothing saved yet: show what the server is using from .env today.
            $smtp = [
                'mailer'       => config('mail.default'),
                'host'         => config('mail.mailers.smtp.host'),
                'port'         => config('mail.mailers.smtp.port'),
                'username'     => config('mail.mailers.smtp.username'),
                'password'     => config('mail.mailers.smtp.password'),
                'encryption'   => config('mail.mailers.smtp.scheme') === 'smtps' ? 'ssl' : 'tls',
                'from_address' => config('mail.from.address'),
                'from_name'    => config('mail.from.name'),
            ];
        }

        return view('admin.settings.smtp-setting.index', compact('smtp'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'mailer'       => 'required|in:smtp,sendmail',
            'host'         => 'required_if:mailer,smtp|nullable|string|max:255',
            'port'         => 'required_if:mailer,smtp|nullable|integer|between:1,65535',
            'username'     => 'nullable|string|max:255',
            'password'     => 'nullable|string|max:255',
            'encryption'   => 'nullable|in:tls,ssl,none',
            'from_address' => 'required|email|max:255',
            'from_name'    => 'required|string|max:255',
        ], [
            'host.required_if' => 'Mail Host is required for SMTP.',
            'port.required_if' => 'Mail Port is required for SMTP.',
        ]);

        $validated['password'] = Setting::encryptSecret($validated['password'] ?? null);

        // Saved to the settings table (password encrypted). The .env file is no longer rewritten.
        Setting::updateOrInsert(
            ['key' => 'smtp'],
            [
                'value'      => json_encode($validated),
                'type'       => 'smtp',
                'status_id'  => 1,
                'created_by' => Auth::id(),
                'ip_address' => $request->ip(),
                'updated_at' => now(),
            ]
        );
        Setting::flushCache();

        return back()->with('success', 'SMTP Settings saved successfully!');
    }
}
