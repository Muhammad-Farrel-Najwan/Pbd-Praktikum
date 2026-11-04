<x-layouts.admin
    title="Tambah Retur"
    breadcrumb="RedStock / <span>Tambah Retur</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Transaksi"
        title="Tambah Retur"
        description="Catat pengembalian barang kepada vendor berdasarkan penerimaan."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard.retur') }}" variant="secondary" icon="arrow-left">
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
        title="Pilih Penerimaan"
        description="Pilih penerimaan yang barangnya masih bisa diretur."
    >
        @if (count($penerimaan) > 0)
            <form method="GET" action="{{ route('admin.retur.create') }}" id="pilih-penerimaan-form">
                <div class="rs-form-grid">
                    <x-ui.field
                        label="Penerimaan"
                        name="penerimaan_idpenerimaan"
                        hint="Detail barang yang bisa diretur akan dimuat setelah penerimaan dipilih."
                        :required="true"
                        class="rs-field-full"
                    >
                        <select
                            @class(['rs-select', 'is-error' => $errors->has('penerimaan_idpenerimaan')])
                            id="penerimaan_idpenerimaan"
                            name="penerimaan_idpenerimaan"
                            onchange="this.form.submit()"
                            required
                        >
                            <option value="">-- Pilih Penerimaan --</option>

                            @foreach ($penerimaan as $item)
                                <option
                                    value="{{ $item->idpenerimaan }}"
                                    @selected((string) $idpenerimaan === (string) $item->idpenerimaan)
                                >
                                    Penerimaan #{{ $item->idpenerimaan }} -
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
                icon="rotate"
                title="Belum ada penerimaan yang bisa diretur"
                description="Semua penerimaan sudah diretur seluruhnya atau belum ada penerimaan yang selesai."
            />
        @endif
    </x-ui.panel>

    <form method="POST" action="{{ route('admin.retur.store') }}" id="retur-form">
        @csrf

        <input type="hidden" name="penerimaan_idpenerimaan" value="{{ $idpenerimaan }}">

        <x-ui.panel
            title="Barang yang Akan Diretur"
            description="Isi jumlah barang yang benar-benar diretur dan alasannya. Isi 0 jika barang tidak diretur."
            :badge="count($detailPenerimaan) . ' Barang'"
            :flush="true"
            class="rs-mt-panel"
        >
            <div class="rs-table-wrap">
                <table class="rs-table">
                    <thead>
                        <tr>
                            <th scope="col">Nama Barang</th>
                            <th scope="col" class="is-right">Jumlah Diterima</th>
                            <th scope="col" class="is-right">Sudah Diretur</th>
                            <th scope="col" class="is-right">Sisa</th>
                            <th scope="col">Jumlah Retur</th>
                            <th scope="col">Alasan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($detailPenerimaan as $item)
                            <tr>
                                <td>
                                    <span class="rs-cell-primary">{{ $item->nama }}</span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-cell-muted">{{ $item->jumlah_terima }}</span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-cell-muted">{{ (int) $item->sudah_diretur }}</span>
                                </td>

                                <td class="is-right">
                                    <x-ui.badge variant="info">{{ (int) $item->sisa }}</x-ui.badge>
                                </td>

                                <td>
                                    <input
                                        type="hidden"
                                        name="detail_penerimaan_iddetail_penerimaan[]"
                                        value="{{ $item->iddetail_penerimaan }}"
                                    >

                                    <input
                                        type="number"
                                        class="rs-input rs-qty-input"
                                        name="jumlah[]"
                                        min="0"
                                        max="{{ (int) $item->sisa }}"
                                        step="1"
                                        value="{{ old('jumlah.' . $loop->index, 0) }}"
                                        aria-label="Jumlah retur {{ $item->nama }}"
                                    >
                                </td>

                                <td>
                                    <input
                                        type="text"
                                        class="rs-input"
                                        name="alasan[]"
                                        maxlength="200"
                                        value="{{ old('alasan.' . $loop->index) }}"
                                        placeholder="Contoh: barang rusak"
                                        aria-label="Alasan retur {{ $item->nama }}"
                                    >
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <x-ui.empty-state
                                        icon="box"
                                        title="Belum ada barang yang dipilih"
                                        description="Pilih penerimaan terlebih dahulu untuk menampilkan barang yang bisa diretur."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-slot:footer>
                <p>
                    <strong>Perhatian:</strong> jumlah retur tidak boleh melebihi sisa, dan barang yang
                    diretur akan mengurangi stok setelah retur diproses.
                </p>

                <div class="rs-form-actions">
                    <x-ui.button href="{{ route('admin.dashboard.retur') }}" variant="secondary">
                        Batal
                    </x-ui.button>

                    @if (count($detailPenerimaan) > 0)
                        <x-ui.button type="submit" icon="check">
                            Simpan Retur
                        </x-ui.button>
                    @endif
                </div>
            </x-slot:footer>
        </x-ui.panel>
    </form>
</x-layouts.admin>
