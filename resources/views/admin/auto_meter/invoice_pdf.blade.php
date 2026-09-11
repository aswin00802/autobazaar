<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice</title>
  <style>
    body {
  font-family: Arial, sans-serif;
  background: #fff;
  /* padding: 20px; */
}

.invoice-container {
  /* width: 400px; */
  margin: auto;
  border: 1px solid #000;
  padding: 15px;
}

.title {
  text-align: center;
  font-size: 22px;
  font-weight: bold;
}

.invoice-header {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}

.billing, .invoice-info {
  margin-top: 10px;
  font-size: 14px;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
  font-size: 13px;
}

table th, table td {
  border: 1px solid #000;
  padding: 5px;
  text-align: left;
}

.summary {
  margin-top: 15px;
  text-align: right;
}

.total {
  font-size: 16px;
  font-weight: bold;
}

.footer {
  margin-top: 20px;
  text-align: center;
  font-size: 13px;
}

.tamil {
  margin-top: 10px;
}

.thankyou {
  text-align: center;
  margin-top: 20px;
  font-weight: bold;
}
  </style>
</head>
<body>

<div class="invoice-container">

  <h1 class="title">AUTO METER CALCULATION</h1>

  <div class="invoice-header">
    <p><strong>INVOICE NO:</strong> {{$data->invoice_no ?? ''}}</p>
    <p>{{date('d-m-Y')}}</p>
  </div>

  <div class="billing">
    <p><strong>BILLED TO :</strong></p>
    <p>Vehicle No: {{ $data->user->userInfo->vehicle_no ?? '' }}</p>
    <p>Name: {{$data->user->name}}</p>
    <p>Brand / Model: {{ $data->user->userInfo->autoBrand->brand_name ?? '' }} {{ $data->user->userInfo->autoModel->model_name ?? '' }}</p>
    <p>Fuel: {{ $data->user->userInfo->autoFueltype->name ?? '' }}</p>
    <p>Mileage: {{ $data->user->userInfo->millage ?? '' }} km</p>
    <p>From: {{$data->from_location}}</p>
    <p>To: {{$data->to_location}}</p>
  </div>

  <div class="invoice-info">
    <p><strong>INVOICE DATE :</strong></p>
    <p>{{date('d-m-Y',strtotime($data->date))}}</p>
    <p>Total Km - {{$data->total_km}}</p>
    <p>Total Time - {{$data->total_time}}</p>
  </div>

  <table>
    <thead>
      <tr>
        <th>ITEM DESCRIPTION</th>
        <th>QTY</th>
        <th>PRICE</th>
        <th>TOTAL</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Basic fare</td>
        <td>1.8 km</td>
        <td>50</td>
        <td>50</td>
      </tr>
      <tr>
        <td>Total km fare</td>
        <td>
          @php
            $km = ($data->total_km - 1.8);
          @endphp
          @if($data->total_km > 1.8)
            {{($data->total_km - 1.8)}} km
          @else
            0.0 km
          @endif
        </td>
        <td>
          @if($data->total_km > 1.8)
            {{($km * 18)}}
          @else
            0.0
          @endif
        </td>
        <td>
          @if($data->total_km > 1.8)
            {{($km * 18)}}
          @else
            0.0
          @endif
        </td>
      </tr>
      <tr>
        <td>Fuel expense</td>
        <td>{{$data->total_km}} km</td>
        <td>{{ $data->fuel_amount }}</td>
        <td>{{ $data->fuel_amount }}</td>
      </tr>
      <tr>
        <td>Wear & friction cost</td>
        <td>{{$data->total_km}} km</td>
        <td>{{ $data->friction_amount }}</td>
        <td>{{ $data->friction_amount }}</td>
      </tr>
      <tr>
        <td>Driver wage</td>
        <td>{{$data->total_time}}</td>
        <td>{{ $data->wages_amount }}</td>
        <td>{{ $data->wages_amount }}</td>
      </tr>
      <tr>
        <td>Additional tips</td>
        <td>-</td>
        <td>-</td>
        <td>{{ $data->tips_amount }}</td>
      </tr>
    </tbody>
  </table>

  <div class="summary">
    <p><strong>DUTY MARGIN</strong> : Rs.{{ $data->margin_amount }}</p>
    <!-- <p>Rs.49</p> -->

    <p><strong>TOTAL</strong> : Rs.{{ $data->total_amount }}</p>
    <!-- <p class="total">Rs.50</p> -->
  </div>



</div>

</body>
</html>