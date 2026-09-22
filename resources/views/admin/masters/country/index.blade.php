@extends('admin.layouts.app')
@section('title')
Country
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
@endpush

@section('content')
@php
    // Paged (the normal case) or the whole table at once — config/admin_lists.php.
    $paged = $countrys instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    $rowOffset = $paged ? $countrys->firstItem() - 1 : 0;
@endphp
<div class="row">
    <div class="col-12">
        <div class="row">
            @can('add_country')
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3 d-flex justify-content-end">
                <a href="{{route('masters.country.create')}}" class="btn create-new btn-primary">
                    <span>
                        <span class="d-flex align-items-center">
                            <i class="icon-base ri ri-add-line icon-18px me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Add New Country</span>
                        </span>
                    </span>
                </a>
            </div>
            @endcan
            @if ($paged)
                <div class="col-12">
                    @include('admin.include.list-search', ['placeholder' => 'Search by country name'])
                </div>
            @endif

            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-datatable text-nowrap">
                        {{-- Paged: a plain table, because the search and paging are
                             done by the server. Unpaged: the old DataTable. --}}
                        <table class="{{ $paged ? '' : 'datatables-basic' }} table table-bordered table-responsive">
                            <thead>
                                <tr>
                                <th>id</th>
                                <th>Name</th>
                                <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($countrys as $country)
                                    <tr>
                                        <td>{{ $rowOffset + $loop->iteration }}</td>
                                        <td>{{$country->name}}</td>
                                        <td>
                                            @can('edit_country')
                                                <a href="{{ route('masters.country.edit',$country->id) }}" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="icon-base ri ri-edit-box-line icon-20px"></i></a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No countries found{{ request()->filled('q') ? ' for “' . request('q') . '”' : '' }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($paged)
                        <div class="card-body pt-0">
                            @include('admin.include.list-pager', ['rows' => $countrys, 'noun' => 'countries'])
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

 <script src="{{asset('admin/js/custom-datatable.js')}}"></script>

@endpush