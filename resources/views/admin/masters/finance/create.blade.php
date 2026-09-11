@extends('admin.layouts.app')
@section('title')
Finance Add
@endsection

@push('css')
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Finance Create</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('masters.auto-finance.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-6">
                            <label class="form-label" for="">Finance Name</label>
                            <input type="text" value="{{ old('finance_name') }}" id="finance_name" class="form-control" placeholder="Enter Finance Name" name="finance_name">
                            @error('finance_name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Finance Type</label>
                            <input type="text" value="{{ old('finance_type') }}" id="finance_type" class="form-control" placeholder="Enter Finance Type" name="finance_type">
                            @error('finance_type')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Location</label>
                            <input type="text" value="{{ old('location') }}" id="location" class="form-control" placeholder="Enter Finance Location" name="location">
                            @error('location')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Address</label>
                            <textarea class="form-control h-px-100" id="address" name="address" placeholder="Enter Finance Address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Contact No</label>
                            <input type="number" value="{{ old('contact') }}" id="contact" class="form-control" placeholder="Enter Finance Contact No" name="contact">
                            @error('contact')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="">Image</label>
                            <input type="file" id="finance_image" class="form-control" name="finance_image" accept="image/*">
                            @error('finance_image')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('masters.auto-finance') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Create Finance
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