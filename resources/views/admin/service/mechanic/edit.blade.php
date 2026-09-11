@extends('admin.layouts.app')
@section('title')
Service/ Mechanic Update
@endsection

@push('css')
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Mechanic Update</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('services.mechanic.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{$mechanic->id}}">
                        <div class="mb-6">
                            <label class="form-label" for="">Shop Name</label>
                            <input type="text" value="{{ old('shop_name',$mechanic->shop_name) }}" id="shop_name" class="form-control" placeholder="Enter Mechanic Shop Name" name="shop_name">
                            @error('shop_name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Shop Location</label>
                            <input type="text" value="{{ old('location',$mechanic->location) }}" id="location" class="form-control" placeholder="Enter Shop Location" name="location">
                            @error('location')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Shop Address</label>
                            <textarea class="form-control h-px-100" id="address" name="address" placeholder="Enter Shop Address" rows="3">{{ old('address',$mechanic->address) }}</textarea>
                            @error('address')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Shop Contact No</label>
                            <input type="number" value="{{ old('contact',$mechanic->contact) }}" id="contact" class="form-control" placeholder="Enter Contact NO" name="contact">
                            @error('contact')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Shop Alternate Contact</label>
                            <input type="number" value="{{ old('alternate',$mechanic->alternate) }}" id="alternate" class="form-control" placeholder="Enter Alternate Contact NO" name="alternate">
                            @error('alternate')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Shop Category</label>
                            <input type="text" value="{{ old('category',$mechanic->category) }}" id="category" class="form-control" placeholder="Enter Shop Category (ex : tinker, mechanic)" name="category">
                            @error('category')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('services.mechanic') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Update Mechanic
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