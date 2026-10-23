@props([
    'target',
    'placeholder' => 'Search this list...',
    'label' => 'Search this list',
    'serverSearch' => false,
    'searchValue' => '',
])

@if($serverSearch)
    <form class="list-search" role="search" method="GET" action="{{ url()->current() }}" data-server-search>
@else
<div class="list-search" role="search">
@endif
    <div class="list-search-field">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m20 20-4-4"></path>
        </svg>
        <label class="visually-hidden" for="{{ $target }}-search">{{ $label }}</label>
        <input
            id="{{ $target }}-search"
            type="search"
            placeholder="{{ $placeholder }}"
            @if($serverSearch) name="search" @endif
            value="{{ $searchValue }}"
            autocomplete="off"
            data-list-search-input
            data-list-search-target="{{ $target }}"
            aria-controls="{{ $target }}"
        >
    </div>

    <p class="list-search-status" data-list-search-status role="status" aria-live="polite" hidden>
        No matching results. Try another name or keyword.
    </p>
@if($serverSearch)
    </form>
@else
</div>
@endif
