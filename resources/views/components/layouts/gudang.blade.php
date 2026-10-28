@props([
    'title' => 'Panel Gudang',
    'breadcrumb' => null,
    'topbarTitle' => null,
])

@php
    $navigation = [
        ['label' => 'Dashboard', 'route' => 'gudang.dashboard', 'active' => 'gudang.dashboard', 'icon' => 'grid'],
        ['section' => 'Inventori'],
        ['label' => 'Penerimaan Barang', 'icon' => 'truck', 'disabled' => true, 'badge' => 'Segera'],
        ['label' => 'Kartu Stok', 'icon' => 'layers', 'disabled' => true, 'badge' => 'Segera'],
        ['label' => 'Pengadaan Barang', 'icon' => 'package', 'disabled' => true, 'badge' => 'Segera'],
    ];
@endphp

<x-layouts.base
    role="gudang"
    role-label="Petugas Gudang"
    home-route="gudang.dashboard"
    :title="$title"
    :breadcrumb="$breadcrumb"
    :topbar-title="$topbarTitle"
    :navigation="$navigation"
>
    {{ $slot }}
</x-layouts.base>
