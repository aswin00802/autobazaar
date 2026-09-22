{{--
    Search box for the long admin lists.

    @include('admin.include.list-search', ['placeholder' => 'Search by city or state name'])

    It submits back to the same screen as ?q=..., so the search runs in the
    database over every row, not only the ones currently on screen. Include it
    only on a screen that is paged (see config/admin_lists.php) — on a screen
    showing everything at once, the table brings its own search box.
--}}
@php($listSearchPlaceholder = $placeholder ?? 'Search…')

<form method="GET" class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <div class="input-group" style="max-width: 380px;">
        <span class="input-group-text"><i class="icon-base ri ri-search-line icon-18px"></i></span>
        <input type="search" name="q" value="{{ request('q') }}" class="form-control"
               placeholder="{{ $listSearchPlaceholder }}" aria-label="{{ $listSearchPlaceholder }}">
        <button class="btn btn-primary" type="submit">Search</button>
    </div>

    @if (request()->filled('q'))
        <a href="{{ url()->current() }}" class="btn btn-label-secondary">Clear</a>
    @endif
</form>
