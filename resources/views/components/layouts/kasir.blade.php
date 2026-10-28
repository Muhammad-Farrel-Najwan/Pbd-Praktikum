@props([
    'title' => 'Panel Kasir',
    'breadcrumb' => null,
    'topbarTitle' => null,
])

@php
    $navigation = [
        ['label' => 'Dashboard', 'route' => 'kasir.dashboard', 'active' => 'kasir.dashboard', 'icon' => 'grid'],
        ['section' => 'Operasional'],
        ['label' => 'Transaksi Penjualan', 'icon' => 'receipt', 'disabled' => true, 'badge' => 'Segera'],
        ['label' => 'Retur Penjualan', 'icon' => 'rotate', 'disabled' => true, 'badge' => 'Segera'],
        ['label' => 'Riwayat Transaksi', 'icon' => 'clock', 'disabled' => true, 'badge' => 'Segera'],
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
