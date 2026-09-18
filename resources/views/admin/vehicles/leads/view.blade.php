@extends('admin.layouts.app')
@section('title')
Vehicle Leads / {{ $lead->enquiry_no }}
@endsection

@push('css')
<style>
    .lead-stepper { display: flex; flex-wrap: wrap; gap: 6px; }
    .lead-stepper .step { flex: 1 1 0; min-width: 90px; text-align: center; padding: 8px 4px; border-radius: 6px; background: #f1f2f6; color: #7d8398; font-size: 12px; position: relative; }
    .lead-stepper .step.done { background: #e6f6ee; color: #28a745; }
    .lead-stepper .step.current { background: #666cff; color: #fff; font-weight: 600; }
    .lead-stepper .step .n { display: block; font-size: 11px; opacity: .8; }
</style>
@endpush

@section('content')
@php
    $currentIdx = array_search($lead->lead_status, $pipeline);
    $wa = preg_replace('/\D/', '', $lead->mobile);
    if (strlen($wa) === 10) { $wa = '91' . $wa; }
@endphp
<div class="row">
    <div class="col-12 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4 class="mb-0">
            Lead {{ $lead->enquiry_no }}
            <span class="badge bg-label-primary ms-2">{{ $sources[$lead->source] ?? $lead->source }}</span>
        </h4>
        <div class="d-flex gap-2">
            <a href="tel:{{ $lead->mobile }}" class="btn btn-outline-primary"><i class="icon-base ri ri-phone-line me-1"></i>Call</a>
            <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Hello ' . $lead->name . ', this is regarding your enquiry ' . $lead->enquiry_no . ($lead->model ? ' for ' . $lead->model->name : '') . '.') }}" target="_blank" class="btn btn-success"><i class="icon-base ri ri-whatsapp-line me-1"></i>WhatsApp</a>
            <a href="{{ route('vehicles.leads') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="card">
            <h5 class="card-header">Pipeline</h5>
            <div class="card-body">
                <div class="lead-stepper mb-3">
                    @foreach($pipeline as $i => $p)
                        <div class="step {{ $currentIdx !== false && $i < $currentIdx ? 'done' : '' }} {{ $i === $currentIdx ? 'current' : '' }}">
                            <span class="n">{{ $i + 1 }}</span>{{ ucwords(str_replace('_', ' ', $p)) }}
                        </div>
                    @endforeach
                </div>
                <form method="POST" action="{{ route('vehicles.leads.status') }}" class="row g-2 align-items-end">
                    @csrf
                    <input type="hidden" name="id" value="{{ $lead->id }}">
                    <div class="col-md-3 col-8">
                        <label class="form-label mb-1">Move to</label>
                        <select name="lead_status" class="form-select">
                            @foreach($pipeline as $p)
                                <option value="{{ $p }}" @selected($lead->lead_status === $p)>{{ ucwords(str_replace('_', ' ', $p)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-4">
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card mb-4">
            <h5 class="card-header">Customer &amp; Request</h5>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr><th style="width:190px">Name</th><td>{{ $lead->name }} @if($lead->otp_verified)<span class="badge bg-label-success">OTP verified</span>@endif</td></tr>
                        <tr><th>Mobile</th><td><a href="tel:{{ $lead->mobile }}">{{ $lead->mobile }}</a></td></tr>
                        <tr><th>Email</th><td>{{ $lead->email ?: '—' }}</td></tr>
                        <tr><th>Location</th><td>{{ collect([$lead->city, $lead->district, $lead->state, $lead->pincode])->filter()->implode(', ') ?: '—' }}</td></tr>
                        <tr><th>Registered user</th><td>{{ $lead->user->name ?? '—' }}</td></tr>
                        <tr><td colspan="2"><hr class="my-1"></td></tr>
                        <tr><th>Model</th><td>
                            {{ $lead->model->name ?? '—' }}
                            @if($lead->model)<a href="{{ route('vehicles.catalogue.edit', $lead->model->id) }}" class="ms-1 small">(open)</a>@endif
                        </td></tr>
                        <tr><th>Variant</th><td>{{ $lead->variant->name ?? '—' }}</td></tr>
                        <tr><th>Source</th><td>{{ $sources[$lead->source] ?? $lead->source }}</td></tr>
                        @if($lead->preferred_at)
                            <tr><th>Preferred date</th><td>{{ $lead->preferred_at->format('d M Y') }} {{ $lead->time_slot }}</td></tr>
                        @endif
                        @if($lead->buying_timeframe)
                            <tr><th>Buying timeframe</th><td>{{ $lead->buying_timeframe }}</td></tr>
                        @endif
                        @if($lead->buying_options)
                            <tr><th>Buying options</th><td>{{ implode(', ', (array) $lead->buying_options) }}</td></tr>
                        @endif
                        @if($lead->loan_amount)
                            <tr><th>Loan amount</th><td>₹{{ number_format($lead->loan_amount) }}</td></tr>
                        @endif
                        <tr><th>Message</th><td class="text-wrap">{{ $lead->message ?: '—' }}</td></tr>
                        <tr><td colspan="2"><hr class="my-1"></td></tr>
                        <tr><th>Received</th><td>{{ $lead->created_at?->format('d M Y H:i') }}</td></tr>
                        <tr><th>Page</th><td class="text-wrap">@if($lead->page_url)<a href="{{ $lead->page_url }}" target="_blank">{{ Str::limit($lead->page_url, 70) }}</a>@else — @endif</td></tr>
                        <tr><th>IP</th><td>{{ $lead->ip_address ?: '—' }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-4">
            <h5 class="card-header">Assignment</h5>
            <div class="card-body">
                <form method="POST" action="{{ route('vehicles.leads.assign') }}" class="row g-2 align-items-end">
                    @csrf
                    <input type="hidden" name="id" value="{{ $lead->id }}">
                    <div class="col-8">
                        <label class="form-label mb-1">Assigned to</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">Unassigned</option>
                            @foreach($admins as $a)
                                <option value="{{ $a->id }}" @selected((int) $lead->assigned_to === (int) $a->id)>{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary w-100">Assign</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <h5 class="card-header">Admin Note</h5>
            <div class="card-body">
                <form method="POST" action="{{ route('vehicles.leads.note') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $lead->id }}">
                    <textarea name="admin_note" class="form-control mb-2" rows="6" placeholder="Call summary, follow-up date, documents pending ...">{{ old('admin_note', $lead->admin_note) }}</textarea>
                    <button type="submit" class="btn btn-primary">Save Note</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
