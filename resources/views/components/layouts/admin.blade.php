@props([
    'title' => 'Panel Admin',
    'breadcrumb' => null,
    'topbarTitle' => null,
])

@php
    $navigation = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'grid'],
        ['label' => 'Margin Penjualan', 'url' => route('admin.dashboard') . '#pengaturan-margin', 'icon' => 'sliders'],
        ['label' => 'Data Barang', 'route' => 'admin.dashboard.barang', 'active' => ['admin.dashboard.barang', 'admin.barang.*'], 'icon' => 'box'],
        ['section' => 'Master Data'],
        ['label' => 'Data Satuan', 'route' => 'admin.dashboard.satuan', 'active' => 'admin.dashboard.satuan', 'icon' => 'ruler'],
        ['label' => 'Data Vendor', 'route' => 'admin.dashboard.vendor', 'active' => ['admin.dashboard.vendor', 'admin.vendor.*'], 'icon' => 'truck'],
        ['label' => 'Manajemen User', 'icon' => 'users', 'disabled' => true, 'badge' => 'Segera'],
    ];
@endphp

<x-layouts.base
    role="admin"
    role-label="Administrator"
    home-route="admin.dashboard"
    :title="$title"
    :breadcrumb="$breadcrumb"
    :topbar-title="$topbarTitle"
    :navigation="$navigation"
>
    {{ $slot }}
</x-layouts.base>
