{{-- Shared by create and edit. $model is null when creating. --}}
@php
    $model = $model ?? null;
    $rel = fn ($name) => $model ? $model->{$name}->map(fn ($r) => $r->toArray())->values()->all() : [];
    $rows = [];
    foreach (['variants', 'specifications', 'scores', 'features', 'offers', 'prices', 'stock'] as $t) {
        $rows[$t] = old($t) !== null ? array_values(array_filter((array) old($t), 'is_array')) : $rel($t);
    }
    $rows['documents'] = [];

    $defaultVariant = old('variant_default');
    if ($defaultVariant === null && $model) {
        $defaultVariant = $model->variants->search(fn ($x) => $x->is_default);
        $defaultVariant = $defaultVariant === false ? null : $defaultVariant;
    }
    $defaultPrice = old('price_default');
    if ($defaultPrice === null && $model) {
        $defaultPrice = $model->prices->search(fn ($x) => $x->is_default);
        $defaultPrice = $defaultPrice === false ? ($model->prices->count() ? 0 : null) : $defaultPrice;
    }

    $suitable = old('suitable_lines', $model ? $model->suitability->where('type', 'suitable')->pluck('label')->implode("\n") : '');
    $notRecommended = old('not_recommended_lines', $model ? $model->suitability->where('type', 'not_recommended')->pluck('label')->implode("\n") : '');
    $useCase = old('use_case', $model && is_array($model->use_case) ? implode(', ', $model->use_case) : '');
    $selectedBrand = old('auto_brand_id', $model->auto_brand_id ?? '');
    $variantOptions = $model ? $model->variants->map(fn ($x) => ['id' => $x->id, 'name' => $x->name])->values() : collect();
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- ------------------------------------------------------------ Basic --}}
<div class="card mb-4">
    <h5 class="card-header">Basic Details</h5>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Brand <span class="text-danger">*</span></label>
                <select name="auto_brand_id" id="auto_brand_id" class="form-select" required>
                    <option value="">Select Brand</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" @selected((string) $selectedBrand === (string) $b->id)>{{ $b->brand_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Master Model <small class="text-muted">(auto_models)</small></label>
                <select name="auto_model_id" id="auto_model_id" class="form-select" data-selected="{{ old('auto_model_id', $model->auto_model_id ?? '') }}">
                    <option value="">Select Model</option>
                    @foreach($masterModels->where('brand_id', $selectedBrand) as $mm)
                        <option value="{{ $mm->id }}" @selected((string) old('auto_model_id', $model->auto_model_id ?? '') === (string) $mm->id)>{{ $mm->model_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" required maxlength="255" value="{{ old('name', $model->name ?? '') }}" placeholder="TVS King Deluxe">
            </div>

            <div class="col-md-4">
                <label class="form-label">Slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" id="slug" class="form-control" required maxlength="120" value="{{ old('slug', $model->slug ?? '') }}" placeholder="tvs-king-deluxe">
                <small class="text-muted">Unique across the catalogue. Auto-filled from the name.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label">Model Slug <span class="text-danger">*</span></label>
                <input type="text" name="model_slug" id="model_slug" class="form-control" required maxlength="80" value="{{ old('model_slug', $model->model_slug ?? '') }}" placeholder="king-deluxe">
                <small class="text-muted">URL: /new-autos/&lt;brand&gt;/&lt;model slug&gt;</small>
            </div>
            <div class="col-md-4">
                <label class="form-label">Badge</label>
                <input type="text" name="badge" class="form-control" maxlength="40" value="{{ old('badge', $model->badge ?? '') }}" placeholder="Most Popular / EV / New">
            </div>

            <div class="col-md-8">
                <label class="form-label">Tagline</label>
                <input type="text" name="tagline" class="form-control" maxlength="255" value="{{ old('tagline', $model->tagline ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Best For <small class="text-muted">(compare table)</small></label>
                <input type="text" name="best_for" class="form-control" maxlength="60" value="{{ old('best_for', $model->best_for ?? '') }}" placeholder="City passenger routes">
            </div>

            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $model->description ?? '') }}</textarea>
            </div>

            <div class="col-md-2">
                <label class="form-label">Segment <span class="text-danger">*</span></label>
                <select name="segment" class="form-select" required>
                    @foreach($segments as $k => $label)
                        <option value="{{ $k }}" @selected(old('segment', $model->segment ?? 'passenger') === $k)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Seating <span class="text-danger">*</span></label>
                <input type="number" name="seating" class="form-control" min="1" max="20" required value="{{ old('seating', $model->seating ?? 3) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Use Case <small class="text-muted">(comma separated)</small></label>
                <input type="text" name="use_case" class="form-control" value="{{ $useCase }}" placeholder="commercial, high-mileage">
            </div>
            <div class="col-md-2">
                <label class="form-label">Maintenance</label>
                <select name="maintenance_level" class="form-select">
                    @foreach($maintenance as $m)
                        <option value="{{ $m }}" @selected(old('maintenance_level', $model->maintenance_level ?? 'Low') === $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $model->sort_order ?? 0) }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status_id" class="form-select" required>
                    <option value="1" @selected((string) old('status_id', $model->status_id ?? 0) === '1')>Live</option>
                    <option value="0" @selected((string) old('status_id', $model->status_id ?? 0) === '0')>Draft</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="is_popular" value="1" id="is_popular" @checked(old('is_popular', $model->is_popular ?? 0))>
                    <label class="form-check-label" for="is_popular">Popular</label>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Meta Title</label>
                <input type="text" name="meta_title" class="form-control" maxlength="255" value="{{ old('meta_title', $model->meta_title ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Meta Description</label>
                <input type="text" name="meta_description" class="form-control" maxlength="255" value="{{ old('meta_description', $model->meta_description ?? '') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Hero Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                @if($model && $model->image)
                    <img src="{{ asset($model->image) }}" class="img-fluid mt-2 rounded border" style="max-height:120px" alt="">
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ------------------------------------------------- Warranty & showroom --}}
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <h5 class="card-header">Warranty &amp; Service</h5>
            <div class="card-body">
                <div class="row g-3">
                    @foreach([
                        'warranty_years' => 'Warranty (years)', 'warranty_km' => 'Warranty (km)',
                        'engine_warranty_years' => 'Engine warranty (years)', 'engine_warranty_km' => 'Engine warranty (km)',
                        'service_interval_km' => 'Service interval (km)', 'service_interval_months' => 'Service interval (months)',
                        'free_services' => 'Free services (count)', 'free_services_km' => 'Free services up to (km)',
                    ] as $f => $label)
                    <div class="col-md-3 col-6">
                        <label class="form-label">{{ $label }}</label>
                        <input type="number" min="0" name="{{ $f }}" class="form-control" value="{{ old($f, $model->{$f} ?? '') }}">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <h5 class="card-header">Showroom</h5>
            <div class="card-body">
                <label class="form-label">Dealer <small class="text-muted">(authorized sellers)</small></label>
                <select name="dealer_id" class="form-select">
                    <option value="">-- None --</option>
                    @foreach($dealers as $d)
                        <option value="{{ $d->id }}" @selected((string) old('dealer_id', $model->dealer_id ?? '') === (string) $d->id)>{{ $d->dealer_name }}{{ $d->location ? ' - ' . $d->location : '' }}</option>
                    @endforeach
                </select>
                @if($dealers->isEmpty())
                    <small class="text-muted">No active authorized sellers yet.</small>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ---------------------------------------------------------- Variants --}}
