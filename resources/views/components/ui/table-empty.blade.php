@props([
    'q' => null,
    'icon' => 'inbox',
    'title' => 'Belum ada data',
    'description' => null,
    'searchTitle' => null,
])

{{-- Membedakan tabel kosong karena belum ada data dengan hasil pencarian yang tidak ketemu --}}
@if (filled($q))
    <x-ui.empty-state
        icon="search"
        :title="$searchTitle ?? 'Data tidak ditemukan'"
        description="Coba kata kunci lain atau bersihkan pencarian."
    />
@else
    <x-ui.empty-state :icon="$icon" :title="$title" :description="$description" />
@endif
