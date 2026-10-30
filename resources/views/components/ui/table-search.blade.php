@props([
    'placeholder' => 'Cari data...',
    'label' => null,
    'total' => null,
])

<div class="rs-table-toolbar">
    <div class="rs-input-group rs-table-search has-action">
        <span class="rs-input-prefix" aria-hidden="true">
            <x-ui.icon name="search" :size="17" />
        </span>

        <input
            type="search"
            class="rs-input"
            placeholder="{{ $placeholder }}"
            aria-label="{{ $label ?? $placeholder }}"
            data-table-search
            autocomplete="off"
            spellcheck="false"
        >

        <button
            type="button"
            class="rs-input-action"
            data-table-search-clear
            aria-label="Bersihkan pencarian"
            hidden
        >
            <x-ui.icon name="x" :size="16" />
        </button>
    </div>

    @if ($total !== null)
        <p class="rs-table-count" data-table-search-count aria-live="polite">
            {{ $total }} data
        </p>
    @endif
</div>
