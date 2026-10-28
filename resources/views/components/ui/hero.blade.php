@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'delay' => 0,
])

<section class="rs-hero" data-reveal style="--reveal-delay: {{ $delay }}ms">
    <span class="rs-hero-scan" aria-hidden="true"></span>

    <div class="rs-hero-content">
        @if ($eyebrow)
            <span class="rs-hero-eyebrow">
                <span aria-hidden="true"></span>
                {{ $eyebrow }}
            </span>
        @endif

        <h2>{{ $title }}</h2>

        @if ($description)
            <p>{{ $description }}</p>
        @endif
    </div>

    @if (isset($aside))
        <div class="rs-hero-aside">
            {{ $aside }}
        </div>
    @endif
</section>
