<?php

/**
 * Shared chrome: brand details, navigation, contact, service area, footer.
 * Sourced from the header/footer that repeat across every reference screen.
 */

return [
    'brand' => [
        'name' => 'AutoBazaar',
        'tagline' => 'Drive Your Tomorrow',
        'copyright' => '© 2026 AutoBazaar. All rights reserved.',
    ],

    'contact' => [
        'phone' => '86088 60893',
        'phone_alt' => '63840 88408',
        'phone_e164' => '+918608860893',
        'whatsapp' => '918608860893',
        'email' => 'jpmoauto@gmail.com',
        'address' => 'No: 7, PHR Road, Kathirvedu, Thiruvallur - 600 066',
        'hours' => 'Mon - Sat, 9:30 AM - 7:00 PM',
    ],

    /* Nav bar, matching the reference screens.
       Per the agreed scope: Used Autos kept; 2 Wheelers, Services and
       Bajaj Refinance are not included. */
    'nav' => [
        ['label' => 'Home', 'route' => 'site.home'],
        ['label' => 'New Autos', 'route' => 'site.new-autos'],
        ['label' => 'Used Autos', 'route' => 'site.used-autos'],
        ['label' => 'Compare', 'route' => 'site.compare'],
        ['label' => 'Offers', 'route' => 'site.offers'],
        ['label' => 'Finance & EMI', 'route' => 'site.finance'],
        ['label' => 'Accessories Shop', 'route' => 'site.accessories'],
        ['label' => 'Government Schemes', 'route' => 'site.schemes'],
        ['label' => 'Auto News', 'route' => 'site.news'],
        ['label' => 'App Download', 'route' => 'site.app'],
        ['label' => 'Contact', 'route' => 'site.contact'],
    ],

    /* Nine quick-action tiles under the home hero (home.jpeg) */
    'quick_actions' => [
        ['icon' => 'auto', 'label' => 'New Autos', 'note' => 'Explore all models', 'route' => 'site.new-autos', 'tone' => 'brand'],
        ['icon' => 'scales', 'label' => 'Compare Autos', 'note' => 'Find the best for you', 'route' => 'site.compare', 'tone' => 'info'],
        ['icon' => 'tag', 'label' => 'Latest Offers', 'note' => 'Save more', 'route' => 'site.offers', 'tone' => 'danger'],
        ['icon' => 'calculator', 'label' => 'EMI Calculator', 'note' => 'Plan your purchase', 'route' => 'site.finance', 'tone' => 'violet'],
        ['icon' => 'gear', 'label' => 'Operating Cost', 'note' => 'Know your earnings', 'route' => 'site.finance', 'tone' => 'accent'],
        ['icon' => 'cart', 'label' => 'Accessories Shop', 'note' => 'Ride in style', 'route' => 'site.accessories', 'tone' => 'brand'],
        ['icon' => 'bank', 'label' => 'Government Schemes', 'note' => 'Benefits for drivers', 'route' => 'site.schemes', 'tone' => 'info'],
        ['icon' => 'news', 'label' => 'Auto News', 'note' => 'Latest updates', 'route' => 'site.news', 'tone' => 'danger'],
        ['icon' => 'phone', 'label' => 'App Download', 'note' => 'Get our mobile app', 'route' => 'site.app', 'tone' => 'info'],
    ],

    /* Trust bar across the bottom of home.jpeg */
    'stats' => [
        ['icon' => 'users', 'value' => '10,000+', 'label' => 'Happy Drivers'],
        ['icon' => 'auto', 'value' => '100+', 'label' => 'Autorickshaw Models'],
        ['icon' => 'star', 'value' => '4.5 ★', 'label' => 'Average Rating'],
        ['icon' => 'shield', 'value' => 'Trusted by', 'label' => 'Drivers Across India'],
    ],

    'service_area' => [
        'districts' => ['Chennai', 'Tiruvallur', 'Kanchipuram', 'Chengalpattu'],
        'headline' => 'Direct vehicle purchase available in Chennai, Tiruvallur, Kanchipuram and Chengalpattu.',
        'note' => 'For other districts in Tamil Nadu and other states, we provide buying assistance and dealer connection.',
    ],

    /* Reassurance strip repeated at the foot of the shop screens */
    'shop_promises' => [
        ['icon' => 'truck', 'title' => 'Free Shipping', 'note' => 'on orders above ₹999'],
        ['icon' => 'shield', 'title' => 'Secure Payment', 'note' => '100% safe & secure'],
        ['icon' => 'refresh', 'title' => 'Easy Returns', 'note' => 'Hassle-free returns'],
        ['icon' => 'headset', 'title' => 'Customer Support', 'note' => '90922 14143'],
    ],

    /* Footer columns. Same links and business details as the original site;
       only the presentation is new. */
    'footer' => [
        [
            'heading' => 'Quick Menu',
            'links' => [
                ['label' => 'New Autos', 'route' => 'site.new-autos'],
                ['label' => 'Used Autos', 'route' => 'site.used-autos'],
                ['label' => 'Compare Autos', 'route' => 'site.compare'],
                ['label' => 'Accessories Shop', 'route' => 'site.accessories.shop'],
            ],
        ],
        [
            'heading' => 'Auto Bazaar',
            'links' => [
                ['label' => 'About Us', 'route' => 'site.about'],
                ['label' => 'Terms & Conditions', 'route' => 'site.page', 'param' => 'terms-conditions'],
                ['label' => 'Privacy Policy', 'route' => 'site.page', 'param' => 'privacy-policy'],
                ['label' => 'Give Feedback', 'route' => 'site.contact'],
            ],
        ],
    ],

    'socials' => [
        ['label' => 'Instagram', 'icon' => 'instagram', 'url' => 'https://www.instagram.com/auto_bazaar_official_/'],
        ['label' => 'Facebook', 'icon' => 'facebook', 'url' => 'https://www.facebook.com/profile.php?id=61573090112984'],
        ['label' => 'WhatsApp', 'icon' => 'whatsapp', 'url' => 'https://wa.me/918608860893'],
    ],

    'testimonials' => [
        [
            'quote' => 'Very easy to compare autos and check EMI. Helpful app!',
            'name' => 'Murugan',
            'city' => 'Tiruvallur',
            'rating' => 5,
        ],
        [
            'quote' => 'Government scheme details are very useful. Genuine information!',
            'name' => 'Selvam',
            'city' => 'Kanchipuram',
            'rating' => 5,
        ],
        [
            'quote' => 'Booked my auto quickly. Great support team!',
            'name' => 'Prakash',
            'city' => 'Chengalpattu',
            'rating' => 5,
        ],
    ],
];
