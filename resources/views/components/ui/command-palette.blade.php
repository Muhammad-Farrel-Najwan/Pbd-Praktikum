@props([
    'navigation' => [],
    'homeRoute' => null,
])

@php
    $items = [];

    foreach ($navigation as $item) {
        if (! empty($item['section'])) {
            $items[] = ['type' => 'group', 'label' => $item['section']];

            continue;
        }

        if (! empty($item['disabled'])) {
            continue;
        }

        $href = null;

        if (! empty($item['route']) && \Illuminate\Support\Facades\Route::has($item['route'])) {
            $href = route($item['route']);
        } elseif (! empty($item['url'])) {
            $href = $item['url'];
        }

        if (! $href) {
            continue;
        }

        $items[] = [
            'type' => 'link',
            'label' => $item['label'],
            'href' => $href,
            'icon' => $item['icon'] ?? 'circle',
        ];
    }
@endphp

<div class="rs-cmdk" data-cmdk hidden>
    <div
        class="rs-cmdk-panel"
        role="dialog"
        aria-modal="true"
        aria-label="Pencarian cepat navigasi"
    >
        <div class="rs-cmdk-head">
            <x-ui.icon name="search" />

            <input
                type="text"
                class="rs-cmdk-input"
                placeholder="Cari halaman, menu, atau aksi…"
                autocomplete="off"
                spellcheck="false"
                aria-label="Cari navigasi"
                data-cmdk-input
            >

            <span class="rs-kbd rs-cmdk-esc">ESC</span>
        </div>

        <ul class="rs-cmdk-list" role="listbox" aria-label="Hasil navigasi" data-cmdk-list>
            @foreach ($items as $item)
                @if ($item['type'] === 'group')
                    <li class="rs-cmdk-group" role="presentation">{{ $item['label'] }}</li>
                @else
                    <li
                        class="rs-cmdk-item"
                        role="option"
                        tabindex="-1"
                        data-cmdk-item
                        data-cmdk-label="{{ \Illuminate\Support\Str::lower($item['label']) }}"
                        data-cmdk-href="{{ $item['href'] }}"
                    >
                        <x-ui.icon :name="$item['icon']" />

                        <span class="rs-cmdk-item-label">{{ $item['label'] }}</span>
                        <span class="rs-cmdk-item-hint">Buka</span>
                    </li>
                @endif
            @endforeach

            <li class="rs-cmdk-empty" data-cmdk-empty hidden>Tidak ada hasil yang cocok.</li>
        </ul>

        <div class="rs-cmdk-foot">
            <span><span class="rs-kbd">↑</span><span class="rs-kbd">↓</span> Jelajahi</span>
            <span><span class="rs-kbd">↵</span> Buka</span>
            <span><span class="rs-kbd">ESC</span> Tutup</span>
        </div>
    </div>
</div>
