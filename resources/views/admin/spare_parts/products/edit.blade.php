@extends('admin.layouts.app')
@section('title')
Spare Parts / Products Update
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
        <form action="{{ route('spare-parts.product.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$product->id}}">
            <div class="card">
                <h5 class="card-header">Product Update</h5>
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-12 col-sm-6 col-md-6 col-lg-6 mb-2">
                            <div class="mb-3">
                                <label>Category</label>
                                <select name="category_id" id="category_id" class="select2 form-select" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                                        <option value="{{ $subcat->id }}" {{ $product->subcategory_id == $subcat->id ? 'selected' : '' }}>{{ $subcat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-8 col-md-8 col-lg-8 mb-2">
                            <div class="mb-3">
                                <label>Product Name</label>
                                <input type="text" value="{{ old('name',$product->name) }}" name="name" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                            <div class="mb-3">
                                <label>Product Image</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                            @if(isset($product->image))
                                <img src="{{asset($product->image)}}" alt="image" class="img-fluid" style="width:150px;height:150px">
                            @endif
                        </div>
                        
                        
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea class="form-control h-px-100" id="description" name="description" placeholder="Enter Product Description" rows="3">{{ old('description',$product->description) }}</textarea>
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
                                            @php
                                                $exists = $product->productBrandModel->where('brand_id',$brand->id)->where('brand_model_id',$model->id)->first();
                                            @endphp
                                                @if($model->id != 0)
                                                    <option value="{{ $brand->id }}_{{ $model->id }}" {{ $exists ? 'selected' : '' }}>{{ $model->model_name }}</option>
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

    // Existing values from backend (brandModels relationship)
        let existingData = {!! json_encode(
            $product->productBrandModel->map(fn($bm) => [
                'brand_id'    => $bm->brand_id,
                'model_id'    => $bm->brand_model_id,
                'price'       => $bm->price,
                'offer_price' => $bm->offer_price,
                'images'      => $bm->images->pluck('image')->toArray(),
            ])->values()->toArray()
        ) !!};

        function renderSelected(selectedItems){
            $('#brandModelPriceContainer').empty();

            selectedItems.forEach(function(item, index){
                let parts = item.split('_');
                let brandId = parts[0];
                let modelId = parts[1];

                let brandText = $('#select2Multiple option[value="'+item+'"]').parent().attr('label');
                let modelText = $('#select2Multiple option[value="'+item+'"]').text();

                // check if already exists in product
                let matched = existingData.find(d => d.brand_id == brandId && d.model_id == modelId);

                let priceVal = matched ? matched.price : '';
                let offerPriceVal = matched ? matched.offer_price : '';
                let existingImages = matched && matched.images.length > 0 ? matched.images : [];

                let html = `
                <div class="card mb-2 p-2">
                    <h6>${brandText} - ${modelText}</h6>

                    <input type="hidden" name="brand_models[${index}][brand_id]" value="${brandId}">
                    <input type="hidden" name="brand_models[${index}][model_id]" value="${modelId}">

                    <div class="row">
                        <div class="col-md-3">
                            <label>Price</label>
                            <input type="number" step="0.01" 
                                name="brand_models[${index}][price]" 
                                value="${priceVal}" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Offer Price</label>
                            <input type="number" step="0.01" 
                                name="brand_models[${index}][offer_price]" 
                                value="${offerPriceVal}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Upload Images</label>
                            <input type="file" 
                                name="brand_models[${index}][images][]" 
                                class="form-control" multiple>
                `;

                // show already uploaded images
                if(existingImages.length > 0){
                    let baseUrl = "{{ asset('') }}/";
                    html += `<div class="mt-2 d-flex flex-wrap">`;
                    existingImages.forEach(img => {
                        html += `
                            <div class="me-2 mb-2">
                                <img src="${baseUrl}${img}" width="80" height="80" 
                                    class="border rounded">
                            </div>`;
                    });
                    html += `</div>`;
                }

                html += `
                        </div>
                    </div>
                </div>
                `;

                $('#brandModelPriceContainer').append(html);
            });
        }

        // On load -> render preselected
        renderSelected($('#select2Multiple').val() || []);

        // On change -> re-render
        $('#select2Multiple').on('change', function(){
            renderSelected($(this).val() || []);
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
