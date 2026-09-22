<?php

/*
|--------------------------------------------------------------------------
| Website design add-ons (September 2026 refresh)
|--------------------------------------------------------------------------
|
| Each line is an independent on/off switch. Set one to false and the site
| looks exactly as it did before that add-on existed: nothing else depends
| on it, and the main stylesheet was never edited for these.
|
| The styles and scripts live in resources/views/site/partials/design-refresh.blade.php
|
| On a server that caches config, run `php artisan config:clear` after a change.
|
*/

return [

    // Page loader (the driving auto).
    //   'every_page'  = shows on every page load and on refresh (shorter after the first page)
    //   'first_visit' = shows only on the first page of a visit
    //   'off'         = no loader at all
    'loader' => 'every_page',

    // Auto cards: stronger lift, bigger zoom, a soft green "stage" behind the photo.
    'card_hover' => true,

    // Auto cards: fuel types as coloured chips (CNG green, Electric blue, Petrol orange...).
    'fuel_chips' => true,

    // Warmer pages: amber bar under section headings, amber highlight under prices,
    // amber numbers in the trust bar, amber top edge on hovered tiles.
    'amber_accents' => true,

    // Trust bar numbers count up from zero when they scroll into view.
    'count_up' => true,

    // Desktop menu: move the routes below into one dropdown. The phone menu is unchanged.
    'short_menu' => true,
    'short_menu_label' => 'More',
    'short_menu_routes' => ['site.schemes', 'site.news', 'site.app'],

    // Buttons glow softly, lift a little and get a light sweep when pointed at.
    'button_glow' => true,

    // Website typeface: 'poppins', 'roboto', or 'default' (the original Plus Jakarta Sans).
    'font' => 'poppins',

    // Credit in the footer's bottom bar: "<text> <name>", with the name linking to url.
    // Original wording was 'Designed by'. Set 'name' to null to hide the credit.
    'footer_credit' => [
        'text' => 'Crafted by',
        'name' => 'Ziga Infotech',
        'url'  => 'https://zigainfotech.com',
    ],

    // Customer sign-in, sign-up and OTP screens.
    //   true  = the website's own design: header, footer, green side panel, Poppins
    //   false = the original standalone pages, kept untouched in
    //           resources/views/web/auth/legacy
    // Both post to the same routes with the same fields, so this is a look-only switch.
    'auth_pages' => true,

    // Second row in the footer: "Popular Brands" and "Popular Models" link lists plus a
    // one-line service area. Styled like the Quick Menu columns. They link to your brand
    // and model pages so Google can reach every model. false = hidden.
    'footer_browse_links' => false,

];
