@props([
    'variant' => 'info',
    'icon' => null,
])

@php
    $icon = $icon ?? match ($variant) {
        'success' => 'check',
        'error' => 'alert',
        default => 'help',
    };
@endphp

<div {{ $attributes->merge(['class' => 'rs-alert rs-alert-' . $variant]) }} role="{{ $variant === 'error' ? 'alert' : 'status' }}">
    <x-ui.icon :name="$icon" />

    <div>
        {{ $slot }}
    </div>
</div>
