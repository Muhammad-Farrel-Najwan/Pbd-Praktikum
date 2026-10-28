<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin | RedStock</title>

    <style>
        :root {
            --primary: #dc2626;
            --primary-dark: #b91c1c;
            --primary-light: #fef2f2;
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #1e293b;
            --muted: #64748b;
            --border: #e2e8f0;
            --success: #15803d;
            --success-bg: #f0fdf4;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
        }

        button,
        input {
            font: inherit;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button {
            cursor: pointer;
        }

        .app {
            min-height: 100vh;
        }

        /* SIDEBAR */

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 250px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 20;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 25px 22px;
            border-bottom: 1px solid var(--border);
        }

        .brand-logo {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--primary);
            color: white;
            font-size: 22px;
            font-weight: 800;
        }

        .brand-name {
            font-size: 21px;
            font-weight: 800;
            letter-spacing: -0.8px;
        }

        .brand-name span {
            color: var(--primary);
        }

        .brand-caption {
            color: var(--muted);
            font-size: 11px;
            margin-top: 4px;
        }

        .nav-section {
            padding: 25px 14px 8px;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .nav-menu {
            padding: 0 12px;
            display: grid;
            gap: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 8px;
            color: #64748b;
            transition: 0.2s;
        }

        .nav-link:hover {
            background: #f8fafc;
            color: var(--primary);
        }

        .nav-link.active {
            color: var(--primary);
            background: var(--primary-light);
            font-weight: 700;
        }

        .nav-icon {
            width: 22px;
            text-align: center;
            font-size: 17px;
        }

        .nav-link.disabled {
            opacity: 0.55;
            cursor: default;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 16px;
            border-top: 1px solid var(--border);
        }

        .account-box {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 5px 16px;
        }

        .avatar {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #fee2e2;
            color: var(--primary-dark);
            font-weight: 800;
        }

        .account-name {
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .account-role {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        .logout-button {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 11px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: white;
            color: #475569;
            font-weight: 600;
        }

        .logout-button:hover {
            color: var(--primary);
            border-color: #fecaca;
            background: var(--primary-light);
        }

        /* MAIN CONTENT */

        .main {
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            min-height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 32px;
            background: white;
            border-bottom: 1px solid var(--border);
        }

        .breadcrumb {
            color: var(--muted);
            font-size: 12px;
            margin-bottom: 5px;
        }

        .breadcrumb span {
            color: var(--primary);
            font-weight: 700;
        }

        .topbar-title {
            font-size: 17px;
            font-weight: 750;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .online-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            font-size: 12px;
        }

        .online-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
        }

        .content {
            max-width: 1500px;
            margin: auto;
            padding: 32px;
        }

        /* PAGE HEADING */

        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 26px;
        }

        .page-heading h1 {
            font-size: 27px;
            letter-spacing: -0.8px;
            margin-bottom: 9px;
        }

        .page-heading p {
            color: var(--muted);
            line-height: 1.7;
        }

        .date-label {
            white-space: nowrap;
            padding: 10px 13px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: #475569;
            font-size: 12px;
        }

        /* WELCOME BANNER */

        .welcome-banner {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 27px 30px;
            margin-bottom: 26px;
            border-radius: 14px;
            color: white;
            background: linear-gradient(110deg, #b91c1c, #ef4444);
        }

        .welcome-banner h2 {
            font-size: 21px;
            margin-bottom: 9px;
        }

        .welcome-banner p {
            max-width: 560px;
            color: #fee2e2;
            line-height: 1.7;
        }

        .welcome-icon {
            flex-shrink: 0;
            display: grid;
            place-items: center;
            width: 72px;
            height: 72px;
            border: 1px solid #ffffff50;
            border-radius: 18px;
            background: #ffffff20;
            font-size: 35px;
        }

        /* STAT CARDS */

        .section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin: 30px 0 17px;
        }

        .section-heading h2 {
            font-size: 17px;
            font-weight: 750;
        }

        .section-description {
            margin-top: 6px;
            color: var(--muted);
            font-size: 12px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 17px;
        }

        .stat-card {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            min-width: 0;
            padding: 22px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
        }

        .stat-icon {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            width: 45px;
            height: 45px;
            border-radius: 11px;
            font-size: 21px;
        }

        .icon-red {
            background: #fef2f2;
            color: #dc2626;
        }

        .icon-green {
            background: #f0fdf4;
            color: #15803d;
        }

        .icon-blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .stat-label {
            color: var(--muted);
            font-size: 12px;
            margin-bottom: 9px;
        }

        .stat-value {
            font-size: 25px;
            font-weight: 800;
            letter-spacing: -0.7px;
        }

        .stat-note {
            margin-top: 7px;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.5;
        }

        /* MARGIN SECTION */

        .panel {
            margin-top: 28px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 22px 24px;
            border-bottom: 1px solid var(--border);
        }

        .panel-header h2 {
            font-size: 17px;
            margin-bottom: 7px;
        }

        .panel-header p {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.6;
        }

        .panel-badge {
            padding: 7px 10px;
            background: #f1f5f9;
            color: #475569;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .alert {
            margin: 20px 24px 0;
            padding: 13px 15px;
            border-radius: 8px;
            line-height: 1.6;
        }

        .alert-success {
            color: #166534;
            background: var(--success-bg);
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            color: #991b1b;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .margin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .margin-table th {
            padding: 15px 24px;
            background: #f8fafc;
            color: #64748b;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            font-weight: 800;
            white-space: nowrap;
        }

        .margin-table td {
            padding: 18px 24px;
            border-top: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .margin-table tbody tr {
            transition: background 0.15s;
        }

        .margin-table tbody tr:hover {
            background: #fafafa;
        }

        .margin-table tbody tr.selected-row {
            background: #fffafa;
        }

        .radio-cell {
            width: 48px;
        }

        .radio-input {
            width: 17px;
            height: 17px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .margin-percent {
            font-size: 15px;
            font-weight: 800;
        }

        .margin-subtitle {
            margin-top: 5px;
            color: var(--muted);
            font-size: 11px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 750;
            white-space: nowrap;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background: #f1f5f9;
            color: #64748b;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 20px 24px;
            border-top: 1px solid var(--border);
            background: #fcfcfd;
        }

        .table-footer p {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.7;
        }

        .save-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 9px;
            padding: 12px 19px;
            border: 0;
            border-radius: 8px;
            background: var(--primary);
            color: white;
            font-weight: 700;
            transition: background 0.2s;
        }

        .save-button:hover {
            background: var(--primary-dark);
        }

        .save-button:disabled {
            cursor: not-allowed;
            opacity: 0.55;
        }

        .empty-state {
            padding: 45px 20px;
            text-align: center;
            color: var(--muted);
        }

        .empty-state strong {
            display: block;
            color: var(--text);
            margin-bottom: 8px;
            font-size: 15px;
        }

        /* FOOTER */

        .footer {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 25px 0 10px;
            color: #94a3b8;
            font-size: 11px;
        }

        /* RESPONSIVE */

        @media (max-width: 1100px) {
            .sidebar {
                width: 220px;
            }

            .main {
                margin-left: 220px;
            }

            .content {
                padding: 25px 22px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .stat-card:last-child {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 760px) {
            .sidebar {
                position: static;
                width: 100%;
                border-right: 0;
                border-bottom: 1px solid var(--border);
            }

            .brand {
                padding: 16px 20px;
            }

            .nav-section {
                padding-top: 15px;
            }

            .nav-menu {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                padding-bottom: 14px;
            }

            .sidebar-bottom {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 20px;
            }

            .account-box {
                flex: 1;
                padding: 5px 0;
                min-width: 0;
            }

            .logout-form {
                width: 125px;
            }

            .main {
                margin-left: 0;
            }

            .topbar {
                padding: 15px 20px;
            }

            .content {
                padding: 23px 16px;
            }

            .page-heading {
                flex-direction: column;
            }

            .welcome-banner {
                padding: 22px;
            }

            .welcome-icon {
                width: 52px;
                height: 52px;
                font-size: 26px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card:last-child {
                grid-column: auto;
            }

            .panel-header {
                align-items: flex-start;
                padding: 19px;
            }

            .margin-table th,
            .margin-table td {
                padding: 15px;
            }

            .table-footer {
                align-items: stretch;
                flex-direction: column;
                padding: 18px;
            }

            .save-button {
                width: 100%;
            }

            .footer {
                flex-direction: column;
            }
        }

        @media (max-width: 420px) {
            .nav-menu {
                grid-template-columns: 1fr;
            }

            .online-indicator {
                display: none;
            }

            .welcome-icon {
                display: none;
            }

            .panel-badge {
                display: none;
            }

            .sidebar-bottom {
                align-items: flex-start;
                flex-direction: column;
            }

            .logout-form {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="app">

        {{-- SIDEBAR --}}
        <aside class="sidebar">
            <a href="{{ route('admin.dashboard') }}" class="brand">
                <div class="brand-logo">R</div>
                <div>
                    <div class="brand-name">Red<span>Stock</span></div>
                    <div class="brand-caption">Inventory Management System</div>
                </div>
            </a>

            <div class="nav-section">Menu Utama</div>

            <nav class="nav-menu">
                <a href="{{ route('admin.dashboard') }}" class="nav-link active">
                    <span class="nav-icon">▦</span>
                    Dashboard
                </a>

                <a href="#pengaturan-margin" class="nav-link">
                    <span class="nav-icon">％</span>
                    Margin Penjualan
                </a>

                {{-- Aktifkan tautan berikut setelah route fiturnya dibuat --}}
                <a href="{{ route('admin.dashboard.barang') }}" class="nav-link">
                    <span class="nav-icon">▤</span>
                    Data Barang
                </a>

                <span class="nav-link disabled" title="Halaman belum dibuat">
                    <span class="nav-icon">♧</span>
                    Data Satuan
                </span>

                <span class="nav-link disabled" title="Halaman belum dibuat">
                    <span class="nav-icon">♙</span>
                    Data Vendor
                </span>

                <span class="nav-link disabled" title="Halaman belum dibuat">
                    <span class="nav-icon">♧</span>
                    Manajemen User
                </span>
            </nav>

            <div class="sidebar-bottom">
                <div class="account-box">
                    <div class="avatar">
                        {{ strtoupper(substr(session('username', 'A'), 0, 1)) }}
                    </div>

                    <div style="min-width: 0;">
                        <div class="account-name">
                            {{ session('username', 'Admin') }}
                        </div>
                        <div class="account-role">Administrator</div>
                    </div>
                </div>

                <form class="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-button">
                        <span>↪</span>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN --}}
        <main class="main">

            {{-- TOPBAR --}}
            <header class="topbar">
                <div>
                    <div class="breadcrumb">
                        RedStock / <span>Dashboard Admin</span>
                    </div>
                    <div class="topbar-title">Panel Administrator</div>
                </div>

                <div class="topbar-right">
                    <div class="online-indicator">
                        <span class="online-dot"></span>
                        Sistem Manajemen Stok
                    </div>

                    <div class="avatar">
                        {{ strtoupper(substr(session('username', 'A'), 0, 1)) }}
                    </div>
                </div>
            </header>

            <div class="content">

                {{-- PAGE HEADING --}}
                <section class="page-heading">
                    <div>
                        <h1>Dashboard Admin</h1>
                        <p>
                            Kelola data master dan konfigurasi sistem
                            inventaris RedStock dari satu tempat.
                        </p>
                    </div>

                    <div class="date-label">
                        ◷ &nbsp; {{ now()->translatedFormat('d F Y') }}
                    </div>
                </section>

                {{-- WELCOME BANNER --}}
                <section class="welcome-banner">
                    <div>
                        <h2>
                            Selamat datang, {{ session('username', 'Admin') }}!
                        </h2>
                        <p>
                            Pantau pengaturan sistem dan pastikan konfigurasi
                            inventaris tetap sesuai kebutuhan operasional.
                        </p>
                    </div>

                    <div class="welcome-icon">▦</div>
                </section>

                {{-- SUMMARY --}}
                <section>
                    <div class="section-heading">
                        <div>
                            <h2>Ringkasan Pengaturan</h2>
                            <p class="section-description">
                                Informasi konfigurasi margin penjualan saat ini.
                            </p>
                        </div>
                    </div>

                    <div class="stats-grid">
                        <article class="stat-card">
                            <div class="stat-icon icon-red">％</div>
                            <div>
                                <div class="stat-label">Pilihan Margin</div>
                                <div class="stat-value">
                                    {{ count($margins) }}
                                </div>
                                <div class="stat-note">
                                    Total opsi margin di database
                                </div>
                            </div>
                        </article>

                        <article class="stat-card">
                            <div class="stat-icon icon-green">✓</div>
                            <div>
                                <div class="stat-label">Margin Aktif</div>
                                <div class="stat-value">
                                    @if ($activeMargin !== null)
                                        @php
                                            $currentMargin = null;
                                            foreach ($margins as $item) {
                                                if ((string) $item->idmargin_penjualan === (string) $activeMargin) {
                                                    $currentMargin = $item;
                                                    break;
                                                }
                                            }
                                        @endphp

                                        {{ $currentMargin ? $currentMargin->persen . '%' : '—' }}
                                    @else
                                        —
                                    @endif
                                </div>
                                <div class="stat-note">
                                    Persentase margin yang berlaku
                                </div>
                            </div>
                        </article>

                        <article class="stat-card">
                            <div class="stat-icon icon-blue">⚙</div>
                            <div>
                                <div class="stat-label">Status Konfigurasi</div>
                                <div class="stat-value" style="font-size: 20px;">
                                    {{ $activeMargin !== null ? 'Tersedia' : 'Perlu Atur' }}
                                </div>
                                <div class="stat-note">
                                    {{ $activeMargin !== null ? 'Margin aktif ditemukan' : 'Belum ada margin aktif' }}
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                {{-- MARGIN MANAGEMENT --}}
                <section class="panel" id="pengaturan-margin">

                    <div class="panel-header">
                        <div>
                            <h2>Pengaturan Margin Penjualan</h2>
                            <p>
                                Pilih margin yang akan digunakan sistem.
                                Hanya satu margin yang boleh aktif.
                            </p>
                        </div>

                        <span class="panel-badge">
                            {{ count($margins) }} Opsi Margin
                        </span>
                    </div>

                    {{-- SUCCESS MESSAGE --}}
                    @if (session('success'))
                        <div class="alert alert-success">
                            ✓ &nbsp; {{ session('success') }}
                        </div>
                    @endif

                    {{-- ERROR MESSAGE --}}
                    @if ($errors->has('idmargin_penjualan'))
                        <div class="alert alert-error">
                            {{ $errors->first('idmargin_penjualan') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.dashboard.margin.update') }}">
                        @csrf

                        <div class="table-wrapper">
                            <table class="margin-table">
                                <thead>
                                    <tr>
                                        <th class="radio-cell">Pilih</th>
                                        <th>Persentase Margin</th>
                                        <th>Status</th>
                                        <th>Terakhir Diperbarui</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($margins as $margin)
                                        <tr class="{{ (int) $margin->status === 1 ? 'selected-row' : '' }}">
                                            <td class="radio-cell">
                                                <input class="radio-input" type="radio" name="idmargin_penjualan"
                                                    value="{{ $margin->idmargin_penjualan }}"
                                                    aria-label="Pilih margin {{ $margin->persen }} persen"
                                                    @checked((string) old('idmargin_penjualan', $activeMargin) === (string) $margin->idmargin_penjualan) required>
                                            </td>

                                            <td>
                                                <div class="margin-percent">
                                                    {{ number_format((float) $margin->persen, 0, ',', '.') }}%
                                                </div>
                                                <div class="margin-subtitle">
                                                    ID Margin: {{ $margin->idmargin_penjualan }}
                                                </div>
                                            </td>

                                            <td>
                                                @if ((int) $margin->status === 1)
                                                    <span class="status-badge status-active">
                                                        <span class="status-dot"></span>
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="status-badge status-inactive">
                                                        <span class="status-dot"></span>
                                                        Nonaktif
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                <span style="color: #64748b; font-size: 12px;">
                                                    {{ $margin->updated_at
                                                        ? \Illuminate\Support\Carbon::parse($margin->updated_at)->format('d/m/Y H:i')
                                                        : 'Belum tersedia' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">
                                                <div class="empty-state">
                                                    <strong>Belum ada data margin</strong>
                                                    Tambahkan data margin ke database terlebih dahulu.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if (count($margins) > 0)
                            <div class="table-footer">
                                <p>
                                    <strong>Perhatian:</strong>
                                    margin yang dipilih akan menjadi margin aktif.
                                    Perubahan diterapkan setelah tombol simpan ditekan.
                                </p>

                                <button type="submit" class="save-button">
                                    <span>✓</span>
                                    Simpan Perubahan
                                </button>
                            </div>
                        @endif
                    </form>
                </section>

                {{-- FOOTER --}}
                <footer class="footer">
                    <span>© {{ now()->year }} RedStock</span>
                    <span>Sistem Informasi Manajemen Stok Barang</span>
                </footer>

            </div>
        </main>
    </div>
</body>

</html>
