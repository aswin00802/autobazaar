@props(['vehicle', 'url'])

{{-- "Write a Review" — posts to preview.vehicles.review; shows after admin approval. --}}

<div {{ $attributes->merge(['class' => 'ab-card p-5']) }}
     x-data="leadForm({ url: @js($url), csrf: @js(csrf_token()), modal: false, defaults: { rating: 5 } })">

    <h3 class="flex items-center gap-2 text-base font-bold">
        <x-ui.icon name="star" :size="18" class="text-accent-500" />
        Write a Review
    </h3>
    <p class="mt-1 text-xs text-muted">Own a {{ $vehicle['name'] }}? Help other drivers decide.</p>

    <div x-show="state === 'success'" x-cloak class="mt-4 rounded-lg bg-brand-50 px-4 py-4 text-center" role="status">
        <x-ui.icon name="check-circle" :size="30" class="mx-auto text-brand-500" />
        <p class="mt-2 text-sm font-bold" x-text="message"></p>
        <button type="button" @click="reset()" class="mt-3 text-xs font-semibold text-brand-500 underline underline-offset-2">
            Write another
        </button>
    </div>

    <form x-show="state !== 'success'" @submit.prevent="submit()" novalidate class="mt-4 space-y-3">

        <div class="absolute -left-[9999px] top-0 h-0 w-0 overflow-hidden" aria-hidden="true">
            <label>Website <input type="text" name="website" x-model="fields.website" tabindex="-1" autocomplete="off"></label>
        </div>

        <p x-show="message && state !== 'success'" x-cloak
           :class="state === 'error' ? 'bg-red-50 text-danger' : 'bg-orange-50 text-warn'"
           class="rounded-lg px-3 py-2 text-xs font-semibold" role="alert" x-text="message"></p>

        {{-- Star picker --}}
        <fieldset>
            <legend class="ab-label">Your Rating <span class="text-danger">*</span></legend>
            <div class="flex items-center gap-1" role="radiogroup" aria-label="Rating out of 5">
                @for ($i = 1; $i <= 5; $i++)
                    <button type="button" role="radio" @click="fields.rating = {{ $i }}"
                            :aria-checked="fields.rating === {{ $i }}"
                            :class="fields.rating >= {{ $i }} ? 'text-accent-500' : 'text-line hover:text-accent-300'"
                            class="rounded p-0.5 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-400"
                            aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}">
                        <x-ui.icon name="star" :size="26" />
                    </button>
                @endfor
                <span class="ml-2 text-xs font-semibold text-muted"
                      x-text="['', 'Poor', 'Fair', 'Good', 'Very good', 'Excellent'][fields.rating] ?? ''"></span>
            </div>
            <p x-show="hasError('rating')" class="mt-1 text-[11px] text-danger" x-text="error('rating')"></p>
        </fieldset>

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="ab-label" :for="$id('rname')">Your Name <span class="text-danger">*</span></label>
                <input :id="$id('rname')" type="text" x-model="fields.name" class="ab-field" autocomplete="name"
                       :aria-invalid="hasError('name')" :class="hasError('name') && 'border-danger'" required>
                <p x-show="hasError('name')" class="mt-1 text-[11px] text-danger" x-text="error('name')"></p>
            </div>
            <div>
                <label class="ab-label" :for="$id('rcity')">City</label>
                <input :id="$id('rcity')" type="text" x-model="fields.city" class="ab-field" autocomplete="address-level2">
            </div>
        </div>

        <div>
            <label class="ab-label" :for="$id('rtitle')">Title</label>
            <input :id="$id('rtitle')" type="text" maxlength="120" x-model="fields.title" class="ab-field"
                   placeholder="Sum it up in a line">
        </div>

        <div>
            <label class="ab-label" :for="$id('rbody')">Your Review <span class="text-danger">*</span></label>
            <textarea :id="$id('rbody')" rows="4" x-model="fields.body" class="ab-field"
                      placeholder="Mileage, comfort, service experience, earnings…"
                      :aria-invalid="hasError('body')" :class="hasError('body') && 'border-danger'" required></textarea>
            <p x-show="hasError('body')" class="mt-1 text-[11px] text-danger" x-text="error('body')"></p>
        </div>

        <button type="submit" :disabled="state === 'saving'" class="ab-btn ab-btn-primary w-full disabled:opacity-60">
            <span x-show="state !== 'saving'">Submit Review</span>
            <span x-show="state === 'saving'" x-cloak>Submitting…</span>
        </button>
        <p class="text-[10px] text-muted">Reviews are checked by our team before they appear.</p>
    </form>
</div>