<div class="card mb-4">
    <h5 class="card-header d-flex justify-content-between align-items-center">
        <span>Variants</span>
        <button type="button" class="btn btn-sm btn-outline-primary" data-add="variants"><i class="icon-base ri ri-add-line me-1"></i>Add Variant</button>
    </h5>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:50px" title="Default">Def.</th><th>Name *</th><th>Fuel</th><th>Fuel Type (price)</th><th>Icon</th>
                        <th>Engine</th><th>Power</th><th>Mileage</th><th>Payload kg</th><th>Transmission</th><th>Ex-showroom</th><th></th>
                    </tr>
                </thead>
                <tbody data-repeat="variants" data-next="{{ count($rows['variants']) }}">
                    @foreach($rows['variants'] as $i => $row)
                        @include('admin.vehicles.catalogue._row', ['type' => 'variants', 'i' => $i, 'row' => $row])
                    @endforeach
                </tbody>
            </table>
        </div>
        <template id="tpl-variants">@include('admin.vehicles.catalogue._row', ['type' => 'variants', 'i' => '__IDX__', 'row' => []])</template>
    </div>
</div>

{{-- ---------------------------------------------------- Specifications --}}
<div class="row">
    <div class="col-lg-7">
        <div class="card mb-4">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span>Specifications</span>
                <button type="button" class="btn btn-sm btn-outline-primary" data-add="specifications"><i class="icon-base ri ri-add-line me-1"></i>Add Spec</button>
            </h5>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0 align-middle">
                        <thead><tr><th style="width:22%">Group</th><th>Label *</th><th>Value *</th><th style="width:60px" title="Show in Key Specifications">Key</th><th style="width:44px"></th></tr></thead>
                        <tbody data-repeat="specifications" data-next="{{ count($rows['specifications']) }}">
                            @foreach($rows['specifications'] as $i => $row)
                                @include('admin.vehicles.catalogue._row', ['type' => 'specifications', 'i' => $i, 'row' => $row])
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <template id="tpl-specifications">@include('admin.vehicles.catalogue._row', ['type' => 'specifications', 'i' => '__IDX__', 'row' => []])</template>
            </div>
        </div>
    </div>

    {{-- ------------------------------------------------------------ Scores --}}
    <div class="col-lg-5">
        <div class="card mb-4">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span>AutoBazaar Score</span>
                <button type="button" class="btn btn-sm btn-outline-primary" data-add="scores"><i class="icon-base ri ri-add-line me-1"></i>Add Score</button>
            </h5>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-4">
                        <label class="form-label">Overall (0-10)</label>
                        <input type="number" step="0.1" min="0" max="10" name="score_overall" class="form-control" value="{{ old('score_overall', $model->score_overall ?? 0) }}">
                    </div>
                    <div class="col-8">
                        <label class="form-label">Rank Note</label>
                        <input type="text" name="score_rank_note" class="form-control" maxlength="80" value="{{ old('score_rank_note', $model->score_rank_note ?? '') }}" placeholder="#1 in passenger segment">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Summary</label>
                        <input type="text" name="score_summary" class="form-control" maxlength="255" value="{{ old('score_summary', $model->score_summary ?? '') }}">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0 align-middle">
                        <thead><tr><th>Label *</th><th style="width:90px">Score</th><th style="width:110px">Tone</th><th style="width:44px"></th></tr></thead>
                        <tbody data-repeat="scores" data-next="{{ count($rows['scores']) }}">
                            @foreach($rows['scores'] as $i => $row)
                                @include('admin.vehicles.catalogue._row', ['type' => 'scores', 'i' => $i, 'row' => $row])
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <template id="tpl-scores">@include('admin.vehicles.catalogue._row', ['type' => 'scores', 'i' => '__IDX__', 'row' => []])</template>
            </div>
        </div>
    </div>
