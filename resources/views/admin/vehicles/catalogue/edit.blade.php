@extends('admin.layouts.app')
@section('title')
Vehicle Catalogue / Edit Model
@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            Edit Vehicle Model
            <span class="badge {{ $model->status_id == 1 ? 'bg-label-success' : 'bg-label-secondary' }} ms-2">{{ $model->status_id == 1 ? 'Live' : 'Draft' }}</span>
        </h4>
        <div>
            @if($model->status_id == 1 && $model->brand && Route::has('site.model'))
                <a href="{{ route('site.model', [Str::slug($model->brand->brand_name), $model->model_slug]) }}" target="_blank" class="btn btn-outline-info">
                    <i class="icon-base ri ri-external-link-line me-1"></i>View on site
                </a>
            @endif
            <a href="{{ route('vehicles.catalogue') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('vehicles.catalogue.update', $model->id) }}" enctype="multipart/form-data">
    @csrf

    @include('admin.vehicles.catalogue._form')

    <div class="mb-4">
        <button type="submit" class="btn btn-primary">Update Model</button>
        <a href="{{ route('vehicles.catalogue') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
