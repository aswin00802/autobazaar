<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>POS Quotation</title>

    <style>

        body {
            font-family: "DejaVu Sans", sans-serif;
            /* font-family: DejaVu Sans, sans-serif; */
            font-size: 12px;
            color: #000;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #6bbf3c;
            padding-bottom: 10px;
        }

        .header img {
            height: 150px;
            float: left;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1a2c7a;
        }

        .clearfix {
            clear: both;
        }

        .section {
            margin-top: 5px;
        }

        /* .vehicle-table td {
            padding: 5px;
            vertical-align: top;
        }

        .label {
            color: #1a4fa3;
            font-weight: bold;
            width: 180px;
        } */

        .vehicle-header {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .vehicle-table {
            width: 100%;
            font-size: 14px;
        }

        .vehicle-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            width: 180px;
        }

        .customer-header {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            /* margin-bottom: 10px; */
        }

        .customer-box {
            /* border-top: 2px solid #000; */
            /* border-bottom: 2px solid #000; */
            padding: 5px 0;
            font-size: 14px;
        }

        .customer-table {
            width: 100%;
        }

        .customer-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .vehicle-img {
            text-align: right;
        }

        .vehicle-img img {
            width: 200px;
        }

        .finance-title {
            text-align: center;
            color: #e53935;
            font-weight: bold;
            font-size: 16px;
            margin: 10px 0 10px;
        }

        .finance-box {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
        }

        .finance-table {
            width: 100%;
        }

        .finance-table td {
            padding: 5px;
            font-size: 13px;
        }

        .finance-label {
            color: #1a4fa3;
            font-weight: bold;
        }

        .finance-value {
            text-align: right;
            font-weight: bold;
        }

        .disclaimer {
            font-size: 9px;
            color: #444;
            margin-top: 15px;
            text-align: center;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            color: #1a2c7a;
        }

        .green-bar {
            height: 6px;
            background: #6bbf3c;
            margin-top: 10px;
        }

        .gifts-section {
            margin-top: 20px;
        }
        .gifts-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .gifts-table {
            width: 100%;
            font-size: 13px;
        }
        .gifts-table td {
            padding: 4px 0;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <img src="{{ public_path('uploads/jp-auto-header.png') }}" width="100%" height="100%" alt="Logo">
        <div class="clearfix"></div>
    </div>

     <!-- CUSTOMER DETAILS -->
    <div class="section">

        <div class="customer-header">
            CUSTOMER DETAILS
        </div>

        <!-- <div class="customer-box">
            <table class="customer-table">
                <tr>
                    <td width="50%">
                        <table width="100%">
                            <tr>
                                <td class="label">Customer Name:</td>
                                <td>{{ $quotation->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Contact:</td>
                                <td>{{ $quotation->mobile ?? '-' }}</td>
                            </tr>
                        </table>
                    </td>

                    <td width="50%">
                        <table width="100%">
                            <tr>
                                <td class="label">Email:</td>
                                <td>{{ $quotation->email ?? '-' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table width="100%">
                            <tr>
                                <td class="label">Address:</td>
                                <td>{{ $quotation->address ?? '-' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div> -->
        <div class="customer-box">
            <table class="customer-table" width="100%" cellpadding="3" cellspacing="0" style="border-collapse: collapse;">
                <tr>
                    <td><strong>Customer Name:</strong> {{ $quotation->name ?? '-' }}</td>
                    <td><strong>Contact:</strong> {{ $quotation->mobile ?? '-' }}</td>
                    <td><strong>Email:</strong> {{ $quotation->email ?? '-' }}</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Address:</strong> {{ $quotation->address ?? '-' }}</td>
                </tr>
            </table>
        </div>

    </div>

    <!-- VEHICLE DETAILS -->
    <div class="section">
        <div class="vehicle-header">
            VEHICLE DETAILS
        </div>
        <table class="vehicle-table">
            <tr>
                <td width="50%">
                    <table>
                        <tr>
                            <td class="label">Quotation No:</td>
                            <td>{{ $quotation->quotation_no ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Brand:</td>
                            <td>{{ $quotation->auto->autoBrands->brand_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Model:</td>
                            <td>{{ $quotation->auto->autoModel->model_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Gear:</td>
                            <td>{{ $quotation->auto->gear ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Vehicle Suitability:</td>
                            <td>{{ $quotation->auto->vehicle_suitable ?? '-' }}</td>
                        </tr>
                    </table>
                </td>

                <td width="50%">
                    <table>
                        <tr>
                            <td class="label">Mileage:</td>
                            <td>{{ $quotation->auto->millage ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Fuel Type:</td>
                            <td>{{ $quotation->auto->autoFueltype->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Engine CC:</td>
                            <td>{{ $quotation->auto->engine_cc ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Passenger Capacity:</td>
                            <td>{{ $quotation->auto->passenger_capacity ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Free Service:</td>
                            <td>{{ $quotation->auto->free_service ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- FINANCE -->
    <div class="finance-title">FINANCE DETAIL</div>

    <div class="finance-box">
        <table class="finance-table">
            <tr>
                <!-- LEFT COLUMN -->
                <td width="50%" valign="top">
                    <table width="100%">
                        <tr>
                            <td class="finance-label">ON ROAD PRICE</td>
                            <td class="finance-value">
                                ₹ {{ number_format($quotation->total_amount) }}
                            </td>
                        </tr>

                        <tr>
                            <td class="finance-label">DISCOUNT</td>
                            <td class="finance-value">
                                ₹ {{ number_format($quotation->discount_amount ?? 0) }}
                            </td>
                        </tr>

                        <tr>
                            <td class="finance-label">LOAN AMOUNT</td>
                            <td class="finance-value">
                                ₹ {{ number_format($quotation->total_loan_amount) }}
                            </td>
                        </tr>

                        <tr>
                            <td class="finance-label">DOWN PAYMENT</td>
                            <td class="finance-value">
                                ₹ {{ number_format($quotation->down_payment) }}
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- RIGHT COLUMN -->
                <td width="50%" valign="top">
                    <table width="100%">
                        <tr>
                            <td class="finance-label">TENURE</td>
                            <td class="finance-value">
                                {{ $quotation->emi_months }} Months
                            </td>
                        </tr>

                        <tr>
                            <td class="finance-label">EMI</td>
                            <td class="finance-value">
                                ₹ {{ number_format($quotation->emi_amount) }}
                            </td>
                        </tr>

                        <tr>
                            <td class="finance-label">RATE OF INTEREST</td>
                            <td class="finance-value">
                                {{ $quotation->interest }} %
                            </td>
                        </tr>

                        
                    </table>
                </td>
            </tr>
        </table>
    </div>

    @if(!empty($quotation->gifts) && is_array($quotation->gifts))

        <div class="gifts-section">
            <div class="gifts-title">ADD ON FREE GIFTS</div>

            <table class="gifts-table" width="100%" cellpadding="3" cellspacing="0" style="border-collapse: collapse;">
                @foreach(collect($quotation->gifts)->chunk(3) as $giftChunk)
                    <tr>
                        @foreach($giftChunk as $gift)
                            <td width="33%">• {{ ucwords(str_replace('_',' ',$gift)) }}</td>
                        @endforeach

                        {{-- Fill empty cells if last row has less than 3 gifts --}}
                        @for($i = $giftChunk->count(); $i < 3; $i++)
                            <td>&nbsp;</td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        </div>


    @endif



    <!-- DISCLAIMER -->

    <!-- FOOTER -->
    <div class="footer">
        <!-- CALL FOR MORE DETAIL - 8608860893 / 6384088408 -->
         <img src="{{ public_path('uploads/jp-auto-footer.png') }}" width="100%" height="100%" alt="Footer">
    </div>

</body>
</html>
