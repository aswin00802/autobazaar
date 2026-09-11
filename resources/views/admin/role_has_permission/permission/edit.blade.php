@extends('admin.layouts.app')
@section('title')
Edit Permission   
@endsection

@push('css')
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Role Update</h4>
                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                    <ul class="list-inline mb-0">
                        <li><a data-action="collapse"><i class="feather icon-minus"></i></a></li>
                        <li><a data-action="reload"><i class="feather icon-rotate-cw"></i></a></li>
                        <li><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                        <li><a data-action="close"><i class="feather icon-x"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('permissions.update') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $permission->id }}">
                        <div class="mb-6">
                            <label for="" class="form-label">Permission Name</label>
                            <input type="text" id="permission" class="form-control" value="{{ old('permission', $permission->name) }}" placeholder="Enter Permission Name" name="permission">
                            @error('permission')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="" class="form-label">Permission Group</label>
                            <input type="text" id="group" class="form-control" value="{{ old('group', $permission->group_name) }}" placeholder="Enter Permission Group Name" name="group">
                            @error('group')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-actions center">
                            <a href="{{ route('permissions') }}" class="btn btn-warning mr-1">
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