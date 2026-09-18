@extends('admin.layouts.app')
@section('title')
Vehicle Catalogue
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="btn-group" role="group">
            @foreach(['all' => 'All', 'live' => 'Live', 'draft' => 'Draft'] as $k => $label)
                <a href="{{ route('vehicles.catalogue', $k === 'all' ? [] : ['status' => $k]) }}" class="btn btn-sm {{ $status === $k ? 'btn-primary' : 'btn-outline-primary' }}">{{ $label }}</a>
            @endforeach
        </div>
        @can('add_vehicle_catalog')
            <a href="{{ route('vehicles.catalogue.create') }}" class="btn create-new btn-primary">
                <span class="d-flex align-items-center">
                    <i class="icon-base ri ri-add-line icon-18px me-sm-1"></i>
                    <span class="d-none d-sm-inline-block">Add Vehicle Model</span>
                </span>
            </a>
        @endcan
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-datatable text-nowrap">
                <table class="datatables-fixed1 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Sl.no</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Variants</th>
                            <th>On-road Price</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($models as $m)
                            @php $price = $m->prices->first(); @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($m->image)
                                        <img src="{{ asset($m->image) }}" style="width:70px;height:50px;object-fit:contain" class="img-fluid" alt="">
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $m->name }}</strong>
                                    @if($m->badge)<span class="badge bg-label-warning ms-1">{{ $m->badge }}</span>@endif
                                    <br><small class="text-muted">{{ $m->segment }} &middot; {{ $m->model_slug }}</small>
                                </td>
                                <td>{{ $m->brand->brand_name ?? '' }}</td>
                                <td>{{ $m->variants_count }}</td>
                                <td>
                                    @if($price)
                                        ₹{{ number_format($price->on_road) }}
                                        <br><small class="text-muted">{{ $price->location }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($m->rating_count)
                                        <i class="icon-base ri ri-star-fill text-warning icon-16px"></i> {{ number_format($m->rating_avg, 1) }}
                                        <small class="text-muted">({{ $m->rating_count }})</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @can('edit_vehicle_catalog')
                                        <label class="switch switch-success">
                                            <input type="checkbox" data-id="{{ $m->id }}" class="switch-input toggle-status" {{ $m->status_id == 1 ? 'checked' : '' }} />
                                            <span class="switch-toggle-slider"><span class="switch-on"></span><span class="switch-off"></span></span>
                                            <span class="switch-label status-label-{{ $m->id }}" style="color: {{ $m->status_id == 1 ? 'green' : '#888' }}">{{ $m->status_id == 1 ? 'Live' : 'Draft' }}</span>
                                        </label>
                                    @else
                                        <span class="badge {{ $m->status_id == 1 ? 'bg-label-success' : 'bg-label-secondary' }}">{{ $m->status_id == 1 ? 'Live' : 'Draft' }}</span>
                                    @endcan
                                </td>
                                <td>
                                    @if($m->status_id == 1 && $m->brand && Route::has('site.model'))
                                        <a href="{{ route('site.model', [Str::slug($m->brand->brand_name), $m->model_slug]) }}" target="_blank" class="btn btn-sm btn-text-info rounded-pill btn-icon" title="View on site"><i class="icon-base ri ri-external-link-line icon-20px"></i></a>
                                    @endif
                                    @can('edit_vehicle_catalog')
                                        <a href="{{ route('vehicles.catalogue.edit', $m->id) }}" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="icon-base ri ri-edit-box-line icon-20px"></i></a>
                                    @endcan
                                    @can('delete_vehicle_catalog')
                                        <a class="btn btn-sm btn-text-danger text-danger rounded-pill btn-icon item-delete" data-id="{{ $m->id }}" data-name="{{ $m->name }}"><i class="icon-base ri ri-delete-bin-2-line icon-20px"></i></a>
                                    @endcan
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
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("click", ".item-delete", function() {
            let el = $(this);
            Swal.fire({
                title: 'Delete "' + el.data('name') + '"?',
                text: "The model and all its variants, prices, images and documents will be permanently removed.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-outline-secondary waves-effect'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('vehicles.catalogue.delete') }}",
                        data: { "_token": "{{ csrf_token() }}", id: el.data('id') },
                        beforeSend: function() {
                            $.blockUI({ message: '<div class="feather icon-refresh-cw icon-spin font-medium-2"></div>',
                                overlayCSS: { backgroundColor: '#FFF', opacity: 0.8, cursor: 'wait' },
                                css: { border: 0, padding: 0, backgroundColor: 'transparent' } });
                        },
                        complete: function() { $.unblockUI(); },
                        success: function (response) {
                            if (response.success == true) {
                                Swal.fire({ icon: 'success', title: 'Deleted!', text: response.message,
                                    customClass: { confirmButton: 'btn btn-success waves-effect' } })
                                    .then(function(){ location.reload(); });
                            } else {
                                Swal.fire({ title: "Error!", text: response.message, icon: "error",
                                    customClass: { confirmButton: 'btn btn-danger' }, buttonsStyling: false });
                            }
                        },
                        error: function () { $.unblockUI(); alert('Could not delete'); }
                    });
                }
            });
        });

        $(document).on('change', '.toggle-status', function() {
            let checkbox = $(this);
            let id = checkbox.data('id');
            let newStatus = checkbox.is(':checked') ? 1 : 0;
            $.ajax({
                url: "{{ route('vehicles.catalogue.status') }}",
                type: "POST",
                data: { _token: "{{ csrf_token() }}", id: id, status_id: newStatus },
                success: function() {
                    let label = $('.status-label-' + id);
                    if (newStatus == 1) { label.text('Live').css('color', 'green'); } else { label.text('Draft').css('color', '#888'); }
                },
                error: function() {
                    alert('Error updating status');
                    checkbox.prop('checked', !checkbox.is(':checked'));
                }
            });
        });
    });
</script>
@endpush
