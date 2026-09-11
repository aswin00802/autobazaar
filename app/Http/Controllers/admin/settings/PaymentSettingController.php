<?php

namespace App\Http\Controllers\admin\settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentSettingController extends Controller
{
    /**
     * Gateway definitions: setting key => label + fields (name => [label, required]).
     */
    public const GATEWAYS = [
        'razorpay' => [
            'label' => 'Razorpay',
            'fields' => [
                'key_id'         => ['label' => 'Razorpay Key ID',         'required' => true],
                'key_secret'     => ['label' => 'Razorpay Key Secret',     'required' => true],
                'webhook_secret' => ['label' => 'Razorpay Webhook Secret', 'required' => false],
            ],
        ],
        'stripe' => [
            'label' => 'Stripe',
            'fields' => [
                'publishable_key' => ['label' => 'Stripe Publishable Key', 'required' => true],
                'secret_key'      => ['label' => 'Stripe Secret Key',      'required' => true],
                'webhook_secret'  => ['label' => 'Stripe Webhook Secret',  'required' => false],
            ],
        ],
    ];

    public function __construct()
    {
        $this->middleware(['permission:payment_setting'])->only(['index', 'update']);
    }

    public function index()
    {
        $gateways = [];

        foreach (self::GATEWAYS as $key => $gateway) {
            $setting = Setting::where('key', $key)->first();
            $values  = $setting ? (json_decode($setting->value, true) ?: []) : [];
            $enabled = $setting ? (int) $setting->status_id === 1 : false;

            // First run: no saved Razorpay row yet, so prefill from .env and show it enabled.
            if (!$setting && $key === 'razorpay' && config('services.razorpay.key')) {
                $values = [
                    'key_id'     => config('services.razorpay.key'),
                    'key_secret' => config('services.razorpay.secret'),
                ];
                $enabled = true;
            }

            $gateways[$key] = $gateway + ['enabled' => $enabled, 'values' => $values];
        }

        return view('admin.settings.payment-setting.index', compact('gateways'));
    }

    public function update(Request $request)
    {
        $rules    = [];
        $messages = [];

        foreach (self::GATEWAYS as $key => $gateway) {
            foreach ($gateway['fields'] as $field => $meta) {
                $input = "{$key}_{$field}";
                $rules[$input] = $meta['required']
                    ? "required_if:{$key}_enabled,on|nullable|string|max:255"
                    : 'nullable|string|max:255';
                $messages["{$input}.required_if"] = "{$meta['label']} is required when {$gateway['label']} is enabled.";
            }
        }

        $request->validate($rules, $messages);

        foreach (self::GATEWAYS as $key => $gateway) {
            $enabled = $request->has("{$key}_enabled");

            if ($enabled) {
                $data = [];
                foreach (array_keys($gateway['fields']) as $field) {
                    $data[$field] = trim((string) $request->input("{$key}_{$field}"));
                }

                Setting::updateOrInsert(
                    ['key' => $key],
                    [
                        'value'      => json_encode($data),
                        'type'       => 'payment',
                        'status_id'  => 1,
                        'created_by' => Auth::id(),
                        'ip_address' => $request->ip(),
                        'updated_at' => now(),
                    ]
                );
            } else {
                Setting::where('key', $key)->update([
                    'status_id'  => 0,
                    'created_by' => Auth::id(),
                    'ip_address' => $request->ip(),
                    'updated_at' => now(),
                ]);
            }
        }

        return back()->with('success', 'Payment Settings saved successfully!');
    }
}
