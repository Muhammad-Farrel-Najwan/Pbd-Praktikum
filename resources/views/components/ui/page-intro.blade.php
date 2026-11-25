@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'metrics' => [],
    'delay' => 0,
])

@php
    // Intro halaman menggabungkan judul, konteks, metrik ringkas, dan aksi
    // dalam satu blok agar dashboard tidak memiliki dua header sekaligus.
    $metrics = collect($metrics)
        ->filter(fn ($item) => isset($item['label']))
        ->values()
        ->all();
@endphp

<section class="rs-page-intro" data-reveal style="--reveal-delay: {{ $delay }}ms">
    <span class="rs-page-intro-rail" aria-hidden="true"></span>
    <span class="rs-page-intro-grid" aria-hidden="true"></span>
    <span class="rs-page-intro-ring" aria-hidden="true"></span>

    <div class="rs-page-intro-main">
        @if ($eyebrow)
            <span class="rs-eyebrow">
                <span class="rs-eyebrow-dot" aria-hidden="true"></span>
                {{ $eyebrow }}
            </span>
        @endif

        <h1>{{ $title }}</h1>

        @if ($description)
            <p>{{ $description }}</p>
        @endif

        @if (! empty($metrics))
            <ul class="rs-intro-metrics">
                @foreach ($metrics as $metric)
                    <li>
                        <span class="rs-intro-metric-label">{{ $metric['label'] }}</span>
                        <span class="rs-intro-metric-value">{{ $metric['value'] ?? '—' }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if (isset($actions))
        <div class="rs-page-intro-actions">
            {{ $actions }}
        </div>
    @endif
</section>
