<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
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
     * Step 1: API Redirect (Return Provider Login URL)
    */
    public function apiRedirectToProvider($provider)
    {
        // $config = Setting::where('key',$provider)->where('status_id',1)->first();
        
        // if (!$config) {
        //     return response()->json(['error' => 'Provider not configured'], 400);
        // }
        // // Convert JSON string to associative array
        // $social_api = json_decode($config->value, true);

        // $clientId       = $social_api['client_id'];
        // $clientSecret   = $social_api['client_secret'];
        // $redirect       = $social_api['redirect'];
        
        // config([
        //     "services.$provider" => [
        //         'client_id'     => $clientId,
        //         'client_secret' => $clientSecret,
        //         'redirect'      => $redirect,
        //     ]
        // ]);
        // $redirectUrl = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        
        // return response()->json([
        //     'auth_url' => $redirectUrl
        // ]);

        if (!$this->isConfigured) {
            return response()->json([
                'error' => 'Provider not configured',
                'provider' => $this->provider
            ], 400);
        }

        $redirectUrl = Socialite::driver($this->provider)->stateless()->redirect()->getTargetUrl();

        return response()->json(['auth_url' => $redirectUrl]);
    }
    /**
     * Step 2: API Callback (Handle Provider Response)
    */
    public function apiHandleProviderCallback($provider)
    {
        // $config = Setting::where('key',$provider)->where('status_id',1)->first();
        
        // if (!$config) {
        //     return response()->json(['error' => 'Provider not configured'], 400);
        // }
        // // Convert JSON string to associative array
        // $social_api = json_decode($config->value, true);

        // $clientId       = $social_api['client_id'];
        // $clientSecret   = $social_api['client_secret'];
        // $redirect       = $social_api['redirect'];
        
        // config([
        //     "services.$provider" => [
        //         'client_id'     => $clientId,
        //         'client_secret' => $clientSecret,
        //         'redirect'      => $redirect,
        //     ]
        // ]);
        // try {
        //     $socialUser = Socialite::driver($provider)->stateless()->user();
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'error' => 'Login failed',
        //         'message' => $e->getMessage()
        //     ], 400);
        // }
       
        // $user = User::updateOrCreate(
        //     [$provider . '_id' => $socialUser->getId()],
        //     [
        //         'name' => $socialUser->getName(),
        //         'email' => $socialUser->getEmail(),
        //         'avatar' => $socialUser->getAvatar(),
        //     ]
        // );

        // $token = $user->createToken('api-token')->plainTextToken;

        // return response()->json([
        //     'user' => $user,
        //     'token' => $token,
        //     'provider' => $provider,
        // ]);

        if (!$this->isConfigured) {
            return response()->json([
                'error' => 'Provider not configured',
                'provider' => $this->provider
            ], 400);
        }

        try {
            $socialUser = Socialite::driver($this->provider)->stateless()->user();
            // $user = User::updateOrCreate(
            //     [$this->provider . '_id' => $socialUser->getId()],
            //     [
            //         'name'   => $socialUser->getName(),
            //         'email'  => $socialUser->getEmail(),
            //         'avatar' => $socialUser->getAvatar(),
            //     ]
            // );

            // $token = $user->createToken('api-token')->plainTextToken;

            // return response()->json([
            //     'user'     => $user,
            //     'token'    => $token,
            //     'provider' => $this->provider,
            // ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Login failed',
                'message' => $e->getMessage(),
            ], 400);
        }

        
    }
}
