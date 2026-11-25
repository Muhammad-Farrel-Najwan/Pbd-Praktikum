@props([
    'title' => 'Panel Manager',
    'breadcrumb' => null,
    'topbarTitle' => null,
])

@php
    $navigation = [
        ['label' => 'Dashboard', 'route' => 'manager.dashboard', 'active' => 'manager.dashboard', 'icon' => 'grid'],
        ['section' => 'Analitik'],
        ['label' => 'Laporan Penjualan', 'route' => 'manager.laporan.penjualan', 'active' => 'manager.laporan.penjualan', 'icon' => 'chart'],
        ['label' => 'Laporan Stok', 'route' => 'manager.laporan.stok', 'active' => ['manager.laporan.stok', 'manager.laporan.stok.detail'], 'icon' => 'database'],
        ['label' => 'Approval Pengadaan', 'route' => 'manager.approval.index', 'active' => ['manager.approval.index', 'manager.approval.detail'], 'icon' => 'check'],
    ];
@endphp

<x-layouts.base
    role="manager"
    role-label="Manager"
    home-route="manager.dashboard"
    :title="$title"
    :breadcrumb="$breadcrumb"
    :topbar-title="$topbarTitle"
    :navigation="$navigation"
>
    {{ $slot }}
</x-layouts.base>
