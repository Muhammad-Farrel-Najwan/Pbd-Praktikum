<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class AuthController extends Controller
{
    //buat tampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    //buat proses login nya

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Cari akun sekaligus nama role pengguna
        $users = DB::select(
            'SELECT
            u.iduser,
            u.username,
            u.password,
            u.role_idrole,
            r.nama_role
         FROM user AS u
         JOIN role AS r ON r.idrole = u.role_idrole
         WHERE u.username = ?
         LIMIT 1',
            [$request->input('username')]
        );

        if (
            empty($users) ||
            !Hash::check($request->input('password'), $users[0]->password)
        ) {
            return back()
                ->withErrors([
                    'username' => 'Username atau password salah.',
                ])
                ->onlyInput('username');
        }

        $user = $users[0];

        // Ubah nama role menjadi format slug
        $roleSlug = preg_replace(
            '/\s+/',
            '-',
            strtolower(trim($user->nama_role))
        );

        // Simpan identitas pengguna ke session
        $request->session()->regenerate();

        $request->session()->put([
            'iduser' => $user->iduser,
            'username' => $user->username,
            'role_idrole' => $user->role_idrole,
            'role_name' => $user->nama_role,
            'role_slug' => $roleSlug,
        ]);

        // Arahkan ke dashboard berdasarkan role
        $dashboardRoutes = [
            'admin' => 'admin.dashboard',
            'kasir' => 'kasir.dashboard',
            'petugas-gudang' => 'gudang.dashboard',
            'manager' => 'manager.dashboard',
            'staff-penjualan' => 'penjualan.dashboard',
        ];

        if (!isset($dashboardRoutes[$roleSlug])) {
            $request->session()->invalidate();

            return back()->withErrors([
                'username' => 'Role akun belum memiliki dashboard.',
            ]);
        }

        return redirect()->route($dashboardRoutes[$roleSlug]);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect()->route('login');
    }
}