</div>

{{-- ------------------------------------------------ Features & suitability --}}
<div class="row">
    <div class="col-lg-5">
        <div class="card mb-4">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span>Features</span>
                <button type="button" class="btn btn-sm btn-outline-primary" data-add="features"><i class="icon-base ri ri-add-line me-1"></i>Add Feature</button>
            </h5>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0 align-middle">
                        <thead><tr><th style="width:120px">Icon</th><th>Label *</th><th style="width:44px"></th></tr></thead>
                        <tbody data-repeat="features" data-next="{{ count($rows['features']) }}">
                            @foreach($rows['features'] as $i => $row)
                                @include('admin.vehicles.catalogue._row', ['type' => 'features', 'i' => $i, 'row' => $row])
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <template id="tpl-features">@include('admin.vehicles.catalogue._row', ['type' => 'features', 'i' => '__IDX__', 'row' => []])</template>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card mb-4">
            <h5 class="card-header">Suitability <small class="text-muted fw-normal">one point per line</small></h5>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-success">Suitable for</label>
                        <textarea name="suitable_lines" class="form-control" rows="6" placeholder="Daily city passenger service&#10;Long-distance high-mileage routes">{{ $suitable }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-danger">Not recommended for</label>
                        <textarea name="not_recommended_lines" class="form-control" rows="6" placeholder="Heavy cargo loads">{{ $notRecommended }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ------------------------------------------------------------ Offers --}}
