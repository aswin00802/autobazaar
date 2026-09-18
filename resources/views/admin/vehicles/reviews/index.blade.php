@extends('admin.layouts.app')
@section('title')
Vehicle Reviews
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="btn-group" role="group">
            @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $k => $label)
                <a href="{{ route('vehicles.reviews', $k === 'all' ? [] : ['status' => $k]) }}" class="btn btn-sm {{ $status === $k ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ $label }}
                    @if($k !== 'all')<span class="badge bg-white text-dark ms-1">{{ $counts[$k] ?? 0 }}</span>@endif
                </a>
            @endforeach
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-datatable text-nowrap">
                <table class="datatables-fixed1 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Sl.no</th>
                            <th>Model</th>
                            <th>Reviewer</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $r)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $r->model->name ?? '—' }}</td>
                                <td>
                                    {{ $r->name }}
                                    @if($r->is_verified)<span class="badge bg-label-success ms-1">Verified</span>@endif
                                    <br><small class="text-muted">{{ $r->city }}{{ $r->mobile ? ' · ' . $r->mobile : '' }}</small>
                                </td>
                                <td>
                                    <span class="text-warning">
                                        @for($s = 1; $s <= 5; $s++)
                                            <i class="icon-base ri {{ $s <= $r->rating ? 'ri-star-fill' : 'ri-star-line' }} icon-14px"></i>
                                        @endfor
                                    </span>
                                    <small class="text-muted">{{ $r->rating }}/5</small>
                                </td>
                                <td class="text-wrap" style="max-width:380px">
                                    @if($r->title)<strong>{{ $r->title }}</strong><br>@endif
                                    <span class="text-muted">{{ Str::limit($r->body, 160) }}</span>
                                </td>
                                <td>
                                    @php $cls = ['pending' => 'bg-label-warning', 'approved' => 'bg-label-success', 'rejected' => 'bg-label-danger'][$r->review_status] ?? 'bg-label-secondary'; @endphp
                                    <span class="badge {{ $cls }}">{{ ucfirst($r->review_status) }}</span>
                                </td>
                                <td>{{ $r->created_at?->format('d M Y H:i') }}</td>
                                <td>
                                    @if($r->review_status !== 'approved')
                                        <form method="POST" action="{{ route('vehicles.reviews.status') }}" class="d-inline" onsubmit="return confirm('Approve this review?')">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $r->id }}">
                                            <input type="hidden" name="review_status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve"><i class="icon-base ri ri-check-line icon-16px"></i></button>
                                        </form>
                                    @endif
                                    @if($r->review_status !== 'rejected')
                                        <form method="POST" action="{{ route('vehicles.reviews.status') }}" class="d-inline" onsubmit="return confirm('Reject this review?')">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $r->id }}">
                                            <input type="hidden" name="review_status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-warning" title="Reject"><i class="icon-base ri ri-close-line icon-16px"></i></button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('vehicles.reviews.delete') }}" class="d-inline" onsubmit="return confirm('Delete this review permanently?')">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $r->id }}">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="icon-base ri ri-delete-bin-line icon-16px"></i></button>
                                    </form>
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
@endpush
