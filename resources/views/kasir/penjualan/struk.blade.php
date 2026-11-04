<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Penjualan #{{ $penjualan->idpenjualan }} | RedStock</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: ui-sans-serif, system-ui, "Segoe UI", Arial, sans-serif;
            background: #0b0f1c;
            color: #111827;
            padding: 24px 16px;
        }

        .rs-print-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .rs-print-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            background: #ff4052;
            color: #ffffff;
        }

        .rs-print-btn.is-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #e5e7eb;
            border: 1px solid rgba(255, 255, 255, 0.14);
        }

        .rs-receipt {
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 22px 18px;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.55);
            font-size: 13px;
            line-height: 1.5;
        }

        .rs-receipt-head {
            text-align: center;
            margin-bottom: 4px;
        }

        .rs-receipt-logo {
            width: 54px;
            height: 54px;
            object-fit: contain;
            display: block;
            margin: 0 auto 6px;
        }

        .rs-receipt-brand {
            font-size: 21px;
            font-weight: 800;
            letter-spacing: 0.14em;
        }

        .rs-receipt-sub {
            font-size: 10px;
            letter-spacing: 0.32em;
            text-transform: uppercase;
            color: #6b7280;
            margin-top: 2px;
        }

        .rs-receipt hr {
            border: none;
            border-top: 1px dashed #9ca3af;
            margin: 12px 0;
        }

        .rs-receipt-meta {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 12px;
        }

        .rs-receipt-meta .row {
            display: flex;
            justify-content: space-between;
            gap: 14px;
        }

        .rs-receipt-meta .label {
            color: #6b7280;
        }

        .rs-receipt-meta .value {
            font-weight: 600;
            text-align: right;
        }

        .rs-receipt table {
            width: 100%;
            border-collapse: collapse;
        }

        .rs-receipt td {
            padding: 5px 0;
            vertical-align: top;
            font-size: 12px;
        }

        .rs-receipt .item-name {
            font-weight: 600;
        }

        .rs-receipt .item-detail {
            color: #6b7280;
            font-size: 11px;
        }

        .rs-receipt .num {
            text-align: right;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .rs-receipt-totals {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 12px;
        }

        .rs-receipt-totals .row {
            display: flex;
            justify-content: space-between;
            gap: 14px;
        }

        .rs-receipt-totals .row.is-total {
            font-size: 16px;
            font-weight: 800;
            margin-top: 4px;
        }

        .rs-receipt-foot {
            text-align: center;
            color: #6b7280;
            font-size: 11px;
            margin-top: 10px;
        }

        .rs-receipt-foot .thanks {
            color: #111827;
            font-weight: 600;
            margin-bottom: 2px;
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 4mm;
            }

            body {
                background: #ffffff;
                padding: 0;
            }

            .rs-print-actions {
                display: none;
            }

            .rs-receipt {
                max-width: none;
                width: 72mm;
                margin: 0;
                padding: 0;
                border-radius: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="rs-print-actions">
        <button type="button" class="rs-print-btn" onclick="window.print()">Cetak Lagi</button>
        <a class="rs-print-btn is-secondary" href="{{ route('kasir.penjualan.detail', $penjualan->idpenjualan) }}">
            Kembali
        </a>
    </div>

    <main class="rs-receipt">
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
            <div class="row">
                <span class="label">No. Transaksi</span>
                <span class="value">#{{ $penjualan->idpenjualan }}</span>
            </div>

            <div class="row">
                <span class="label">Tanggal</span>
                <span class="value">
                    {{ $penjualan->created_at
                        ? \Illuminate\Support\Carbon::parse($penjualan->created_at)->translatedFormat('d M Y, H:i')
                        : '-' }}
                </span>
            </div>

            <div class="row">
                <span class="label">Kasir</span>
                <span class="value">{{ $penjualan->kasir }}</span>
            </div>

            <div class="row">
                <span class="label">Status</span>
                <span class="value">
                    {{ $penjualan->status === 'S' ? 'Selesai' : 'Pending' }}
                </span>
            </div>

            <div class="row">
                <span class="label">Margin</span>
                <span class="value">{{ $penjualan->margin_persen ?? 0 }}%</span>
            </div>
        </div>

        <hr>

        <table>
            <tbody>
                @foreach ($detail as $row)
                    <tr>
                        <td>
                            <div class="item-name">{{ $row->nama_barang }}</div>
                            <div class="item-detail">
                                {{ (int) $row->jumlah }} x
                                Rp {{ number_format((float) $row->harga_satuan, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="num">
                            Rp {{ number_format((float) $row->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr>

        <div class="rs-receipt-totals">
            <div class="row">
                <span>Subtotal</span>
                <span class="num">Rp {{ number_format((float) $penjualan->subtotal_nilai, 0, ',', '.') }}</span>
            </div>

            <div class="row">
                <span>PPN</span>
                <span class="num">Rp {{ number_format((float) $penjualan->ppn, 0, ',', '.') }}</span>
            </div>

            <div class="row is-total">
                <span>TOTAL</span>
                <span class="num">Rp {{ number_format((float) $penjualan->total_nilai, 0, ',', '.') }}</span>
            </div>
        </div>

        <hr>

        <footer class="rs-receipt-foot">
            <p class="thanks">Terima kasih telah berbelanja di RedStock.</p>
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
