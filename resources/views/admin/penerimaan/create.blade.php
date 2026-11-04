<x-layouts.admin
    title="Tambah Penerimaan"
    breadcrumb="RedStock / <span>Tambah Penerimaan</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Transaksi"
        title="Tambah Penerimaan"
        description="Catat barang yang telah diterima dari vendor berdasarkan pengadaan."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard.penerimaan') }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if ($errors->any())
        <x-ui.alert variant="error">
            {{ $errors->first() }}
        </x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert variant="error">
            {{ session('error') }}
        </x-ui.alert>
    @endif

    <x-ui.panel
        title="Pilih Pengadaan"
        description="Pilih pengadaan yang barangnya belum diterima seluruhnya."
    >
        @if (count($pengadaan) > 0)
            <form method="GET" action="{{ route('admin.penerimaan.create') }}" id="pilih-pengadaan-form">
                <div class="rs-form-grid">
                    <x-ui.field
                        label="Pengadaan"
                        name="pengadaan_idpengadaan"
                        hint="Detail barang akan dimuat setelah pengadaan dipilih."
                        :required="true"
                        class="rs-field-full"
                    >
                        <select
                            @class(['rs-select', 'is-error' => $errors->has('pengadaan_idpengadaan')])
                            id="pengadaan_idpengadaan"
                            name="pengadaan_idpengadaan"
                            onchange="this.form.submit()"
                            required
                        >
                            <option value="">-- Pilih Pengadaan --</option>

                            @foreach ($pengadaan as $item)
                                <option
                                    value="{{ $item->idpengadaan }}"
                                    @selected((string) $idpengadaan === (string) $item->idpengadaan)
                                >
                                    #{{ $item->idpengadaan }} -
                                    {{ $item->nama_vendor }} -
                                    {{ $item->created_at
                                        ? \Illuminate\Support\Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i')
                                        : '-' }}
                                </option>
                            @endforeach
                        </select>
                    </x-ui.field>
                </div>

                <div class="rs-form-actions">
                    <x-ui.button type="submit" variant="secondary" icon="search">
                        Tampilkan Barang
                    </x-ui.button>
                </div>
            </form>
        @else
            <x-ui.empty-state
                icon="package"
                title="Belum ada pengadaan yang bisa diterima"
                description="Semua pengadaan sudah diterima seluruhnya atau belum ada transaksi pengadaan."
            />
        @endif
    </x-ui.panel>

    <form method="POST" action="{{ route('admin.penerimaan.store') }}" id="penerimaan-form">
        @csrf

        <input type="hidden" name="pengadaan_idpengadaan" value="{{ $idpengadaan }}">

        <x-ui.panel
            title="Barang yang Diterima"
            description="Isi jumlah barang yang benar-benar diterima. Isi 0 jika barang belum diterima."
            :badge="count($detailPengadaan) . ' Barang'"
            :flush="true"
            class="rs-mt-panel"
        >
            <div class="rs-table-wrap">
                <table class="rs-table">
                    <thead>
                        <tr>
                            <th scope="col">Nama Barang</th>
                            <th scope="col" class="is-right">Jumlah Pesan</th>
                            <th scope="col" class="is-right">Sudah Diterima</th>
                            <th scope="col" class="is-right">Sisa</th>
                            <th scope="col" class="is-right">Harga Satuan</th>
                            <th scope="col">Jumlah Diterima</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($detailPengadaan as $item)
                            @php
                                $sudahDiterima = (int) $item->jumlah - (int) $item->sisa_belum_diterima;
                            @endphp

                            <tr>
                                <td>
                                    <span class="rs-cell-primary">{{ $item->nama }}</span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-cell-muted">{{ $item->jumlah }}</span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-cell-muted">{{ $sudahDiterima }}</span>
                                </td>

                                <td class="is-right">
                                    <x-ui.badge variant="info">{{ (int) $item->sisa_belum_diterima }}</x-ui.badge>
                                </td>

                                <td class="is-right">
                                    <span class="rs-price">
                                        Rp {{ number_format((float) $item->harga_satuan, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td>
                                    <input
                                        type="hidden"
                                        name="barang_idbarang[]"
                                        value="{{ $item->barang_idbarang }}"
                                    >

                                    <input
                                        type="number"
                                        class="rs-input rs-qty-input"
                                        name="jumlah_terima[]"
                                        min="0"
                                        max="{{ (int) $item->sisa_belum_diterima }}"
                                        step="1"
                                        value="{{ old('jumlah_terima.' . $loop->index, (int) $item->sisa_belum_diterima) }}"
                                        aria-label="Jumlah diterima {{ $item->nama }}"
                                        required
                                    >
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <x-ui.empty-state
                                        icon="box"
                                        title="Belum ada barang yang dipilih"
                                        description="Pilih pengadaan terlebih dahulu untuk menampilkan barang yang belum diterima."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-slot:footer>
                <p>
                    <strong>Perhatian:</strong> jumlah diterima tidak boleh melebihi sisa pesanan.
                </p>

                <div class="rs-form-actions">
                    <x-ui.button href="{{ route('admin.dashboard.penerimaan') }}" variant="secondary">
                        Batal
                    </x-ui.button>

                    @if (count($detailPengadaan) > 0)
                        <x-ui.button type="submit" icon="check">
                            Simpan Penerimaan
                        </x-ui.button>
                    @endif
                </div>
            </x-slot:footer>
        </x-ui.panel>
    </form>
</x-layouts.admin>
