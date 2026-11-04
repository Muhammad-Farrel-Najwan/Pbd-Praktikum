<x-layouts.admin
    title="Data Penerimaan"
    breadcrumb="RedStock / <span>Data Penerimaan</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Transaksi"
        title="Data Penerimaan"
        description="Kelola dan pantau seluruh transaksi penerimaan barang dari pengadaan."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard') }}" variant="secondary" icon="arrow-left">
                Dashboard
            </x-ui.button>

            <x-ui.button href="{{ route('admin.penerimaan.create') }}" icon="plus">
                Tambah Penerimaan
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.panel
        title="Daftar Penerimaan"
        description="Seluruh transaksi penerimaan yang tersimpan di database RedStock."
        :badge="count($penerimaan) . ' Penerimaan'"
        :flush="true"
    >
        @if (session('success'))
            <x-ui.alert variant="success">
                {{ session('success') }}
            </x-ui.alert>
        @endif

        @if (session('error'))
            <x-ui.alert variant="error">
                {{ session('error') }}
            </x-ui.alert>
        @endif

        @if (count($penerimaan) > 0)
            <x-ui.table-search
                placeholder="Cari ID, vendor, atau petugas..."
                label="Cari data penerimaan"
                :total="count($penerimaan)"
            />
        @endif

        <div class="rs-table-wrap">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Pengadaan</th>
                        <th scope="col">Vendor</th>
                        <th scope="col">Petugas</th>
                        <th scope="col" class="is-right">Total</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($penerimaan as $item)
                        <tr data-table-row>
                            <td>
                                <span class="rs-mono">#{{ $item->idpenerimaan }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-muted">
                                    {{ $item->created_at
                                        ? \Illuminate\Support\Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i')
                                        : '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="rs-mono">#{{ $item->pengadaan_idpengadaan }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-primary">{{ $item->nama_vendor }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-muted">{{ $item->username }}</span>
                            </td>

                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $item->total_nilai, 0, ',', '.') }}
                                </span>
                            </td>

                            <td>
                                @if ($item->status === 'S')
                                    <x-ui.badge variant="success">Selesai</x-ui.badge>
                                @elseif ($item->status === 'P')
                                    <x-ui.badge variant="warning">Proses</x-ui.badge>
                                @else
                                    <x-ui.badge variant="muted">-</x-ui.badge>
                                @endif
                            </td>

                            <td>
                                <div class="rs-table-actions">
                                    <x-ui.button
                                        href="{{ route('admin.penerimaan.detail', $item->idpenerimaan) }}"
                                        variant="secondary"
                                        size="sm"
                                        icon="eye"
                                    >
                                        Detail
                                    </x-ui.button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-ui.empty-state
                                    icon="truck"
                                    title="Belum ada data penerimaan"
                                    description="Catat penerimaan barang pertama melalui tombol Tambah Penerimaan."
                                />
                            </td>
                        </tr>
                    @endforelse

                    <tr data-table-search-empty hidden>
                        <td colspan="8">
                            <x-ui.empty-state
                                icon="search"
                                title="Penerimaan tidak ditemukan"
                                description="Coba kata kunci lain atau bersihkan pencarian."
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-ui.panel>
</x-layouts.admin>
