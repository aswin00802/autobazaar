@extends('admin.layouts.app')
@section('title')
Service/ Gas Station Update
@endsection

@push('css')
@endpush

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="basic-layout-card-center">Gas Station Update</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('services.gas-station.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{$gasStation->id}}">
                        <div class="mb-6">
                            <label class="form-label" for="">Fuel Type </label>
                            <select class="form-select" name="fuel_id" id="fuel_id">
                                <option value="">View Fuel Type</option>
                                @if(!empty($fuels))
                                    @foreach ($fuels as $fuel)
                                    
                                        <option value="{{ $fuel->id }}" @selected((int) $gasStation->fuel_id === (int) $fuel->id)>{{ $fuel->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('fuel_id')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="">Gas Station Name</label>
                            <input type="text" value="{{ old('name',$gasStation->name) }}" id="name" class="form-control" placeholder="Enter Gas Station Name" name="name">
                            @error('name')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Location</label>
                            <input type="text" value="{{ old('location',$gasStation->location) }}" id="location" class="form-control" placeholder="Enter Gas Station Location" name="location">
                            @error('location')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Address</label>
                            <textarea class="form-control h-px-100" id="address" name="address" placeholder="Enter Gas Station Address" rows="3">{{ old('address',$gasStation->address) }}</textarea>
                            @error('address')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Map Link</label>
                            <input type="text" value="{{ old('map_link',$gasStation->map_link) }}" id="map_link" class="form-control" placeholder="Enter Gas Station Map Link" name="map_link">
                            @error('map_link')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('services.gas-station') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Update Gas Station
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