<x-layouts.gudang
    title="Kartu Stok"
    breadcrumb="RedStock / <span>Kartu Stok</span>"
    topbar-title="Panel Petugas Gudang"
>
    <x-ui.page-header
        eyebrow="Inventori"
        title="Kartu Stok"
        description="Pantau stok setiap barang berdasarkan catatan kartu stok."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('gudang.dashboard') }}" variant="secondary" icon="arrow-left">
                Dashboard
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.panel
        title="Daftar Stok Barang"
        description="Sisa stok dihitung dari total masuk dikurangi total keluar."
        :badge="$stok->total() . ' Barang'"
        :flush="true"
    >
        @if ($stok->total() > 0 || filled($q))
            <x-ui.table-search
                placeholder="Cari nama barang atau satuan..."
                label="Cari data stok"
                :total="$stok->total()"
                :action="route('gudang.stok.index')"
                :value="$q"
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
                                        href="{{ route('gudang.stok.detail', $item->idbarang) }}"
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
                                <x-ui.table-empty
                                    q="{{ $q }}"
                                    icon="layers"
                                    title="Belum ada data barang"
                                    description="Data barang dikelola melalui panel admin."
                                    search-title="Barang tidak ditemukan"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot:footer>
            <x-ui.table-footer :paginator="$stok" label="barang" />
        </x-slot:footer>
    </x-ui.panel>
</x-layouts.gudang>
