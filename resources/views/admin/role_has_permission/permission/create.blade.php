@extends('admin.layouts.app')
@section('title')
Add Permission 
@endsection

@push('css')
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Permission Create</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('permissions.store') }}" method="post">
                        @csrf
                        <div class="mb-6">
                            <label for="" class="form-label">Permission Name</label>
                            <input type="text" value="{{ old('permission') }}" id="permission" class="form-control" placeholder="Enter Permission Name" name="permission">
                            @error('permission')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="" class="form-label">Permission Group</label>
                            <input type="text" value="{{ old('group') }}" id="group" class="form-control" placeholder="Enter Permission Group Name" name="group">
                            @error('group')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('permissions') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Save
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