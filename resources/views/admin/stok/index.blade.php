<x-layouts.admin
    title="Data Stok"
    breadcrumb="RedStock / <span>Data Stok</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Master Data"
        title="Data Stok"
        description="Pantau stok setiap barang berdasarkan catatan kartu stok."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard.barang') }}" variant="secondary" icon="arrow-left">
                Data Barang
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.panel
        title="Daftar Stok Barang"
        description="Sisa stok dihitung dari total masuk dikurangi total keluar."
        :badge="count($stok) . ' Barang'"
        :flush="true"
    >
        @if (count($stok) > 0)
            <x-ui.table-search
                placeholder="Cari nama barang atau satuan..."
                label="Cari data stok"
                :total="count($stok)"
            />
        @endif

        <div class="rs-table-wrap">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Satuan</th>
                        <th scope="col" class="is-right">Stok</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($stok as $item)
                        <tr data-table-row>
                            <td>
                                <span class="rs-mono">#{{ $item->idbarang }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-primary">{{ $item->nama }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-muted">{{ $item->nama_satuan ?? '-' }}</span>
                            </td>

                            <td class="is-right">
                                @if ((int) $item->stok > 0)
                                    <span class="rs-chip">{{ (int) $item->stok }}</span>
                                @else
                                    <x-ui.badge variant="red" dot>Habis</x-ui.badge>
                                @endif
                            </td>

                            <td>
                                @if ((int) $item->status === 1)
                                    <x-ui.badge variant="success" dot>Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="red" dot>Tidak Aktif</x-ui.badge>
                                @endif
                            </td>

                            <td>
                                <div class="rs-table-actions">
                                    <x-ui.button
                                        href="{{ route('admin.stok.detail', $item->idbarang) }}"
                                        variant="secondary"
                                        size="sm"
                                        icon="clock"
                                    >
                                        Riwayat
                                    </x-ui.button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-ui.empty-state
                                    icon="layers"
                                    title="Belum ada data barang"
                                    description="Tambahkan barang terlebih dahulu melalui menu Data Barang."
                                />
                            </td>
                        </tr>
                    @endforelse

                    <tr data-table-search-empty hidden>
                        <td colspan="6">
                            <x-ui.empty-state
                                icon="search"
                                title="Barang tidak ditemukan"
                                description="Coba kata kunci lain atau bersihkan pencarian."
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-ui.panel>
</x-layouts.admin>
