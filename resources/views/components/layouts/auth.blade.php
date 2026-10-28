@props([
    'title' => 'Login',
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script>
        document.documentElement.classList.add('js');
    </script>

    <title>{{ $title }} | RedStock</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="rs-auth-body">
    <div class="rs-bg" aria-hidden="true">
        <span class="rs-bg-grid"></span>
        <span class="rs-bg-glow rs-bg-glow-a"></span>
        <span class="rs-bg-glow rs-bg-glow-b"></span>
        <span class="rs-bg-scanlines"></span>
    </div>

    <main class="rs-auth">
        <section class="rs-auth-visual" aria-label="Tentang RedStock">
            <span class="rs-auth-orb rs-auth-orb-a" aria-hidden="true"></span>
            <span class="rs-auth-orb rs-auth-orb-b" aria-hidden="true"></span>

            <div class="rs-auth-radar" aria-hidden="true">
                <span class="rs-auth-radar-sweep"></span>
            </div>

            <div class="rs-auth-brand">
                <span class="rs-brand-mark" aria-hidden="true">R</span>

                <span class="rs-brand-text">
                    <span class="rs-brand-name">RED<span>STOCK</span></span>
                    <span class="rs-brand-caption">Inventory OS // Secure Access</span>
                </span>
            </div>

            <div class="rs-auth-visual-content">
                <span class="rs-hero-eyebrow">
                    <span aria-hidden="true"></span>
                    Sistem Informasi Manajemen Stok
                </span>

                <h2>Kendalikan stok.<br><span>Amankan operasi.</span></h2>

                <p>
                    Satu panel untuk memantau barang, transaksi, dan alur inventaris
                    RedStock secara terpusat.
                </p>

                <ul class="rs-auth-features">
                    <li class="rs-auth-feature">
                        <span class="rs-auth-feature-icon" aria-hidden="true">
                            <x-ui.icon name="shield" :size="14" />
                        </span>
                        Akses sesuai role dengan proteksi sesi.
                    </li>

                    <li class="rs-auth-feature">
                        <span class="rs-auth-feature-icon" aria-hidden="true">
                            <x-ui.icon name="activity" :size="14" />
                        </span>
                        Pantau pergerakan stok dan transaksi.
                    </li>

                    <li class="rs-auth-feature">
                        <span class="rs-auth-feature-icon" aria-hidden="true">
                            <x-ui.icon name="sliders" :size="14" />
                        </span>
                        Atur margin dan konfigurasi penjualan.
                    </li>
                </ul>

                <div class="rs-auth-roles">
                    <span class="rs-auth-roles-label">Role Access</span>

                    <ul class="rs-auth-role-list">
                        <li>
                            <x-ui.icon name="shield" :size="12" />
                            Administrator
                        </li>
                        <li>
                            <x-ui.icon name="receipt" :size="12" />
                            Kasir
                        </li>
                        <li>
                            <x-ui.icon name="warehouse" :size="12" />
                            Gudang
                        </li>
                        <li>
                            <x-ui.icon name="chart" :size="12" />
                            Manager
                        </li>
                        <li>
                            <x-ui.icon name="tag" :size="12" />
                            Penjualan
                        </li>
                    </ul>
                </div>
            </div>

            <div class="rs-auth-visual-footer">
                <span class="rs-status-chip">
                    <span class="rs-status-dot" aria-hidden="true"></span>
                    System Online
                </span>

                <span>Role Matrix // 05 Nodes</span>
            </div>
        </section>

        <section class="rs-auth-panel">
            <div class="rs-auth-card">
                <span class="rs-auth-card-scan" aria-hidden="true"></span>

                {{ $slot }}
            </div>
        </section>
    </main>

    @stack('scripts')
</body>
</html>
