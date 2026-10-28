@props([
    'title' => 'Panel Penjualan',
    'breadcrumb' => null,
    'topbarTitle' => null,
])

@php
    $navigation = [
        ['label' => 'Dashboard', 'route' => 'penjualan.dashboard', 'active' => 'penjualan.dashboard', 'icon' => 'grid'],
        ['section' => 'Penjualan'],
        ['label' => 'Penjualan', 'icon' => 'receipt', 'disabled' => true, 'badge' => 'Segera'],
        ['label' => 'Retur Penjualan', 'icon' => 'rotate', 'disabled' => true, 'badge' => 'Segera'],
        ['label' => 'Riwayat Penjualan', 'icon' => 'clock', 'disabled' => true, 'badge' => 'Segera'],
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
