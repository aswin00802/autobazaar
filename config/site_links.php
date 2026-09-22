<?php

/*
|--------------------------------------------------------------------------
| App store links for the website
|--------------------------------------------------------------------------
|
| Used by the store badges, the app page and the footer QR code.
| The QR image (public/assets/site/app-qr-play.svg) encodes play_url, so if
| that link ever changes the QR must be generated again.
|
| ios_url: leave null while there is no iPhone app. The App Store badge then
| shows an animated "Coming soon" message instead of opening a dead link.
|
*/

return [

    'play_url' => 'https://play.google.com/store/apps/details?id=com.jpautozone.app&hl=en_IN',

    'ios_url' => null,

];