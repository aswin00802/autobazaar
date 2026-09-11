<?php

/**
 * Prototype vehicle catalogue.
 *
 * Values are lifted from the client reference screens so the prototype renders
 * identically to the mockups:
 *   - TVS King Deluxe: specs, price breakup, scores and suitability from new_autos.jpeg
 *   - the compare table figures (EMI, monthly fuel cost, "best for") from compare.jpeg
 *   - card ratings, review counts and "From" prices from home.jpeg
 *
 * NOTE: the mockups disagree with themselves on the TVS King — new_autos.jpeg
 * says 225.8 cc and prints 2,52,000 as the ON-ROAD price, while compare.jpeg
 * says 199.6 cc and prints 2.52 Lakh as the EX-SHOWROOM price. new_autos.jpeg
 * is treated as authoritative for that model since it is the flagship screen,
 * and the compare row is derived from the same numbers so the prototype stays
 * internally consistent. Flagged for the client.
 */

return [
    [
        'slug' => 'tvs-king-deluxe',
        'model_slug' => 'king-deluxe',
        'name' => 'TVS King Deluxe',
        'brand' => 'TVS',
        'brand_slug' => 'tvs',
        'brand_logo' => 'uploads/brand_images/tvs_logo.png',
        'image' => 'assets/image/auto_brands/tvs.png',
        'tagline' => 'Reliable. Efficient. Built for Your Success.',
        'badge' => 'Most Popular',
        'rating' => 4.5,
        'reviews' => 120,
        'from_price' => 252000,
        'popularity' => 100,
        'is_popular' => true,
        'seating' => 3,
        'use_case' => ['commercial', 'high-mileage'],
        'description' => 'The TVS King Deluxe is designed for superior mileage, low maintenance and high earnings. Ideal for city and commercial usage, it offers comfort, durability and better performance for drivers.',

        'variants' => [
            ['key' => 'petrol', 'label' => 'Petrol', 'icon' => 'fuel', 'is_default' => true],
            ['key' => 'cng', 'label' => 'CNG', 'icon' => 'gas'],
            ['key' => 'electric', 'label' => 'Electric', 'icon' => 'bolt'],
        ],

        'features' => [
            ['icon' => 'fuel', 'label' => 'Best Mileage'],
            ['icon' => 'wrench', 'label' => 'Low Maintenance'],
            ['icon' => 'rupee', 'label' => 'High Earnings'],
            ['icon' => 'shield', 'label' => 'Trusted Brand'],
        ],

        'specifications' => [
            ['label' => 'Engine', 'value' => '225.8 cc, 4 Stroke'],
            ['label' => 'Max Power', 'value' => '9.5 PS @ 5,500 rpm'],
            ['label' => 'Max Torque', 'value' => '15.5 Nm @ 4,000 rpm'],
            ['label' => 'Fuel Type', 'value' => 'Petrol | CNG | LPG'],
            ['label' => 'Mileage', 'value' => '30 – 35 kmpl (Petrol)'],
            ['label' => 'Transmission', 'value' => '4 Speed Constant Mesh'],
            ['label' => 'Seating Capacity', 'value' => 'Driver + 3 Passengers'],
            ['label' => 'Dimensions (L x W x H)', 'value' => '2635 x 1300 x 1700 mm'],
            ['label' => 'Ground Clearance', 'value' => '180 mm'],
            ['label' => 'Kerb Weight', 'value' => '394 kg'],
            ['label' => 'Fuel Tank Capacity', 'value' => '10.5 litres'],
            ['label' => 'Warranty', 'value' => '2 Years / 50,000 km'],
        ],

        'price_breakup' => [
            ['label' => 'Ex-Showroom Price', 'amount' => 220000],
            ['label' => 'RTO Charges (Chennai)', 'amount' => 18500],
            ['label' => 'Insurance (1 Year)', 'amount' => 9800],
            ['label' => 'Registration & Handling', 'amount' => 3000],
            ['label' => 'Other Charges', 'amount' => 2700],
        ],
        'on_road_price' => 252000,
        'ex_showroom' => 220000,

        'scores' => [
            'overall' => 8.4,
            'rank_note' => '#2 in Petrol Segment',
            'summary' => 'Ranked among the best in its segment for mileage, low operating cost and high reliability.',
            'breakdown' => [
                ['label' => 'Specifications', 'score' => 8.5, 'tone' => 'brand'],
                ['label' => 'Performance', 'score' => 8.0, 'tone' => 'accent'],
                ['label' => 'Mileage', 'score' => 8.8, 'tone' => 'brand'],
                ['label' => 'Operating Cost', 'score' => 8.6, 'tone' => 'info'],
                ['label' => 'Maintenance', 'score' => 8.2, 'tone' => 'brand'],
                ['label' => 'Comfort', 'score' => 8.0, 'tone' => 'violet'],
                ['label' => 'Reliability', 'score' => 8.5, 'tone' => 'violet'],
                ['label' => 'Value for Money', 'score' => 8.3, 'tone' => 'brand'],
            ],
        ],

        'suitable_for' => ['City Usage', 'Daily Auto Service', 'Commercial Usage', 'Fleet Business', 'Rental Business'],
        'not_recommended_for' => ['Long Distance', 'Hilly Areas', 'Heavy Load Transport'],

        'operating_cost_fuels' => [
            ['key' => 'petrol', 'label' => 'Petrol', 'price' => 102, 'mileage' => 32, 'unit' => 'km/litre'],
            ['key' => 'cng', 'label' => 'CNG', 'price' => 91.5, 'mileage' => 26, 'unit' => 'km/kg'],
            ['key' => 'lpg', 'label' => 'LPG', 'price' => 61.12, 'mileage' => 24, 'unit' => 'km/kg'],
            ['key' => 'electric', 'label' => 'Electric', 'price' => 18, 'mileage' => 45, 'unit' => 'km/unit'],
        ],

        'compare' => [
            'engine' => '225.8 cc (Petrol/CNG)',
            'power' => '9.5 PS',
            'mileage' => "26 km/kg (CNG)\n30 km/l (Petrol)",
            'seating' => '3',
            'fuel' => 'Petrol / CNG',
            'ex_showroom_label' => '₹2.20 Lakh*',
            'emi' => 5499,
            'maintenance' => 'Low',
            'best_for' => 'All Rounder',
            'monthly_fuel' => 4200,
            'fuel_note' => 'CNG',
        ],

        'offers' => [
            'Exchange Bonus up to ₹25,000',
            'Low Down Payment Finance Available',
        ],
    ],

    [
        'slug' => 'bajaj-re',
        'model_slug' => 're',
        'name' => 'Bajaj RE',
        'brand' => 'Bajaj',
        'brand_slug' => 'bajaj',
        'brand_logo' => 'uploads/brand_images/bajaj_logo.png',
        'image' => 'assets/image/auto_brands/bajaj_auto.png',
        'tagline' => 'The mileage leader, trusted on every route.',
        'badge' => null,
        'rating' => 4.4,
        'reviews' => 98,
        'from_price' => 248000,
        'popularity' => 95,
        'is_popular' => true,
        'seating' => 3,
        'use_case' => ['commercial', 'high-mileage', 'low-maintenance'],
        'description' => 'The Bajaj RE is the segment benchmark for fuel efficiency, with a proven service network and among the lowest running costs available to auto drivers.',

        'variants' => [
            ['key' => 'petrol', 'label' => 'Petrol', 'icon' => 'fuel', 'is_default' => true],
            ['key' => 'cng', 'label' => 'CNG', 'icon' => 'gas'],
            ['key' => 'electric', 'label' => 'Electric', 'icon' => 'bolt'],
        ],

        'features' => [
            ['icon' => 'fuel', 'label' => 'Best Mileage'],
            ['icon' => 'wrench', 'label' => 'Low Maintenance'],
            ['icon' => 'shield', 'label' => 'Trusted Brand'],
        ],

        'specifications' => [
            ['label' => 'Engine', 'value' => '198.88 cc, 4 Stroke'],
            ['label' => 'Max Power', 'value' => '8.6 PS @ 5,000 rpm'],
            ['label' => 'Max Torque', 'value' => '16.6 Nm @ 3,500 rpm'],
            ['label' => 'Fuel Type', 'value' => 'Petrol | CNG | LPG'],
            ['label' => 'Mileage', 'value' => '32 km/kg (CNG)'],
            ['label' => 'Transmission', 'value' => '4 Speed Constant Mesh'],
            ['label' => 'Seating Capacity', 'value' => 'Driver + 3 Passengers'],
            ['label' => 'Ground Clearance', 'value' => '190 mm'],
            ['label' => 'Kerb Weight', 'value' => '379 kg'],
            ['label' => 'Fuel Tank Capacity', 'value' => '8 litres'],
            ['label' => 'Warranty', 'value' => '2 Years / 50,000 km'],
        ],

        'price_breakup' => [
            ['label' => 'Ex-Showroom Price', 'amount' => 245000],
            ['label' => 'RTO Charges (Chennai)', 'amount' => 18200],
            ['label' => 'Insurance (1 Year)', 'amount' => 9600],
            ['label' => 'Registration & Handling', 'amount' => 3000],
            ['label' => 'Other Charges', 'amount' => 2600],
        ],
        'on_road_price' => 278400,
        'ex_showroom' => 245000,

        'scores' => [
            'overall' => 8.6,
            'rank_note' => '#1 in Mileage',
            'summary' => 'Best-in-class fuel efficiency and the widest service network in the segment.',
            'breakdown' => [
                ['label' => 'Specifications', 'score' => 8.2, 'tone' => 'brand'],
                ['label' => 'Performance', 'score' => 7.8, 'tone' => 'accent'],
                ['label' => 'Mileage', 'score' => 9.2, 'tone' => 'brand'],
                ['label' => 'Operating Cost', 'score' => 9.0, 'tone' => 'info'],
                ['label' => 'Maintenance', 'score' => 8.6, 'tone' => 'brand'],
                ['label' => 'Comfort', 'score' => 7.9, 'tone' => 'violet'],
                ['label' => 'Reliability', 'score' => 8.8, 'tone' => 'violet'],
                ['label' => 'Value for Money', 'score' => 8.5, 'tone' => 'brand'],
            ],
        ],

        'suitable_for' => ['City Usage', 'High Mileage Routes', 'Commercial Usage', 'Fleet Business'],
        'not_recommended_for' => ['Heavy Load Transport', 'Hilly Areas'],

        'operating_cost_fuels' => [
            ['key' => 'petrol', 'label' => 'Petrol', 'price' => 102, 'mileage' => 18, 'unit' => 'km/litre'],
            ['key' => 'cng', 'label' => 'CNG', 'price' => 91.5, 'mileage' => 32, 'unit' => 'km/kg'],
            ['key' => 'lpg', 'label' => 'LPG', 'price' => 61.12, 'mileage' => 28, 'unit' => 'km/kg'],
        ],

        'compare' => [
            'engine' => '198.88 cc (Petrol/CNG)',
            'power' => '8.6 PS',
            'mileage' => "32 km/kg (CNG)\n18 km/l (Petrol)",
            'seating' => '3',
            'fuel' => 'Petrol / CNG',
            'ex_showroom_label' => '₹2.45 Lakh*',
            'emi' => 5299,
            'maintenance' => 'Low',
            'best_for' => 'High Mileage',
            'monthly_fuel' => 3800,
            'fuel_note' => 'CNG',
        ],

        'offers' => [
            'Low Down Payment Finance Available',
        ],
    ],

    [
        'slug' => 'piaggio-ape-xtra',
        'model_slug' => 'ape-xtra',
        'name' => 'Piaggio Ape Xtra',
        'brand' => 'Piaggio',
        'brand_slug' => 'piaggio',
        'brand_logo' => 'uploads/brand_images/piaggio_logo.png',
        'image' => 'assets/image/auto_brands/piaggio_auto.png',
        'tagline' => 'Built tough for heavy-duty daily work.',
        'badge' => null,
        'rating' => 4.3,
        'reviews' => 76,
        'from_price' => 260000,
        'popularity' => 88,
        'is_popular' => true,
        'seating' => 3,
        'use_case' => ['commercial'],
        'description' => 'The Piaggio Ape Xtra pairs the largest engine in its class with a reinforced chassis, making it the pick for heavy-duty and long-shift commercial use.',

        'variants' => [
            ['key' => 'petrol', 'label' => 'Petrol', 'icon' => 'fuel', 'is_default' => true],
            ['key' => 'cng', 'label' => 'CNG', 'icon' => 'gas'],
            ['key' => 'electric', 'label' => 'Electric', 'icon' => 'bolt'],
        ],

        'features' => [
            ['icon' => 'rupee', 'label' => 'High Earnings'],
            ['icon' => 'shield', 'label' => 'Trusted Brand'],
        ],

        'specifications' => [
            ['label' => 'Engine', 'value' => '230.9 cc, 4 Stroke'],
            ['label' => 'Max Power', 'value' => '8.5 PS @ 4,000 rpm'],
            ['label' => 'Max Torque', 'value' => '17.6 Nm @ 2,750 rpm'],
            ['label' => 'Fuel Type', 'value' => 'Petrol | CNG'],
            ['label' => 'Mileage', 'value' => '28 km/kg (CNG)'],
            ['label' => 'Transmission', 'value' => '4 Speed + Reverse'],
            ['label' => 'Seating Capacity', 'value' => 'Driver + 3 Passengers'],
            ['label' => 'Ground Clearance', 'value' => '200 mm'],
            ['label' => 'Kerb Weight', 'value' => '410 kg'],
            ['label' => 'Fuel Tank Capacity', 'value' => '9 litres'],
            ['label' => 'Warranty', 'value' => '2 Years / 50,000 km'],
        ],

        'price_breakup' => [
            ['label' => 'Ex-Showroom Price', 'amount' => 260000],
            ['label' => 'RTO Charges (Chennai)', 'amount' => 19100],
            ['label' => 'Insurance (1 Year)', 'amount' => 10200],
            ['label' => 'Registration & Handling', 'amount' => 3000],
            ['label' => 'Other Charges', 'amount' => 2800],
        ],
        'on_road_price' => 295100,
        'ex_showroom' => 260000,

        'scores' => [
            'overall' => 8.1,
            'rank_note' => '#1 in Heavy Duty',
            'summary' => 'The strongest engine in the segment, best suited to sustained heavy-duty running.',
            'breakdown' => [
                ['label' => 'Specifications', 'score' => 8.7, 'tone' => 'brand'],
                ['label' => 'Performance', 'score' => 8.6, 'tone' => 'accent'],
                ['label' => 'Mileage', 'score' => 7.6, 'tone' => 'brand'],
                ['label' => 'Operating Cost', 'score' => 7.4, 'tone' => 'info'],
                ['label' => 'Maintenance', 'score' => 7.5, 'tone' => 'brand'],
                ['label' => 'Comfort', 'score' => 8.2, 'tone' => 'violet'],
                ['label' => 'Reliability', 'score' => 8.4, 'tone' => 'violet'],
                ['label' => 'Value for Money', 'score' => 7.9, 'tone' => 'brand'],
            ],
        ],

        'suitable_for' => ['Heavy Load Transport', 'Commercial Usage', 'Long Shifts', 'Fleet Business'],
        'not_recommended_for' => ['Low Budget Buyers', 'Short City Trips'],

        'operating_cost_fuels' => [
            ['key' => 'petrol', 'label' => 'Petrol', 'price' => 102, 'mileage' => 18, 'unit' => 'km/litre'],
            ['key' => 'cng', 'label' => 'CNG', 'price' => 91.5, 'mileage' => 28, 'unit' => 'km/kg'],
        ],

        'compare' => [
            'engine' => '230.9 cc (Petrol/CNG)',
            'power' => '8.5 PS',
            'mileage' => "28 km/kg (CNG)\n18 km/l (Petrol)",
            'seating' => '3',
            'fuel' => 'Petrol / CNG',
            'ex_showroom_label' => '₹2.60 Lakh*',
            'emi' => 5799,
            'maintenance' => 'Moderate',
            'best_for' => 'Heavy Duty',
            'monthly_fuel' => 4000,
            'fuel_note' => 'CNG',
        ],

        'offers' => [],
    ],

    [
        'slug' => 'mahindra-treo-plus',
        'model_slug' => 'treo-plus',
        'name' => 'Mahindra Treo Plus',
        'brand' => 'Mahindra',
        'brand_slug' => 'mahindra',
        'brand_logo' => 'uploads/brand_images/mahindra_logo.png',
        'image' => 'assets/image/auto_brands/montra_auto1f.png',
        'tagline' => 'Go electric. Cut your running cost to a fraction.',
        'badge' => 'EV',
        'rating' => 4.6,
        'reviews' => 88,
        'from_price' => 370000,
        'popularity' => 82,
        'is_popular' => true,
        'seating' => 4,
        'use_case' => ['low-maintenance', 'personal'],
        'description' => 'The Mahindra Treo Plus is a lithium-ion electric autorickshaw with four-passenger seating and the lowest running cost in the line-up — around ₹1,200 a month in energy.',

        'variants' => [
            ['key' => 'electric', 'label' => 'Electric', 'icon' => 'bolt', 'is_default' => true],
        ],

        'features' => [
            ['icon' => 'bolt', 'label' => 'Zero Emission'],
            ['icon' => 'wrench', 'label' => 'Very Low Maintenance'],
            ['icon' => 'rupee', 'label' => 'Lowest Running Cost'],
            ['icon' => 'shield', 'label' => 'Trusted Brand'],
        ],

        'specifications' => [
            ['label' => 'Motor', 'value' => 'Electric Motor (48V)'],
            ['label' => 'Max Power', 'value' => '8.0 kW'],
            ['label' => 'Battery', 'value' => '7.37 kWh Lithium-Ion'],
            ['label' => 'Fuel Type', 'value' => 'Electric'],
            ['label' => 'Range', 'value' => '120 – 140 km per charge'],
            ['label' => 'Charging Time', 'value' => '3 hours 50 minutes'],
            ['label' => 'Seating Capacity', 'value' => 'Driver + 4 Passengers'],
            ['label' => 'Ground Clearance', 'value' => '170 mm'],
            ['label' => 'Kerb Weight', 'value' => '445 kg'],
            ['label' => 'Warranty', 'value' => '3 Years / 80,000 km'],
        ],

        'price_breakup' => [
            ['label' => 'Ex-Showroom Price', 'amount' => 370000],
            ['label' => 'RTO Charges (Chennai)', 'amount' => 0],
            ['label' => 'Insurance (1 Year)', 'amount' => 12400],
            ['label' => 'Registration & Handling', 'amount' => 3000],
            ['label' => 'Other Charges', 'amount' => 2900],
        ],
        'on_road_price' => 388300,
        'ex_showroom' => 370000,

        'scores' => [
            'overall' => 8.7,
            'rank_note' => '#1 in Electric Segment',
            'summary' => 'Unmatched running cost and refinement; the higher purchase price pays back over long daily distances.',
            'breakdown' => [
                ['label' => 'Specifications', 'score' => 8.6, 'tone' => 'brand'],
                ['label' => 'Performance', 'score' => 8.4, 'tone' => 'accent'],
                ['label' => 'Mileage', 'score' => 9.4, 'tone' => 'brand'],
                ['label' => 'Operating Cost', 'score' => 9.6, 'tone' => 'info'],
                ['label' => 'Maintenance', 'score' => 9.2, 'tone' => 'brand'],
                ['label' => 'Comfort', 'score' => 8.8, 'tone' => 'violet'],
                ['label' => 'Reliability', 'score' => 8.3, 'tone' => 'violet'],
                ['label' => 'Value for Money', 'score' => 7.8, 'tone' => 'brand'],
            ],
        ],

        'suitable_for' => ['City Usage', 'Low Running Cost', 'Daily Auto Service', 'Fleet Business'],
        'not_recommended_for' => ['Long Distance', 'Areas Without Charging'],

        'operating_cost_fuels' => [
            ['key' => 'electric', 'label' => 'Electric', 'price' => 18, 'mileage' => 45, 'unit' => 'km/unit'],
        ],

        'compare' => [
            'engine' => 'Electric Motor (48V)',
            'power' => '8.0 kW',
            'mileage' => '120 – 140 km/charge',
            'seating' => '4',
            'fuel' => 'Electric',
            'ex_showroom_label' => '₹3.70 Lakh*',
            'emi' => 7999,
            'maintenance' => 'Very Low',
            'best_for' => 'Low Running Cost',
            'monthly_fuel' => 1200,
            'fuel_note' => 'Electric',
        ],

        'offers' => [
            'EV Subsidy up to ₹50,000 (Tamil Nadu)',
            'Zero RTO Charges on Electric Vehicles',
        ],
    ],

    [
        'slug' => 'mahindra-alfa',
        'model_slug' => 'alfa',
        'name' => 'Mahindra Alfa',
        'brand' => 'Mahindra',
        'brand_slug' => 'mahindra',
        'brand_logo' => 'uploads/brand_images/mahindra_logo.png',
        'image' => 'assets/image/auto_brands/mahindra.png',
        'tagline' => 'Diesel torque for the long haul.',
        'badge' => null,
        'rating' => 4.2,
        'reviews' => 64,
        'from_price' => 255000,
        'popularity' => 74,
        'is_popular' => true,
        'seating' => 3,
        'use_case' => ['commercial'],
        'description' => 'The Mahindra Alfa offers diesel torque and a load-friendly body, favoured by drivers running longer routes and heavier daily loads.',

        'variants' => [
            ['key' => 'diesel', 'label' => 'Diesel', 'icon' => 'fuel', 'is_default' => true],
            ['key' => 'cng', 'label' => 'CNG', 'icon' => 'gas'],
        ],

        'features' => [
            ['icon' => 'rupee', 'label' => 'High Earnings'],
            ['icon' => 'shield', 'label' => 'Trusted Brand'],
        ],

        'specifications' => [
            ['label' => 'Engine', 'value' => '436 cc, Direct Injection Diesel'],
            ['label' => 'Max Power', 'value' => '8.9 PS @ 3,600 rpm'],
            ['label' => 'Max Torque', 'value' => '19.6 Nm @ 2,200 rpm'],
            ['label' => 'Fuel Type', 'value' => 'Diesel | CNG'],
            ['label' => 'Mileage', 'value' => '35 kmpl (Diesel)'],
            ['label' => 'Transmission', 'value' => '4 Speed + Reverse'],
            ['label' => 'Seating Capacity', 'value' => 'Driver + 3 Passengers'],
            ['label' => 'Ground Clearance', 'value' => '185 mm'],
            ['label' => 'Kerb Weight', 'value' => '425 kg'],
            ['label' => 'Fuel Tank Capacity', 'value' => '10 litres'],
            ['label' => 'Warranty', 'value' => '2 Years / 50,000 km'],
        ],

        'price_breakup' => [
            ['label' => 'Ex-Showroom Price', 'amount' => 255000],
            ['label' => 'RTO Charges (Chennai)', 'amount' => 18800],
            ['label' => 'Insurance (1 Year)', 'amount' => 10000],
            ['label' => 'Registration & Handling', 'amount' => 3000],
            ['label' => 'Other Charges', 'amount' => 2700],
        ],
        'on_road_price' => 289500,
        'ex_showroom' => 255000,

        'scores' => [
            'overall' => 7.9,
            'rank_note' => '#1 in Diesel Segment',
            'summary' => 'Strong low-end torque and load capacity, at the cost of refinement and service intervals.',
            'breakdown' => [
                ['label' => 'Specifications', 'score' => 8.3, 'tone' => 'brand'],
                ['label' => 'Performance', 'score' => 8.5, 'tone' => 'accent'],
                ['label' => 'Mileage', 'score' => 8.1, 'tone' => 'brand'],
                ['label' => 'Operating Cost', 'score' => 7.8, 'tone' => 'info'],
                ['label' => 'Maintenance', 'score' => 7.2, 'tone' => 'brand'],
                ['label' => 'Comfort', 'score' => 7.4, 'tone' => 'violet'],
                ['label' => 'Reliability', 'score' => 8.0, 'tone' => 'violet'],
                ['label' => 'Value for Money', 'score' => 7.9, 'tone' => 'brand'],
            ],
        ],

        'suitable_for' => ['Long Distance', 'Heavy Load Transport', 'Commercial Usage'],
        'not_recommended_for' => ['City Stop-Start Usage', 'Low Noise Requirements'],

        'operating_cost_fuels' => [
            ['key' => 'diesel', 'label' => 'Diesel', 'price' => 92.4, 'mileage' => 35, 'unit' => 'km/litre'],
            ['key' => 'cng', 'label' => 'CNG', 'price' => 91.5, 'mileage' => 24, 'unit' => 'km/kg'],
        ],

        'compare' => [
            'engine' => '436 cc (Diesel)',
            'power' => '8.9 PS',
            'mileage' => "35 km/l (Diesel)\n24 km/kg (CNG)",
            'seating' => '3',
            'fuel' => 'Diesel / CNG',
            'ex_showroom_label' => '₹2.55 Lakh*',
            'emi' => 5599,
            'maintenance' => 'Moderate',
            'best_for' => 'Long Distance',
            'monthly_fuel' => 4400,
            'fuel_note' => 'Diesel',
        ],

        'offers' => [],
    ],

    [
        'slug' => 'atul-gemini',
        'model_slug' => 'gemini',
        'name' => 'Atul Gemini',
        'brand' => 'Atul',
        'brand_slug' => 'atul',
        'brand_logo' => 'uploads/brand_images/atul_logo.png',
        'image' => 'assets/image/auto_brands/atul.png',
        'tagline' => 'Value-first, easy on the pocket.',
        'badge' => null,
        'rating' => 4.1,
        'reviews' => 52,
        'from_price' => 249000,
        'popularity' => 66,
        'is_popular' => true,
        'seating' => 3,
        'use_case' => ['personal', 'low-maintenance'],
        'description' => 'The Atul Gemini is the value pick of the segment — a low entry price and simple mechanicals that keep servicing costs down for owner-drivers.',

        'variants' => [
            ['key' => 'petrol', 'label' => 'Petrol', 'icon' => 'fuel', 'is_default' => true],
            ['key' => 'cng', 'label' => 'CNG', 'icon' => 'gas'],
            ['key' => 'electric', 'label' => 'Electric', 'icon' => 'bolt'],
        ],

        'features' => [
            ['icon' => 'wrench', 'label' => 'Low Maintenance'],
            ['icon' => 'rupee', 'label' => 'Best Value'],
        ],

        'specifications' => [
            ['label' => 'Engine', 'value' => '236.7 cc, 4 Stroke'],
            ['label' => 'Max Power', 'value' => '8.2 PS @ 3,600 rpm'],
            ['label' => 'Max Torque', 'value' => '16.2 Nm @ 2,400 rpm'],
            ['label' => 'Fuel Type', 'value' => 'Petrol | CNG'],
            ['label' => 'Mileage', 'value' => '25 km/kg (CNG)'],
            ['label' => 'Transmission', 'value' => '4 Speed + Reverse'],
            ['label' => 'Seating Capacity', 'value' => 'Driver + 3 Passengers'],
            ['label' => 'Ground Clearance', 'value' => '175 mm'],
            ['label' => 'Kerb Weight', 'value' => '398 kg'],
            ['label' => 'Fuel Tank Capacity', 'value' => '8 litres'],
            ['label' => 'Warranty', 'value' => '2 Years / 40,000 km'],
        ],

        'price_breakup' => [
            ['label' => 'Ex-Showroom Price', 'amount' => 249000],
            ['label' => 'RTO Charges (Chennai)', 'amount' => 18000],
            ['label' => 'Insurance (1 Year)', 'amount' => 9500],
            ['label' => 'Registration & Handling', 'amount' => 3000],
            ['label' => 'Other Charges', 'amount' => 2500],
        ],
        'on_road_price' => 282000,
        'ex_showroom' => 249000,

        'scores' => [
            'overall' => 7.6,
            'rank_note' => '#1 in Value for Money',
            'summary' => 'The lowest cost of entry in the segment, with simple mechanicals that are cheap to service.',
            'breakdown' => [
                ['label' => 'Specifications', 'score' => 7.5, 'tone' => 'brand'],
                ['label' => 'Performance', 'score' => 7.2, 'tone' => 'accent'],
                ['label' => 'Mileage', 'score' => 7.6, 'tone' => 'brand'],
                ['label' => 'Operating Cost', 'score' => 7.9, 'tone' => 'info'],
                ['label' => 'Maintenance', 'score' => 8.0, 'tone' => 'brand'],
                ['label' => 'Comfort', 'score' => 7.0, 'tone' => 'violet'],
                ['label' => 'Reliability', 'score' => 7.4, 'tone' => 'violet'],
                ['label' => 'Value for Money', 'score' => 8.9, 'tone' => 'brand'],
            ],
        ],

        'suitable_for' => ['City Usage', 'Owner Drivers', 'Budget Buyers'],
        'not_recommended_for' => ['Heavy Load Transport', 'Long Distance'],

        'operating_cost_fuels' => [
            ['key' => 'petrol', 'label' => 'Petrol', 'price' => 102, 'mileage' => 17, 'unit' => 'km/litre'],
            ['key' => 'cng', 'label' => 'CNG', 'price' => 91.5, 'mileage' => 25, 'unit' => 'km/kg'],
        ],

        'compare' => [
            'engine' => '236.7 cc (Petrol/CNG)',
            'power' => '8.2 PS',
            'mileage' => "25 km/kg (CNG)\n17 km/l (Petrol)",
            'seating' => '3',
            'fuel' => 'Petrol / CNG',
            'ex_showroom_label' => '₹2.49 Lakh*',
            'emi' => 5199,
            'maintenance' => 'Low',
            'best_for' => 'Best Value',
            'monthly_fuel' => 4300,
            'fuel_note' => 'CNG',
        ],

        'offers' => [],
    ],
];
