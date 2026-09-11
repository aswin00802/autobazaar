<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // User login pannala na redirect
        if (!Auth::check()) {
            // Remember where they were headed (e.g. checkout) so the OTP flow
            // can send them straight back after signing in.
            if ($request->isMethod('GET') && !$request->expectsJson()) {
                session()->put('url.intended', $request->fullUrl());
            }

            return redirect('/user/login');
        }

        return $next($request);
    }
}
