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

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        $isStaff = $user && $user->isStaff();

        if (! $isStaff) {
            abort(403, 'This area is for AutoBazaar staff only.');
        }

        return $next($request);
    }
}
