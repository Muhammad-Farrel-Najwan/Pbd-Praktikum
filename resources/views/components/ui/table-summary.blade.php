@props([
    'total' => 0,
    'label' => 'data',
    'note' => null,
])

{{-- Ringkasan jumlah baris untuk tabel yang tidak memakai paginator. --}}
<div class="rs-table-summary-bar">
    <p class="rs-table-summary">
        Menampilkan <strong>{{ (int) $total }}</strong> {{ $label }}
    </p>

    @if ($note)
        <p class="rs-table-summary-note">{{ $note }}</p>
    @endif
</div>
