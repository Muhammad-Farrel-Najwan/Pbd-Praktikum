<x-layouts.admin
    title="Data Retur"
    breadcrumb="RedStock / <span>Data Retur</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Transaksi"
        title="Data Retur"
        description="Kelola dan pantau seluruh transaksi retur barang kepada vendor."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard') }}" variant="secondary" icon="arrow-left">
                Dashboard
            </x-ui.button>

            <x-ui.button href="{{ route('admin.retur.create') }}" icon="plus">
                Tambah Retur
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.panel
        title="Daftar Retur"
        description="Seluruh transaksi retur yang tersimpan di database RedStock."
        :badge="count($retur) . ' Retur'"
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

        @if (count($retur) > 0)
            <x-ui.table-search
                placeholder="Cari ID, vendor, atau petugas..."
                label="Cari data retur"
                :total="count($retur)"
            />
        @endif

        <div class="rs-table-wrap">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Penerimaan</th>
                        <th scope="col">Vendor</th>
                        <th scope="col">Petugas</th>
                        <th scope="col" class="is-right">Jumlah Barang</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($retur as $item)
                        <tr data-table-row>
                            <td>
                                <span class="rs-mono">#{{ $item->idretur }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-muted">
                                    {{ $item->created_at
                                        ? \Illuminate\Support\Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i')
                                        : '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="rs-mono">#{{ $item->idpenerimaan }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-primary">{{ $item->nama_vendor }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-muted">{{ $item->username }}</span>
                            </td>

                            <td class="is-right">
                                <span class="rs-cell-muted">{{ (int) $item->total_barang }}</span>
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
                                        href="{{ route('admin.retur.detail', $item->idretur) }}"
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
                                    icon="rotate"
                                    title="Belum ada data retur"
                                    description="Catat retur barang pertama melalui tombol Tambah Retur."
                                />
                            </td>
                        </tr>
                    @endforelse

                    <tr data-table-search-empty hidden>
                        <td colspan="8">
                            <x-ui.empty-state
                                icon="search"
                                title="Retur tidak ditemukan"
                                description="Coba kata kunci lain atau bersihkan pencarian."
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-ui.panel>
</x-layouts.admin>
