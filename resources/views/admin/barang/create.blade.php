<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang | RedStock</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 32px;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        .container {
            max-width: 700px;
            margin: auto;
        }

        .panel {
            padding: 28px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }

        h1 {
            margin-top: 0;
        }

        .description {
            color: #64748b;
            margin-bottom: 26px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input,
        select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font: inherit;
        }

        input:focus,
        select:focus {
            outline: 2px solid #fecaca;
            border-color: #dc2626;
        }

        .error {
            margin-top: 6px;
            color: #dc2626;
            font-size: 13px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 28px;
        }

        .button {
            display: inline-block;
            padding: 11px 16px;
            border: 0;
            border-radius: 7px;
            text-decoration: none;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }

        .primary {
            background: #dc2626;
            color: white;
        }

        .secondary {
            background: #f1f5f9;
            color: #334155;
        }

        @media (max-width: 600px) {
            body {
                padding: 16px;
            }

            .panel {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="panel">
            <h1>Tambah Barang</h1>
            <p class="description">
                Masukkan informasi barang baru ke dalam sistem RedStock.
            </p>

            <form method="POST" action="{{ route('admin.barang.store') }}">
                @csrf

                <div class="form-group">
                    <label for="jenis">Jenis Barang</label>
                    <input type="text" id="jenis" name="jenis" maxlength="1" value="{{ old('jenis') }}"
                        placeholder="Masukkan satu karakter jenis" required>
                    @error('jenis')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nama">Nama Barang</label>
                    <input type="text" id="nama" name="nama" maxlength="45" value="{{ old('nama') }}"
                        placeholder="Masukkan nama barang" required>
                    @error('nama')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="satuan_idsatuan">Satuan</label>
                    <select id="satuan_idsatuan" name="satuan_idsatuan" required>
                        <option value="">-- Pilih Satuan --</option>

                        @foreach ($satuans as $satuan)
                            <option value="{{ $satuan->idsatuan }}" @selected((string) old('satuan_idsatuan') === (string) $satuan->idsatuan)>
                                {{ $satuan->nama_satuan }}
                            </option>
                        @endforeach
                    </select>

                    @error('satuan_idsatuan')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" id="harga" name="harga" min="0" step="1"
                        value="{{ old('harga') }}" placeholder="Masukkan harga" required>
                    @error('harga')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="1" @selected(old('status', '1') === '1')>
                            Aktif
                        </option>
                        <option value="0" @selected(old('status') === '0')>
                            Nonaktif
                        </option>
                    </select>

                    @error('status')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="actions">
                    <button type="submit" class="button primary">
                        Simpan Barang
                    </button>

                    <a href="{{ route('admin.barang.index') }}" class="button secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
