@extends('admin.layouts.app')
@section('title')
Vehicle Catalogue / Add Model
@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Add Vehicle Model</h4>
        <a href="{{ route('vehicles.catalogue') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<form method="POST" action="{{ route('vehicles.catalogue.store') }}" enctype="multipart/form-data">
    @csrf

    @include('admin.vehicles.catalogue._form')

    <div class="mb-4">
        <button type="submit" class="btn btn-primary">Save Model</button>
        <a href="{{ route('vehicles.catalogue') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
