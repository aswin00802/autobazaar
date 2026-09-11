@extends('admin.layouts.app')
@section('title')
Spare Parts / Products Add
@endsection
@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush
@section('content')
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
        <a href="{{route('spare-parts.product')}}" class="btn btn-secondary" style="float:right">Back</a>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <form action="{{ route('spare-parts.product.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <h5 class="card-header">Product Create</h5>
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-2">
                            <div class="mb-3">
                                <label>Category</label>
                                <select name="category_id" id="category_id" class="select2 form-select" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-2">
                            <div class="mb-3">
                                <label>Subcategory</label>
                                <select name="subcategory_id" id="subcategory_id" class="select2 form-select" required>
                                    <option value="">-- Select Subcategory --</option>
                                    @foreach($subcategories as $subcat)
                                        <option value="{{ $subcat->id }}">{{ $subcat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-8 col-md-8 col-lg-8 mb-2">
                            <div class="mb-3">
                                <label>Product Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                            <div class="mb-3">
                                <label>Product Image</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                        </div>
                        
                        
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea class="form-control h-px-100" id="description" name="description" placeholder="Enter Product Description" rows="3"></textarea>
                                <!-- <textarea name="description" class="form-control"></textarea> -->
                            </div>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                            <div class="mb-3">
                                <label>Select Brand & Models</label>
                                <!-- <select id="brandModelSelect" class="form-control" multiple>
                                    @foreach($brands as $brand)
                                        <optgroup label="{{ $brand->brand_name }}">
                                            @foreach($brand->autoModel as $model)
                                                @if($model->id != 0)
                                                <option value="{{ $brand->id }}_{{ $model->id }}">
                                                    {{ $model->model_name }}
                                                </option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select> -->
                                <select id="select2Multiple" class="select2 form-select" multiple>
                                    @foreach($brands as $brand)
                                        <optgroup label="{{ $brand->brand_name }}">
                                            @foreach($brand->autoModel as $model)
                                                @if($model->id != 0)
                                                    <option value="{{ $brand->id }}_{{ $model->id }}">{{ $model->model_name }}</option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                            <div id="brandModelPriceContainer"></div>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                            <button type="submit" class="btn btn-primary">Save Product</button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('admin/assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('admin/assets/js/forms-selects.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>

<script type="text/javascript">


$(document).ready(function(){

    $('#select2Multiple').on('change', function(){
        $('#brandModelPriceContainer').empty();

        let selected = $(this).val();

        if(selected){
            selected.forEach(function(item, index){
                let parts = item.split('_');
                let brandId = parts[0];
                let modelId = parts[1];

                let brandText = $('#select2Multiple option[value="'+item+'"]').parent().attr('label');
                let modelText = $('#select2Multiple option[value="'+item+'"]').text();

                let html = `
                <div class="card mb-2 p-2">
                    <h6>${brandText} - ${modelText}</h6>
                    
                    <input type="hidden" name="brand_models[${index}][brand_id]" value="${brandId}">
                    <input type="hidden" name="brand_models[${index}][model_id]" value="${modelId}">
                    
                    <div class="row">
                        <div class="col-md-4">
                            <label>Price</label>
                            <input type="number" step="0.01" name="brand_models[${index}][price]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Offer Price</label>
                            <input type="number" step="0.01" name="brand_models[${index}][offer_price]" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Images</label>
                            <input type="file" name="brand_models[${index}][images][]" class="form-control" multiple>
                        </div>
                    </div>
                </div>
                `;

                $('#brandModelPriceContainer').append(html);
            });
        }
    });

    $('#category_id').change(function(){
        let category_id = $('#category_id').val()
        $('#subcategory_id').empty()
        $('#subcategory_id').append('<option>Select Brand Model</option>')
        $.ajax({
            type:'POST',
            url:"{{route('spare-parts.product.get-subcategories')}}",
            data: {
                "_token": "{{ csrf_token() }}",
                category_id:category_id,
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
                   $.each(response.data, function(index, item) {
                        $('#subcategory_id').append('<option value="'+item.id+'">'+item.name+'</option>')
                    });
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
@endsection
