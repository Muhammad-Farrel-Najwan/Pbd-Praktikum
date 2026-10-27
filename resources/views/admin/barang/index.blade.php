<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang | RedStock</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 32px;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }
        .container {
            max-width: 1200px;
            margin: auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }
        h1 { margin: 0 0 8px; font-size: 27px; }
        .muted { color: #64748b; }
        .button {
            display: inline-block;
            padding: 11px 16px;
            border-radius: 8px;
            background: #dc2626;
            color: white;
            text-decoration: none;
            font-weight: 600;
        }
        .panel {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }
        .table-wrapper { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th {
            padding: 15px 20px;
            background: #f8fafc;
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 16px 20px;
            border-top: 1px solid #e2e8f0;
        }
        tbody tr:hover { background: #fffafa; }
        .empty {
            padding: 32px;
            text-align: center;
            color: #64748b;
        }
        @media (max-width: 600px) {
            body { padding: 16px; }
            .header { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Data Barang</h1>
                <p class="muted">Kelola dan pantau data barang RedStock.</p>
            </div>
            <a class="button" href="{{ route('admin.dashboard') }}">
                Kembali ke Dashboard
            </a>
        </div>

        <div class="panel">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Jenis</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangs as $barang)
                            <tr>
                                <td>{{ $barang->idbarang }}</td>
                                <td>{{ $barang->jenis }}</td>
                                <td>{{ $barang->nama }}</td>
                                <td>{{ $barang->nama_satuan ?? '-' }}</td>
                                <td>Rp {{ number_format((float) $barang->harga, 0, ',', '.') }}</td>
                                <td>{{ $barang->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty">
                                    Belum ada data barang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
