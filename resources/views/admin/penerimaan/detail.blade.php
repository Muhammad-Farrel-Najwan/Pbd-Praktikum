<x-layouts.admin
    title="Detail Penerimaan"
    breadcrumb="RedStock / <span>Detail Penerimaan</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Transaksi"
        title="Detail Penerimaan"
        description="Informasi lengkap transaksi penerimaan barang dari pengadaan."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard.penerimaan') }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>

            @if ($penerimaan->status === 'S')
                <x-ui.button
                    href="{{ route('admin.retur.create', ['penerimaan_idpenerimaan' => $penerimaan->idpenerimaan]) }}"
                    icon="rotate"
                >
                    Buat Retur
                </x-ui.button>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    <div class="rs-form-layout">
        <x-ui.panel
            title="Detail Barang Diterima"
            description="Rincian barang yang diterima pada transaksi ini."
            :badge="count($detail) . ' Barang'"
            :flush="true"
        >
            <div class="rs-table-wrap">
                <table class="rs-table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col" class="is-right">Harga Satuan</th>
                            <th scope="col" class="is-right">Jumlah Diterima</th>
                            <th scope="col" class="is-right">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($detail as $item)
                            <tr>
                                <td>
                                    <span class="rs-mono">{{ $loop->iteration }}</span>
                                </td>

                                <td>
                                    <span class="rs-cell-primary">{{ $item->nama }}</span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-price">
                                        Rp {{ number_format((float) $item->harga_satuan_terima, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-cell-muted">{{ $item->jumlah_terima }}</span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-price">
                                        Rp {{ number_format((float) $item->sub_total_terima, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-ui.empty-state
                                        icon="box"
                                        title="Belum ada detail penerimaan"
                                        description="Transaksi ini belum memiliki rincian barang."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="4" class="is-right">
                                <span class="rs-cell-primary">Total Penerimaan</span>
                            </td>
                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $totalPenerimaan, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-ui.panel>

        <x-ui.panel
            title="Informasi Penerimaan"
            description="Ringkasan data transaksi penerimaan."
        >
            <div class="rs-form-grid">
                <div class="rs-field rs-field-full">
                    <span class="rs-label">ID Penerimaan</span>
                    <span class="rs-cell-primary">#{{ $penerimaan->idpenerimaan }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Tanggal Penerimaan</span>
                    <span class="rs-cell-muted">
                        {{ $penerimaan->created_at
                            ? \Illuminate\Support\Carbon::parse($penerimaan->created_at)->translatedFormat('d F Y, H:i')
                            : '-' }}
                    </span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">ID Pengadaan</span>
                    <span class="rs-mono">#{{ $penerimaan->pengadaan_idpengadaan }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Vendor</span>
                    <span class="rs-cell-primary">{{ $penerimaan->nama_vendor }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Petugas</span>
                    <span class="rs-cell-muted">{{ $penerimaan->username }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Status</span>

                    @if ($penerimaan->status === 'S')
                        <div>
                            <x-ui.badge variant="success">Selesai</x-ui.badge>
                        </div>
                    @elseif ($penerimaan->status === 'P')
                        <div>
                            <x-ui.badge variant="warning">Proses</x-ui.badge>
                        </div>
                    @else
                        <div>
                            <x-ui.badge variant="muted">-</x-ui.badge>
                        </div>
                    @endif
                </div>
            </div>
        </x-ui.panel>
    </div>
</x-layouts.admin>
