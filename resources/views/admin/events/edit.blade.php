@extends('admin.layouts.app')
@section('title')
Events Update
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
                <h4 class="card-title" id="basic-layout-card-center">Event Update</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('events.update') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="id" value="{{ $event->id }}">
                        <div class="mb-6">
                            <label class="form-label" for="">Event Title</label>
                            <input type="text" value="{{ old('title',$event->title) }}" id="title" class="form-control" placeholder="Enter Event Title" name="title">
                            @error('title')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Location</label>
                            <select id="location" name="location" class="select2 form-select" data-allow-clear="true">
                                <option value="">Select Location</option>
                                @if(!empty($areas))
                                    @foreach ($areas as $area)
                                        <option value="{{ $area['id'] }}" {{ old('location',$event->location_id) == $area->id ? 'selected' : '' }}>
                                            {{ $area['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('brand')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Description</label>
                            <textarea class="form-control h-px-100" id="description" name="description" placeholder="Enter Description" rows="3">{{ old('description',$event->description) }}</textarea>
                            @error('description')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="">Image</label>
                            <input type="file" id="event_image" class="form-control" name="event_image" accept="image/*">
                            @if($event->image)                                      
                                <img src="{{ asset($event->image) }}" width="300" class="img-fluid mt-3"/>
                            @else
                                <p>Image Not Available, Please Select One</p>                                       
                            @endif
                            @error('event_image')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Map Link</label>
                            <input type="text" id="map_link" value="{{ old('map_link',$event->map_link) }}" class="form-control" name="map_link" placeholder="Enter Map Link">
                            @error('map_link')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Audio File</label>
                            <input type="file" id="audio_file" class="form-control" name="audio_file" accept=".mp3,audio/mpeg">
                            @if(!empty($event->audio_file))
                                <div class="mb-3 mt-3">
                                    <label class="form-label">Audio Preview</label>
                                    <audio controls>
                                        <source src="{{ asset($event->audio_file) }}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                </div>
                            @endif
                            @error('audio_file')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="form-label" for="">Event View End Date</label>
                            <input type="date" id="end_date" value="{{ old('end_date',$event->end_date) }}" class="form-control" name="end_date">
                            @error('end_date')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions center">
                            <a href="{{ route('events') }}" class="btn btn-warning mr-1">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Update Event
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