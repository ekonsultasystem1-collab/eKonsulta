@props([
    'target',
    'placeholder' => 'Search this list...',
    'label' => 'Search this list',
    'sortOptions' => [
        'newest' => 'Newest first',
        'oldest' => 'Oldest first',
    ],
    'defaultSort' => 'newest',
])

<div class="list-search" role="search">
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
            autocomplete="off"
            data-list-search-input
            data-list-search-target="{{ $target }}"
            aria-controls="{{ $target }}"
        >
    </div>

    <div class="list-sort" aria-label="Sort items">
        <label for="{{ $target }}-sort" class="visually-hidden">Sort results</label>
        <select
            id="{{ $target }}-sort"
            data-list-sort-select
            data-list-sort-target="{{ $target }}"
            aria-label="Sort results"
            data-default-sort="{{ $defaultSort }}"
        >
            @foreach($sortOptions as $value => $sortLabel)
                <option value="{{ $value }}" {{ $value === $defaultSort ? 'selected' : '' }}>
                    {{ $sortLabel }}
                </option>
            @endforeach
        </select>
    </div>

    <p class="list-search-status" data-list-search-status role="status" aria-live="polite" hidden>
        No matching results. Try another name or keyword.
    </p>
</div>
