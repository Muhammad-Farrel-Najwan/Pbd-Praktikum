@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1>Manajemen User</h1>
    <p>Kelola akun dan hak akses pengguna RedStock.</p>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <a href="{{ route('admin.user.create') }}">
        + Tambah User
    </a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->nama_pengguna }}</td>
                    <td>{{ $user->peran }}</td>
                    <td>
                        <a href="{{ route('admin.user.edit', $user->id) }}">
                            Edit
                        </a>

                        <form
                            action="{{ route('admin.user.destroy', $user->id) }}"
                            method="POST"
                            style="display: inline;"
                            onsubmit="return confirm('Yakin ingin menghapus user ini?');"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Belum ada data user.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
