@props([
    'paginator',
    'label' => 'data',
])

{{-- Ringkasan jumlah baris yang sedang tampil beserta navigasi halaman --}}
@if ($paginator->total() > 0)
    <p class="rs-table-summary">
        Menampilkan <strong>{{ $paginator->firstItem() ?? 0 }}</strong>&ndash;<strong>{{ $paginator->lastItem() ?? 0 }}</strong>
        dari <strong>{{ $paginator->total() }}</strong> {{ $label }}
    </p>

    <x-ui.pagination :paginator="$paginator" />
@endif
