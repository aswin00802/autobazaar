@extends('admin.layouts.app')
@section('title')
State Update 
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
                <h4 class="card-title" id="basic-layout-card-center">State Update</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('masters.state.update') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" id="id" value="{{ $state->id }}">
                        <div class="mb-6">
                            <label class="form-label" for="">Country Name</label>
                            <select id="country_id" name="country_id" class="select2 form-select" data-allow-clear="true">
                                <option value="">Select Country</option>
                                @if(!empty($countrys))
                                    @foreach ($countrys as $country)
                                        <option value="{{ $country['id'] }}"  {{ old('country_id', $state->country_id ?? '') == $country['id'] ? 'selected' : '' }}>
                                            {{ $country['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('country_id')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="">State Name</label>
                            <input type="text" value="{{ old('name',$state->name) }}" id="name" class="form-control" placeholder="Enter State Name" name="name">
                            @error('name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('masters.state') }}" class="btn btn-warning mr-1">
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
<script src="{{asset('admin/assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('admin/assets/js/forms-selects.js')}}"></script>
@endpush