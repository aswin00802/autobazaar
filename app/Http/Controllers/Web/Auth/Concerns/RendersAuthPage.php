<?php

namespace App\Http\Controllers\Web\Auth\Concerns;

use App\Support\SiteData;
use Illuminate\Contracts\View\View;

/**
 * Renders the customer sign-in / sign-up / OTP screens.
 *
 * Which design is used is a single switch in config/site_design.php:
 *
 *   'auth_pages' => true   the pages in the website's own design (header,
 *                          footer, brand panel) — resources/views/web/auth
 *   'auth_pages' => false  the original standalone pages, untouched, in
 *                          resources/views/web/auth/legacy
 *
 * The forms post to the same routes with the same field names either way, so
 * the switch changes nothing but the look.
 */
trait RendersAuthPage
{
    protected function authView(string $name, array $data = []): View
    {
        if (! config('site_design.auth_pages', true)) {
            return view("web.auth.legacy.{$name}", $data);
        }

        // site/layout.blade.php needs the header, footer and location switcher data.
        return view("web.auth.{$name}", [
            'site'      => SiteData::site(),
            'locations' => require resource_path('fixtures/locations.php'),
            ...$data,
        ]);
    }
}
