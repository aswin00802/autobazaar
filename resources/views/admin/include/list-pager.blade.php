{{--
    "Showing 1 to 50 of 47,941" plus the page links, for the long admin lists.

    @include('admin.include.list-pager', ['rows' => $citys, 'noun' => 'cities'])

    Prints nothing when the screen is not paged, so a view can include it
    without checking first.
--}}
@php($listNoun = $noun ?? 'rows')

@if (isset($rows) && $rows instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3">
        <small class="text-muted">
            @if ($rows->total() === 0)
                No {{ $listNoun }} found{{ request()->filled('q') ? ' for “' . request('q') . '”' : '' }}.
            @else
                Showing {{ number_format($rows->firstItem()) }} to {{ number_format($rows->lastItem()) }}
                of {{ number_format($rows->total()) }} {{ $listNoun }}{{ request()->filled('q') ? ' matching “' . request('q') . '”' : '' }}.
            @endif
        </small>

        <div class="ms-auto">
            {{ $rows->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
