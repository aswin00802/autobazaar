@extends('admin.layouts.app')
@section('title')
Role Edit
@endsection

@push('css')
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Role Update</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('roles.update') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $role->id }}">
                        <div class="form-body">

                            <div class="mb-6">
                                <label for="" class="form-label">Role Name</label>
                                <input type="text" value="{{ old('role', $role->name) }}" id="role" class="form-control" placeholder="Enter Role Name" name="role">
                                @error('role')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('roles') }}" class="btn btn-warning mr-1">
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