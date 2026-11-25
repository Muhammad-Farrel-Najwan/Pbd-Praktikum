@props([
    'title' => 'Panel Gudang',
    'breadcrumb' => null,
    'topbarTitle' => null,
])

@php
    $navigation = [
        ['label' => 'Dashboard', 'route' => 'gudang.dashboard', 'active' => 'gudang.dashboard', 'icon' => 'grid'],
        ['section' => 'Inventori'],
        ['label' => 'Pengadaan Barang', 'route' => 'gudang.pengadaan.index', 'active' => ['gudang.pengadaan.index', 'gudang.pengadaan.create', 'gudang.pengadaan.detail'], 'icon' => 'package'],
        ['label' => 'Penerimaan Barang', 'route' => 'gudang.penerimaan.index', 'active' => ['gudang.penerimaan.index', 'gudang.penerimaan.create', 'gudang.penerimaan.detail'], 'icon' => 'truck'],
        ['label' => 'Kartu Stok', 'route' => 'gudang.stok.index', 'active' => ['gudang.stok.index', 'gudang.stok.detail'], 'icon' => 'layers'],
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
