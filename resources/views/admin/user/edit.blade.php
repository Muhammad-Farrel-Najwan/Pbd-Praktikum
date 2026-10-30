<x-layouts.admin
    title="Edit User"
    breadcrumb="RedStock / <span>Edit User</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Master Data"
        title="Edit User"
        description="Perbarui akun dan hak akses pengguna RedStock."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard.user') }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="rs-form-layout">
        <x-ui.panel
            title="Informasi User"
            description="Kolom bertanda * wajib diisi."
        >
            <form method="POST" action="{{ route('admin.user.update', $user->iduser) }}" id="user-form">
                @csrf
                @method('PUT')

                <div class="rs-form-grid">
                    <x-ui.field
                        label="Username"
                        name="username"
                        hint="Maksimal 45 karakter dan harus unik."
                        :required="true"
                        class="rs-field-full"
                    >
                        <input
                            @class(['rs-input', 'is-error' => $errors->has('username')])
                            type="text"
                            id="username"
                            name="username"
                            maxlength="45"
                            value="{{ old('username', $user->username) }}"
                            placeholder="Contoh: admin_redstock"
                            required
                            autofocus
                        >
                    </x-ui.field>

                    <x-ui.field
                        label="Role"
                        name="role_idrole"
                        :required="true"
                        class="rs-field-full"
                    >
                        <select
                            @class(['rs-select', 'is-error' => $errors->has('role_idrole')])
                            id="role_idrole"
                            name="role_idrole"
                            required
                        >
                            @foreach ($roles as $role)
                                <option
                                    value="{{ $role->idrole }}"
                                    @selected((string) old('role_idrole', $user->role_idrole) === (string) $role->idrole)
                                >
                                    {{ $role->nama_role }}
                                </option>
                            @endforeach
                        </select>
                    </x-ui.field>

                    <x-ui.field
                        label="Password Baru"
                        name="password"
                        hint="Kosongkan jika tidak ingin mengganti password."
                        class="rs-field-full"
                    >
                        <input
                            @class(['rs-input', 'is-error' => $errors->has('password')])
                            type="password"
                            id="password"
                            name="password"
                            minlength="8"
                            autocomplete="new-password"
                        >
                    </x-ui.field>

                    <x-ui.field
                        label="Konfirmasi Password Baru"
                        name="password_confirmation"
                        hint="Ulangi password baru yang sama."
                        class="rs-field-full"
                    >
                        <input
                            @class(['rs-input', 'is-error' => $errors->has('password_confirmation')])
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            minlength="8"
                            autocomplete="new-password"
                        >
                    </x-ui.field>
                </div>
            </form>

            <x-slot:footer>
                <p>
                    <strong>Perhatian:</strong> kosongkan kolom password jika tidak ingin menggantinya.
                </p>

                <div class="rs-form-actions">
                    <x-ui.button href="{{ route('admin.dashboard.user') }}" variant="secondary">
                        Batal
                    </x-ui.button>

                    <x-ui.button type="submit" form="user-form" icon="check">
                        Simpan Perubahan
                    </x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.panel>
    </div>
</x-layouts.admin>
