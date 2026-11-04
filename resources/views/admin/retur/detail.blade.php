<x-layouts.admin
    title="Detail Retur"
    breadcrumb="RedStock / <span>Detail Retur</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Transaksi"
        title="Detail Retur"
        description="Informasi lengkap transaksi retur barang kepada vendor."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard.retur') }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

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

    <div class="rs-form-layout">
        <x-ui.panel
            title="Detail Barang Retur"
            description="Rincian barang yang dikembalikan pada transaksi ini."
            :badge="count($detail) . ' Barang'"
            :flush="true"
        >
            <div class="rs-table-wrap">
                <table class="rs-table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col" class="is-right">Jumlah Diterima</th>
                            <th scope="col" class="is-right">Jumlah Diretur</th>
                            <th scope="col">Alasan</th>
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
                                    <span class="rs-cell-muted">{{ $item->jumlah_terima }}</span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-cell-muted">{{ (int) $item->jumlah }}</span>
                                </td>

                                <td>
                                    <span class="rs-cell-muted">{{ $item->alasan ?: '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-ui.empty-state
                                        icon="box"
                                        title="Belum ada detail retur"
                                        description="Transaksi ini belum memiliki rincian barang."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="3" class="is-right">
                                <span class="rs-cell-primary">Total Barang Diretur</span>
                            </td>
                            <td class="is-right">
                                <span class="rs-cell-muted">{{ (int) $totalBarang }}</span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-ui.panel>

        <x-ui.panel
            title="Informasi Retur"
            description="Ringkasan data transaksi retur."
        >
            <div class="rs-form-grid">
                <div class="rs-field rs-field-full">
                    <span class="rs-label">ID Retur</span>
                    <span class="rs-cell-primary">#{{ $retur->idretur }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Tanggal Retur</span>
                    <span class="rs-cell-muted">
                        {{ $retur->created_at
                            ? \Illuminate\Support\Carbon::parse($retur->created_at)->translatedFormat('d F Y, H:i')
                            : '-' }}
                    </span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">ID Penerimaan</span>
                    <span class="rs-mono">#{{ $retur->idpenerimaan }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Vendor</span>
                    <span class="rs-cell-primary">{{ $retur->nama_vendor }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Petugas</span>
                    <span class="rs-cell-muted">{{ $retur->username }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Status</span>

                    @if ($retur->status === 'S')
                        <div>
                            <x-ui.badge variant="success">Selesai</x-ui.badge>
                        </div>
                    @elseif ($retur->status === 'P')
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

    @if ($retur->status === 'P')
        <x-ui.panel
            title="Pemrosesan Retur"
            description="Setelah diproses, stok barang akan berkurang dan retur tidak dapat diubah."
            class="rs-mt-panel"
        >
            <form
                method="POST"
                action="{{ route('admin.retur.proses', $retur->idretur) }}"
                data-confirm="Stok akan berkurang dan retur tidak dapat diubah setelah diproses."
                data-confirm-title="Selesaikan Retur?"
                data-confirm-label="Ya, Proses"
                data-confirm-variant="danger"
            >
                @csrf

                <x-ui.button type="submit" variant="danger" icon="check">
                    Proses dan Selesaikan Retur
                </x-ui.button>
            </form>
        </x-ui.panel>
    @else
        <x-ui.alert variant="success" class="rs-mt-panel">
            Retur sudah selesai. Barang telah dicatat sebagai stok keluar.
        </x-ui.alert>
    @endif
</x-layouts.admin>
