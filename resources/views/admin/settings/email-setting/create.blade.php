@extends('admin.layouts.app')
@section('title')
Email Template Create
@endsection

@push('css')

@section('content')
<div class="row">
<!-- FormValidation -->
    <div class="col-12">
        <div class="card">
            <h5 class="card-header">Email Template Create</h5>
            <div class="card-body">
                <form action="{{ route('settings.email-template-settings.store') }}" name="email_template" id="email_template" class="row g-5" method="post">
                    @csrf
                    
                    <div class="col-md-4 form-control-validation">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="title" class="form-control" placeholder="Enter Email Title" name="title" />
                            <label for="title">Title</label>
                        </div>
                    </div>
                    <div class="col-md-8 form-control-validation">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="subject" class="form-control" placeholder="Enter Email Subject" name="subject" />
                            <label for="subject">Email Subject</label>
                        </div>
                    </div>
                    <div class="col-md-12 form-control-validation">
                        <label for="body">Email Body</label>
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control h-px-100" id="body" name="body" rows="3"></textarea>
                            <!-- <label for="body">Email Body</label> -->
                        </div>
                    </div>

                    
                    
                    <div class="col-12">
                        <a href="{{route('settings.email-template-settings')}}" class="btn btn-dark" style="color:white">Back</a>
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
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('body');
</script>
@endpush