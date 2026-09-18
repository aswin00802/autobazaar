@extends('admin.layouts.app')
@section('title')
Vehicle Leads
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('vehicles.leads') }}" class="row g-2 align-items-end">
                    <div class="col-md-3 col-6">
                        <label class="form-label mb-1">Search</label>
                        <input type="text" name="q" class="form-control form-control-sm" value="{{ $filters['q'] }}" placeholder="Mobile / name / enquiry no">
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label mb-1">Source</label>
                        <select name="source" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($sources as $k => $label)<option value="{{ $k }}" @selected($filters['source'] === $k)>{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label mb-1">Lead Status</label>
                        <select name="lead_status" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($pipeline as $p)<option value="{{ $p }}" @selected($filters['lead_status'] === $p)>{{ ucwords(str_replace('_', ' ', $p)) }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label mb-1">Model</label>
                        <select name="model_id" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($models as $m)<option value="{{ $m->id }}" @selected((string) $filters['model_id'] === (string) $m->id)>{{ $m->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-1 col-6">
                        <label class="form-label mb-1">From</label>
                        <input type="date" name="from" class="form-control form-control-sm" value="{{ $filters['from'] }}">
                    </div>
                    <div class="col-md-1 col-6">
                        <label class="form-label mb-1">To</label>
                        <input type="date" name="to" class="form-control form-control-sm" value="{{ $filters['to'] }}">
                    </div>
                    <div class="col-md-1 col-12 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('vehicles.leads') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-datatable text-nowrap">
                <table class="datatables-fixed1 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Enquiry No</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Model</th>
                            <th>Source</th>
                            <th>Lead Status</th>
                            <th>Assigned To</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leads as $lead)
                            <tr>
                                <td><a href="{{ route('vehicles.leads.view', $lead->id) }}">{{ $lead->enquiry_no }}</a></td>
                                <td>{{ $lead->created_at?->format('d M Y H:i') }}</td>
                                <td>
                                    {{ $lead->name }}
                                    @if($lead->otp_verified)<i class="icon-base ri ri-shield-check-line text-success icon-14px" title="OTP verified"></i>@endif
                                    <br><small class="text-muted">{{ $lead->city ?: $lead->district }}</small>
                                </td>
                                <td><a href="tel:{{ $lead->mobile }}">{{ $lead->mobile }}</a></td>
                                <td>
                                    {{ $lead->model->name ?? '—' }}
                                    @if($lead->variant)<br><small class="text-muted">{{ $lead->variant->name }}</small>@endif
                                </td>
                                <td>
                                    @php $cls = ['enquiry' => 'bg-label-primary', 'quotation' => 'bg-label-info', 'test_drive' => 'bg-label-warning', 'loan' => 'bg-label-success', 'call' => 'bg-label-secondary', 'whatsapp' => 'bg-label-success'][$lead->source] ?? 'bg-label-secondary'; @endphp
                                    <span class="badge {{ $cls }}">{{ $sources[$lead->source] ?? $lead->source }}</span>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm lead-status" data-id="{{ $lead->id }}" style="min-width:130px">
                                        @foreach($pipeline as $p)
                                            <option value="{{ $p }}" @selected($lead->lead_status === $p)>{{ ucwords(str_replace('_', ' ', $p)) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm lead-assign" data-id="{{ $lead->id }}" style="min-width:140px">
                                        <option value="">Unassigned</option>
                                        @foreach($admins as $a)
                                            <option value="{{ $a->id }}" @selected((int) $lead->assigned_to === (int) $a->id)>{{ $a->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <a href="{{ route('vehicles.leads.view', $lead->id) }}" class="btn btn-sm btn-primary"><i class="icon-base ri ri-eye-line icon-16px"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        function post(url, data, $el) {
            $el.prop('disabled', true);
            $.ajax({
                url: url, type: 'POST', dataType: 'json',
                data: $.extend({ _token: "{{ csrf_token() }}" }, data),
                success: function () { $el.prop('disabled', false).addClass('border-success'); setTimeout(function () { $el.removeClass('border-success'); }, 1200); },
                error: function () { $el.prop('disabled', false); alert('Could not save'); }
            });
        }
        $(document).on('change', '.lead-status', function () {
            post("{{ route('vehicles.leads.status') }}", { id: $(this).data('id'), lead_status: $(this).val() }, $(this));
        });
        $(document).on('change', '.lead-assign', function () {
            post("{{ route('vehicles.leads.assign') }}", { id: $(this).data('id'), assigned_to: $(this).val() }, $(this));
        });
    });
</script>
@endpush