<div class="card mb-4">
    <h5 class="card-header d-flex justify-content-between align-items-center">
        <span>Offers</span>
        <button type="button" class="btn btn-sm btn-outline-primary" data-add="offers"><i class="icon-base ri ri-add-line me-1"></i>Add Offer</button>
    </h5>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0 align-middle">
                <thead><tr><th>Title *</th><th style="width:150px">Worth (₹)</th><th style="width:170px">Valid From</th><th style="width:170px">Valid To</th><th style="width:70px">Active</th><th style="width:44px"></th></tr></thead>
                <tbody data-repeat="offers" data-next="{{ count($rows['offers']) }}">
                    @foreach($rows['offers'] as $i => $row)
                        @include('admin.vehicles.catalogue._row', ['type' => 'offers', 'i' => $i, 'row' => $row])
                    @endforeach
                </tbody>
            </table>
        </div>
        <template id="tpl-offers">@include('admin.vehicles.catalogue._row', ['type' => 'offers', 'i' => '__IDX__', 'row' => []])</template>
    </div>
</div>

{{-- ------------------------------------------------------------ Prices --}}
<div class="card mb-4">
    <h5 class="card-header d-flex justify-content-between align-items-center">
        <span>On-road Prices <small class="text-muted fw-normal">on-road = ex-showroom + RTO + insurance + registration + other + accessories</small></span>
        <button type="button" class="btn btn-sm btn-outline-primary" data-add="prices"><i class="icon-base ri ri-add-line me-1"></i>Add Location</button>
    </h5>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:50px" title="Default">Def.</th><th>Variant</th><th>Location *</th><th>State</th>
                        <th>Ex-showroom</th><th>RTO</th><th>Insurance</th><th>Registration</th><th>Other</th><th>Accessories</th><th class="text-end">On-road</th><th></th>
                    </tr>
                </thead>
                <tbody data-repeat="prices" data-next="{{ count($rows['prices']) }}">
                    @foreach($rows['prices'] as $i => $row)
                        @include('admin.vehicles.catalogue._row', ['type' => 'prices', 'i' => $i, 'row' => $row])
                    @endforeach
                </tbody>
            </table>
        </div>
        <template id="tpl-prices">@include('admin.vehicles.catalogue._row', ['type' => 'prices', 'i' => '__IDX__', 'row' => []])</template>
    </div>
</div>

