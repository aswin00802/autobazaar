<?php

/*
|--------------------------------------------------------------------------
| How many rows the long admin lists show at once
|--------------------------------------------------------------------------
|
| These four screens used to print every row they had into one page. The City
| screen was the worst: 47,941 rows, 34 MB of HTML, and one database lookup per
| row for the state name — about two minutes before anything appeared, and on a
| live server it would simply have timed out.
|
| Each screen now asks the database for one page at a time and searches across
| the whole table, not just what is on screen.
|
| Set a number to 0 to go back to the old behaviour for that screen: every row
| in one page, no search box, no page links. (The one-lookup-per-row fault is
| fixed either way — that part was never worth keeping.)
|
| On a server that caches config, run `php artisan config:clear` after a change.
|
*/

return [

    'per_page' => [
        'city'       => 50,     // 47,941 rows — 34 MB and about a minute unpaged
        'state'      => 50,     //  4,092 rows
        'country'    => 50,     //    246 rows — paged so it matches State and City

        // These two are back on the same footing as every other admin list: one
        // page, the usual table, and the export built into it. They are small
        // enough to print in one go (Users 3.4 MB in 1.7s, Quotations 3.2 MB in
        // 1.7s). Put 50 here and they page and gain a search box instead.
        'users'      => 0,
        'quotations' => 0,
    ],

];
