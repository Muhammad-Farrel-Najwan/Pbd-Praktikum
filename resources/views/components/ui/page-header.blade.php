@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'delay' => 0,
])

{{-- Header halaman standar. Strukturnya sama dengan x-ui.page-intro agar
     seluruh halaman terasa satu sistem, hanya tanpa baris metrik. --}}
<section class="rs-page-intro is-compact" data-reveal style="--reveal-delay: {{ $delay }}ms">
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
    </div>

    @if (isset($actions))
        <div class="rs-page-intro-actions">
            {{ $actions }}
        </div>
    @endif
</section>
