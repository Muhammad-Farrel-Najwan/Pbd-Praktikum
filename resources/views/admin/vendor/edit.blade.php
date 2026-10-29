<x-layouts.admin
    title="Edit Vendor"
    breadcrumb="RedStock / <span>Edit Vendor</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Master Data"
        title="Edit Vendor"
        description="Perbarui informasi vendor yang sudah terdaftar di RedStock."
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
            <form method="POST" action="{{ route('admin.vendor.update', $vendor->id) }}" id="vendor-form">
                @csrf
                @method('PUT')

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
                            value="{{ old('nama_vendor', $vendor->nama) }}"
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
                            <option value="P" @selected(old('badan_hukum', $badanHukum) === 'P')>PT</option>
                            <option value="C" @selected(old('badan_hukum', $badanHukum) === 'C')>CV</option>
                            <option value="U" @selected(old('badan_hukum', $badanHukum) === 'U')>UD / Perorangan</option>
                            <option value="K" @selected(old('badan_hukum', $badanHukum) === 'K')>Koperasi</option>
                            <option value="Y" @selected(old('badan_hukum', $badanHukum) === 'Y')>Yayasan</option>
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
                            <option value="A" @selected(old('status', $status) === 'A')>Aktif</option>
                            <option value="N" @selected(old('status', $status) === 'N')>Tidak Aktif</option>
                        </select>
                    </x-ui.field>
                </div>
            </form>

            <x-slot:footer>
                <p>
                    <strong>Perhatian:</strong> pastikan perubahan data vendor sudah benar sebelum disimpan.
                </p>

                <div class="rs-form-actions">
                    <x-ui.button href="{{ route('admin.dashboard.vendor') }}" variant="secondary">
                        Batal
                    </x-ui.button>

                    <x-ui.button type="submit" form="vendor-form" icon="check">
                        Simpan Perubahan
                    </x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.panel>
    </div>
</x-layouts.admin>
