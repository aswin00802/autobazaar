<?php

/**
 * Cart, checkout and order fixtures.
 *
 * The cart contents, coupon, delivery options, payment methods and totals are
 * exactly those printed on cart.jpeg (subtotal ₹2,997, AUTO5 discount −₹149,
 * free shipping, total ₹2,848). The vehicle order matches my_orders.jpeg.
 */

return [
    'cart' => [
        ['id' => 'floor-mat-premium', 'name' => 'Premium Auto Floor Mat', 'subtitle' => '(3 Wheeler)', 'meta' => 'Anti-Skid | Waterproof | Durable Quality', 'price' => 899, 'qty' => 1, 'art' => 'mat'],
        ['id' => 'seat-cover-deluxe', 'name' => 'Deluxe Seat Cover', 'subtitle' => 'Universal Fit', 'meta' => 'Premium Leather Finish | Comfortable | Durable', 'price' => 1299, 'qty' => 1, 'art' => 'seat'],
        ['id' => 'led-headlight', 'name' => 'LED Headlight', 'subtitle' => '(High Brightness)', 'meta' => '12V | Waterproof | Long Life', 'price' => 799, 'qty' => 1, 'art' => 'led'],
    ],

    'addresses' => [
        [
            'id' => 'home',
            'label' => 'Home',
            'name' => 'Sivam R',
            'lines' => ['12, Anna Nagar, Chennai - 600040', 'Tamil Nadu, India'],
            'phone' => '+91 90922 14143',
            'is_default' => true,
        ],
        [
            'id' => 'office',
            'label' => 'Office',
            'name' => 'AutoBazaar Office',
            'lines' => ['OMR, Chennai - 600096', 'Tamil Nadu, India'],
            'phone' => '+91 90922 14143',
            'is_default' => false,
        ],
    ],

    'delivery_options' => [
        [
            'id' => 'standard',
            'label' => 'Standard Delivery',
            'note' => '3 - 6 business days',
            'price' => 0,
            'free_above_threshold' => true,
            'free_note' => '(on orders above ₹999)',
        ],
        [
            'id' => 'express',
            'label' => 'Express Delivery',
            'note' => '1 - 3 business days',
            'price' => 69,
            'free_above_threshold' => false,
            'free_note' => null,
        ],
    ],

    'payment_methods' => [
        ['id' => 'upi', 'label' => 'UPI', 'note' => '(Google Pay, PhonePe, Paytm, etc.)', 'brands' => ['GPay', 'PhonePe', 'Paytm', 'BHIM']],
        ['id' => 'card', 'label' => 'Credit / Debit Card', 'note' => null, 'brands' => ['VISA', 'Mastercard', 'RuPay']],
        ['id' => 'netbanking', 'label' => 'Net Banking', 'note' => null, 'brands' => ['Bank']],
        ['id' => 'wallet', 'label' => 'Wallets', 'note' => '(Amazon Pay, etc.)', 'brands' => ['Amazon Pay']],
        ['id' => 'cod', 'label' => 'Cash on Delivery (COD)', 'note' => 'Available for select locations', 'brands' => ['COD']],
    ],

    'checkout_steps' => [
        ['step' => 1, 'label' => 'Cart'],
        ['step' => 2, 'label' => 'Address'],
        ['step' => 3, 'label' => 'Delivery'],
        ['step' => 4, 'label' => 'Payment'],
        ['step' => 5, 'label' => 'Order Confirmed'],
    ],

    /* --------------------------------------------------------- vehicle order */
    'order' => [
        'id' => 'ABZ20250905C001',
        'placed_at' => '05 Sep 2026, 11:24 AM',
        'status' => 'Ready for Delivery',
        'headline' => 'Your order is confirmed!',
        'subhead' => 'Thank you for choosing AutoBazaar. We will keep you updated at every step.',

        'vehicle' => [
            'name' => 'TVS King Deluxe',
            'slug' => 'tvs-king-deluxe',
            'tag' => 'New Vehicle',
            'meta' => 'Petrol | BS6 | 3 Seater',
            'image' => 'assets/image/auto_brands/tvs.png',
            'facts' => [
                ['icon' => 'palette', 'label' => 'Colour', 'value' => 'Yellow'],
                ['icon' => 'doc', 'label' => 'Ex-Showroom Price', 'value' => '₹2,52,000'],
                ['icon' => 'rupee', 'label' => 'EMI (Approx.)', 'value' => '₹5,499/month'],
                ['icon' => 'fuel', 'label' => 'Mileage', 'value' => '26 km/kg (CNG)'],
            ],
        ],

        'tracker' => [
            ['label' => 'Order Placed', 'note' => '05 Sep, 11:24 AM', 'state' => 'done', 'icon' => 'check'],
            ['label' => 'Payment Confirmed', 'note' => '05 Sep, 11:30 AM', 'state' => 'done', 'icon' => 'check'],
            ['label' => 'Vehicle Processing', 'note' => '05 Sep, 2:15 PM', 'state' => 'done', 'icon' => 'check'],
            ['label' => 'Ready for Delivery', 'note' => "Expected by\n08 Sep 2026", 'state' => 'current', 'icon' => 'box'],
            ['label' => 'Delivered', 'note' => null, 'state' => 'pending', 'icon' => 'truck'],
        ],

        'delivery' => [
            'expected' => '08 Sep 2026',
            'location' => ['12, Anna Nagar', 'Chennai - 600040, Tamil Nadu'],
        ],

        'payment' => [
            'method' => 'UPI (Google Pay)',
            'paid' => '₹50,000',
            'paid_note' => '(Advance)',
            'balance' => '₹2,02,000',
            'balance_note' => '(On Delivery)',
        ],

        'documents' => [
            ['label' => 'Order Invoice'],
            ['label' => 'Payment Receipt'],
            ['label' => 'Proforma Invoice'],
        ],
    ],

    /* Account sidebar on my_orders.jpeg */
    'account' => [
        'user' => [
            'name' => 'Sivam R',
            'email' => 'sivam@gmail.com',
            'initial' => 'S',
        ],
        'menu' => [
            ['label' => 'Dashboard', 'icon' => 'grid', 'route' => 'site.account'],
            ['label' => 'My Orders', 'icon' => 'doc', 'route' => 'site.account.orders'],
            ['label' => 'Enquiries', 'icon' => 'doc', 'route' => 'site.account.section', 'param' => 'enquiries'],
            ['label' => 'Saved Vehicles', 'icon' => 'heart', 'route' => 'site.account.section', 'param' => 'saved'],
            ['label' => 'My Comparisons', 'icon' => 'chart', 'route' => 'site.account.section', 'param' => 'comparisons'],
            ['label' => 'Addresses', 'icon' => 'pin', 'route' => 'site.account.section', 'param' => 'addresses'],
            ['label' => 'Payment Methods', 'icon' => 'card', 'route' => 'site.account.section', 'param' => 'payment-methods'],
            ['label' => 'Notifications', 'icon' => 'bell', 'route' => 'site.account.section', 'param' => 'notifications'],
            ['label' => 'Refer & Earn', 'icon' => 'users', 'route' => 'site.account.section', 'param' => 'refer'],
            ['label' => 'Support', 'icon' => 'headset', 'route' => 'site.account.section', 'param' => 'support'],
            ['label' => 'Logout', 'icon' => 'logout', 'route' => 'site.home'],
        ],
    ],
];
