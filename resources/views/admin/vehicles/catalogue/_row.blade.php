{{-- One repeatable row. $type picks the block, $i is the array index (or __IDX__ in the
     <template>), $row is the existing/old values. Used by both the rendered rows and
     the clone template so the markup lives in one place. --}}
@php
    $row = $row ?? [];
    $v = fn ($k, $d = '') => e((string) ($row[$k] ?? $d));
    $sel = fn ($k, $val, $d = '') => ((string) ($row[$k] ?? $d) === (string) $val) ? 'selected' : '';
    $date = fn ($k) => isset($row[$k]) ? substr((string) $row[$k], 0, 10) : '';
@endphp

@switch($type)

@case('variants')
<tr class="repeat-row">
    <td class="text-center align-middle">
        <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $v('id') }}" class="variant-id">
        <input type="radio" name="variant_default" value="{{ $i }}" class="form-check-input" title="Default variant" @checked(($defaultVariant ?? null) !== null && (string) ($defaultVariant ?? '') === (string) $i)>
    </td>
    <td><input type="text" name="variants[{{ $i }}][name]" class="form-control form-control-sm variant-name" value="{{ $v('name') }}" placeholder="Petrol / CNG" required></td>
    <td>
        <select name="variants[{{ $i }}][fuel_key]" class="form-select form-select-sm">
            @foreach($fuelKeys as $k => $label)<option value="{{ $k }}" {{ $sel('fuel_key', $k, 'petrol') }}>{{ $label }}</option>@endforeach
        </select>
    </td>
    <td>
        <select name="variants[{{ $i }}][fuel_type_id]" class="form-select form-select-sm">
            <option value="">--</option>
            @foreach($fuels as $f)<option value="{{ $f->id }}" {{ $sel('fuel_type_id', $f->id) }}>{{ $f->name }}</option>@endforeach
        </select>
    </td>
    <td>
        <select name="variants[{{ $i }}][icon]" class="form-select form-select-sm">
            @foreach($variantIcons as $k => $label)<option value="{{ $k }}" {{ $sel('icon', $k, 'fuel') }}>{{ $label }}</option>@endforeach
        </select>
    </td>
    <td><input type="text" name="variants[{{ $i }}][engine_cc]" class="form-control form-control-sm" value="{{ $v('engine_cc') }}" placeholder="236 cc"></td>
    <td><input type="text" name="variants[{{ $i }}][power]" class="form-control form-control-sm" value="{{ $v('power') }}" placeholder="10.3 bhp"></td>
    <td>
        <div class="input-group input-group-sm">
            <input type="number" step="0.1" min="0" name="variants[{{ $i }}][mileage]" class="form-control" value="{{ $v('mileage') }}">
            <input type="text" name="variants[{{ $i }}][mileage_unit]" class="form-control" style="max-width:90px" value="{{ $v('mileage_unit', 'km/litre') }}">
        </div>
    </td>
    <td><input type="number" name="variants[{{ $i }}][payload_kg]" class="form-control form-control-sm" value="{{ $v('payload_kg') }}"></td>
    <td><input type="text" name="variants[{{ $i }}][transmission]" class="form-control form-control-sm" value="{{ $v('transmission') }}" placeholder="4-speed manual"></td>
    <td><input type="number" step="0.01" min="0" name="variants[{{ $i }}][ex_showroom_price]" class="form-control form-control-sm" value="{{ $v('ex_showroom_price', 0) }}"></td>
    <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-text-danger btn-icon repeat-remove"><i class="icon-base ri ri-delete-bin-line"></i></button></td>
</tr>
@break

@case('specifications')
<tr class="repeat-row">
    <td><input type="text" name="specifications[{{ $i }}][spec_group]" class="form-control form-control-sm" value="{{ $v('spec_group', 'General') }}" placeholder="Engine"></td>
    <td><input type="text" name="specifications[{{ $i }}][label]" class="form-control form-control-sm" value="{{ $v('label') }}" placeholder="Displacement" required></td>
    <td><input type="text" name="specifications[{{ $i }}][value]" class="form-control form-control-sm" value="{{ $v('value') }}" placeholder="236 cc" required></td>
    <td class="text-center align-middle"><input type="checkbox" name="specifications[{{ $i }}][is_key]" value="1" class="form-check-input" @checked(!empty($row['is_key']))></td>
    <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-text-danger btn-icon repeat-remove"><i class="icon-base ri ri-delete-bin-line"></i></button></td>
</tr>
@break

@case('scores')
<tr class="repeat-row">
    <td><input type="text" name="scores[{{ $i }}][label]" class="form-control form-control-sm" value="{{ $v('label') }}" placeholder="Mileage" required></td>
    <td><input type="number" step="0.1" min="0" max="10" name="scores[{{ $i }}][score]" class="form-control form-control-sm" value="{{ $v('score', 0) }}"></td>
    <td>
        <select name="scores[{{ $i }}][tone]" class="form-select form-select-sm">
            @foreach($scoreTones as $k => $label)<option value="{{ $k }}" {{ $sel('tone', $k, 'brand') }}>{{ $label }}</option>@endforeach
        </select>
    </td>
    <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-text-danger btn-icon repeat-remove"><i class="icon-base ri ri-delete-bin-line"></i></button></td>
