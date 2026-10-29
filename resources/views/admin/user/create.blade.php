@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1>Tambah User</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.user.store') }}" method="POST">
        @csrf

        <div>
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                maxlength="45"
                value="{{ old('username') }}"
                required
            >
        </div>

        <div>
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                required
                autocomplete="new-password"
            >
            <small>Minimal 8 karakter.</small>
        </div>

        <div>
            <label for="password_confirmation">Konfirmasi Password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                minlength="8"
                required
                autocomplete="new-password"
            >
        </div>

        <div>
            <label for="role_idrole">Role</label>
            <select id="role_idrole" name="role_idrole" required>
                <option value="">-- Pilih Role --</option>

                @foreach ($roles as $role)
                    <option
                        value="{{ $role->idrole }}"
                        {{ old('role_idrole') == $role->idrole ? 'selected' : '' }}
                    >
                        {{ $role->nama_role }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit">Simpan User</button>
        <a href="{{ route('admin.dashboard.user') }}">Batal</a>
    </form>
</div>
@endsection
