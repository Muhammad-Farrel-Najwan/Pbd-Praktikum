<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Penjualan #{{ $penjualan->idpenjualan }} | RedStock</title>
    <script>
        (function () {
            try {
                var theme = window.localStorage.getItem('redstock:theme');

                document.documentElement.dataset.theme =
                    theme === 'light' ? 'light' : 'dark';
            } catch (error) {
                document.documentElement.dataset.theme = 'dark';
            }
        })();
    </script>

    @vite(['resources/css/receipt.css'])
</head>
<body>
    <div class="rs-print-actions">
        <button type="button" class="rs-print-btn" onclick="window.print()">Cetak Lagi</button>
        <a class="rs-print-btn is-secondary" href="{{ route('penjualan.penjualan.detail', $penjualan->idpenjualan) }}">
            Kembali
        </a>
    </div>

    <main class="rs-receipt">
    @if (($penjualan->status ?? null) === 'S')
        <span class="rs-receipt-stamp" aria-hidden="true">LUNAS</span>
    @endif
        <header class="rs-receipt-head">
            <img
                src="{{ asset('images/logo-mu.svg') }}"
                alt=""
                class="rs-receipt-logo"
                aria-hidden="true"
            >

            <h1 class="rs-receipt-brand">REDSTOCK</h1>
            <p class="rs-receipt-sub">Struk Penjualan</p>
        </header>

        <hr>

        <div class="rs-receipt-meta">
            <div class="rs-receipt-row">
                <span class="rs-receipt-label">No. Transaksi</span>
                <span class="rs-receipt-value">#{{ $penjualan->idpenjualan }}</span>
            </div>

            <div class="rs-receipt-row">
                <span class="rs-receipt-label">Tanggal</span>
                <span class="rs-receipt-value">
                    {{ $penjualan->created_at
                        ? \Illuminate\Support\Carbon::parse($penjualan->created_at)->translatedFormat('d M Y, H:i')
                        : '-' }}
                </span>
            </div>

            <div class="rs-receipt-row">
                <span class="rs-receipt-label">Kasir</span>
                <span class="rs-receipt-value">{{ $penjualan->kasir }}</span>
            </div>

            <div class="rs-receipt-row">
                <span class="rs-receipt-label">Status</span>
                <span class="rs-receipt-value">
                    {{ $penjualan->status === 'S' ? 'Selesai' : 'Pending' }}
                </span>
            </div>

            <div class="rs-receipt-row">
                <span class="rs-receipt-label">Margin</span>
                <span class="rs-receipt-value">{{ $penjualan->margin_persen ?? 0 }}%</span>
            </div>
        </div>

        <hr>

        <table>
            <tbody>
                @foreach ($detail as $row)
                    <tr>
                        <td>
                            <div class="rs-receipt-item-name">{{ $row->nama_barang }}</div>
                            <div class="rs-receipt-item-detail">
                                {{ (int) $row->jumlah }} x
                                Rp {{ number_format((float) $row->harga_satuan, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="rs-receipt-num">
                            Rp {{ number_format((float) $row->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr>

        <div class="rs-receipt-totals">
            <div class="rs-receipt-row">
                <span>Subtotal</span>
                <span class="rs-receipt-num">Rp {{ number_format((float) $penjualan->subtotal_nilai, 0, ',', '.') }}</span>
            </div>

            <div class="rs-receipt-row">
                <span>PPN</span>
                <span class="rs-receipt-num">Rp {{ number_format((float) $penjualan->ppn, 0, ',', '.') }}</span>
            </div>

            <div class="rs-receipt-row is-total">
                <span>TOTAL</span>
                <span class="rs-receipt-num">Rp {{ number_format((float) $penjualan->total_nilai, 0, ',', '.') }}</span>
            </div>
        </div>

        <hr>

        <footer class="rs-receipt-foot">
            <p class="rs-receipt-thanks">Terima kasih telah berbelanja di RedStock.</p>
            <p>Dicetak: {{ now()->translatedFormat('d M Y, H:i') }}</p>
        </footer>
    </main>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 350);
        });
    </script>
</body>
</html>
