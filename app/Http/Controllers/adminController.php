<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class adminController extends Controller
{
    // dashboard
    public function index()
    {
        $margins = DB::select(
            '
        select idmargin_penjualan,
        persen,
        status,
        updated_at
        from margin_penjualan
        order by persen asc'
        );

        $activeMargin = null;

        foreach ($margins as $margin) {
            if ((int) $margin->status === 1) {
                $activeMargin = $margin->idmargin_penjualan;
                break;
            }
        }

        return view('admin.dashboard', [
            'margins' => $margins,
            'activeMargin' => $activeMargin,
        ]);
    }

    // ubah margin pake sp
    public function updateMargin(Request $request)
    {
        $request->validate([
            'idmargin_penjualan' => 'required|integer|min:1',
        ]);

        $idmargin = (int) $request->input('idmargin_penjualan');
        $iduser = $request->session()->get('iduser');

        // Pastikan margin tersedia
        $margin = DB::select(
            'SELECT idmargin_penjualan
         FROM margin_penjualan
         WHERE idmargin_penjualan = ?
         LIMIT 1',
            [$idmargin]
        );

        if (empty($margin)) {
            return back()->withErrors([
                'idmargin_penjualan' => 'Margin tidak ditemukan',
            ]);
        }

        try {
            DB::statement(
                'CALL sp_aktifkan_margin(?, ?)',
                [$idmargin, $iduser]
            );
        } catch (QueryException $e) {
            return back()->withErrors([
                'idmargin_penjualan' => 'Margin gagal diperbarui, silakan coba lagi.',
            ]);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Margin penjualan berhasil diperbarui');
    }

    // Tabel barang menggunakan VIEW
    public function dataBarang()
    {
        $barangs = DB::select(
            'SELECT id, kategori, nama_barang, satuan,
                status_barang, harga_barang
         FROM view_barang
         ORDER BY id ASC'
        );

        return view('admin.barang.index', [
            'barangs' => $barangs,
        ]);
    }

    // tabel tambah barang
    public function createBarang()
    {
        $satuans = DB::select(
            'select idsatuan,
            nama_satuan
            from satuan
            order by nama_satuan'
        );

        return view('admin.barang.create', [
            'satuans' => $satuans,
        ]);
    }

    // tambah barang
    public function storeBarang(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|string|size:1',
            'nama' => 'required|string|max:45',
            'satuan_idsatuan' => 'required|integer|min:1',
            'harga' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ]);
        // pastikan satuan yang terpilih aktif
        $satuan = DB::select(
            'select idsatuan
            from satuan
            where idsatuan = ?
            and status = 1
            limit 1',
            [$validated['satuan_idsatuan']]
        );

        if (empty($satuan)) {
            return back()
                ->withInput()
                ->withErrors([
                    'satuan_idsatuan' => 'Satuan tidak ditemukan atau tidak aktif',
                ]);
        }

        DB::insert(
            'insert into barang(jenis, nama, satuan_idsatuan, status, harga)
            values (?, ?, ?, ?, ?)',
            [
                $validated['jenis'],
                $validated['nama'],
                $validated['satuan_idsatuan'],
                $validated['status'],
                $validated['harga'],
            ]
        );

        return redirect()
            ->route('admin.dashboard.barang')
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function editBarang($id)
    {
        $barangs = DB::select(
            'SELECT id, kategori, nama_barang, satuan,
                status_barang, harga_barang
         FROM view_barang
         WHERE id = ?
         LIMIT 1',
            [$id]
        );

        if (empty($barangs)) {
            abort(404, 'Data barang tidak ditemukan.');
        }

        $dataAsli = DB::select(
            'SELECT jenis, nama, satuan_idsatuan, status, harga
         FROM barang
         WHERE idbarang = ?
         LIMIT 1',
            [$id]
        );

        $satuans = DB::select(
            'SELECT idsatuan, nama_satuan
         FROM satuan
         WHERE status = 1
            OR idsatuan = ?
         ORDER BY nama_satuan ASC',
            [$dataAsli[0]->satuan_idsatuan]
        );

        return view('admin.barang.edit', [
            'barang' => $barangs[0],
            'dataAsli' => $dataAsli[0],
            'satuans' => $satuans,
        ]);
    }

    public function updateBarang(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:1,2',
            'nama' => 'required|string|max:45',
            'satuan_idsatuan' => 'required|integer',
            'harga' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $barang = DB::select(
            'SELECT idbarang
         FROM barang
         WHERE idbarang = ?
         LIMIT 1',
            [$id]
        );

        if (empty($barang)) {
            abort(404, 'Data barang tidak ditemukan.');
        }

        $satuan = DB::select(
            'SELECT idsatuan
         FROM satuan
         WHERE idsatuan = ?
           AND status = 1
         LIMIT 1',
            [$validated['satuan_idsatuan']]
        );

        // Izinkan satuan lama tetap dipakai jika satuan itu sudah nonaktif.
        $satuanLama = DB::select(
            'SELECT satuan_idsatuan
         FROM barang
         WHERE idbarang = ?
         LIMIT 1',
            [$id]
        );

        $idSatuanBaru = (int) $validated['satuan_idsatuan'];
        $idSatuanLama = (int) $satuanLama[0]->satuan_idsatuan;

        if (empty($satuan) && $idSatuanBaru !== $idSatuanLama) {
            return back()
                ->withInput()
                ->withErrors([
                    'satuan_idsatuan' => 'Satuan baru harus berstatus aktif.',
                ]);
        }

        try {
            DB::update(
                'UPDATE barang
             SET jenis = ?,
                 nama = ?,
                 satuan_idsatuan = ?,
                 harga = ?,
                 status = ?
             WHERE idbarang = ?',
                [
                    $validated['jenis'],
                    $validated['nama'],
                    $idSatuanBaru,
                    $validated['harga'],
                    $validated['status'],
                    $id,
                ]
            );
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama' => 'Barang gagal diperbarui. Periksa kembali relasi data.',
                ]);
        }

        return redirect()
            ->route('admin.dashboard.barang')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroyBarang($id)
    {
        // Pastikan barang tersedia
        $barang = DB::select(
            'SELECT idbarang
         FROM barang
         WHERE idbarang = ?
         LIMIT 1',
            [$id]
        );

        if (empty($barang)) {
            abort(404, 'Data barang tidak ditemukan.');
        }

        // Tabel-tabel yang mungkin menyimpan riwayat barang
        $relasi = [
            'detail_pengadaan' => 'barang_idbarang',
            'detail_penerimaan' => 'barang_idbarang',
            'detail_penjualan' => 'barang_idbarang',
            'kartu_stok' => 'barang_idbarang',
        ];

        // Tolak penghapusan jika barang sudah digunakan
        foreach ($relasi as $tabel => $kolom) {
            $hasil = DB::select(
                "SELECT 1 FROM {$tabel}
             WHERE {$kolom} = ?
             LIMIT 1",
                [$id]
            );

            if (! empty($hasil)) {
                return redirect()
                    ->route('admin.dashboard.barang')
                    ->with(
                        'error',
                        'Barang tidak dapat dihapus karena sudah memiliki riwayat transaksi atau stok. Silakan nonaktifkan barang melalui fitur Edit.'
                    );
            }
        }

        // Hapus hanya jika tidak ada riwayat terkait
        try {
            DB::delete(
                'DELETE FROM barang WHERE idbarang = ?',
                [$id]
            );
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.dashboard.barang')
                ->with(
                    'error',
                    'Barang gagal dihapus karena masih terhubung dengan data lain.'
                );
        }

        return redirect()
            ->route('admin.dashboard.barang')
            ->with('success', 'Barang berhasil dihapus.');
    }

    // Tabel satuan menggunakan VIEW
    public function dataSatuan()
    {
        $satuans = DB::select(
            'SELECT id, nama, status_data
         FROM view_satuan
         ORDER BY id ASC'
        );

        return view('admin.satuan.index', [
            'satuans' => $satuans,
        ]);
    }

    // form tambah satuan
    public function createSatuan()
    {
        return view('admin.satuan.create');
    }

    // tambah satuan
    public function storeSatuan(Request $request)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:45',
            'status' => 'required|in:0,1',
        ]);

        try {
            DB::insert(
                'INSERT into satuan(nama_satuan, status)
                values(?, ?)',
                [
                    $validated['nama_satuan'],
                    $validated['status'],
                ]
            );
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama_satuan' => 'Satuan gagal disimpan. Silahkan coba lagi',
                ]);
        }

        return redirect()
            ->route('admin.dashboard.satuan')
            ->with('success', 'Satuan berhasil ditambahkan');
    }

    // form edit
    public function editSatuan($id)
    {
        $satuan = DB::select(
            'select idsatuan,
            nama_satuan,
            status
            from satuan
            where idsatuan = ?',
            [$id]
        );

        if (empty($satuan)) {
            abort(404, 'Data satuan tidak ditemukan');
        }

        return view('admin.satuan.edit', [
            'satuan' => $satuan[0],
        ]);
    }

    public function updateSatuan(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:45',
            'status' => 'required|in:0,1',
        ]);

        $satuan = DB::select(
            'select idsatuan,
            status
            from satuan
            where idsatuan = ?
            limit 1',
            [$id]
        );

        if (empty($satuan)) {
            abort(404, 'Data satuan tidak ditemukan');
        }
        $statusLama = (int) $satuan[0]->status;
        $statusBaru = $validated['status'];

        // aturan aktif nonaktif
        if ($statusLama === 1 && $statusBaru === 0) {
            $barang = DB::select(
                'select idbarang
                from satuan
                where satuan_idsatuan = ?
                limit 1',
                [$id]
            );

            if (empty($barang)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'status' => 'Satuan tidak bisa dinonaktifkan karena masih digunakan oleh barang.',
                    ]);
            }
        }

        try {
            DB::update(
                'update satuan
                set nama_satuan = ?,
                status = ?
                where idsatuan = ?',
                [
                    $validated['nama_satuan'],
                    $validated['status'],
                    $id,
                ]
            );
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama_satuan' => 'Gagal memperbarui satuan. Silakan coba lagi',
                ]);
        }

        return redirect()
            ->route('admin.dashboard.satuan')
            ->with('success', 'Satuan berhasil diperbarui');
    }

    // hapus satuan
    public function destroySatuan($id)
    {
        $satuan = DB::select(
            'select idsatuan
            from satuan
            where idsatuan = ?
            limit 1',
            [$id]
        );

        if (empty($satuan)) {
            abort(404, 'Data satuan tidak ditemukan');
        }

        try {
            DB::delete(
                'delete from satuan
                where idsatuan = ?',
                [$id]
            );
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.dashboard.satuan')
                ->with('error', 'Satuan tidak dapat dihapus karena masih digunakan oleh barang.');
        }

        return redirect()
            ->route('admin.dashboard.satuan')
            ->with('success', 'Satuan berhasil dihapus');
    }

    // Tabel vendor menggunakan VIEW
    public function dataVendor()
    {
        $vendors = DB::select(
            'SELECT id, nama, jenis_badan_usaha, status_vendor
         FROM view_vendor
         ORDER BY id ASC'
        );

        return view('admin.vendor.index', [
            'vendors' => $vendors,
        ]);
    }

    // Form tambah vendor
    public function createVendor()
    {
        return view('admin.vendor.create');
    }

    // Simpan vendor menggunakan SQL manual
    public function storeVendor(Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:100',
            'badan_hukum' => 'required|in:P,C,U,K,Y',
            'status' => 'required|in:A,N',
        ]);

        try {
            DB::insert(
                'INSERT INTO vendor (nama_vendor, badan_hukum, status)
             VALUES (?, ?, ?)',
                [
                    $validated['nama_vendor'],
                    $validated['badan_hukum'],
                    $validated['status'],
                ]
            );
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama_vendor' => 'Vendor gagal disimpan. Silakan coba lagi.',
                ]);
        }

        return redirect()
            ->route('admin.dashboard.vendor')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    // Form edit vendor
    public function editVendor($id)
    {
        $vendors = DB::select(
            'SELECT id, nama, jenis_badan_usaha, status_vendor
         FROM view_vendor
         WHERE id = ?
         LIMIT 1',
            [$id]
        );

        if (empty($vendors)) {
            abort(404, 'Data vendor tidak ditemukan.');
        }

        // Ambil kode asli badan hukum dan status dari tabel vendor
        $dataVendor = DB::select(
            'SELECT badan_hukum, status
         FROM vendor
         WHERE idvendor = ?
         LIMIT 1',
            [$id]
        );

        return view('admin.vendor.edit', [
            'vendor' => $vendors[0],
            'badanHukum' => $dataVendor[0]->badan_hukum,
            'status' => $dataVendor[0]->status,
        ]);
    }

    // Proses update vendor
    public function updateVendor(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:100',
            'badan_hukum' => 'required|in:P,C,U,K,Y',
            'status' => 'required|in:A,N',
        ]);

        $vendor = DB::select(
            'SELECT idvendor
         FROM vendor
         WHERE idvendor = ?
         LIMIT 1',
            [$id]
        );

        if (empty($vendor)) {
            abort(404, 'Data vendor tidak ditemukan.');
        }

        try {
            DB::update(
                'UPDATE vendor
             SET nama_vendor = ?,
                 badan_hukum = ?,
                 status = ?
             WHERE idvendor = ?',
                [
                    $validated['nama_vendor'],
                    $validated['badan_hukum'],
                    $validated['status'],
                    $id,
                ]
            );
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama_vendor' => 'Vendor gagal diperbarui. Silakan coba lagi.',
                ]);
        }

        return redirect()
            ->route('admin.dashboard.vendor')
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroyVendor($id)
    {
        // 1. Pastikan vendor tersedia
        $vendor = DB::select(
            'SELECT idvendor
         FROM vendor
         WHERE idvendor = ?
         LIMIT 1',
            [$id]
        );

        if (empty($vendor)) {
            abort(404, 'Data vendor tidak ditemukan.');
        }

        // 2. Periksa apakah vendor pernah digunakan dalam pengadaan
        $pengadaan = DB::select(
            'SELECT idpengadaan
         FROM pengadaan
         WHERE vendor_idvendor = ?
         LIMIT 1',
            [$id]
        );

        if (! empty($pengadaan)) {
            return redirect()
                ->route('admin.dashboard.vendor')
                ->with(
                    'error',
                    'Vendor tidak dapat dihapus karena sudah digunakan dalam riwayat pengadaan. Ubah statusnya menjadi tidak aktif.'
                );
        }

        // 3. Hapus vendor jika tidak memiliki riwayat pengadaan
        try {
            DB::delete(
                'DELETE FROM vendor WHERE idvendor = ?',
                [$id]
            );
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.dashboard.vendor')
                ->with('error', 'Vendor gagal dihapus karena masih memiliki relasi data.');
        }

        return redirect()
            ->route('admin.dashboard.vendor')
            ->with('success', 'Vendor berhasil dihapus.');
    }
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

        if (!empty($existing)) {
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

        if (!empty($duplicate)) {
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
            if (!empty($validated['password'])) {
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

            if (!empty($references)) {
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
