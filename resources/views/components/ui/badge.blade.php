@props([
    'variant' => 'muted',
    'dot' => false,
    'icon' => null,
])

<span {{ $attributes->merge(['class' => 'rs-badge rs-badge-' . $variant]) }}>
    @if ($dot)
        <span class="rs-badge-dot" aria-hidden="true"></span>
    @endif

    @if ($icon)
        <x-ui.icon :name="$icon" :size="12" />
    @endif

    {{ $slot }}
</span>
