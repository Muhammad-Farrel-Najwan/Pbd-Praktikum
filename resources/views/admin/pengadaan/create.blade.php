<x-layouts.admin
    title="Tambah Pengadaan"
    breadcrumb="RedStock / <span>Tambah Pengadaan</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Transaksi"
        title="Tambah Pengadaan"
        description="Catat pembelian barang dari vendor untuk menambah persediaan."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard.pengadaan') }}" variant="secondary" icon="arrow-left">
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

    <form method="POST" action="{{ route('admin.pengadaan.store') }}" id="pengadaan-form">
        @csrf

        <x-ui.panel
            title="Informasi Pengadaan"
            description="Pilih vendor yang akan memasok barang."
        >
            <div class="rs-form-grid">
                <x-ui.field
                    label="Vendor"
                    name="vendor_idvendor"
                    hint="Hanya vendor berstatus aktif yang ditampilkan."
                    :required="true"
                    class="rs-field-full"
                >
                    <select
                        @class(['rs-select', 'is-error' => $errors->has('vendor_idvendor')])
                        id="vendor_idvendor"
                        name="vendor_idvendor"
                        required
                    >
                        <option value="">-- Pilih Vendor --</option>

                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->idvendor }}" @selected((string) old('vendor_idvendor') === (string) $vendor->idvendor)>
                                {{ $vendor->nama_vendor }}
                            </option>
                        @endforeach
                    </select>
                </x-ui.field>
            </div>
        </x-ui.panel>

        <x-ui.panel
            title="Pilih Barang Pengadaan"
            description="Centang barang yang dibeli, lalu tentukan jumlahnya."
            :badge="count($barang) . ' Barang'"
            :flush="true"
            class="rs-mt-panel"
        >
            @if (count($barang) > 0)
                <x-ui.table-search
                    placeholder="Cari nama barang..."
                    label="Cari barang yang akan dibeli"
                    :total="count($barang)"
                />
            @endif

            <div class="rs-table-wrap">
                <table class="rs-table">
                    <thead>
                        <tr>
                            <th scope="col">Pilih</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col" class="is-right">Harga Satuan</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col" class="is-right">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($barang as $item)
                            @php
                                $dipilih = in_array($item->idbarang, old('barang_idbarang', []));
                            @endphp

                            <tr data-table-row>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="rs-checkbox pilih-barang"
                                        name="barang_idbarang[]"
                                        value="{{ $item->idbarang }}"
                                        data-harga="{{ (int) $item->harga }}"
                                        aria-label="Pilih {{ $item->nama }}"
                                        @checked($dipilih)
                                    >
                                </td>

                                <td>
                                    <span class="rs-cell-primary">{{ $item->nama }}</span>
                                </td>

                                <td class="is-right">
                                    <span class="rs-price">
                                        Rp {{ number_format((float) $item->harga, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        class="rs-input rs-qty-input jumlah-barang"
                                        name="jumlah[{{ $item->idbarang }}]"
                                        value="{{ old('jumlah.' . $item->idbarang, 1) }}"
                                        min="1"
                                        step="1"
                                        aria-label="Jumlah {{ $item->nama }}"
                                        @disabled(! $dipilih)
                                    >
                                </td>

                                <td class="is-right">
                                    <span class="rs-price subtotal-barang">Rp 0</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-ui.empty-state
                                        icon="box"
                                        title="Belum ada barang aktif"
                                        description="Tambahkan barang melalui menu Data Barang terlebih dahulu."
                                    />
                                </td>
                            </tr>
                        @endforelse

                        <tr data-table-search-empty hidden>
                            <td colspan="5">
                                <x-ui.empty-state
                                    icon="search"
                                    title="Barang tidak ditemukan"
                                    description="Coba kata kunci lain atau bersihkan pencarian."
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-slot:footer>
                <div class="rs-pengadaan-total">
                    <span>Total Pengadaan</span>
                    <strong id="total-pengadaan">Rp 0</strong>
                </div>

                <div class="rs-form-actions">
                    <x-ui.button href="{{ route('admin.dashboard.pengadaan') }}" variant="secondary">
                        Batal
                    </x-ui.button>

                    <x-ui.button type="submit" icon="check">
                        Simpan Pengadaan
                    </x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.panel>
    </form>
</x-layouts.admin>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.pilih-barang');
    const totalElement = document.getElementById('total-pengadaan');

    if (! totalElement) {
        return;
    }

    const formatRupiah = (angka) => 'Rp ' + angka.toLocaleString('id-ID');

    const hitungTotal = () => {
        let total = 0;

        checkboxes.forEach((checkbox) => {
            const row = checkbox.closest('tr');
            const jumlahInput = row.querySelector('.jumlah-barang');
            const subtotalElement = row.querySelector('.subtotal-barang');
            const harga = Number(checkbox.dataset.harga);
            const jumlah = Number(jumlahInput.value) || 0;
            const subtotal = checkbox.checked ? harga * jumlah : 0;

            subtotalElement.textContent = formatRupiah(subtotal);
            total += subtotal;
        });

        totalElement.textContent = formatRupiah(total);
    };

    checkboxes.forEach((checkbox) => {
        const row = checkbox.closest('tr');
        const jumlahInput = row.querySelector('.jumlah-barang');

        checkbox.addEventListener('change', () => {
            jumlahInput.disabled = ! checkbox.checked;

            if (checkbox.checked && Number(jumlahInput.value) < 1) {
                jumlahInput.value = 1;
            }

            hitungTotal();
        });

        jumlahInput.addEventListener('input', hitungTotal);
    });

    hitungTotal();
});
</script>
