@extends('admin.layouts.app')
@section('title')
Assign Role has Permission 
@endsection

@push('css')
@endpush

@section('content')
<section>
    <form action="{{ route('role_has_permission.update') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $role->id }}">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                @error('permissions')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                <div class="form-group">
                    <label for="" class="form-label">Role Name</label>
                    <input type="text" id="role" class="form-control" value="{{ $role->name }}" readonly required placeholder="Enter Role Name" name="role">
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="font-weight-bold">
                            Administrator Access <i class="icon-base bx bx-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Allows a full access to the system" data-bs-original-title="Allows a full access to the system"></i>
                        </h4>
                        <div class="card-content">
                            <div class="row">
                                @foreach ($permission_groups as $permission_group)
                                    <div class="col-12 col-sm-3 col-md-3 col-lg-3 mb-3">
                                        <table class="table table-bordered ">
                                            <thead>
                                                <tr>
                                                    <th>{{ $permission_group[0]['group_name'] }}</th>
                                                    <th style="text-align:center;">
                                                        <input type="checkbox" id="all-permission" data-group="group-{{ $loop->index }}" class="form-check-input all-permission">
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Process</th>
                                                    <th style="text-align:center;">Access</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($permission_group as $permission)
                                                    <tr>
                                                        <td>{{ ucwords(str_replace('_', ' ', $permission->name)) }}</td>
                                                        <td style="text-align:center;">
                                                            <input class="form-check-input permission-checkbox group-{{ $loop->parent->index }}" name="permissions[]" type="checkbox" value="{{ $permission->id }}" @if ($role->hasPermissionTo($permission->id)) checked @endif id="permission-{{ $permission->id }}" />
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{route('role_has_permission')}}" class="btn btn-dark" style="color:white">Back</a>
                        <button class="btn btn-primary" type="submit">
                            <i data-feather="save" class="me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("change", ".all-permission", function () {
            let groupClass = $(this).data("group");
            $("." + groupClass).prop("checked", this.checked);
        });

        $(document).on("change", ".permission-checkbox", function () {
            let groupClass = $(this).attr("class").match(/group-\d+/)[0]; // group-x nu eduthukanum
            let allInGroup = $("." + groupClass).length;
            let checkedInGroup = $("." + groupClass + ":checked").length;

            if (allInGroup === checkedInGroup) {
                $(`.all-permission[data-group='${groupClass}']`).prop("checked", true);
            } else {
                $(`.all-permission[data-group='${groupClass}']`).prop("checked", false);
            }
        });
    });
</script>
@endpush