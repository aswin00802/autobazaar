@extends('admin.layouts.app')
@section('title')
Users List
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
@php
    // Paged (the normal case) or every user at once — config/admin_lists.php.
    $paged = $users instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    $rowOffset = $paged ? $users->firstItem() - 1 : 0;
@endphp
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3">
        <div class="card">
            <h5 class="card-header">Admin Details</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr class="text-nowrap">
                            <th>Sl.no</th> 
                            <th>User Type</th>  
                            <th>Name</th> 
                            <th>Email</th> 
                            <th>Phone</th> 
                            <th>Reg. Date</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <tr>
                            <td>1</td>
                            <td>
                                <span class="badge rounded-pill text-bg-success">Admin User</span>
                            </td>
                            <td>{{ $admin->name ??'Not Available'}}</td>
                            <td>{{ $admin->email ??'Not Available'}}</td>
                            <td>{{ $admin->phone_number ?? 'Not Available' }}</td>
                            <td>{{ $admin->created_at ?? 'Not Available' }}</td>
                            <td><a href="{{ route('user-management.users-info' ,  Crypt::encryptString($admin->id)) }}" class="btn btn-sm btn-text-primary rounded-pill btn-icon" title="More info"><i class="icon-base ri ri-eye-line icon-20px"></i></a></td>
                        </tr>
                        
                        <tr>
                            <td>2</td>
                            <td>
                                <span class="badge rounded-pill text-bg-info">Playstore User</span>
                            </td>
                            <td>{{ $playstore_test->name ??'Not Available'}}</td>
                            <td>{{ $playstore_test->email ??'Not Available'}}</td>
                            <td>{{ $playstore_test->phone_number ?? 'Not Available' }}</td>
                            <td>{{ $playstore_test->created_at ?? 'Not Available' }}</td>
                            <td><a href="{{ route('user-management.users-info' ,  Crypt::encryptString($playstore_test->id)) }}" class="btn btn-sm btn-text-primary rounded-pill btn-icon" title="More info"><i class="icon-base ri ri-eye-line icon-20px"></i></a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <h5 class="card-header">Other User's Details</h5>

            {{-- One person is often several of these at once, so these are filters
                 on what somebody has done, not separate kinds of account. --}}
            <div class="card-body pb-0">
                <ul class="nav nav-pills flex-wrap mb-3">
                    @foreach ([
                        'all' => 'All', 'online' => 'Online now', 'drivers' => 'Drivers',
                        'fairprice' => 'FairPrice', 'sellers' => 'Sellers', 'buyers' => 'Buyers',
                        'inactive' => 'Inactive',
                    ] as $key => $label)
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $show === $key ? 'active' : '' }}"
                               href="{{ $key === 'all' ? url()->current() : url()->current() . '?show=' . $key }}">
                                {{ $label }}
                                <span class="badge {{ $show === $key ? 'bg-white text-primary' : 'bg-label-secondary' }} ms-1">
                                    {{ number_format($tabCounts[$key] ?? 0) }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="card-body pt-0">
                @if ($paged)
                    @include('admin.include.list-search', ['placeholder' => 'Search by name, phone, email or area'])
                @endif

                {{-- Paged: a plain table, because the search and paging are done by
                     the server. Unpaged: the old DataTable. --}}
                <table class="{{ $paged ? '' : 'datatables-fixed2' }} table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Sl.no</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>User Area</th>
                            <th>Is</th>
                            <th>Last Active</th>
                            <th>Reg. Date</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            @php
                                $seen = $user->last_seen_at;
                                $online = $seen && $seen->gt(now()->subMinutes(5));
                            @endphp
                            <tr>
                                <td>{{ $rowOffset + $loop->iteration }}</td>
                                <td>{{ $user->name ?? 'Not Available'}}</td>
                                <td>{{ $user->phone_number ?? 'Not Available' }}</td>
                                <td class="{{ $user->autoAreas?->name ? '' : 'text-danger' }}">
                                    {{ $user->autoAreas?->name ?? 'Not Available' }}
                                </td>

                                {{-- What this person actually is, from what they have done --}}
                                <td>
                                    @if ($user->is_driver)
                                        <span class="badge bg-label-primary">Driver</span>
                                    @endif
                                    @if ($user->listings_count)
                                        <span class="badge bg-label-warning">Seller</span>
                                    @endif
                                    @if ($user->app_orders_count)
                                        <span class="badge bg-label-success">Buyer</span>
                                    @endif
                                    @if (! $user->is_driver && ! $user->listings_count && ! $user->app_orders_count)
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                {{-- A green dot means seen in the last five minutes --}}
                                <td>
                                    @if ($seen)
                                        <span class="d-inline-block rounded-circle me-1 align-middle
                                                     {{ $online ? 'bg-success' : 'bg-secondary opacity-50' }}"
                                              style="width:8px; height:8px;"></span>
                                        <span class="align-middle {{ $online ? 'text-success fw-medium' : '' }}"
                                              title="{{ $seen->format('d M Y, g:i A') }}">
                                            {{ $online ? 'Online now' : $seen->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-muted small">Not seen yet</span>
                                    @endif
                                </td>

                                <td>{{ $user->created_at?->toDateString() }}</td>
                                <td><a href="{{ route('user-management.users-info' , Crypt::encryptString($user->id)) }}" class="btn btn-sm btn-text-primary rounded-pill btn-icon" title="More info"><i class="icon-base ri ri-eye-line icon-20px"></i></a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No users found{{ request()->filled('q') ? ' for “' . request('q') . '”' : '' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($paged)
                    @include('admin.include.list-pager', ['rows' => $users, 'noun' => 'users'])
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="{{ asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js') }}"></script>
@endpush