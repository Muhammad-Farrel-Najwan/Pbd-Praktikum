<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function dataUser()
    {
        $users = DB::select(
            'SELECT id, nama_pengguna, peran
         FROM view_user
         ORDER BY id ASC'
        );

        return view('admin.user.index', [
            'users' => $users,
        ]);
    }

    public function createUser()
    {
        $roles = DB::select(
            'SELECT idrole, nama_role
         FROM role
         ORDER BY nama_role ASC'
        );

        return view('admin.user.create', [
            'roles' => $roles,
        ]);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:45',
            'password' => 'required|string|min:8|confirmed',
            'role_idrole' => 'required|integer',
        ]);

        $existing = DB::select(
            'SELECT iduser
         FROM `user`
         WHERE username = ?
         LIMIT 1',
            [$validated['username']]
        );

        if (! empty($existing)) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors([
                    'username' => 'Username sudah digunakan.',
                ]);
        }

        $role = DB::select(
            'SELECT idrole
         FROM role
         WHERE idrole = ?
         LIMIT 1',
            [$validated['role_idrole']]
        );

        if (empty($role)) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors([
                    'role_idrole' => 'Role yang dipilih tidak ditemukan.',
                ]);
        }

        try {
            DB::insert(
                'INSERT INTO `user` (username, password, role_idrole)
             VALUES (?, ?, ?)',
                [
                    $validated['username'],
                    Hash::make($validated['password']),
                    $validated['role_idrole'],
                ]
            );
        } catch (QueryException $e) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors([
                    'username' => 'User gagal disimpan. Periksa kembali data yang dimasukkan.',
                ]);
        }

        return redirect()
            ->route('admin.dashboard.user')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $users = DB::select(
            'SELECT iduser, username, role_idrole
         FROM `user`
         WHERE iduser = ?
         LIMIT 1',
            [$id]
        );

        if (empty($users)) {
            abort(404, 'Data user tidak ditemukan.');
        }

        $roles = DB::select(
            'SELECT idrole, nama_role
         FROM role
         ORDER BY nama_role ASC'
        );

        return view('admin.user.edit', [
            'user' => $users[0],
            'roles' => $roles,
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:45',
            'role_idrole' => 'required|integer',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = DB::select(
            'SELECT iduser
         FROM `user`
         WHERE iduser = ?
         LIMIT 1',
            [$id]
        );

        if (empty($user)) {
            abort(404, 'Data user tidak ditemukan.');
        }

        $duplicate = DB::select(
            'SELECT iduser
         FROM `user`
         WHERE username = ?
           AND iduser <> ?
         LIMIT 1',
            [$validated['username'], $id]
        );

        if (! empty($duplicate)) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors([
                    'username' => 'Username sudah digunakan oleh user lain.',
                ]);
        }

        $role = DB::select(
            'SELECT idrole
         FROM role
         WHERE idrole = ?
         LIMIT 1',
            [$validated['role_idrole']]
        );

        if (empty($role)) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors([
                    'role_idrole' => 'Role yang dipilih tidak ditemukan.',
                ]);
        }

        try {
            if (! empty($validated['password'])) {
                DB::update(
                    'UPDATE `user`
                 SET username = ?, role_idrole = ?, password = ?
                 WHERE iduser = ?',
                    [
                        $validated['username'],
                        $validated['role_idrole'],
                        Hash::make($validated['password']),
                        $id,
                    ]
                );
            } else {
                DB::update(
                    'UPDATE `user`
                 SET username = ?, role_idrole = ?
                 WHERE iduser = ?',
                    [
                        $validated['username'],
                        $validated['role_idrole'],
                        $id,
                    ]
                );
            }
        } catch (QueryException $e) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors([
                    'username' => 'User gagal diperbarui. Silakan periksa kembali data.',
                ]);
        }

        return redirect()
            ->route('admin.dashboard.user')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        $users = DB::select(
            'SELECT iduser
         FROM `user`
         WHERE iduser = ?
         LIMIT 1',
            [$id]
        );

        if (empty($users)) {
            abort(404, 'Data user tidak ditemukan.');
        }

        // Sesuaikan key session jika aplikasi menyimpan ID dengan nama lain.
        $loggedInUserId = session('iduser');

        if ($loggedInUserId !== null && (int) $loggedInUserId === (int) $id) {
            return redirect()
                ->route('admin.dashboard.user')
                ->with('error', 'Akun yang sedang digunakan tidak boleh dihapus.');
        }

        $relations = [
            ['margin_penjualan', 'user_iduser'],
            ['penerimaan', 'user_iduser'],
            ['pengadaan', 'user_iduser'],
            ['penjualan', 'user_iduser'],
            ['retur', 'user_iduser'],
        ];

        foreach ($relations as [$table, $column]) {
            $references = DB::select(
                "SELECT 1
             FROM {$table}
             WHERE {$column} = ?
             LIMIT 1",
                [$id]
            );

            if (! empty($references)) {
                return redirect()
                    ->route('admin.dashboard.user')
                    ->with(
                        'error',
                        'User tidak dapat dihapus karena masih tercatat dalam riwayat transaksi.'
                    );
            }
        }

        try {
            DB::delete(
                'DELETE FROM `user` WHERE iduser = ?',
                [$id]
            );
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.dashboard.user')
                ->with('error', 'User tidak dapat dihapus karena masih memiliki relasi data.');
        }

        return redirect()
            ->route('admin.dashboard.user')
            ->with('success', 'User berhasil dihapus.');
    }
}
