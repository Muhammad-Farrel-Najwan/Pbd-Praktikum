<x-layouts.manager
    title="Detail Approval Pengadaan"
    breadcrumb="RedStock / <span>Detail Approval Pengadaan</span>"
    topbar-title="Panel Manager"
>
    <x-ui.page-header
        eyebrow="Analitik"
        title="Detail Pengadaan #{{ $pengadaan->idpengadaan }}"
        description="Tinjau rincian pengadaan sebelum memberikan approval."
    >
        <x-slot:actions>
            @if ($pengadaan->status === 'P')
                <form
                    action="{{ route('manager.approval.setujui', $pengadaan->idpengadaan) }}"
                    method="POST"
                    data-confirm="Pengadaan akan disetujui dan dapat diterima oleh petugas gudang."
                    data-confirm-title="Setujui Pengadaan?"
                    data-confirm-label="Ya, Setujui"
                    data-confirm-variant="primary"
                >
                    @csrf

                    <x-ui.button type="submit" icon="check">
                        Setujui
                    </x-ui.button>
                </form>
            @endif

            <x-ui.button href="{{ route('manager.approval.index') }}" variant="secondary" icon="arrow-left">
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
            title="Detail Barang Pengadaan"
            description="Daftar barang yang diajukan pada transaksi ini."
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
                            <th scope="col" class="is-right">Jumlah</th>
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
                                        Rp {{ number_format((float) $item->harga_satuan, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-cell-muted">{{ $item->jumlah }}</span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-price">
                                        Rp {{ number_format((float) $item->sub_total, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
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
                            <td colspan="4" class="is-right">
                                <span class="rs-cell-muted">Subtotal Pengadaan</span>
                            </td>
                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $pengadaan->subtotal_nilai, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4" class="is-right">
                                <span class="rs-cell-muted">PPN</span>
                            </td>
                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) ($pengadaan->ppn ?? 0), 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="4" class="is-right">
                                <span class="rs-cell-primary">Total Pengadaan</span>
                            </td>
                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $pengadaan->total_nilai, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-ui.panel>

        <x-ui.panel
            title="Informasi Pengadaan"
            description="Ringkasan data transaksi pengadaan."
        >
            <div class="rs-form-grid">
                <div class="rs-field rs-field-full">
                    <span class="rs-label">ID Pengadaan</span>
                    <span class="rs-cell-primary">#{{ $pengadaan->idpengadaan }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Tanggal Pengadaan</span>
                    <span class="rs-cell-muted">
                        {{ $pengadaan->created_at
                            ? \Illuminate\Support\Carbon::parse($pengadaan->created_at)->translatedFormat('d F Y, H:i')
                            : '-' }}
                    </span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Vendor</span>
                    <span class="rs-cell-primary">{{ $pengadaan->nama_vendor }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Petugas</span>
                    <span class="rs-cell-muted">{{ $pengadaan->username }}</span>
                </div>

                <div class="rs-field rs-field-full">
                    <span class="rs-label">Status</span>

                    @if ($pengadaan->status === 'P')
                        <div>
                            <x-ui.badge variant="warning" dot>Menunggu Approval</x-ui.badge>
                        </div>
                    @elseif ($pengadaan->status === 'S')
                        <div>
                            <x-ui.badge variant="success" dot>Disetujui</x-ui.badge>
                        </div>
                    @else
                        <div>
                            <x-ui.badge variant="muted">Belum ditentukan</x-ui.badge>
                        </div>
                    @endif
                </div>
            </div>

            @if ($pengadaan->status === 'S')
                <x-ui.alert variant="success">
                    Pengadaan ini sudah disetujui dan siap diproses melalui penerimaan barang.
                </x-ui.alert>
            @endif
        </x-ui.panel>
    </div>
</x-layouts.manager>
