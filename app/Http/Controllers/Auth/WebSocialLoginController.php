<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class WebSocialLoginController extends Controller
{
    protected $provider;
    protected $isConfigured = false;

    public function __construct(Request $request)
    {
        $this->provider = $request->route('provider');

        if ($this->provider) {
            $config = Setting::where('key', $this->provider)
                ->where('status_id', 1)
                ->first();

            if ($config) {
                $social_api = json_decode($config->value, true);

                // config([
                //     "services.{$this->provider}.client_id"     => $social_api['client_id'] ?? null,
                //     "services.{$this->provider}.client_secret" => $social_api['client_secret'] ?? null,
                //     "services.{$this->provider}.redirect"      => $social_api['redirect'] ?? null,
                // ]);
                $clientId       = $social_api['client_id'];
                $clientSecret   = $social_api['client_secret'];
                $redirect       = $social_api['redirect'];
                
                config([
                    "services.$this->provider" => [
                        'client_id'     => $clientId,
                        'client_secret' => $clientSecret,
                        'redirect'      => $redirect,
                    ]
                ]);

                $this->isConfigured = true;
            }
        }
    }

    /**
     * Step 1: Redirect user to provider
     */
    public function redirectToProvider()
    {
        if (!$this->isConfigured) {
            return redirect('/login')->withErrors([
                'error' => ucfirst($this->provider) . ' login not configured'
            ]);
        }

        return Socialite::driver($this->provider)->redirect();
    }

    /**
     * Step 2: Handle provider callback
     */
    public function handleProviderCallback()
    {
        if (!$this->isConfigured) {
            return redirect('/login')->withErrors([
                'error' => ucfirst($this->provider) . ' login not configured'
            ]);
        }

        try {
            $socialUser = Socialite::driver($this->provider)->user();
            // $user = User::updateOrCreate(
            //     [$this->provider . '_id' => $socialUser->getId()],
            //     [
            //         'name'   => $socialUser->getName(),
            //         'email'  => $socialUser->getEmail(),
            //         'avatar' => $socialUser->getAvatar(),
            //     ]
            // );

            // auth()->login($user);

            // return redirect('/dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors([
                'error' => 'Login failed: ' . $e->getMessage()
            ]);
        }

        
    }
}
