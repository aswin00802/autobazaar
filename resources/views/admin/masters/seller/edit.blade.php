@extends('admin.layouts.app')
@section('title')
Seller Update
@endsection

@push('css')
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Seller Update</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('masters.auto-seller.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{$seller->id}}">
                        <div class="mb-6">
                            <label class="form-label" for="">Seller Name</label>
                            <input type="text" value="{{ old('seller_name',$seller->seller_name) }}" id="seller_name" class="form-control" placeholder="Enter Seller Name" name="seller_name">
                            @error('seller_name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Seller Type</label>
                            <input type="text" value="{{ old('seller_type',$seller->seller_type) }}" id="seller_type" class="form-control" placeholder="Enter Seller Type" name="seller_type">
                            @error('seller_type')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Location</label>
                            <input type="text" value="{{ old('location',$seller->location) }}" id="location" class="form-control" placeholder="Enter Seller Location" name="location">
                            @error('location')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Address</label>
                            <textarea class="form-control h-px-100" id="address" name="address" placeholder="Enter Seller Address" rows="3">{{ old('address',$seller->address) }}</textarea>
                            @error('address')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Contact No</label>
                            <input type="number" value="{{ old('contact',$seller->contact) }}" id="contact" class="form-control" placeholder="Enter Seller Contact No" name="contact">
                            @error('contact')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('masters.auto-seller') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Update Seller
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