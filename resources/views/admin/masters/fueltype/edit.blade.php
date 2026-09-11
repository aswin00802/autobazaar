@extends('admin.layouts.app')
@section('title')
Fuel Type Update
@endsection

@push('css')
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Fuel Type Update</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('masters.auto-fueltype.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{$fueltype->id}}">
                        <div class="mb-6">
                            <label class="form-label" for="">Fuel Type Name</label>
                            <input type="text" value="{{ old('name',$fueltype->name) }}" id="name" class="form-control" placeholder="Enter Fuel Type Name" name="name">
                            @error('name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Fuel Price</label>
                            <input type="text" value="{{ old('price',$fueltype->price) }}" id="price" class="form-control" placeholder="Enter Fuel Price" name="price">
                            @error('price')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('masters.auto-fueltype') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Update Fuel Type
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