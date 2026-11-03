<x-layouts.admin
    title="Data Pengadaan"
    breadcrumb="RedStock / <span>Data Pengadaan</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Transaksi"
        title="Data Pengadaan"
        description="Kelola dan pantau seluruh transaksi pengadaan barang dari vendor."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard') }}" variant="secondary" icon="arrow-left">
                Dashboard
            </x-ui.button>

            <x-ui.button href="{{ route('admin.pengadaan.create') }}" icon="plus">
                Tambah Pengadaan
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.panel
        title="Daftar Pengadaan"
        description="Seluruh transaksi pengadaan yang tersimpan di database RedStock."
        :badge="count($pengadaan) . ' Pengadaan'"
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

        @if (count($pengadaan) > 0)
            <x-ui.table-search
                placeholder="Cari ID, vendor, atau petugas..."
                label="Cari data pengadaan"
                :total="count($pengadaan)"
            />
        @endif

        <div class="rs-table-wrap">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Vendor</th>
                        <th scope="col">Petugas</th>
                        <th scope="col" class="is-right">Subtotal</th>
                        <th scope="col" class="is-right">Total</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pengadaan as $item)
                        <tr data-table-row>
                            <td>
                                <span class="rs-mono">#{{ $item->idpengadaan }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-muted">
                                    {{ $item->created_at
                                        ? \Illuminate\Support\Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i')
                                        : '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="rs-cell-primary">{{ $item->nama_vendor }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-muted">{{ $item->username }}</span>
                            </td>

                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $item->subtotal_nilai, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $item->total_nilai, 0, ',', '.') }}
                                </span>
                            </td>

                            <td>
                                <div class="rs-table-actions">
                                    <x-ui.button
                                        href="{{ route('admin.pengadaan.detail', $item->idpengadaan) }}"
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
                            <td colspan="7">
                                <x-ui.empty-state
                                    icon="package"
                                    title="Belum ada data pengadaan"
                                    description="Tambahkan transaksi pengadaan pertama melalui tombol Tambah Pengadaan."
                                />
                            </td>
                        </tr>
                    @endforelse

                    <tr data-table-search-empty hidden>
                        <td colspan="7">
                            <x-ui.empty-state
                                icon="search"
                                title="Pengadaan tidak ditemukan"
                                description="Coba kata kunci lain atau bersihkan pencarian."
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-ui.panel>
</x-layouts.admin>
