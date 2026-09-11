<?php

/**
 * Geography for the header location switcher and the /buying-options gate.
 *
 * Deliberately shallow — enough states and districts to demonstrate the three
 * serviceability tiers from enquiry2.jpeg without shipping the 47,941-row
 * `cities` table into a static prototype.
 */

return [
    /* Chennai, Tiruvallur, Kanchipuram, Chengalpattu — the only districts where
       AutoBazaar sells directly. Everything else falls back to a lower tier. */
    'direct_purchase_districts' => ['Chennai', 'Tiruvallur', 'Kanchipuram', 'Chengalpattu'],

    'default' => [
        'state' => 'Tamil Nadu',
        'district' => 'Chennai',
        'city' => 'Chennai',
        'pincode' => '600001',
    ],

    'states' => [
        [
            'name' => 'Tamil Nadu',
            'districts' => [
                ['name' => 'Chennai', 'cities' => [['name' => 'Chennai'], ['name' => 'Ambattur'], ['name' => 'Anna Nagar']]],
                ['name' => 'Tiruvallur', 'cities' => [['name' => 'Tiruvallur'], ['name' => 'Avadi'], ['name' => 'Poonamallee']]],
                ['name' => 'Kanchipuram', 'cities' => [['name' => 'Kanchipuram'], ['name' => 'Sriperumbudur']]],
                ['name' => 'Chengalpattu', 'cities' => [['name' => 'Chengalpattu'], ['name' => 'Tambaram'], ['name' => 'Maraimalai Nagar']]],
                ['name' => 'Coimbatore', 'cities' => [['name' => 'Coimbatore'], ['name' => 'Pollachi']]],
                ['name' => 'Madurai', 'cities' => [['name' => 'Madurai'], ['name' => 'Melur']]],
                ['name' => 'Salem', 'cities' => [['name' => 'Salem'], ['name' => 'Attur']]],
                ['name' => 'Tiruchirappalli', 'cities' => [['name' => 'Tiruchirappalli'], ['name' => 'Srirangam']]],
                ['name' => 'Erode', 'cities' => [['name' => 'Erode'], ['name' => 'Bhavani']]],
                ['name' => 'Vellore', 'cities' => [['name' => 'Vellore'], ['name' => 'Katpadi']]],
            ],
        ],
        [
            'name' => 'Karnataka',
            'districts' => [
                ['name' => 'Bengaluru Urban', 'cities' => [['name' => 'Bengaluru']]],
                ['name' => 'Mysuru', 'cities' => [['name' => 'Mysuru']]],
            ],
        ],
        [
            'name' => 'Kerala',
            'districts' => [
                ['name' => 'Ernakulam', 'cities' => [['name' => 'Kochi']]],
                ['name' => 'Thiruvananthapuram', 'cities' => [['name' => 'Thiruvananthapuram']]],
            ],
        ],
        [
            'name' => 'Andhra Pradesh',
            'districts' => [
                ['name' => 'Visakhapatnam', 'cities' => [['name' => 'Visakhapatnam']]],
                ['name' => 'Guntur', 'cities' => [['name' => 'Guntur']]],
            ],
        ],
        [
            'name' => 'Maharashtra',
            'districts' => [
                ['name' => 'Mumbai Suburban', 'cities' => [['name' => 'Mumbai']]],
                ['name' => 'Pune', 'cities' => [['name' => 'Pune']]],
            ],
        ],
    ],

    /* The three outcome cards on enquiry2.jpeg */
    'tiers' => [
        'direct_purchase' => [
            'key' => 'direct_purchase',
            'badge' => 'Direct Purchase Available',
            'title' => 'Chennai / Tiruvallur / Kanchipuram / Chengalpattu',
            'note' => 'You can directly purchase your autorickshaw from AutoBazaar.',
            'tone' => 'brand',
            'icon' => 'cart',
            'cta' => 'Book Now',
            'bullets' => [
                'On-Road Price Available',
                'Net Cash Purchase',
                'Private Finance Options',
                'Bank Loan Options',
                'EMI Calculator',
                'Available Offers',
                'Book Now',
            ],
        ],
        'buying_assistance' => [
            'key' => 'buying_assistance',
            'badge' => 'Buying Assistance',
            'title' => 'Other Districts in Tamil Nadu',
            'note' => 'We will assist you with buying guidance and information.',
            'tone' => 'info',
            'icon' => 'handshake',
            'cta' => 'Get Buying Assistance',
            'bullets' => [
                'Ex-Showroom Price',
                'Finance Information',
                'EMI Calculator',
                'Operating Cost Calculator',
                'Get Buying Assistance',
                'Request a Call',
                'Enquire Now',
            ],
        ],
        'dealer_connect' => [
            'key' => 'dealer_connect',
            'badge' => 'Dealer Lead Assistance',
            'title' => 'Other States in India',
            'note' => 'We will connect you with a suitable nearby dealer.',
            'tone' => 'warn',
            'icon' => 'map',
            'cta' => 'Find a Dealer Near You',
            'bullets' => [
                'Vehicle Information',
                'Specifications & Features',
                'Ex-Showroom Price (where available)',
                'EMI Calculator',
                'Find a Dealer Near You',
                'Request a Call',
                'Submit Enquiry',
            ],
        ],
    ],

    'how_it_works' => [
        ['step' => 1, 'title' => 'Enter Your Location', 'note' => 'State, District, City, Pincode'],
        ['step' => 2, 'title' => 'View Available Options', 'note' => 'Based on your location'],
        ['step' => 3, 'title' => 'Enquire or Book', 'note' => 'Choose the right option for you'],
    ],

    /* Enquiry form dropdowns (enquiry.jpeg) */
    'buying_options' => [
        ['value' => 'net-cash', 'label' => 'Net Cash Purchase'],
        ['value' => 'private-finance', 'label' => 'Private Finance'],
        ['value' => 'bank-loan', 'label' => 'Bank Loan'],
        ['value' => 'need-guidance', 'label' => 'Need Guidance'],
        ['value' => 'just-enquiry', 'label' => 'Just Enquiry'],
    ],

    'buying_timeframes' => [
        'Immediately',
        'Within 15 days',
        'Within 1 month',
        'Within 3 months',
        'Just exploring',
    ],
];
