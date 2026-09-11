@extends('admin.layouts.app')
@section('title')
Role
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="row">
            @can('add_roles')
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3 d-flex justify-content-end">
                <a href="{{route('roles.create')}}" class="btn create-new btn-primary">
                    <span>
                        <span class="d-flex align-items-center">
                            <i class="icon-base ri ri-add-line icon-18px me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Add New Role</span>
                        </span>
                    </span>
                </a>
            </div>
            @endcan
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-datatable text-nowrap">
                        <table class="datatables-basic table table-bordered table-responsive">
                            <thead>
                                <tr>
                                <th>id</th>
                                <th>Name</th>
                                <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($roles))
                                    @foreach($roles as $role)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$role->name}}</td>
                                            <td>
                                                @can('edit_roles')
                                                    @if ($role['name'] != 'Super Admin')
                                                        <a href="{{ route('roles.edit',$role->id) }}" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="icon-base ri ri-edit-box-line icon-20px"></i></a>
                                                    @endif
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

 <script src="{{asset('admin/js/custom-datatable.js')}}"></script>

@endpush