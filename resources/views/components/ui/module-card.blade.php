@props([
    'title',
    'description',
    'icon' => 'box',
    'status' => 'soon',
    'statusLabel' => null,
    'href' => null,
    'delay' => 0,
])

@php
    $isAvailable = $status === 'available' && $href;
    $label = $statusLabel ?? ($isAvailable ? 'Tersedia' : 'Segera');
@endphp

@if ($isAvailable)
    <a href="{{ $href }}" class="rs-module-card is-available" data-reveal style="--reveal-delay: {{ $delay }}ms">
        <div class="rs-module-top">
            <span class="rs-module-icon" aria-hidden="true">
                <x-ui.icon :name="$icon" />
            </span>

            <x-ui.badge variant="success" dot>{{ $label }}</x-ui.badge>
        </div>

        <h3 class="rs-module-title">{{ $title }}</h3>
        <p class="rs-module-text">{{ $description }}</p>

        <span class="rs-module-meta">
            Buka modul
            <x-ui.icon name="arrow-up-right" :size="14" />
        </span>
    </a>
@else
    <article class="rs-module-card is-soon" data-reveal style="--reveal-delay: {{ $delay }}ms">
        <div class="rs-module-top">
            <span class="rs-module-icon" aria-hidden="true">
                <x-ui.icon :name="$icon" />
            </span>

            <x-ui.badge variant="warning" dot>{{ $label }}</x-ui.badge>
        </div>

        <h3 class="rs-module-title">{{ $title }}</h3>
        <p class="rs-module-text">{{ $description }}</p>

        <span class="rs-module-meta">
            <x-ui.icon name="clock" :size="14" />
            Menunggu modul
        </span>
    </article>
@endif
