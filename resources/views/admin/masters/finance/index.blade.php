@extends('admin.layouts.app')
@section('title')
Auto Finance
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="row">
            @can('add_auto_finance')
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3 d-flex justify-content-end">
                <a href="{{route('masters.auto-finance.create')}}" class="btn create-new btn-primary">
                    <span>
                        <span class="d-flex align-items-center">
                            <i class="icon-base ri ri-add-line icon-18px me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Add New Finance</span>
                        </span>
                    </span>
                </a>
            </div>
            @endcan
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-datatable text-nowrap">
                        <table class="datatables-basic table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>Sl.no</th>
                                    <th>Finance Name</th>
                                    <th>Finance Type</th>
                                    <th>Location </th>
                                    <th>Address </th>
                                    <th>Contact</th>
                                    <th>Interest Rate</th>
                                    <th>Tenure</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($finances))
                                    @foreach($finances as $finance)
                                        <tr>
                                            <td>{{ $loop->iteration}}</td>
                                            <td>{{ $finance->finance_name ?? null }}</td>
                                            <td>{{ $finance->finance_type ?? null}}</td>
                                            <td>{{ $finance->location ?? null }}</td>
                                            <td>{{ $finance->address ?? null }}</td>
                                            <td>{{ $finance->contact ?? null}}</td>
                                            @php $rate = $rates[$finance->id] ?? null; @endphp
                                            <td>
                                                @if($rate)
                                                    {{ number_format($rate->interest_rate, 2) }}% p.a.
                                                    @if($rate->is_featured)<span class="badge bg-label-warning ms-1">Featured</span>@endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $rate ? $rate->min_tenure_months . ' – ' . $rate->max_tenure_months . ' months' : '—' }}</td>
                                            <td>
                                                <label class="switch switch-success">
                                                    <input type="checkbox" data-id="{{ $finance->id }}" class="switch-input toggle-status" {{ $finance->status_id === 1 ? 'checked' : '' }} />
                                                    <span class="switch-toggle-slider">
                                                        <span class="switch-on"></span>
                                                        <span class="switch-off"></span>
                                                    </span>
                                                     <span class="switch-label status-label-{{ $finance->id }}" style="color: {{ $finance->status_id == 1 ? 'green' : 'red' }}">
                                                        {{ $finance->status_id == 1 ? 'Active' : 'Deactive' }}
                                                    </span>
                                                </label>
                                            </td>
                                            <td>
                                                @can('edit_auto_finance')
                                                    <a href="{{ route('masters.auto-finance.edit',$finance->id) }}" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="icon-base ri ri-edit-box-line icon-20px"></i></a>
                                                @endcan
                                                @can('delete_auto_finance')
                                                    <a class="btn btn-sm btn-text-danger text-danger rounded-pill btn-icon item-delete" data-id="{{$finance->id}}"><i class="icon-base ri ri-delete-bin-2-line icon-20px"></i></a>
                                                @endcan
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
                title: 'Are you sure?',
                text: "You won't be able to delete this Auto Finance!",
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
                    let id = el.data('id');
                    
                    $.ajax({
                        type:'POST',
                        url:"{{route('masters.auto-finance.delete')}}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id:id,
                        },
                        beforeSend: function() {
                            $.blockUI({
                                message: '<div class="feather icon-refresh-cw icon-spin font-medium-2"></div>',
                                overlayCSS: {
                                    backgroundColor: '#FFF',
                                    opacity: 0.8,
                                    cursor: 'wait'
                                },
                                css: {
                                    border: 0,
                                    padding: 0,
                                    backgroundColor: 'transparent'
                                }
                            });
                        },
                        complete: function(response) {
                            $.unblockUI();
                        },
                        success: function (response) {
                            $.unblockUI();
                            if(response.success == true){
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect'
                                    }
                                }).then(function(){
                                    location.reload();
                                })
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    type: "error",
                                    confirmButtonClass: "btn btn-danger",
                                    buttonsStyling: !1
                                })
                            }
                        },
                        error: function (error) {
                            $.unblockUI();
                            alert('error; ' + eval(error));
                        }
                    });
                    // Swal.fire({
                    //     icon: 'success',
                    //     title: 'Deleted!',
                    //     text: 'Your file has been deleted.',
                    //     customClass: {
                    //     confirmButton: 'btn btn-success waves-effect'
                    //     }
                    // });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: 'Cancelled',
                    text: 'Your email tempalte file is safe :)',
                    icon: 'error',
                    customClass: {
                    confirmButton: 'btn btn-success waves-effect'
                    }
                });
                }
            });
        })

        $(document).on('change', '.toggle-status', function() {
            let checkbox = $(this);
            let userId = checkbox.data('id');
            let newStatus = checkbox.is(':checked') ? 1 : 2;

            $.ajax({
                url: "{{ route('masters.auto-finance.statustoggle') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: userId,
                    status_id: newStatus
                },
                success: function(response) {
                    // Update label text dynamically
                    // $('.status-label-' + userId).text(newStatus == 1 ? 'Active' : 'Deactive');
                    let label = $('.status-label-' + userId);
                    if (newStatus == 1) {
                        label.text('Active').css('color', 'green');
                    } else {
                        label.text('Deactive').css('color', 'red');
                    }
                },
                error: function(xhr) {
                    alert('Error updating status');
                    // Revert checkbox state if error occurs
                    checkbox.prop('checked', !checkbox.is(':checked'));
                }
            });
        });
    });
</script>
@endpush