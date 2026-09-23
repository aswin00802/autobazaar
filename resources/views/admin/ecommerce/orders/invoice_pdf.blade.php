{{--
    The invoice for a website accessory order.

    Built for dompdf, which only understands fairly old HTML — so this uses a
    table for the layout and inline styles rather than anything modern. Keep it
    that way or the PDF comes out misaligned.

    The business name, address, phone and email come from Settings, so changing
    them there changes every invoice printed afterwards.
--}}
@php
    $contact = $site['contact'] ?? [];
    $brand = $site['brand']['name'] ?? 'AutoBazaar';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; margin: 0; }
        .wrap { padding: 28px 32px; }
        .muted { color: #6b7671; }
        .right { text-align: right; }
        .center { text-align: center; }
        h1 { font-size: 20px; margin: 0 0 2px; color: #0B5D3B; }
        h2 { font-size: 13px; margin: 0 0 6px; }
        table { width: 100%; border-collapse: collapse; }
        .head td { vertical-align: top; padding: 0 0 18px; }
        .meta td { padding: 2px 0; font-size: 11px; }
        .items th { background: #0B5D3B; color: #fff; padding: 7px 8px; font-size: 11px; text-align: left; }
        .items td { padding: 7px 8px; border-bottom: 1px solid #e3e8e5; }
        .totals td { padding: 4px 8px; font-size: 12px; }
        .totals .grand td { border-top: 2px solid #0B5D3B; font-size: 14px; font-weight: bold; padding-top: 8px; }
        .box { border: 1px solid #e3e8e5; padding: 10px 12px; }
        .foot { margin-top: 26px; border-top: 1px solid #e3e8e5; padding-top: 10px; font-size: 10px; }
        .pill { background: #eef5f1; color: #0B5D3B; padding: 2px 8px; font-size: 10px; }
    </style>
</head>
<body>
<div class="wrap">

    <table class="head">
        <tr>
            <td style="width:60%">
                <h1>{{ $brand }}</h1>
                <div class="muted" style="font-size:11px; line-height:1.5;">
                    @if (! empty($contact['address'])){{ $contact['address'] }}<br>@endif
                    @if (! empty($contact['phone'])){{ $contact['phone'] }}@endif
                    @if (! empty($contact['email'])) &middot; {{ $contact['email'] }}@endif
                </div>
            </td>
            <td style="width:40%" class="right">
                <h2>INVOICE</h2>
                <table class="meta">
                    <tr><td class="right muted">Invoice No</td><td class="right" style="padding-left:10px"><strong>{{ $order->order_number }}</strong></td></tr>
                    <tr><td class="right muted">Date</td><td class="right" style="padding-left:10px">{{ $order->created_at->format('d M Y') }}</td></tr>
                    <tr><td class="right muted">Payment</td><td class="right" style="padding-left:10px">{{ strtoupper($order->payment_mode) }} · {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</td></tr>
                    <tr><td class="right muted">Status</td><td class="right" style="padding-left:10px"><span class="pill">{{ ucfirst($order->order_status) }}</span></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="width:50%; padding-right:10px;">
                <div class="box">
                    <strong>Deliver to</strong><br>
                    {{ $order->shipping_name }}<br>
                    {{ $order->shipping_mobile }}<br>
                    <span class="muted">
                        {{ $order->shipping_address_line_1 }}<br>
                        @if ($order->shipping_address_line_2){{ $order->shipping_address_line_2 }}<br>@endif
                        {{ collect([$order->shipping_city, $order->shipping_pincode])->filter()->implode(' - ') }}<br>
                        {{ collect([$order->shipping_district, $order->shipping_state])->filter()->implode(', ') }}
                    </span>
                </div>
            </td>
            <td style="width:50%; padding-left:10px;">
                <div class="box">
                    <strong>Delivery</strong><br>
                    {{ ucfirst($order->delivery_option) }}<br>
                    <span class="muted">
                        {{ $order->items->sum('qty') }} item{{ $order->items->sum('qty') === 1 ? '' : 's' }}<br>
                        Placed {{ $order->created_at->format('d M Y, g:i A') }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <table class="items" style="margin-top:18px;">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:45%">Product</th>
                <th style="width:18%">Fits</th>
                <th style="width:8%" class="center">Qty</th>
                <th style="width:12%" class="right">Rate</th>
                <th style="width:12%" class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="muted">{{ $item->brand_name ?: '—' }}</td>
                    <td class="center">{{ $item->qty }}</td>
                    <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="right">{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top:14px;">
        <tr>
            <td style="width:58%"></td>
            <td style="width:42%">
                <table class="totals">
                    <tr>
                        <td class="muted">Subtotal</td>
                        <td class="right">Rs. {{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    @if ($order->discount_amount > 0)
                        <tr>
                            <td class="muted">Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif</td>
                            <td class="right">− Rs. {{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="muted">Shipping</td>
                        <td class="right">{{ $order->shipping_amount > 0 ? 'Rs. ' . number_format($order->shipping_amount, 2) : 'Free' }}</td>
                    </tr>
                    <tr class="grand">
                        <td>Total</td>
                        <td class="right">Rs. {{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="foot muted">
        <strong>Prices include tax.</strong>
        This is a computer-generated invoice and needs no signature.
        @if (! empty($contact['phone']))
            Questions about this order? Call {{ $contact['phone'] }} quoting {{ $order->order_number }}.
        @endif
    </div>

</div>
</body>
</html>
