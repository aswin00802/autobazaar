@extends('admin.layouts.app')
@section('title')
Auto Brands Add
@endsection

@push('css')
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Auto Brand Create</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <div class="mb-6">
                        <label class="form-label" for="">View Existing Brands For Referrence</label>
                        <select class="form-select">
                            <option value="">View Brands</option>
                            @if(!empty($auto_brands))
                                @foreach ($auto_brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <form class="form" action="{{ route('masters.auto-brands.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-6">
                            <label class="form-label" for="">Brand Name</label>
                            <input type="text" value="{{ old('brand_name') }}" id="brand_name" class="form-control" placeholder="Enter Brand Name" name="brand_name">
                            @error('brand_name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Brand Image</label>
                            <input type="file" id="brand_image" class="form-control" placeholder="Enter Brand Image" name="brand_image">
                            @error('brand_image')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('masters.auto-brands') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Create Brand
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
@endpush