{{-- ------------------------------------------------- Images & documents --}}
<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <h5 class="card-header">Gallery Images</h5>
            <div class="card-body">
                <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                <small class="text-muted">New images are added to the gallery. Existing ones are removed with the delete button.</small>
                @if($model && $model->images->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 mt-3" id="existing-images">
                        @foreach($model->images as $img)
                            <div class="position-relative border rounded p-1" data-image-id="{{ $img->id }}">
                                <img src="{{ asset($img->image) }}" style="width:110px;height:80px;object-fit:cover" class="rounded" alt="">
                                <button type="button" class="btn btn-xs btn-danger position-absolute top-0 end-0 m-1 px-1 py-0 delete-image" data-id="{{ $img->id }}" title="Delete">&times;</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card mb-4">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span>Documents</span>
                <button type="button" class="btn btn-sm btn-outline-primary" data-add="documents"><i class="icon-base ri ri-add-line me-1"></i>Add Document</button>
            </h5>
            <div class="card-body p-0">
                @if($model && $model->documents->isNotEmpty())
                    <ul class="list-group list-group-flush" id="existing-documents">
                        @foreach($model->documents as $doc)
                            <li class="list-group-item d-flex justify-content-between align-items-center" data-document-id="{{ $doc->id }}">
                                <span>
                                    <span class="badge bg-label-info me-2">{{ $docTypes[$doc->type] ?? $doc->type }}</span>
                                    <a href="{{ asset($doc->file) }}" target="_blank">{{ $doc->title }}</a>
                                </span>
                                <button type="button" class="btn btn-sm btn-text-danger btn-icon delete-document" data-id="{{ $doc->id }}"><i class="icon-base ri ri-delete-bin-line"></i></button>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0 align-middle">
                        <thead><tr><th style="width:160px">Type</th><th>Title</th><th>File (pdf/jpg/png)</th><th style="width:44px"></th></tr></thead>
                        <tbody data-repeat="documents" data-next="0"></tbody>
                    </table>
                </div>
                <template id="tpl-documents">@include('admin.vehicles.catalogue._row', ['type' => 'documents', 'i' => '__IDX__', 'row' => []])</template>
            </div>
        </div>
    </div>
</div>

{{-- ------------------------------------------------------------- Stock --}}
<div class="card mb-4">
    <h5 class="card-header d-flex justify-content-between align-items-center">
        <span>Stock &amp; Delivery</span>
        <button type="button" class="btn btn-sm btn-outline-primary" data-add="stock"><i class="icon-base ri ri-add-line me-1"></i>Add Stock Row</button>
    </h5>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0 align-middle">
                <thead><tr><th>Variant</th><th>Dealer</th><th>Colour</th><th style="width:100px">Qty</th><th style="width:130px">Delivery min (days)</th><th style="width:130px">Delivery max (days)</th><th style="width:44px"></th></tr></thead>
                <tbody data-repeat="stock" data-next="{{ count($rows['stock']) }}">
                    @foreach($rows['stock'] as $i => $row)
                        @include('admin.vehicles.catalogue._row', ['type' => 'stock', 'i' => $i, 'row' => $row])
                    @endforeach
                </tbody>
            </table>
        </div>
        <template id="tpl-stock">@include('admin.vehicles.catalogue._row', ['type' => 'stock', 'i' => '__IDX__', 'row' => []])</template>
    </div>
</div>

@push('scripts')
<script src="{{ asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {
    var slugify = function (s) {
        return String(s || '').toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    };

    // Slug from name (only while the user has not edited the slug by hand)
    var slugTouched = $('#slug').val() !== '';
    $('#slug').on('input', function () { slugTouched = $(this).val() !== ''; });
    $('#name').on('input', function () {
        if (!slugTouched) { $('#slug').val(slugify($(this).val())); }
        if ($('#model_slug').val() === '') {
            var brand = $('#auto_brand_id option:selected').text();
            var s = slugify($(this).val());
            var b = slugify(brand);
            if (b && s.indexOf(b + '-') === 0) { s = s.substring(b.length + 1); }
            $('#model_slug').attr('placeholder', s || 'king-deluxe');
        }
    });
    $('#model_slug').on('blur', function () {
        if ($(this).val() === '' && $(this).attr('placeholder')) { $(this).val($(this).attr('placeholder')); }
        $(this).val(slugify($(this).val()));
    });
    $('#slug').on('blur', function () { $(this).val(slugify($(this).val())); });

    // Brand -> master models (same endpoint as the New Auto screen)
    $('#auto_brand_id').on('change', function () {
        var brand_id = $(this).val();
        var $m = $('#auto_model_id');
        $m.empty().append('<option value="">Select Model</option>');
        if (!brand_id) { return; }
        $.ajax({
            type: 'POST',
            url: "{{ route('brand.get-model') }}",
            data: { "_token": "{{ csrf_token() }}", brand_id: brand_id },
            success: function (response) {
                if (response.success == true) {
                    $.each(response.data, function (index, item) {
                        $m.append('<option value="' + item.id + '">' + item.model_name + '</option>');
                    });
                    if ($m.data('selected')) { $m.val(String($m.data('selected'))); }
                }
            }
        });
    });

    // Repeatable rows
    $(document).on('click', '[data-add]', function () {
        var type = $(this).data('add');
        var $body = $('[data-repeat="' + type + '"]');
        var next = parseInt($body.attr('data-next') || '0', 10);
        var html = $('#tpl-' + type).html().replace(/__IDX__/g, next);
        $body.append(html);
        $body.attr('data-next', next + 1);
        refreshVariantSelects();
        recalcOnRoad();
    });
    $(document).on('click', '.repeat-remove', function () {
        $(this).closest('.repeat-row').remove();
        refreshVariantSelects();
    });

    // Variant options in price / stock rows: existing rows by id, new rows by "new:<index>"
    function variantList() {
        var list = [];
        $('[data-repeat="variants"] .repeat-row').each(function () {
            var id = $(this).find('.variant-id').val();
            var name = $(this).find('.variant-name').val() || ('Variant ' + (list.length + 1));
            var idx = ($(this).find('input[name="variant_default"]').val());
            list.push({ value: id ? String(id) : ('new:' + idx), label: name });
        });
        return list;
    }
    function refreshVariantSelects() {
        var list = variantList();
        $('.variant-select').each(function () {
            var $s = $(this);
            var current = $s.val() || String($s.data('selected') || '');
            var first = $s.find('option').first().clone();
            $s.empty().append(first);
            $.each(list, function (i, v) { $s.append($('<option>').val(v.value).text(v.label)); });
            $s.val(current);
            if ($s.val() === null) { $s.val(''); }
            $s.data('selected', '');
        });
    }
    $(document).on('input', '.variant-name', function () { refreshVariantSelects(); });

    // On-road total per price row
    function recalcOnRoad() {
        $('.price-row').each(function () {
            var total = 0;
            $(this).find('.price-part').each(function () { total += parseFloat($(this).val()) || 0; });
            $(this).find('.on-road-cell').text(total.toLocaleString('en-IN', { maximumFractionDigits: 0 }));
        });
    }
    $(document).on('input', '.price-part', recalcOnRoad);

    refreshVariantSelects();
    recalcOnRoad();

    // Existing image / document delete (ajax)
    function ajaxDelete(url, id, $el) {
        if (!confirm('Delete this file?')) { return; }
        $.post(url, { "_token": "{{ csrf_token() }}", id: id }, function (r) {
            if (r.success) { $el.remove(); } else { alert(r.message || 'Could not delete'); }
        }).fail(function () { alert('Could not delete'); });
    }
    $(document).on('click', '.delete-image', function () {
        ajaxDelete("{{ route('vehicles.catalogue.image-delete') }}", $(this).data('id'), $(this).closest('[data-image-id]'));
    });
    $(document).on('click', '.delete-document', function () {
        ajaxDelete("{{ route('vehicles.catalogue.document-delete') }}", $(this).data('id'), $(this).closest('[data-document-id]'));
    });
});
</script>
@endpush
