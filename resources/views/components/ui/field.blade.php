@props([
    'label',
    'name',
    'hint' => null,
    'required' => false,
])

<div {{ $attributes->merge(['class' => 'rs-field']) }}>
    <label class="rs-label" for="{{ $name }}">
        {{ $label }}

        @if ($required)
            <span class="rs-required" aria-hidden="true">*</span>
        @endif
    </label>

    {{ $slot }}

    @if ($hint)
        <p class="rs-field-hint">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="rs-field-error">
            <x-ui.icon name="alert" :size="14" />
            {{ $message }}
        </p>
    @enderror
</div>
