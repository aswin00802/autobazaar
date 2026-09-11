@extends('admin.layouts.app')
@section('title')
Authorize Auto Seller Add
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Dealer Create</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('masters.auto-authorized-seller.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-6">
                            <label class="form-label" for="">Brand</label>
                            <select id="brand" name="brand" class="select2 form-select" data-allow-clear="true">
                                <option value="">Select Brand</option>
                                @if(!empty($brands))
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand['id'] }}" {{ old('brand') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand['brand_name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('brand')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="">Dealer Name</label>
                            <input type="text" value="{{ old('seller_name') }}" id="seller_name" class="form-control" placeholder="Enter Dealer Name" name="seller_name">
                            @error('seller_name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Dealer Type</label>
                            <input type="text" value="{{ old('seller_type') }}" id="seller_type" class="form-control" placeholder="Enter Dealer Type" name="seller_type">
                            @error('seller_type')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Location</label>
                            <input type="text" value="{{ old('location') }}" id="location" class="form-control" placeholder="Enter Seller Location" name="location">
                            @error('location')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Address</label>
                            <textarea class="form-control h-px-100" id="address" name="address" placeholder="Enter Seller Address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Contact No</label>
                            <input type="number" value="{{ old('contact') }}" id="contact" class="form-control" placeholder="Enter Seller Contact No" name="contact">
                            @error('contact')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="">Image</label>
                            <input type="file" id="seller_image" class="form-control" name="seller_image" accept="image/*">
                            @error('seller_image')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('masters.auto-authorized-seller') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Create Dealer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('admin/assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('admin/assets/js/forms-selects.js')}}"></script>
@endpush