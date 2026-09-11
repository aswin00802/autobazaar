@extends('admin.layouts.app')
@section('title')
Country Update 
@endsection

@push('css')
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Country Update</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('masters.country.update') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" id="id" value="{{ $country->id }}">
                        <div class="mb-6">
                            <label class="form-label" for="">Country Name</label>
                            <input type="text" value="{{ old('name',$country->name) }}" id="name" class="form-control" placeholder="Enter Country Name" name="name">
                            @error('name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('masters.country') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Update
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