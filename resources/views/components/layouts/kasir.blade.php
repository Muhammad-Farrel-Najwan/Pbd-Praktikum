@props([
    'title' => 'Panel Kasir',
    'breadcrumb' => null,
    'topbarTitle' => null,
])

@php
    $navigation = [
        ['label' => 'Dashboard', 'route' => 'kasir.dashboard', 'active' => 'kasir.dashboard', 'icon' => 'grid'],
        ['section' => 'Operasional'],
        ['label' => 'Transaksi Penjualan', 'route' => 'kasir.penjualan.create', 'active' => 'kasir.penjualan.create', 'icon' => 'receipt'],
        ['label' => 'Riwayat Transaksi', 'route' => 'kasir.penjualan.index', 'active' => 'kasir.penjualan.index', 'icon' => 'clock'],
    ];
@endphp

<x-layouts.base
    role="kasir"
    role-label="Kasir"
    home-route="kasir.dashboard"
    :title="$title"
    :breadcrumb="$breadcrumb"
    :topbar-title="$topbarTitle"
    :navigation="$navigation"
>
    {{ $slot }}
</x-layouts.base>
