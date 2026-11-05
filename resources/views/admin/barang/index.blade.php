<x-layouts.admin title="Data Barang" breadcrumb="RedStock / <span>Data Barang</span>" topbar-title="Panel Administrator">
    <x-ui.page-header eyebrow="Master Data" title="Data Barang"
        description="Kelola dan pantau seluruh data barang yang terdaftar di RedStock.">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard') }}" variant="secondary" icon="arrow-left">
                Dashboard
            </x-ui.button>

            <x-ui.button href="{{ route('admin.barang.create') }}" icon="plus">
                Tambah Barang
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.panel title="Daftar Barang" description="Seluruh barang yang tersimpan di database RedStock."
        :badge="count($barangs) . ' Barang'" :flush="true">
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

        @if (count($barangs) > 0)
            <x-ui.table-search placeholder="Cari nama barang, kategori, atau satuan..." label="Cari data barang"
                :total="count($barangs)" />
        @endif

        <div class="rs-table-wrap">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Satuan</th>
                        <th scope="col" class="is-right">Harga</th>
                        <th scope="col">Stok</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($barangs as $barang)
                        <tr data-table-row>
                            <td>
                                <span class="rs-mono">#{{ $barang->id }}</span>
                            </td>

                            <td>
                                <span class="rs-chip">{{ $barang->kategori }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-primary">{{ $barang->nama_barang }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-muted">{{ $barang->satuan ?? '-' }}</span>
                            </td>

                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $barang->harga_barang, 0, ',', '.') }}
                                </span>
                            </td>

                            <td>
                                @if ((int) $barang->stok > 0)
                                    <span class="rs-chip">{{ (int) $barang->stok }}</span>
                                @else
                                    <span class="rs-cell-muted">0</span>
                                @endif
                            </td>

                            <td>
                                @if ($barang->status_barang === 'Aktif')
                                    <x-ui.badge variant="success" dot>Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="red" dot>Tidak Aktif</x-ui.badge>
                                @endif
                            </td>
                            <td>
                                <div class="rs-table-actions">
                                    <x-ui.button
                                        href="{{ route('admin.barang.edit', $barang->id) }}"
                                        variant="secondary"
                                        size="sm"
                                        icon="settings"
                                    >
                                        Edit
                                    </x-ui.button>

                                    <form
                                        action="{{ route('admin.barang.destroy', $barang->id) }}"
                                        method="POST"
                                        data-confirm="Data barang yang dihapus tidak dapat dikembalikan."
                                        data-confirm-title="Hapus Barang?"
                                        data-confirm-label="Ya, Hapus"
                                        data-confirm-variant="danger"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <x-ui.button type="submit" variant="danger" size="sm" icon="x">
                                            Hapus
                                        </x-ui.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-ui.empty-state icon="box" title="Belum ada data barang"
                                    description="Tambahkan barang pertama melalui tombol Tambah Barang." />
                            </td>
                        </tr>
                    @endforelse

                    <tr data-table-search-empty hidden>
                        <td colspan="8">
                            <x-ui.empty-state icon="search" title="Barang tidak ditemukan"
                                description="Coba kata kunci lain atau bersihkan pencarian." />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-ui.panel>
</x-layouts.admin>
