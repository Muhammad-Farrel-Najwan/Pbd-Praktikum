@props([
    'placeholder' => 'Cari data...',
    'label' => null,
    'total' => null,
    'action' => null,
    'value' => null,
    'name' => 'q',
])

@php
    // Bila `action` diisi, pencarian dilakukan di server (form GET) supaya
    // hasilnya mencakup seluruh data, bukan hanya baris pada halaman aktif.
    $serverSide = filled($action);
    $current = old($name, $value ?? request()->query($name));

    // Pertahankan filter lain (status, rentang tanggal, dan sebagainya)
    // agar tidak hilang saat pengguna mencari.
    $preserved = collect(request()->query())
        ->except([$name, 'page'])
        ->filter(fn ($item) => $item !== null && $item !== '')
        ->all();
@endphp

@if ($serverSide)
    <form method="GET" action="{{ $action }}" class="rs-table-toolbar" role="search" data-table-search-form>
        @foreach ($preserved as $key => $items)
            @foreach ((array) $items as $item)
                <input type="hidden" name="{{ $key }}{{ is_array($items) ? '[]' : '' }}" value="{{ $item }}">
            @endforeach
        @endforeach

        <div class="rs-input-group rs-table-search has-action">
            <span class="rs-input-prefix" aria-hidden="true">
                <x-ui.icon name="search" :size="17" />
            </span>

            <input
                type="search"
                class="rs-input"
                name="{{ $name }}"
                value="{{ $current }}"
                placeholder="{{ $placeholder }}"
                aria-label="{{ $label ?? $placeholder }}"
                autocomplete="off"
                spellcheck="false"
            >

            <button
                type="button"
                class="rs-input-action"
                data-table-search-clear
                aria-label="Bersihkan pencarian"
                @if (blank($current)) hidden @endif
            >
                <x-ui.icon name="x" :size="16" />
            </button>
        </div>

        @if ($total !== null)
            <p class="rs-table-count" aria-live="polite">
                {{ $total }} data
            </p>
        @endif
    </form>
@else
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
@endif
