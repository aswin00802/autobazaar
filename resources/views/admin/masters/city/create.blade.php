@extends('admin.layouts.app')
@section('title')
City Add 
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
                <h4 class="card-title" id="basic-layout-card-center">City Create</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('masters.city.store') }}" method="post">
                        @csrf
                        <div class="mb-6">
                            <label class="form-label" for="">State Name</label>
                            <select id="state_id" name="state_id" class="select2 form-select" data-allow-clear="true">
                                <option value="">Select State</option>
                                @if(!empty($states))
                                    @foreach ($states as $state)
                                        <option value="{{ $state['id'] }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                            {{ $state['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('state_id')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="">State Name</label>
                            <input type="text" value="{{ old('name') }}" id="name" class="form-control" placeholder="Enter State Name" name="name">
                            @error('name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('masters.city') }}" class="btn btn-warning mr-1">
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
<script src="{{asset('admin/assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('admin/assets/js/forms-selects.js')}}"></script>
@endpush