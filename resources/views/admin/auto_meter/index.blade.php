@extends('admin.layouts.app')
@section('title')
    Auto Meter
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('auto-meter') }}">
                    <div class="row">
                        <div class="col-md-4 form-control-validation">
                            <div class="form-floating form-floating-outline">
                                <input type="date" id="from_date" value="{{ $from_date }}" class="form-control"name="from_date">
                                <label for="from_date">
                                    From Date
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 form-control-validation">
                            <div class="form-floating form-floating-outline">
                                <input type="date" id="to_date" value="{{ $to_date }}" class="form-control"name="to_date">
                                <label for="to_date">
                                    To Date
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 form-control-validation">
                            <div class="form-floating form-floating-outline">
                                <button type="submit" class="btn btn-facebook waves-effect waves-light"><i class="icon-base ri ri-filter-line icon-16px me-2"></i>Filter</button>
                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-body">
                <table class="datatables-fixed1 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Sl.no</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Invoice No</th>
                            <th>Total KM</th>
                            <th>Total Amount</th>
                            <th>Margin Amount</th>
                            <th>Date</th>
                            <th>Download Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($auto_meters))
                            @foreach ($auto_meters as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>{{ $item->user->phone_number }}</td>
                                    <td>
                                        <a href="{{ route('auto-meter.invoice',$item->id) }}" target="_blank">
                                            {{ $item->invoice_no }}
                                        </a>
                                    </td>
                                    <td>{{ $item->total_km ?? '' }}</td>
                                    <td>{{ $item->total_amount ?? '' }}</td>
                                    <td>{{ $item->margin_amount ?? '' }}</td>
                                    <td>{{ $item->date ?? '' }}</td>
                                    <td>
                                        <a href="{{ route('auto-meter.invoice.download',$item->id) }}" class="btn btn-sm btn-text-primary rounded-pill btn-icon" title="Download invoice"><i class="icon-base ri ri-download-2-line icon-20px"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="{{ asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js') }}"></script>
@endpush