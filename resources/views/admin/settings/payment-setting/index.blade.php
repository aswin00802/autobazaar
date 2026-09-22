@extends('admin.layouts.app')
@section('title')
Payments Setting
@endsection

@include('admin.include.secret-toggle')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <h5 class="card-header">Payments Setting</h5>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('settings.payment-settings.update') }}" name="payment_settings" id="payment_settings" class="row g-5" method="post" novalidate>
                    @csrf

                    <div class="col-12">
                        <h6>1. Payment Gateways</h6>
                        <hr class="mt-0" />
                    </div>

                    @foreach ($gateways as $key => $gateway)
                        @php
                            $enabled = old('_token') ? old("{$key}_enabled") === 'on' : $gateway['enabled'];
                        @endphp
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" data-class="{{ $key }}" @if ($enabled) checked @endif id="{{ $key }}_enabled" name="{{ $key }}_enabled" value="on" />
                                <label class="form-check-label" for="{{ $key }}_enabled">Enable {{ $gateway['label'] }}</label>
                            </div>

                            @foreach ($gateway['fields'] as $field => $meta)
                                @php $input = "{$key}_{$field}"; @endphp
                                <div class="form-floating form-floating-outline mb-4 {{ $key }} d-none">
                                    <input type="{{ !empty($meta['secret']) ? 'password' : 'text' }}" @if (!empty($meta['secret'])) data-secret autocomplete="new-password" @endif
                                           class="form-control @error($input) is-invalid @enderror"
                                           name="{{ $input }}"
                                           id="{{ $input }}"
                                           value="{{ old($input, $gateway['values'][$field] ?? '') }}"
                                           placeholder="{{ $meta['label'] }}"
                                           data-required="{{ $meta['required'] ? '1' : '0' }}" />
                                    <label for="{{ $input }}">{{ $meta['label'] }}@if (!$meta['required']) <small class="text-muted">(optional)</small>@endif</label>
                                    @error($input)
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    <div class="col-12">
                        <hr class="mt-0" />
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function () {

        // Show/hide the gateway's fields and toggle their required state.
        $(".form-check-input").on("change", function () {
            let targetClass = $(this).data("class");
            let $fields = $("." + targetClass);
            if ($(this).is(":checked")) {
                $fields.removeClass('d-none').addClass('d-block');
                $fields.find('input[data-required="1"]').attr('required', true);
            } else {
                $fields.removeClass('d-block').addClass('d-none');
                $fields.find('input').removeAttr('required').removeClass('is-invalid');
                $fields.find('.invalid-feedback.js-error').remove();
            }
        });

        $(".form-check-input:checked").each(function () {
            $(this).trigger("change");
        });

        // Client-side check: enabled gateways must have their required fields filled.
        $("#payment_settings").on("submit", function (e) {
            let valid = true;
            $(this).find('.invalid-feedback.js-error').remove();

            $(this).find('input[required]').each(function () {
                let $input = $(this);
                if ($.trim($input.val()) === '') {
                    valid = false;
                    $input.addClass('is-invalid');
                    if (!$input.siblings('.invalid-feedback').length) {
                        $input.closest('.form-floating').append(
                            '<div class="invalid-feedback d-block js-error">' + $input.attr('placeholder') + ' is required.</div>'
                        );
                    }
                } else {
                    $input.removeClass('is-invalid');
                }
            });

            if (!valid) {
                e.preventDefault();
                $(this).find('.is-invalid').first().focus();
            }
        });

        $("#payment_settings").on("input", "input.is-invalid", function () {
            $(this).removeClass('is-invalid').siblings('.invalid-feedback.js-error').remove();
        });
    });
</script>
@endpush
