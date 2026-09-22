    @extends('admin.layouts.app')
    @section('title')
    Service/Gas Station
    @endsection

    @push('css')
    <link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
    <link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
    <link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />


    @section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                @canany(['add_gas_station','upload_gas_station'])
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3 d-flex justify-content-end">
                    @can('upload_gas_station')
                    <a href="javascript:void(0)" class="btn create-new btn-success me-2" data-bs-target="#blukupload" data-bs-toggle="modal">
                        <span>
                            <span class="d-flex align-items-center">
                                <i class="icon-base ri ri-upload-2-line icon-18px me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Bluk Upload</span>
                            </span>
                        </span>
                    </a>
                    @endcan
                    @can('add_gas_station')
                    <a href="{{route('services.gas-station.create')}}" class="btn create-new btn-primary">
                        <span>
                            <span class="d-flex align-items-center">
                                <i class="icon-base ri ri-add-line icon-18px me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add New Gas Station</span>
                            </span>
                        </span>
                    </a>
                    @endcan
                </div>
                @endcanany
                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-datatable text-nowrap">
                            <table class="datatables-basic table table-bordered table-responsive">
                                <thead>
                                    <tr>
                                        <th>Sl.no</th>
                                        <th>Fuel Type</th>                      
                                        <th>Name</th>                           
                                        <th>Location</th>                           
                                        <th>Address</th>                          
                                        <th>Map Link</th>  
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($gasStations))
                                        @foreach($gasStations as $gas_station)
                                            <tr>
                                                <td>{{ $loop->iteration}}</td>
                                                <td>{{$gas_station->fuel->name ?? null}}</td>
                                                <td>{{$gas_station->name ?? null}}</td>
                                                <td>{{$gas_station->location ?? null}}</td>
                                                <td>{{$gas_station->address ?? null}}</td>
                                                <td>{{$gas_station->map_link ?? null}}</td>
                                                <td>
                                                    @can('edit_gas_station')
                                                        <a href="{{ route('services.gas-station.edit',$gas_station->id) }}" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="icon-base ri ri-edit-box-line icon-20px"></i></a>
                                                    @endcan
                                                    @can('delete_gas_station')
                                                        <a class="btn btn-sm btn-text-danger text-danger rounded-pill btn-icon item-delete" data-id="{{$gas_station->id}}"><i class="icon-base ri ri-delete-bin-2-line icon-20px"></i></a>
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

    <!-- model -->
    <div class="modal fade" id="blukupload" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <form method="post" name="bluk_upload_form" id="bluk_upload_form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel3">Gas Station Bluk Uploads</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4 mb-4">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="file" id="upload_file" name="upload_file" class="form-control" require>
                                    <label for="upload_file">Upload File</label>
                                </div>
                            </div>
                            <a href="{{ asset('uploads/gas_station.xlsx') }}">Upload Format</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button>
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
    <script src="{{ asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js') }}"></script>
    <script src="{{asset('admin/js/jquery.validate.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $(document).on("click", ".item-delete", function() {
                let el = $(this);
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to delete this Auto Gas Station!",
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
                            url:"{{route('services.gas-station.delete')}}",
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

            $("#bluk_upload_form").validate({
                errorClass: 'errors',
                rules: {
                    upload_file: "required",
                },
                submitHandler: function(form) {
                    var data = new FormData($("#bluk_upload_form")[0]);
                    let url = "{{ route('services.gas-station.upload') }}"
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: data,
                        processData: false,
                        contentType: false,

                        beforeSend: function () {
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

                        complete: function () {
                            $.unblockUI();
                        },

                        success: function (response) {

                            // Only 200 responses come here
                            if (response.status === true) {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then(() => location.reload());
                            }
                        },

                        error: function (xhr) {

                            $.unblockUI();

                            let title = "Error!";
                            let htmlMessage = "Something went wrong";

                            if (xhr.responseJSON) {

                                // 🔥 Missing master case
                                if (xhr.responseJSON.missing_masters) {

                                    title = "Data Missing";

                                    htmlMessage = xhr.responseJSON.message + "<br><br>";

                                    $.each(xhr.responseJSON.missing_masters, function (key, values) {
                                        htmlMessage += `<b>${key}</b> : ${values.join(', ')}<br>`;
                                    });

                                }
                                // 🔥 Validation error
                                else if (xhr.responseJSON.errors) {

                                    htmlMessage = Object.values(xhr.responseJSON.errors)
                                        .flat()
                                        .join('<br>');
                                }
                                // 🔥 Normal error message
                                else if (xhr.responseJSON.message) {

                                    htmlMessage = xhr.responseJSON.message;
                                }
                            }

                            Swal.fire({
                                title: title,
                                html: htmlMessage, // ❗ html use pannunga
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    });

                }
            });
        });
    </script>
    @endpush