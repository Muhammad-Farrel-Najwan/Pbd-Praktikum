@props([
    'icon' => 'inbox',
    'title',
    'description' => null,
])

<div class="rs-empty">
    <span class="rs-empty-icon" aria-hidden="true">
        <x-ui.icon :name="$icon" />
    </span>

    <strong>{{ $title }}</strong>

    @if ($description)
        <p>{{ $description }}</p>
    @endif
</div>
