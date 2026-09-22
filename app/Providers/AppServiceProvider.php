<?php

namespace App\Providers;

use App\Interfaces\AutoInterface;
use App\Models\Setting;
use App\Repositories\AutoRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Api\V1\SoldAutoController type-hints the interface (GET api/get-sold-auto).
        $this->app->bind(AutoInterface::class, AutoRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiters();
        $this->configureTokenIdleExpiry();
        $this->applyMailSettings();
    }

    /**
     * Limits for the mobile login endpoints. Keyed by phone number so one person
     * cannot brute-force a 4-digit OTP or drain SMS credit, with a much higher
     * per-network ceiling so many drivers behind one office/shop Wi-Fi are fine.
     * Normal use never gets near these numbers.
     */
    private function configureRateLimiters(): void
    {
        $phoneOf = fn (Request $request) => preg_replace('/\D+/', '', (string) ($request->input('phone') ?? $request->input('phone_number') ?? ''));

        RateLimiter::for('otp-send', function (Request $request) use ($phoneOf) {
            $phone = $phoneOf($request);

            return array_filter([
                $phone !== '' ? Limit::perMinute(5)->by('otp-send:phone:' . $phone) : null,
                $phone !== '' ? Limit::perHour(20)->by('otp-send-hour:phone:' . $phone) : null,
                Limit::perMinute(60)->by('otp-send:ip:' . $request->ip()),
            ]);
        });

        RateLimiter::for('otp-verify', function (Request $request) use ($phoneOf) {
            $phone = $phoneOf($request);

            return array_filter([
                $phone !== '' ? Limit::perMinute(10)->by('otp-verify:phone:' . $phone) : null,
                Limit::perMinute(120)->by('otp-verify:ip:' . $request->ip()),
            ]);
        });

        RateLimiter::for('api-login', function (Request $request) use ($phoneOf) {
            $phone = $phoneOf($request);

            return array_filter([
                $phone !== '' ? Limit::perMinute(10)->by('api-login:phone:' . $phone) : null,
                Limit::perMinute(120)->by('api-login:ip:' . $request->ip()),
            ]);
        });
    }

    /**
     * API tokens stop working after a long period of no use, so a leaked or
     * abandoned token does not live forever. Anyone who opens the app within the
     * window is never logged out. Tokens issued before the cut-off date are left
     * exactly as they were, so installed apps see no change.
     */
    private function configureTokenIdleExpiry(): void
    {
        $idleDays = (int) config('sanctum.idle_days', 0);

        if ($idleDays <= 0) {
            return;
        }

        $appliesAfter = Carbon::parse(config('sanctum.idle_applies_after', '2026-09-21'));

        Sanctum::authenticateAccessTokensUsing(function ($token, bool $isValid) use ($idleDays, $appliesAfter) {
            if (! $isValid) {
                return false;
            }

            if (! $token->created_at || $token->created_at->lt($appliesAfter)) {
                return true; // legacy token: untouched
            }

            $lastSeen = $token->last_used_at ?? $token->created_at;

            return $lastSeen->gt(now()->subDays($idleDays));
        });
    }

    /**
     * SMTP details saved in Admin > Settings > SMTP override the .env values.
     * With nothing saved, the .env values keep working as before.
     */
    private function applyMailSettings(): void
    {
        $row = Setting::cachedRow('smtp');

        if (! $row || (int) $row['status_id'] !== 1) {
            return;
        }

        $smtp = Setting::decodeCredentials($row['value'], ['password']);

        if (empty($smtp['host'])) {
            return;
        }

        $encryption = strtolower((string) ($smtp['encryption'] ?? ''));

        config([
            'mail.default'                 => ($smtp['mailer'] ?? null) ?: config('mail.default'),
            'mail.mailers.smtp.host'       => $smtp['host'],
            'mail.mailers.smtp.port'       => (int) (($smtp['port'] ?? null) ?: 587),
            'mail.mailers.smtp.username'   => $smtp['username'] ?? null,
            'mail.mailers.smtp.password'   => $smtp['password'] ?? null,
            // Symfony mailer: "smtps" = implicit TLS (465); anything else negotiates STARTTLS.
            'mail.mailers.smtp.scheme'     => $encryption === 'ssl' ? 'smtps' : 'smtp',
            'mail.from.address'            => ($smtp['from_address'] ?? null) ?: config('mail.from.address'),
            'mail.from.name'               => ($smtp['from_name'] ?? null) ?: config('mail.from.name'),
        ]);
    }
}
