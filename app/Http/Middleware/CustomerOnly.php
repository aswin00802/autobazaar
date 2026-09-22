<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * The mirror image of EnsureStaff, for the website's personal pages
 * (My Account, Orders, Checkout).
 *
 * Staff and customers share one login session, so an admin who is signed in to
 * the admin panel is also "logged in" on the website. Without this guard they
 * could open the customer account area and even place an order from an admin
 * account, which muddles order numbers and sales reports.
 *
 * Staff can still browse every public page. To buy something, a staff member
 * uses a separate customer login with a different phone number.
 *
 * Runs after UserAuth, so guests have already been sent to the login page.
 */
class CustomerOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->isStaff()) {
            return $next($request);
        }

        $message = 'Customer pages are not available for staff accounts. To place an order, sign in with a separate customer number.';

        if ($request->expectsJson()) {
            return response()->json([
                'success'      => false,
                'message'      => $message,
                'redirect_url' => route('dashboard'),
            ], 403);
        }

        return redirect()->route('dashboard')->with('error', $message);
    }
}
