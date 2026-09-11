<?php

/**
 * Editorial promo banners for the accessories landing page.
 *
 * The product catalogue, categories and brands used to live here too — they
 * now come from the database (see PreviewController::liveProducts). Only these
 * three marketing panels remain static, because there is no CMS for them yet.
 */

return [
    'promos' => [
        [
            'title' => 'Premium Auto Floor Mats',
            'meta' => 'Durable | Anti-Skid | Easy to Clean',
            'from' => 899,
            'badge' => 'BEST SELLER',
            'art' => 'mat',
            'tone' => 'dark',
            'bullets' => [],
        ],
        [
            'title' => 'Comfortable Seat Covers',
            'meta' => 'Stylish | Long Lasting | Easy Fit',
            'from' => 1299,
            'badge' => null,
            'art' => 'seat',
            'tone' => 'dark',
            'bullets' => ['Water Resistant', 'Premium Finish', 'Custom Fit', 'Multiple Designs'],
        ],
        [
            'title' => 'Brighten Your Journey',
            'meta' => 'LED Lights & Headlamps',
            'from' => 799,
            'badge' => null,
            'art' => 'led',
            'tone' => 'dark',
            'bullets' => ['High Brightness', 'Long Life', 'Low Power', 'All Weather Use'],
        ],
    ],
];
