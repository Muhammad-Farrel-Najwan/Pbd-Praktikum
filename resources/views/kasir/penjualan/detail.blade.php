<x-layouts.kasir
    title="Detail Penjualan"
    breadcrumb="RedStock / <span>Detail Penjualan</span>"
    topbar-title="Panel Kasir"
>
    <x-ui.page-header
        eyebrow="Operasional"
        title="Detail Penjualan #{{ $penjualan->idpenjualan }}"
        description="Rincian barang yang terjual pada transaksi ini."
    >
        <x-slot:actions>
            <x-ui.button
                href="{{ route('kasir.penjualan.struk', $penjualan->idpenjualan) }}"
                variant="primary"
                icon="receipt"
            >
                Cetak Struk
            </x-ui.button>

            <x-ui.button href="{{ route('kasir.penjualan.index') }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="rs-form-layout">
        <x-ui.panel
            title="Informasi Transaksi"
            description="Ringkasan data transaksi penjualan."
        >
            <div class="rs-form-grid">
                <div class="rs-field rs-field-full">
                    <span class="rs-label">ID Penjualan</span>
                    <span class="rs-cell-primary">#{{ $penjualan->idpenjualan }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Tanggal Penjualan</span>
                    <span class="rs-cell-muted">
                        {{ $penjualan->created_at
                            ? \Illuminate\Support\Carbon::parse($penjualan->created_at)->translatedFormat('d F Y, H:i')
                            : '-' }}
                    </span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Kasir</span>
                    <span class="rs-cell-primary">{{ $penjualan->kasir }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Status</span>
                    @if ($penjualan->status === 'S')
                        <x-ui.badge variant="success" dot>Selesai</x-ui.badge>
                    @else
                        <x-ui.badge variant="warning" dot>Pending</x-ui.badge>
                    @endif
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Margin Penjualan</span>
                    <span class="rs-chip">{{ $penjualan->margin_persen ?? 0 }}%</span>
                </div>
            </div>
        </x-ui.panel>

        <x-ui.panel
            title="Rincian Barang"
            description="Daftar barang yang dijual pada transaksi ini."
            :badge="count($detail) . ' Barang'"
            :flush="true"
        >
            <div class="rs-table-wrap">
                <table class="rs-table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Barang</th>
                            <th scope="col">Satuan</th>
                            <th scope="col" class="is-right">Harga</th>
                            <th scope="col" class="is-right">Jumlah</th>
                            <th scope="col" class="is-right">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($detail as $row)
                            <tr>
                                <td>
                                    <span class="rs-mono">{{ $loop->iteration }}</span>
                                </td>

                                <td>
                                    <span class="rs-cell-primary">{{ $row->nama_barang }}</span>
                                </td>

                                <td>
                                    <span class="rs-cell-muted">{{ $row->nama_satuan ?? '-' }}</span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-price">
                                        Rp {{ number_format((float) $row->harga_satuan, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-mono">{{ (int) $row->jumlah }}</span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-price">
                                        Rp {{ number_format((float) $row->subtotal, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <x-ui.empty-state
                                        icon="box"
                                        title="Belum ada detail barang"
                                        description="Transaksi ini belum memiliki rincian barang."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="5" class="is-right">
                                <span class="rs-cell-muted">Subtotal Penjualan</span>
                            </td>
                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $penjualan->subtotal_nilai, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5" class="is-right">
                                <span class="rs-cell-muted">PPN</span>
                            </td>
                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $penjualan->ppn, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5" class="is-right">
                                <span class="rs-cell-primary">Total Penjualan</span>
                            </td>
                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $penjualan->total_nilai, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-ui.panel>
    </div>
</x-layouts.kasir>
