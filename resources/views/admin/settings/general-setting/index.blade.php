@extends('admin.layouts.app')
@section('title')
Business General Settings
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/cropperjs/cropper.css') }}">
@endpush

@include('admin.include.secret-toggle')

@section('content')
<div class="row">
<!-- FormValidation -->
    <div class="col-12">
        <div class="card">
            <h5 class="card-header">General Settings</h5>
            <div class="card-body">
                <form action="{{ route('settings.general-settings.update') }}" enctype="multipart/form-data" name="general_settings" id="general_settings" class="row g-5" method="post">
                    @csrf
                    <!-- Basic Details -->
                    <div class="col-12">
                        <h6>1. Basic Details</h6>
                        <hr class="mt-0" />
                    </div>
                    <div class="col-md-6 form-control-validation">
                        <div class="form-floating form-floating-outline">
                            <input type="text" value="{{ old('business_name', getSetting('business_name')) }}" id="business_name" class="form-control" placeholder="Enter Business name" name="business_name" />
                            <label for="business_name">Business Name</label>
                        </div>
                    </div>
                    <div class="col-md-6 form-control-validation">
                        <div class="form-floating form-floating-outline">
                            <input type="email" value="{{ old('business_email', getSetting('business_email') ?: \App\Support\SiteData::currentValue('business_email')) }}" id="business_email" class="form-control @error('business_email') is-invalid @enderror" placeholder="Enter Business Email" name="business_email" />
                            <label for="business_email">Business Email</label>
                            @error('business_email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6 form-control-validation">
                        <div class="form-floating form-floating-outline">
                            <input type="text" inputmode="numeric" value="{{ old('business_mobile', getSetting('business_mobile') ?: \App\Support\SiteData::currentValue('business_mobile')) }}" id="business_mobile" class="form-control @error('business_mobile') is-invalid @enderror" placeholder="Enter Business Mobile" name="business_mobile" />
                            <label for="business_mobile">Business Mobile</label>
                            @error('business_mobile')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div class="form-text">10 digits. Shown on the website, and used for tap-to-call and the WhatsApp button.</div>
                        </div>
                    </div>
                    <div class="col-md-6 form-control-validation">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control h-px-100" id="business_address" name="business_address" placeholder="Enter Business Address" rows="3">{{ old('business_address', getSetting('business_address') ?: \App\Support\SiteData::currentValue('business_address')) }}</textarea>
                            <label for="business_address">Business Address</label>
                        </div>
                    </div>
                    <div class="col-md-6 form-control-validation">
                        <div class="form-floating form-floating-outline">
                            <select id="language" name="language" class="select2 form-select" data-allow-clear="true">
                                <option value="">Select</option>
                                
                            </select>
                            <label for="language">Language</label>
                        </div>
                    </div>
                    <div class="col-md-6 form-control-validation">
                        <div class="form-floating form-floating-outline">
                            <select id="time_zone" name="time_zone" class="select2 form-select" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($timezones as $timezone)
                                    <option value="{{ $timezone }}" {{ old('time_zone', getSetting('time_zone')) == $timezone ? 'selected' : '' }}>
                                        {{ $timezone }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="time_zone">Time Zone</label>
                        </div>
                    </div>
                    <div class="col-md-6 form-control-validation">
                        <div class="form-floating form-floating-outline">
                            <input type="text" value="{{ old('footer_text', getSetting('footer_text')) }}" id="footer_text" class="form-control" placeholder="Enter Footer Text" name="footer_text" />
                            <label for="footer_text">Footer Text</label>
                        </div>
                    </div>
                    <div class="col-md-6 form-control-validation">
                        
                    </div>

                    <!-- Image Info -->
                    <div class="col-12">
                        <h6 class="mt-2">2. Image Info</h6>
                        <hr class="mt-0" />
                    </div>
                    <div class="col-md-4 form-control-validation">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="file" id="admin_logo" name="admin_logo" />
                            <label for="admin_logo">Admin Logo</label>
                            @if(getSetting('admin_logo'))
                                <div class="position-relative d-inline-block preview-container">
                                    <img src="{{ asset(getSetting('admin_logo')) }}" alt="Admin Logo" class="img-thumbnail" style="max-height: 80px;">

                                    <button type="button"
                                            class="btn-close position-absolute text-danger top-0 end-0 remove-file"
                                            aria-label="Close"
                                            data-key="admin_logo"
                                            style="background-color: white; border-radius: 50%;">
                                    </button>
                                </div>
                            @endif
                        </div>
                        
                    </div>
                    <div class="col-md-4 form-control-validation">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="file" id="web_logo" name="web_logo" />
                            <!-- <input type="file" class="form-control" id="fileInput_web_logo" accept="image/*">
                            <input type="hidden" name="web_logo" id="web_logo_croppedImage">
                            <label for="web_logo">Web App Logo</label>
                            <div id="cropModal" style="display:none; margin-top:20px;">
                                <img id="imagePreview_web_logo" style="max-width:100%; max-height:400px;">
                                <br><br>
                                <button type="button" id="cropBtn">Crop Image</button>
                            </div> -->
                            @if(getSetting('web_logo'))
                                <div class="position-relative d-inline-block preview-container">
                                    <img src="{{ asset(getSetting('web_logo')) }}" alt="Admin Logo" class="img-thumbnail" style="max-height: 80px;">

                                    <button type="button"
                                            class="btn-close position-absolute text-danger top-0 end-0 remove-file"
                                            aria-label="Close"
                                            data-key="web_logo"
                                            style="background-color: white; border-radius: 50%;">
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 form-control-validation">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="file" id="fav_icon" name="fav_icon" />
                            <label for="fav_icon">Fav Icon</label>
                            @if(getSetting('fav_icon'))
                                <div class="position-relative d-inline-block preview-container">
                                    <img src="{{ asset(getSetting('fav_icon')) }}" alt="Admin Logo" class="img-thumbnail" style="max-height: 80px;">

                                    <button type="button"
                                            class="btn-close position-absolute text-danger top-0 end-0 remove-file"
                                            aria-label="Close"
                                            data-key="fav_icon"
                                            style="background-color: white; border-radius: 50%;">
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Social Media Info -->
                    <div class="col-12">
                        <h6 class="mt-2">3. Social Media Info</h6>
                        <hr class="mt-0" />
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="social_web" @if(getSetting('social_web') ?? 0) checked @endif data-class="web" id="social_web" value="web" />
                            <label class="form-check-label" for="social_web">Enable Web</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="social_android" @if(getSetting('social_android') ?? 0) checked @endif data-class="android" id="social_android" value="android" />
                            <label class="form-check-label" for="social_android">Enable Android</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="google_login" @if(getSetting('google') ?? 0) checked @endif data-class="google" id="google_login" value="on" />
                            <label class="form-check-label" for="google_login">Google Login</label>
                        </div>
                        @php
                            $google_credentials = \App\Models\Setting::decodeCredentials(getSetting('google'), ['client_secret']);
                        @endphp
                        
                        <div class="form-floating form-floating-outline mb-4 google d-none">
                            <input type="text" class="form-control" name="google_client_id" value="{{ old('google_client_id', $google_credentials['client_id'] ?? '') }}" id="google_client_id" placeholder="Google Client ID" />
                            <label for="google_client_id">Google Client ID</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 google d-none">
                            <input type="password" data-secret autocomplete="new-password" class="form-control" name="google_client_secret" value="{{ old('google_client_secret', $google_credentials['client_secret'] ?? '') }}" id="google_client_secret" placeholder="Google Client Secret" />
                            <label for="google_client_secret">Google Client Secret</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 google d-none">
                            <input type="text" class="form-control" name="google_redirect" value="{{ old('google_redirect', $google_credentials['redirect'] ?? '') }}" id="google_redirect" placeholder="Google Redirect URL" />
                            <label for="google_redirect">Google Redirect URL</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" data-class="facebook" @if(getSetting('facebook') ?? 0) checked @endif id="facebook_login" name="facebook_login" value="on" />
                            <label class="form-check-label" for="facebook_login">Facebook Login</label>
                        </div>
                        @php
                            $facebook_credentials = \App\Models\Setting::decodeCredentials(getSetting('facebook'), ['client_secret']);
                        @endphp
                        <div class="form-floating form-floating-outline mb-4 facebook d-none">
                            <input type="text" class="form-control" name="facebook_client_id" value="{{ old('facebook_client_id', $facebook_credentials['client_id'] ?? '') }}" id="facebook_client_id" placeholder="Facebook Client ID" />
                            <label for="facebook_client_id">Facebook Client ID</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 facebook d-none">
                            <input type="password" data-secret autocomplete="new-password" class="form-control" name="facebook_client_secret" value="{{ old('facebook_client_secret', $facebook_credentials['client_secret'] ?? '') }}" id="facebook_client_secret" placeholder="Facebook Client Secret" />
                            <label for="facebook_client_secret">Facebook Client Secret</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 facebook d-none">
                            <input type="text" class="form-control" name="facebook_redirect" value="{{ old('facebook_redirect', $facebook_credentials['redirect'] ?? '') }}" id="facebook_redirect" placeholder="Facebook Redirect URL" />
                            <label for="facebook_redirect">Facebook Redirect URL</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" data-class="twitter" @if(getSetting('twitter') ?? 0) checked @endif id="twitter_login" name="twitter_login" value="on" />
                            <label class="form-check-label" for="twitter_login">Twitter Login</label>
                        </div>
                        @php
                            $twitter_credentials = \App\Models\Setting::decodeCredentials(getSetting('twitter'), ['client_secret']);
                        @endphp
                        <div class="form-floating form-floating-outline mb-4 twitter d-none">
                            <input type="text" class="form-control" name="twitter_client_id" value="{{ old('twitter_client_id', $twitter_credentials['client_id'] ?? '') }}" id="twitter_client_id" placeholder="Twitter Client ID" />
                            <label for="twitter_client_id">Twitter Client ID</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 twitter d-none">
                            <input type="password" data-secret autocomplete="new-password" class="form-control" name="twitter_client_secret" value="{{ old('twitter_client_secret', $twitter_credentials['client_secret'] ?? '') }}" id="twitter_client_secret" placeholder="Twitter Client Secret" />
                            <label for="twitter_client_secret">Twitter Client Secret</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 twitter d-none">
                            <input type="text" class="form-control" name="twitter_redirect" value="{{ old('twitter_redirect', $twitter_credentials['redirect'] ?? '') }}" id="twitter_redirect" placeholder="Twitter Redirect URL" />
                            <label for="twitter_redirect">Twitter Redirect URL</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" data-class="github" @if(getSetting('github') ?? 0) checked @endif id="github_login" name="github_login" value="on" />
                            <label class="form-check-label" for="github_login">Github Login</label>
                        </div>
                        @php
                            $github_credentials = \App\Models\Setting::decodeCredentials(getSetting('github'), ['client_secret']);
                        @endphp
                        <div class="form-floating form-floating-outline mb-4 github d-none">
                            <input type="text" class="form-control" name="github_client_id" value="{{ old('github_client_id', $github_credentials['client_id'] ?? '') }}" id="github_client_id" placeholder="Github Client ID" />
                            <label for="github_client_id">Github Client ID</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 github d-none">
                            <input type="password" data-secret autocomplete="new-password" class="form-control" name="github_client_secret" value="{{ old('github_client_secret', $github_credentials['client_secret'] ?? '') }}" id="github_client_secret" placeholder="Github Client Secret" />
                            <label for="github_client_secret">Github Client Secret</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 github d-none">
                            <input type="text" class="form-control" name="github_redirect" value="{{ old('github_redirect', $github_credentials['redirect'] ?? '') }}" id="github_redirect" placeholder="Github Redirect URL" />
                            <label for="github_redirect">Github Redirect URL</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" data-class="linkedin" @if(getSetting('linkedin') ?? 0) checked @endif id="linkedin_login" name="linkedin_login" value="on" />
                            <label class="form-check-label" for="linkedin_login">LinkedIn Login</label>
                        </div>
                        @php
                            $linkedin_credentials = \App\Models\Setting::decodeCredentials(getSetting('linkedin'), ['client_secret']);
                        @endphp
                        <div class="form-floating form-floating-outline mb-4 linkedin d-none">
                            <input type="text" class="form-control" name="linkedin_client_id" value="{{ old('linkedin_client_id', $linkedin_credentials['client_id'] ?? '') }}" id="linkedin_client_id" placeholder="LinkedIn Client ID" />
                            <label for="linkedin_client_id">LinkedIn Client ID</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 linkedin d-none">
                            <input type="password" data-secret autocomplete="new-password" class="form-control" name="linkedin_client_secret" value="{{ old('linkedin_client_secret', $linkedin_credentials['client_secret'] ?? '') }}" id="linkedin_client_secret" placeholder="LinkedIn Client Secret" />
                            <label for="linkedin_client_secret">LinkedIn Client Secret</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 linkedin d-none">
                            <input type="text" class="form-control" name="linkedin_redirect" value="{{ old('linkedin_redirect', $linkedin_credentials['redirect'] ?? '') }}" id="linkedin_redirect" placeholder="LinkedIn Redirect URL" />
                            <label for="linkedin_redirect">LinkedIn Redirect URL</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" data-class="apple" @if(getSetting('apple') ?? 0) checked @endif id="apple_login" name="apple_login" value="on" />
                            <label class="form-check-label" for="apple_login">Apple Login</label>
                        </div>
                        @php
                            $apple_credentials = \App\Models\Setting::decodeCredentials(getSetting('apple'), ['client_secret']);
                        @endphp
                        <div class="form-floating form-floating-outline mb-4 apple d-none">
                            <input type="text" class="form-control" name="apple_client_id" value="{{ old('apple_client_id', $apple_credentials['client_id'] ?? '') }}" id="apple_client_id" placeholder="Apple Client ID" />
                            <label for="apple_client_id">Apple Client ID</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 apple d-none">
                            <input type="password" data-secret autocomplete="new-password" class="form-control" name="apple_client_secret" value="{{ old('apple_client_secret', $apple_credentials['client_secret'] ?? '') }}" id="apple_client_secret" placeholder="Apple Client Secret" />
                            <label for="apple_client_secret">Apple Client Secret</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 apple d-none">
                            <input type="text" class="form-control" name="apple_redirect" value="{{ old('apple_redirect', $apple_credentials['redirect'] ?? '') }}" id="apple_redirect" placeholder="Apple Redirect URL" />
                            <label for="apple_redirect">Apple Redirect URL</label>
                        </div>
                    </div>

                    <!-- App store review login -->
                    @php
                        $reviewRow = \App\Models\Setting::cachedRow('review_login');
                        $review = $reviewRow ? (json_decode((string) $reviewRow['value'], true) ?: []) : [];
                        $reviewEnabled = old('_token') ? old('review_login_enabled') === 'on' : (!$reviewRow || (int) $reviewRow['status_id'] === 1);
                    @endphp
                    <div class="col-12">
                        <h6 class="mt-2">4. App Store Review Login</h6>
                        <hr class="mt-0" />
                        <p class="text-body-secondary mb-0">Google and Apple reviewers cannot receive our SMS, so these numbers sign in with a fixed code. Keep this on only while an app update is in review, then switch it off.</p>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" data-class="review-login" @if ($reviewEnabled) checked @endif id="review_login_enabled" name="review_login_enabled" value="on" />
                            <label class="form-check-label" for="review_login_enabled">Enable Review Login</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 review-login d-none">
                            <input type="text" inputmode="numeric" class="form-control @error('review_driver_phone') is-invalid @enderror" name="review_driver_phone" value="{{ old('review_driver_phone', $review['driver_phone'] ?? '9094262603') }}" id="review_driver_phone" placeholder="AutoBazaar app test number" />
                            <label for="review_driver_phone">AutoBazaar App Test Number</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 review-login d-none">
                            <input type="text" inputmode="numeric" class="form-control @error('review_customer_phone') is-invalid @enderror" name="review_customer_phone" value="{{ old('review_customer_phone', $review['customer_phone'] ?? '8939345008') }}" id="review_customer_phone" placeholder="FairPrice app test number" />
                            <label for="review_customer_phone">FairPrice App Test Number</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-4 review-login d-none">
                            <input type="password" data-secret autocomplete="new-password" inputmode="numeric" class="form-control @error('review_otp') is-invalid @enderror" name="review_otp" value="{{ old('review_otp', $review['otp'] ?? '2203') }}" id="review_otp" placeholder="Fixed OTP" />
                            <label for="review_otp">Fixed OTP (4 to 6 digits)</label>
                        </div>
                        @foreach (['review_driver_phone', 'review_customer_phone', 'review_otp'] as $reviewField)
                            @error($reviewField)
                                <div class="text-danger small mb-2">{{ $message }}</div>
                            @enderror
                        @endforeach
                    </div>

                    <!-- Website contact + social links -->
                    <div class="col-12">
                        <h6 class="mt-2">5. Website Social Media Links</h6>
                        <hr class="mt-0" />
                        <p class="text-body-secondary mb-0">These are the icons in the website footer. Paste the full link, starting with https://. Leave a box empty and the website keeps the link it has today. The WhatsApp icon always follows the Business Mobile number above.</p>
                    </div>
                    @foreach (\App\Support\SiteData::SOCIAL_NETWORKS as $socialKey => [$socialLabel, $socialIcon])
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="url" inputmode="url" class="form-control @error($socialKey) is-invalid @enderror" name="{{ $socialKey }}" id="{{ $socialKey }}"
                                       value="{{ old($socialKey, getSetting($socialKey) ?: \App\Support\SiteData::currentValue($socialKey)) }}"
                                       placeholder="https://" />
                                <label for="{{ $socialKey }}">{{ $socialLabel }} link</label>
                                @error($socialKey)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
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
        </div>~
    </div>
<!-- /FormValidation -->
</div>
@endsection

@push('scripts')
<script src="{{asset('admin/assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('admin/assets/js/forms-selects.js')}}"></script>
<script src="{{ asset('admin/assets/vendor/libs/cropperjs/cropper.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function(){

        $(".form-check-input").on("change", function(){

            let targetClass = $(this).data("class");
            if($(this).is(":checked")){
                $("." + targetClass).removeClass('d-none').addClass('d-block');
            } else {
                $("." + targetClass).removeClass('d-block').addClass('d-none');
            }
        });

        $(".form-check-input:checked").each(function () {
            $(this).trigger("change");
        });

    });

    document.querySelectorAll(".remove-file").forEach(function (btn) {
        btn.addEventListener("click", function () {
            let key = this.dataset.key;
            let container = this.closest(".preview-container");
            if(confirm("Are you sure you want to remove this file?")) {
                fetch("{{ route('settings.general-settings.removeFile') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ key: key })
                })
                .then(res => res.json())
                .then(data => {
                    if (data == 1) {
                        container.remove();
                    } else {
                        alert("Error removing file");
                    }
                });
            }
        });
    });

</script>

<script>
let cropper;

document.getElementById('fileInput_web_logo').addEventListener('change', function(e){
    const file = e.target.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(event){
            const image = document.getElementById('imagePreview_web_logo');
            image.src = event.target.result;

            document.getElementById('cropModal').style.display = 'block';

            if(cropper) cropper.destroy();
            cropper = new Cropper(image, {
                aspectRatio: 1, // square
                viewMode: 1
            });
        }
        reader.readAsDataURL(file);
    }
});

document.getElementById('cropBtn').addEventListener('click', function(){
    const canvas = cropper.getCroppedCanvas({
        width: 100,
        height: 100,
    });

    // convert cropped image to base64
    document.getElementById('web_logo_croppedImage').value = canvas.toDataURL('image/png');

    alert("✅ Cropped image ready, now click Upload button!");
});
</script>

@endpush