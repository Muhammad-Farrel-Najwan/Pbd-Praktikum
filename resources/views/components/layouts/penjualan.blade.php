@props([
    'title' => 'Panel Penjualan',
    'breadcrumb' => null,
    'topbarTitle' => null,
])

@php
    $navigation = [
        ['label' => 'Dashboard', 'route' => 'penjualan.dashboard', 'active' => 'penjualan.dashboard', 'icon' => 'grid'],
        ['section' => 'Penjualan'],
        ['label' => 'Transaksi Penjualan', 'route' => 'penjualan.penjualan.create', 'active' => 'penjualan.penjualan.create', 'icon' => 'receipt'],
        ['label' => 'Riwayat Penjualan', 'route' => 'penjualan.penjualan.index', 'active' => ['penjualan.penjualan.index', 'penjualan.penjualan.detail', 'penjualan.penjualan.struk'], 'icon' => 'clock'],
    ];
@endphp

<x-layouts.base
    role="penjualan"
    role-label="Staff Penjualan"
    home-route="penjualan.dashboard"
    :title="$title"
    :breadcrumb="$breadcrumb"
    :topbar-title="$topbarTitle"
    :navigation="$navigation"
>
    {{ $slot }}
</x-layouts.base>