</tr>
@break

@case('features')
<tr class="repeat-row">
    <td><input type="text" name="features[{{ $i }}][icon]" class="form-control form-control-sm" value="{{ $v('icon', 'check') }}" placeholder="check"></td>
    <td><input type="text" name="features[{{ $i }}][label]" class="form-control form-control-sm" value="{{ $v('label') }}" placeholder="Low running cost" required></td>
    <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-text-danger btn-icon repeat-remove"><i class="icon-base ri ri-delete-bin-line"></i></button></td>
</tr>
@break

@case('offers')
<tr class="repeat-row">
    <td><input type="text" name="offers[{{ $i }}][title]" class="form-control form-control-sm" value="{{ $v('title') }}" placeholder="Festive cash discount" required></td>
    <td><input type="number" step="0.01" min="0" name="offers[{{ $i }}][value_amount]" class="form-control form-control-sm" value="{{ $v('value_amount') }}"></td>
    <td><input type="date" name="offers[{{ $i }}][valid_from]" class="form-control form-control-sm" value="{{ $date('valid_from') }}"></td>
    <td><input type="date" name="offers[{{ $i }}][valid_to]" class="form-control form-control-sm" value="{{ $date('valid_to') }}"></td>
    <td class="text-center align-middle"><input type="checkbox" name="offers[{{ $i }}][status_id]" value="1" class="form-check-input" @checked(!array_key_exists('status_id', $row) || !empty($row['status_id']))></td>
    <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-text-danger btn-icon repeat-remove"><i class="icon-base ri ri-delete-bin-line"></i></button></td>
</tr>
@break

@case('prices')
<tr class="repeat-row price-row">
    <td class="text-center align-middle"><input type="radio" name="price_default" value="{{ $i }}" class="form-check-input" title="Default price" @checked(($defaultPrice ?? null) !== null && (string) ($defaultPrice ?? '') === (string) $i)></td>
    <td>
        <select name="prices[{{ $i }}][vehicle_variant_id]" class="form-select form-select-sm variant-select" data-selected="{{ $v('vehicle_variant_id') }}">
            <option value="">All / default</option>
        </select>
    </td>
    <td><input type="text" name="prices[{{ $i }}][location]" class="form-control form-control-sm" value="{{ $v('location', 'Chennai') }}" required></td>
    <td><input type="text" name="prices[{{ $i }}][state]" class="form-control form-control-sm" value="{{ $v('state', 'Tamil Nadu') }}"></td>
    @foreach(['ex_showroom', 'rto', 'insurance', 'registration', 'other', 'accessories'] as $pf)
    <td><input type="number" step="0.01" min="0" name="prices[{{ $i }}][{{ $pf }}]" class="form-control form-control-sm price-part" value="{{ $v($pf, 0) }}"></td>
    @endforeach
    <td class="align-middle text-end fw-semibold on-road-cell">0</td>
    <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-text-danger btn-icon repeat-remove"><i class="icon-base ri ri-delete-bin-line"></i></button></td>
</tr>
@break

@case('documents')
<tr class="repeat-row">
    <td>
        <select name="documents[{{ $i }}][type]" class="form-select form-select-sm">
            @foreach($docTypes as $k => $label)<option value="{{ $k }}" {{ $sel('type', $k, 'brochure') }}>{{ $label }}</option>@endforeach
        </select>
    </td>
    <td><input type="text" name="documents[{{ $i }}][title]" class="form-control form-control-sm" value="{{ $v('title') }}" placeholder="Brochure 2026"></td>
    <td><input type="file" name="documents[{{ $i }}][file]" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png"></td>
    <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-text-danger btn-icon repeat-remove"><i class="icon-base ri ri-delete-bin-line"></i></button></td>
</tr>
@break

@case('stock')
<tr class="repeat-row">
    <td>
        <select name="stock[{{ $i }}][vehicle_variant_id]" class="form-select form-select-sm variant-select" data-selected="{{ $v('vehicle_variant_id') }}">
            <option value="">Any</option>
        </select>
    </td>
    <td>
        <select name="stock[{{ $i }}][dealer_id]" class="form-select form-select-sm">
            <option value="">--</option>
            @foreach($dealers as $d)<option value="{{ $d->id }}" {{ $sel('dealer_id', $d->id) }}>{{ $d->dealer_name }}{{ $d->location ? ' - ' . $d->location : '' }}</option>@endforeach
        </select>
    </td>
    <td><input type="text" name="stock[{{ $i }}][colour]" class="form-control form-control-sm" value="{{ $v('colour') }}" placeholder="Yellow"></td>
    <td><input type="number" min="0" name="stock[{{ $i }}][qty]" class="form-control form-control-sm" value="{{ $v('qty', 0) }}"></td>
    <td><input type="number" min="0" max="127" name="stock[{{ $i }}][delivery_days_min]" class="form-control form-control-sm" value="{{ $v('delivery_days_min', 1) }}"></td>
    <td><input type="number" min="0" max="127" name="stock[{{ $i }}][delivery_days_max]" class="form-control form-control-sm" value="{{ $v('delivery_days_max', 3) }}"></td>
    <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-text-danger btn-icon repeat-remove"><i class="icon-base ri ri-delete-bin-line"></i></button></td>
</tr>
@break

@endswitch
