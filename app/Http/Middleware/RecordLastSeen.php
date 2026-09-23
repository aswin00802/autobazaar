<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Notes when each person was last doing something.
 *
 * This runs on every authenticated request from the website and both mobile
 * apps, so it is the one place that knows about all three. It deliberately:
 *
 *   - lets the request finish first, and only then writes, so nothing a visitor
 *     or an app is waiting for is held up by it
 *   - writes at most once every few minutes per person, rather than on every
 *     tap, so a driver's app polling for rides does not hammer the table
 *   - writes the column straight rather than saving the model, so it cannot
 *     fire model events or touch updated_at
 *   - swallows its own errors. Recording when somebody was last seen is never
 *     a reason to fail their request, and on a server where the column has not
 *     been added yet this simply does nothing.
 *
 * It changes no response, so the apps cannot tell it is there.
 */
class RecordLastSeen
{
    /** One write per person per this many minutes. */
    private const EVERY_MINUTES = 5;

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /** Runs after the response has been sent. */
    public function terminate(Request $request, Response $response): void
    {
        try {
            $user = $request->user();

            if (! $user) {
                return;
            }

            $key = 'seen:' . $user->getAuthIdentifier();

            if (Cache::has($key)) {
                return;
            }

            Cache::put($key, true, now()->addMinutes(self::EVERY_MINUTES));

            DB::table($user->getTable())
                ->where($user->getKeyName(), $user->getKey())
                ->update(['last_seen_at' => now()]);
        } catch (\Throwable $e) {
            // Never break a request over this.
        }
    }
}
