@extends('admin.layouts.app')
@section('title')
Auto Brand Model Add
@endsection

@push('css')
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Auto Brand Model Create</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('masters.auto-brands.model.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-6">
                            <label class="form-label" for="">Brand Name</label>
                            <select class="form-select" name="brand_id" id="brand_id">
                                <option value="">View Brands</option>
                                @if(!empty($auto_brands))
                                    @foreach ($auto_brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('brand_id')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="">Model Name</label>
                            <input type="text" value="{{ old('model_name') }}" id="model_name" class="form-control" placeholder="Enter Brand Model Name" name="model_name">
                            @error('model_name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-actions center">
                            <a href="{{ route('masters.auto-brands.model') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Create Brand Model
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