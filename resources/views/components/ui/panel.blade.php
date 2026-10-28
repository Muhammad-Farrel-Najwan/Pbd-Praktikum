@props([
    'title' => null,
    'description' => null,
    'badge' => null,
    'flush' => false,
])

<section {{ $attributes->merge(['class' => 'rs-panel']) }}>
    @if ($title || isset($actions))
        <header class="rs-panel-head">
            <div>
                @if ($title)
                    <h2>{{ $title }}</h2>
                @endif

                @if ($description)
                    <p>{{ $description }}</p>
                @endif
            </div>

            @if (isset($actions))
                <div class="rs-panel-actions">
                    {{ $actions }}
                </div>
            @elseif ($badge)
                <span class="rs-panel-badge">{{ $badge }}</span>
            @endif
        </header>
    @endif

    <div @class(['rs-panel-body', 'is-flush' => $flush])>
        {{ $slot }}
    </div>

    @if (isset($footer))
        <footer class="rs-panel-foot">
            {{ $footer }}
        </footer>
    @endif
</section>
