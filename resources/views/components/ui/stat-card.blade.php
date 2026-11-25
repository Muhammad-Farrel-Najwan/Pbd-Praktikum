@props([
    'label',
    'value',
    'note' => null,
    'icon' => 'activity',
    'tone' => 'red',
    'suffix' => '',
    'meter' => null,
    'meterLabel' => null,
    'delay' => 0,
])

@php
    $isNumeric = is_numeric($value);
    $meterValue = $meter === null ? null : max(0, min(100, (int) $meter));
@endphp

<article class="rs-stat-card" data-reveal style="--reveal-delay: {{ $delay }}ms">
    <span class="rs-stat-icon {{ $tone !== 'red' ? 'rs-tone-' . $tone : '' }}" aria-hidden="true">
        <x-ui.icon :name="$icon" />
    </span>

    <div class="rs-stat-main">
        <p class="rs-stat-label">{{ $label }}</p>

        <p @class(['rs-stat-value', 'is-text' => ! $isNumeric])>
            @if ($isNumeric)
                <span data-count="{{ $value }}" data-suffix="{{ $suffix }}">{{ $value }}{{ $suffix }}</span>
            @else
                {{ $value }}{{ $suffix }}
            @endif
        </p>

        @if ($note)
            <p class="rs-stat-note">{{ $note }}</p>
        @endif

        @if ($meterValue !== null)
            <div class="rs-stat-meter" role="img" aria-label="{{ $meterLabel ?? $label }}">
                <span class="rs-stat-meter-fill" style="--rs-meter: {{ $meterValue }}%"></span>
            </div>
        @endif
    </div>
</article>
