@extends('admin.layouts.app')
@section('title')
Service/RTO Request
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <!-- <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3 d-flex justify-content-end">
                <a href="" class="btn create-new btn-primary">
                    <span>
                        <span class="d-flex align-items-center">
                            <i class="icon-base ri ri-add-line icon-18px me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Add New Gas Station</span>
                        </span>
                    </span>
                </a>
            </div> -->
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-datatable text-nowrap">
                        <table class="datatables-basic table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>Sl.no</th>
                                    <th>User Name</th>
                                    <th>Phone Number</th>
                                    <th>RTO Number</th>
                                    <th>Service Type</th>
                                    <th>Status</th>    
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($rtos))
                                    @foreach($rtos as $rto)
                                        <tr>
                                            <td>{{ $loop->iteration}}</td>
                                            <td>{{$rto->user->name ?? null}}</td>
                                            <td>{{$rto->user->phone_number ?? null}}</td>
                                            <td>{{$rto->rto_number ?? null}}</td>
                                            <td>{{$rto->service_type ?? null}}</td>
                                            <td>
                                                @if($rto->status == 'requested')
                                                    <span class="badge rounded-pill text-bg-danger">Requested</span>
                                                @elseif($rto->status == 'pending')
                                                    <span class="badge rounded-pill text-bg-danger">Pending</span>
                                                @elseif($rto->status == 'contacted')
                                                    <span class="badge rounded-pill text-bg-secondary">Contacted</span>
                                                @elseif($rto->status == 'follow_up')
                                                    <span class="badge rounded-pill text-bg-success">Follow Up</span>
                                                @elseif($rto->status == 'approved')
                                                    <span class="badge rounded-pill text-bg-success">Approved</span>
                                                @elseif($rto->status == 'rejected')
                                                    <span class="badge rounded-pill text-bg-danger">Rejected</span>
                                                @else
                                                    <td><span class="badge rounded-pill text-bg-danger">{{$rto->status ?? null}}</span></td>
                                                @endif
                                                
                                            </td>
                                            <td>
                                                @can('rto_update')
                                                    <button type="button" class="btn btn-primary status_update_btn" data-status="{{$rto->status}}" data-id ="{{$rto->id}}"><i class="icon-base ri ri-file-edit-line icon-20px"></i></button>
                                                @endcan
                                            </td>
                                            <!-- <td>
                                                <a href="{{ route('services.gas-station.edit',$rto->id) }}" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="icon-base ri ri-edit-box-line icon-20px"></i></a>
                                                <a class="btn btn-sm btn-text-danger text-danger rounded-pill btn-icon item-delete" data-id="{{$rto->id}}"><i class="icon-base ri ri-delete-bin-2-line icon-20px"></i></a>
                                            </td> -->
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

<div class="modal fade auto_enquiry_status" id="auto_enquiry_status_update_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" name="auto_enquiry_status_update" id="auto_enquiry_status_update">
                @csrf
                <div class="modal-header">
                    <h5>Auto RTO Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="table_id" name="table_id">
                    <small class="fw-medium">RTO Status</small>
                    <select name="auto_enquiry_status" id="auto_enquiry_status" class="form-select form-select-sm">
                        <option value="requested">Requested</option>
                        <option value="pending">Pending</option>
                        <option value="contacted">Contacted</option>
                        <option value="follow_up">Follow-up</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info">Submit</button>
                </div>
            </form>
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
                text: "You won't be able to delete this Auto RTO Request!",
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
                        url:"",
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

        //update enquiry status
        $(document).on('click','.status_update_btn' ,function() {
            // let id = $(this).data('id');
            $('#table_id').val('')
            let id = $(this).data('id')
            let status = $(this).data('status')
            $('#table_id').val(id)
            $('#auto_enquiry_status').val(status)
            $('.auto_enquiry_status').modal('show');
        });

        $("#auto_enquiry_status_update").on("submit", function (e) {
            e.preventDefault(); // 🔴 Prevent normal page reload
            let id      = $('#table_id').val()
            let status  = $('#auto_enquiry_status').val()
            $.ajax({
                type:'POST',
                url:"{{route('services.rto.update-status')}}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id:id,
                    status:status,
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
                            title: 'RTO Status!',
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
        });
    });
</script>
@endpush