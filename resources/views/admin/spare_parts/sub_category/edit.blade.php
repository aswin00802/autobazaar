@extends('admin.layouts.app')
@section('title')
Spare Parts / Sub Categories Update
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
                <h4 class="card-title" id="basic-layout-card-center">Sub Categories Update</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('spare-parts.sub-categories.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $subcategories->id }}">
                        <div class="mb-6">
                            <label class="form-label" for="">Select Categories</label>
                            <select id="category_id" name="category_id" class="select2 form-select" data-allow-clear="true">
                                <option value="">Select Categories</option>
                                @if(!empty($categories))
                                    @foreach($categories as $categorie)
                                        <option value="{{$categorie->id}}" @selected($subcategories->category_id === $categorie->id)>{{$categorie->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('category_id')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="">Sub Categories Name</label>
                            <input type="text" value="{{ old('name',$subcategories->name) }}" id="name" class="form-control" placeholder="Enter Categories Name" name="name">
                            @error('name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Sub Categories Description</label>
                            <textarea class="form-control h-px-100" id="description" name="description" placeholder="Enter Description" rows="3"> {{ old('description',$subcategories->description) }} </textarea>
                            @error('description')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Sub Categories Image</label>
                            <input type="file" id="image" class="form-control" name="image">
                            @error('image')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                            @if(isset($subcategories->image))
                                <img src="{{asset($subcategories->image)}}" alt="image" class="img-fluid" style="width:150px;height:150px">
                            @endif
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('spare-parts.sub-categories') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Update SubCategories
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