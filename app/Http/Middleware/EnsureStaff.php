<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Second line of defence for the admin panel.
 *
 * Customers and staff share the `web` guard, so "logged in" alone must never
 * open an admin URL. A staff account is one with an admin role_id or any
 * Spatie role other than "user". Individual screens still enforce their own
 * permission; this only keeps customer accounts out of the whole group.
 */
class EnsureStaff
{
    /** users.role_id values that mark a customer account. */
    private const CUSTOMER_ROLE_IDS = [null, 0, 1000];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        $isStaff = $user && (
            ! in_array($user->role_id, self::CUSTOMER_ROLE_IDS, false)
            || $user->roles()->where('name', '!=', 'user')->exists()
        );

        if (! $isStaff) {
            abort(403, 'This area is for AutoBazaar staff only.');
        }

        return $next($request);
    }
}
