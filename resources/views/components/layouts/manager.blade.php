@props([
    'title' => 'Panel Manager',
    'breadcrumb' => null,
    'topbarTitle' => null,
])

@php
    $navigation = [
        ['label' => 'Dashboard', 'route' => 'manager.dashboard', 'active' => 'manager.dashboard', 'icon' => 'grid'],
        ['section' => 'Analitik'],
        ['label' => 'Laporan Penjualan', 'icon' => 'chart', 'disabled' => true, 'badge' => 'Segera'],
        ['label' => 'Laporan Stok', 'icon' => 'database', 'disabled' => true, 'badge' => 'Segera'],
        ['label' => 'Approval Pengadaan', 'icon' => 'check', 'disabled' => true, 'badge' => 'Segera'],
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
