{{-- Loan terms (finance_lender_rates) shown on the vehicle page "Finance Options" card.
     $rate is null for a new partner; defaults match the table defaults. --}}
@php $rate = $rate ?? null; @endphp
<hr class="my-4">
<h5 class="mb-3">Loan Terms <small class="text-muted fw-normal">(vehicle page finance card)</small></h5>
<div class="row g-3 mb-4">
    <div class="col-md-4 col-6">
        <label class="form-label">Interest Rate (% p.a.)</label>
        <input type="number" step="0.01" min="0" max="99.99" name="interest_rate" class="form-control" value="{{ old('interest_rate', $rate->interest_rate ?? 11.50) }}">
        @error('interest_rate')<span style="color:red;">{{ $message }}</span>@enderror
    </div>
    <div class="col-md-4 col-6">
        <label class="form-label">Min Tenure (months)</label>
        <input type="number" min="1" max="120" name="min_tenure_months" class="form-control" value="{{ old('min_tenure_months', $rate->min_tenure_months ?? 12) }}">
        @error('min_tenure_months')<span style="color:red;">{{ $message }}</span>@enderror
    </div>
    <div class="col-md-4 col-6">
        <label class="form-label">Max Tenure (months)</label>
        <input type="number" min="1" max="120" name="max_tenure_months" class="form-control" value="{{ old('max_tenure_months', $rate->max_tenure_months ?? 60) }}">
        @error('max_tenure_months')<span style="color:red;">{{ $message }}</span>@enderror
    </div>
    <div class="col-md-4 col-6">
        <label class="form-label">Max Loan % (with CIBIL)</label>
        <input type="number" min="0" max="100" name="max_loan_pct" class="form-control" value="{{ old('max_loan_pct', $rate->max_loan_pct ?? 85) }}">
        @error('max_loan_pct')<span style="color:red;">{{ $message }}</span>@enderror
    </div>
    <div class="col-md-4 col-6">
        <label class="form-label">Max Loan % (no CIBIL)</label>
        <input type="number" min="0" max="100" name="max_loan_pct_no_cibil" class="form-control" value="{{ old('max_loan_pct_no_cibil', $rate->max_loan_pct_no_cibil ?? 70) }}">
        @error('max_loan_pct_no_cibil')<span style="color:red;">{{ $message }}</span>@enderror
    </div>
    <div class="col-md-4 col-6">
        <label class="form-label">Processing Fee (%)</label>
        <input type="number" step="0.01" min="0" max="99.99" name="processing_fee_pct" class="form-control" value="{{ old('processing_fee_pct', $rate->processing_fee_pct ?? 0) }}">
        @error('processing_fee_pct')<span style="color:red;">{{ $message }}</span>@enderror
    </div>
    <div class="col-12">
        <label class="form-label">Documents Required <small class="text-muted">(one per line)</small></label>
        <textarea name="documents" class="form-control" rows="5" placeholder="Aadhaar card&#10;PAN card&#10;Driving licence">{{ old('documents', $rate->documents ?? '') }}</textarea>
        @error('documents')<span style="color:red;">{{ $message }}</span>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $rate->sort_order ?? 0) }}">
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked(old('is_featured', $rate->is_featured ?? 0))>
            <label class="form-check-label" for="is_featured">Featured partner</label>
        </div>
    </div>
</div>
