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

        // Users is paged: 4,600 rows in one page made the screen slow to open,
        // and it now carries tabs and a Last Active column on top of that. The
        // tabs and the search box cover finding somebody. Put 0 back here for
        // one page with the table's own search and export.
        'users'      => 50,

        // Quotations still prints in one go — 1,020 rows, 1.7s — so it keeps
        // the usual table and the export built into it.
        'quotations' => 0,
    ],

];
