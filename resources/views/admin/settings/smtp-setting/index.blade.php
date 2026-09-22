@extends('admin.layouts.app')
@section('title')
SMTP Setting
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
@endpush

@include('admin.include.secret-toggle')

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

                    <form class="form" action="{{ route('settings.smtp-settings.update') }}" method="post" autocomplete="off">
                        @csrf

                        @php $mailer = old('mailer', $smtp['mailer'] ?? 'smtp'); @endphp
                        <div class="mb-3">
                            <label class="form-label" for="mailer">Type</label>
                            <div class="position-relative has-icon-left">
                                <select class="select2 form-select" name="mailer" id="mailer" required data-allow-clear="true">
                                    <option value="sendmail" @selected($mailer == 'sendmail')>Sendmail</option>
                                    <option value="smtp" @selected($mailer == 'smtp')>SMTP</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="host">Mail Host</label>
                            <input type="text" id="host" value="{{ old('host', $smtp['host'] ?? '') }}" class="form-control @error('host') is-invalid @enderror" placeholder="Enter Mail Host" name="host">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="port">Mail Port</label>
                            <input type="text" inputmode="numeric" id="port" value="{{ old('port', $smtp['port'] ?? '') }}" class="form-control @error('port') is-invalid @enderror" placeholder="Enter Mail Port" name="port">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="username">Mail Username</label>
                            <input type="text" id="username" value="{{ old('username', $smtp['username'] ?? '') }}" class="form-control @error('username') is-invalid @enderror" placeholder="Enter Mail Username" name="username" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Mail Password</label>
                            <div class="position-relative">
                                <input type="password" data-secret id="password" value="{{ old('password', $smtp['password'] ?? '') }}" class="form-control @error('password') is-invalid @enderror" placeholder="Enter Mail Password" name="password" autocomplete="new-password">
                            </div>
                        </div>
                        @php $encryption = old('encryption', $smtp['encryption'] ?? 'tls'); @endphp
                        <div class="mb-3">
                            <label class="form-label" for="encryption">Mail Encryption</label>
                            <select class="form-select @error('encryption') is-invalid @enderror" name="encryption" id="encryption">
                                <option value="tls" @selected($encryption == 'tls')>TLS (port 587)</option>
                                <option value="ssl" @selected($encryption == 'ssl')>SSL (port 465)</option>
                                <option value="none" @selected($encryption == 'none')>None</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="from_address">Mail From Address</label>
                            <input type="email" id="from_address" value="{{ old('from_address', $smtp['from_address'] ?? '') }}" class="form-control @error('from_address') is-invalid @enderror" placeholder="Enter Mail From Address" name="from_address" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="from_name">Mail From Name</label>
                            <input type="text" id="from_name" value="{{ old('from_name', $smtp['from_name'] ?? '') }}" class="form-control @error('from_name') is-invalid @enderror" placeholder="Enter Mail From Name" name="from_name" required>
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
