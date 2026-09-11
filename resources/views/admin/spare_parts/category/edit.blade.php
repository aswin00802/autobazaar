@extends('admin.layouts.app')
@section('title')
Spare Parts / Categories Update
@endsection

@push('css')
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Categories Update</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('spare-parts.categories.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{$categories->id}}">
                        <div class="mb-6">
                            <label class="form-label" for="">Categories Name</label>
                            <input type="text" value="{{ old('name',$categories->name) }}" id="name" class="form-control" placeholder="Enter Categories Name" name="name">
                            @error('name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Categories Description</label>
                            <textarea class="form-control h-px-100" id="description" name="description" placeholder="Enter Description" rows="3"> {{ old('description',$categories->description) }} </textarea>
                            @error('description')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Categories Image</label>
                            <input type="file" id="image" class="form-control" name="image">
                            
                            @error('image')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                            @if(isset($categories->image))
                                <img src="{{asset($categories->image)}}" alt="image" class="img-fluid" style="width:150px;height:150px">
                            @endif
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('spare-parts.categories') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Update Categories
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