@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1>Edit User</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('admin.user.update', $user->iduser) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <div>
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                maxlength="45"
                value="{{ old('username', $user->username) }}"
                required
            >
        </div>

        <div>
            <label for="role_idrole">Role</label>
            <select id="role_idrole" name="role_idrole" required>
                @foreach ($roles as $role)
                    <option
                        value="{{ $role->idrole }}"
                        {{ old('role_idrole', $user->role_idrole) == $role->idrole ? 'selected' : '' }}
                    >
                        {{ $role->nama_role }}
                    </option>
                @endforeach
            </select>
        </div>

        <hr>

        <h3>Ganti Password</h3>
        <p>Kosongkan kedua kolom password jika tidak ingin menggantinya.</p>

        <div>
            <label for="password">Password Baru</label>
            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                autocomplete="new-password"
            >
        </div>

        <div>
            <label for="password_confirmation">Konfirmasi Password Baru</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                minlength="8"
                autocomplete="new-password"
            >
        </div>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('admin.dashboard.user') }}">Batal</a>
    </form>
</div>
@endsection
