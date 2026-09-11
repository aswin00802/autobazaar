@extends('admin.layouts.app')
@section('title')
SMTP Setting
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
                <h4 class="card-title" id="basic-layout-card-center">SMTP Setting</h4>
            </div>
            <div class="mb-3">
                <hr class="mt-0" />
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <form class="form" action="{{ route('settings.smtp-settings.update') }}" method="post">
                        @csrf
                        
                        <div class="mb-3">
                            <input type="hidden" name="types[]" value="MAIL_MAILER">
                            <label class="form-label" for="employeename">Type</label>
                            <div class="position-relative has-icon-left">
                                <select class="select2 form-select" name="MAIL_MAILER" id="MAIL_MAILER" required data-allow-clear="true">
                                    <option value="sendmail" @if (env('MAIL_MAILER') == 'sendmail') selected @endif>Sendmail</option>
                                    <option value="smtp" @if (env('MAIL_MAILER') == 'smtp') selected @endif>SMTP</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="MAIL_HOST" class="">Mail Host</label>
                            <input type="hidden" name="types[]" value="MAIL_HOST">
                            <input type="text" id="MAIL_HOST"  value="{{ env('MAIL_HOST') }}" class="form-control" placeholder="Enter Mail Host" name="MAIL_HOST">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="MAIL_PORT" class="">Mail Port</label>
                            <input type="hidden" name="types[]" value="MAIL_PORT">
                            <input type="text" id="MAIL_PORT"  value="{{ env('MAIL_PORT') }}" class="form-control" placeholder="Enter Mail Port" name="MAIL_PORT">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="MAIL_USERNAME" class="">Mail Username</label>
                            <input type="hidden" name="types[]" value="MAIL_USERNAME">
                            <input type="text" id="MAIL_USERNAME"  value="{{ env('MAIL_USERNAME') }}" class="form-control" placeholder="Enter Mail Username" name="MAIL_USERNAME">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="MAIL_PASSWORD" class="">Mail Password</label>
                            <input type="hidden" name="types[]" value="MAIL_PASSWORD">
                            <input type="text" id="MAIL_PASSWORD"  value="{{ env('MAIL_PASSWORD') }}" class="form-control" placeholder="Enter Mail Password" name="MAIL_PASSWORD">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="MAIL_ENCRYPTION" class="">Mail Encryption</label>
                            <input type="hidden" name="types[]" value="MAIL_ENCRYPTION">
                            <input type="text" id="MAIL_ENCRYPTION"  value="{{ env('MAIL_ENCRYPTION') }}" class="form-control" placeholder="Enter Mail Encryption" name="MAIL_ENCRYPTION">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="MAIL_FROM_ADDRESS" class="">Mail From Address</label>
                            <input type="hidden" name="types[]" value="MAIL_FROM_ADDRESS">
                            <input type="text" id="MAIL_FROM_ADDRESS"  value="{{ env('MAIL_FROM_ADDRESS') }}" class="form-control" placeholder="Enter Mail From Address" name="MAIL_FROM_ADDRESS">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="MAIL_FROM_NAME" class="">Mail From Name</label>
                            <input type="hidden" name="types[]" value="MAIL_FROM_NAME">
                            <input type="text" id="MAIL_FROM_NAME"  value="{{ env('MAIL_FROM_NAME') }}" class="form-control" placeholder="Enter Mail From Name" name="MAIL_FROM_NAME">
                        </div>
                        <div class="mb-3">
                            <hr class="mt-0" />
                        </div>
                        <div class="form-actions center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> Save
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