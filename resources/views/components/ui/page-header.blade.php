@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'delay' => 0,
])

<section class="rs-page-heading" data-reveal style="--reveal-delay: {{ $delay }}ms">
    <div>
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
        <div class="rs-heading-actions">
            {{ $actions }}
        </div>
    @endif
</section>
