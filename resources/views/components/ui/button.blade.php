@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconAfter' => null,
    'type' => 'button',
])

@php
    $classes = 'rs-btn rs-btn-' . $variant;

    if ($size !== 'md') {
        $classes .= ' rs-btn-' . $size;
    }
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <x-ui.icon :name="$icon" />
        @endif

        <span>{{ $slot }}</span>

        @if ($iconAfter)
            <x-ui.icon :name="$iconAfter" />
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <x-ui.icon :name="$icon" />
        @endif

        <span>{{ $slot }}</span>

        @if ($iconAfter)
            <x-ui.icon :name="$iconAfter" />
        @endif
    </button>
@endif
