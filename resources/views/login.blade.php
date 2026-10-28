<x-layouts.auth title="Login">
    <div class="rs-auth-terminal" aria-hidden="true">
        <span class="rs-auth-terminal-dots">
            <i></i>
            <i></i>
            <i></i>
        </span>

        <span class="rs-auth-terminal-label">Auth Gateway</span>

        <span class="rs-auth-terminal-status">
            <span class="rs-auth-pulse"></span>
            Ready
        </span>
    </div>

    <div class="rs-auth-card-head">
        <span class="rs-eyebrow">
            <span class="rs-eyebrow-dot" aria-hidden="true"></span>
            Secure Access
        </span>

        <h1>Akses Panel RedStock</h1>
        <p>Masukkan kredensial akun Anda untuk membuka panel sesuai peran.</p>
    </div>

    <form method="POST" action="{{ route('login.process') }}" class="rs-auth-form">
        @csrf

        <x-ui.field label="Username" name="username" :required="true">
            <div class="rs-input-group">
                <span class="rs-input-prefix" aria-hidden="true">
                    <x-ui.icon name="user" :size="17" />
                </span>

                <input
                    @class(['rs-input', 'is-error' => $errors->has('username')])
                    type="text"
                    id="username"
                    name="username"
                    value="{{ old('username') }}"
                    placeholder="Masukkan username"
                    autocomplete="username"
                    autocapitalize="none"
                    spellcheck="false"
                    required
                    autofocus
                >
            </div>
        </x-ui.field>

        <x-ui.field label="Password" name="password" :required="true">
            <div class="rs-input-group has-action">
                <span class="rs-input-prefix" aria-hidden="true">
                    <x-ui.icon name="lock" :size="17" />
                </span>

                <input
                    @class(['rs-input', 'is-error' => $errors->has('password')])
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                >

                <button
                    type="button"
                    class="rs-input-action"
                    data-password-toggle="password"
                    data-visible="false"
                    aria-label="Tampilkan password"
                >
                    <x-ui.icon name="eye" class="rs-eye-show" :size="17" />
                    <x-ui.icon name="eye-off" class="rs-eye-hide" :size="17" />
                </button>
            </div>
        </x-ui.field>

        <x-ui.button type="submit" size="lg" icon="lock" class="rs-btn-block">
            Masuk
        </x-ui.button>
    </form>

    <div class="rs-auth-meta">
        <span>
            <x-ui.icon name="shield" :size="13" />
            Sesi terproteksi
        </span>

        <span>Neon Core // v1.0</span>
    </div>
</x-layouts.auth>
