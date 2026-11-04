<x-layouts.admin
    title="Riwayat Stok"
    breadcrumb="RedStock / <span>Riwayat Stok</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Master Data"
        title="Riwayat Stok {{ $barang->nama }}"
        description="Catatan mutasi masuk dan keluar barang dari kartu stok."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard.stok') }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="rs-form-layout">
        <x-ui.panel
            title="Informasi Barang"
            description="Ringkasan barang dan stok saat ini."
        >
            <div class="rs-form-grid">
                <div class="rs-field rs-field-full">
                    <span class="rs-label">ID Barang</span>
                    <span class="rs-mono">#{{ $barang->idbarang }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Nama Barang</span>
                    <span class="rs-cell-primary">{{ $barang->nama }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Satuan</span>
                    <span class="rs-cell-muted">{{ $barang->nama_satuan ?? '-' }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Status</span>
                    @if ((int) $barang->status === 1)
                        <x-ui.badge variant="success" dot>Aktif</x-ui.badge>
                    @else
                        <x-ui.badge variant="red" dot>Tidak Aktif</x-ui.badge>
                    @endif
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Stok Saat Ini</span>
                    <span class="rs-chip">{{ (int) $barang->stok }}</span>
                </div>
            </div>
        </x-ui.panel>

        <x-ui.panel
            title="Riwayat Mutasi Stok"
            description="Seluruh transaksi masuk dan keluar yang memengaruhi stok barang ini."
            :badge="count($mutasi) . ' Mutasi'"
            :flush="true"
        >
            @if (count($mutasi) > 0)
                <x-ui.table-search
                    placeholder="Cari jenis transaksi..."
                    label="Cari riwayat mutasi"
                    :total="count($mutasi)"
                />
            @endif

            <div class="rs-table-wrap">
                <table class="rs-table">
                    <thead>
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Jenis</th>
                            <th scope="col" class="is-right">Masuk</th>
                            <th scope="col" class="is-right">Keluar</th>
                            <th scope="col" class="is-right">Saldo</th>
                            <th scope="col">No. Transaksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($mutasi as $row)
                            <tr data-table-row>
                                <td>
                                    <span class="rs-cell-muted">
                                        {{ $row->created_at
                                            ? \Illuminate\Support\Carbon::parse($row->created_at)->translatedFormat('d M Y, H:i')
                                            : '-' }}
                                    </span>
                                </td>

                                <td>
                                    @if ($row->jenis_transaksi === 'P')
                                        <x-ui.badge variant="success" dot>Penerimaan</x-ui.badge>
                                    @elseif ($row->jenis_transaksi === 'J')
                                        <x-ui.badge variant="warning" dot>Penjualan</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="info" dot>Retur</x-ui.badge>
                                    @endif
                                </td>

                                <td class="is-right">
                                    <span class="rs-price">
                                        {{ (int) $row->masuk }}
                                    </span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-price">
                                        {{ (int) $row->keluar }}
                                    </span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-mono">{{ (int) $row->stock }}</span>
                                </td>

                                <td>
                                    <span class="rs-mono">
                                        {{ $row->jenis_transaksi }}#{{ $row->idtransaksi }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <x-ui.empty-state
                                        icon="layers"
                                        title="Belum ada mutasi stok"
                                        description="Stok barang ini masih kosong karena belum pernah ada transaksi."
                                    />
                                </td>
                            </tr>
                        @endforelse

                        <tr data-table-search-empty hidden>
                            <td colspan="6">
                                <x-ui.empty-state
                                    icon="search"
                                    title="Mutasi tidak ditemukan"
                                    description="Coba kata kunci lain atau bersihkan pencarian."
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-ui.panel>
    </div>
</x-layouts.admin>
