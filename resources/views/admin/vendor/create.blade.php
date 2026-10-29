<x-layouts.admin
    title="Tambah Vendor"
    breadcrumb="RedStock / <span>Tambah Vendor</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Master Data"
        title="Tambah Vendor"
        description="Masukkan vendor baru ke dalam sistem RedStock."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard.vendor') }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="rs-form-layout">
        <x-ui.panel
            title="Informasi Vendor"
            description="Kolom bertanda * wajib diisi."
        >
            <form method="POST" action="{{ route('admin.vendor.store') }}" id="vendor-form">
                @csrf

                <div class="rs-form-grid">
                    <x-ui.field
                        label="Nama Vendor"
                        name="nama_vendor"
                        hint="Maksimal 100 karakter."
                        :required="true"
                        class="rs-field-full"
                    >
                        <input
                            @class(['rs-input', 'is-error' => $errors->has('nama_vendor')])
                            type="text"
                            id="nama_vendor"
                            name="nama_vendor"
                            maxlength="100"
                            value="{{ old('nama_vendor') }}"
                            placeholder="Contoh: PT Maju Jaya"
                            required
                            autofocus
                        >
                    </x-ui.field>

                    <x-ui.field
                        label="Badan Usaha"
                        name="badan_hukum"
                        :required="true"
                        class="rs-field-full"
                    >
                        <select
                            @class(['rs-select', 'is-error' => $errors->has('badan_hukum')])
                            id="badan_hukum"
                            name="badan_hukum"
                            required
                        >
                            <option value="">-- Pilih Badan Usaha --</option>
                            <option value="P" @selected(old('badan_hukum') === 'P')>PT</option>
                            <option value="C" @selected(old('badan_hukum') === 'C')>CV</option>
                            <option value="U" @selected(old('badan_hukum') === 'U')>UD / Perorangan</option>
                            <option value="K" @selected(old('badan_hukum') === 'K')>Koperasi</option>
                            <option value="Y" @selected(old('badan_hukum') === 'Y')>Yayasan</option>
                        </select>
                    </x-ui.field>

                    <x-ui.field
                        label="Status"
                        name="status"
                        :required="true"
                        class="rs-field-full"
                    >
                        <select
                            @class(['rs-select', 'is-error' => $errors->has('status')])
                            id="status"
                            name="status"
                            required
                        >
                            <option value="">-- Pilih Status --</option>
                            <option value="A" @selected(old('status') === 'A')>Aktif</option>
                            <option value="N" @selected(old('status') === 'N')>Tidak Aktif</option>
                        </select>
                    </x-ui.field>
                </div>
            </form>

            <x-slot:footer>
                <p>
                    <strong>Perhatian:</strong> pastikan data vendor sudah benar sebelum disimpan.
                </p>

                <div class="rs-form-actions">
                    <x-ui.button href="{{ route('admin.dashboard.vendor') }}" variant="secondary">
                        Batal
                    </x-ui.button>

                    <x-ui.button type="submit" form="vendor-form" icon="check">
                        Simpan Vendor
                    </x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.panel>
    </div>
</x-layouts.admin>
