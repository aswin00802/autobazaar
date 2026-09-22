@extends('admin.layouts.app')
@section('title')
Emergency List
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-datatable text-nowrap">
                <table class="datatables-fixed1 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Sl.no</th>
                            <th>Name</th> 
                            <th>Contact</th>                           
                            <th>Location</th>                           
                            <th>Emergency Type</th>                 
                            <th>Description</th>                    
                            <th>Requested Date</th>                    
                            <th>Status</th>                     
                            <th>Action</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($emergency))
                            @foreach($emergency as $data)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{$data->user->name ?? 'Not Available'}}</td>
                                    <td>{{$data->user->phone_number ?? 'Not Available'}}</td>
                                    <td>{{$data->user->location ?? 'Not Available'}}</td>
                                    <td>{{$data->emergency_type ?? 'Not Available'}}</td>
                                    <td>{{$data->description ?? 'Not Available'}}</td>
                                    <td>{{$data->created_at ?? 'Not Available'}}</td>
                                    
                                    <td>
                                        @if($data->status == 'requested')
                                            <span class="badge rounded-pill text-bg-danger">Requested</span>
                                        @elseif($data->status == 'acknowledged')
                                            <span class="badge rounded-pill text-bg-success">Acknowledged</span>
                                        @elseif($data->status == 'in_progress')
                                            <span class="badge rounded-pill text-bg-success">In Progress</span>
                                        @elseif($data->status == 'dispatched')
                                            <span class="badge rounded-pill text-bg-success">Dispatched</span>
                                        @elseif($data->status == 'resolved')
                                            <span class="badge rounded-pill text-bg-success">Resolved</span>
                                        @elseif($data->status == 'closed')
                                            <span class="badge rounded-pill text-bg-danger">Closed</span>
                                        @elseif($data->status == 'cancelled')
                                            <span class="badge rounded-pill text-bg-danger">Cancelled</span>
                                        @else
                                            <td>'Not Available'</td>
                                        @endif
                                    </td>
                                    <td>
                                        @can('auto_emergency_update')
                                            <button type="button" class="btn btn-primary status_update_btn" data-id ="{{$data->id}}"><i class="icon-base ri ri-file-edit-line icon-20px"></i></button>
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



<!-- auto status update model -->
<div class="modal fade auto_enquiry_status" id="auto_enquiry_status_update_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" name="auto_enquiry_status_update" id="auto_enquiry_status_update">
                @csrf
                <div class="modal-header">
                    <h5>Auto Enquiry Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="table_id" name="table_id">
                    <small class="fw-medium">Enquiry Status</small>
                    <select name="auto_enquiry_status" id="auto_enquiry_status" class="form-select form-select-sm">
                        <option value="requested">Requested</option>
                        <option value="acknowledged">Acknowledged</option>
                        <option value="in_progress">In Progress</option>
                        <option value="dispatched">Dispatched</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                        <option value="cancelled">Cancelled</option>
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
<!-- end auto status update model -->

@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="{{ asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        //update enquiry status
        $(document).on('click','.status_update_btn' ,function() {
            // let id = $(this).data('id');
            $('#table_id').val('')
            let id = $(this).data('id')
            $('#table_id').val(id)
            $('.auto_enquiry_status').modal('show');
        });

        $("#auto_enquiry_status_update").on("submit", function (e) {
            e.preventDefault(); // 🔴 Prevent normal page reload
            let id      = $('#table_id').val()
            let status  = $('#auto_enquiry_status').val()
            $.ajax({
                type:'POST',
                url:"{{route('emergency.request.update')}}",
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
                            title: 'Emergency Status!',
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