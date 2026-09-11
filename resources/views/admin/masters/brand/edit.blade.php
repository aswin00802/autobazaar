@extends('admin.layouts.app')
@section('title')
Auto Brands Update
@endsection

@push('css')
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Auto Brand Update</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('masters.auto-brands.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="id" value="{{ $auto_brand->id }}">
                        <div class="mb-6">
                            <label class="form-label" for="">Brand Name</label>
                            <input type="text" value="{{ old('brand_name',$auto_brand->brand_name) }}" id="brand_name" class="form-control" placeholder="Enter Brand Name" name="brand_name">
                            @error('brand_name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Brand Image</label>
                            <input type="file" id="brand_image" class="form-control" name="brand_image">
                            @if($auto_brand->profile)                                      
                                <img src="{{ asset($auto_brand->profile) }}" width="300" class="img-fluid"/>
                            @else
                                <p>Image Not Available, Please Select One</p>                                       
                            @endif
                            @error('brand_image')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('masters.auto-brands') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Update Brand
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