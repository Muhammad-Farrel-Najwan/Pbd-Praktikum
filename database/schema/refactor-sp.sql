/* ============================================================
   Refactor routine: 5 PROCEDURE + 2 FUNCTION
   Tidak mengubah struktur tabel / trigger / view.
   Konsep B: SP transaksi memakai OUT parameter + IF/ELSE tanpa SIGNAL.
   Setiap SP transaksi mengembalikan:
       p_status  = 'S' (sukses) / 'E' (gagal)
       p_message = pesan untuk controller

   Setiap SP transaksi memiliki 2 DML:
   1. normalisasi nilai detail
   2. finalisasi header (memicu trigger kartu_stok)

   sp_aktifkan_margin: satu margin aktif, margin lain dimatikan.
   ============================================================ */

SET NAMES utf8mb4;

-- FUNCTION: harga jual dari margin aktif
DROP FUNCTION IF EXISTS `fn_hitung_harga_jual`;
DELIMITER ;;
CREATE FUNCTION `fn_hitung_harga_jual`(p_harga INT)
RETURNS DECIMAL(15,2)
READS SQL DATA
BEGIN
    DECLARE v_margin DECIMAL(5,2) DEFAULT NULL;

    SELECT persen
    INTO v_margin
    FROM margin_penjualan
    WHERE status = 1;

    IF v_margin IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Margin penjualan aktif tidak ditemukan';
    END IF;

    RETURN ROUND(p_harga + (p_harga * v_margin / 100), 0);
END ;;
DELIMITER ;

-- FUNCTION: PPN 11%
DROP FUNCTION IF EXISTS `fn_hitung_ppn`;
DELIMITER ;;
CREATE FUNCTION `fn_hitung_ppn`(p_subtotal INT)
RETURNS INT
DETERMINISTIC
BEGIN
    RETURN ROUND(p_subtotal * 0.11);
END ;;
DELIMITER ;

-- PROCEDURE: selesaikan pengadaan
DROP PROCEDURE IF EXISTS `sp_selesai_pengadaan`;
DELIMITER ;;
CREATE PROCEDURE `sp_selesai_pengadaan`(
    IN p_idpengadaan BIGINT,
    OUT p_status CHAR(1),
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_idpengadaan BIGINT DEFAULT NULL;
    DECLARE v_status CHAR(1) DEFAULT NULL;
    DECLARE v_vendor_idvendor INT DEFAULT NULL;
    DECLARE v_jumlah_detail INT DEFAULT 0;
    DECLARE v_invalid INT DEFAULT 0;
    DECLARE v_valid INT DEFAULT 1;

    SET p_status = 'E';
    SET p_message = 'Pengadaan gagal disetujui';

    SELECT idpengadaan, status, vendor_idvendor
    INTO v_idpengadaan, v_status, v_vendor_idvendor
    FROM pengadaan
    WHERE idpengadaan = p_idpengadaan
    FOR UPDATE;

    IF v_idpengadaan IS NULL THEN
        SET v_valid = 0;
        SET p_message = 'Pengadaan tidak ditemukan';
    ELSEIF v_status = 'S' THEN
        SET v_valid = 0;
        SET p_message = 'Pengadaan sudah selesai diproses';
    ELSEIF v_status IS NULL OR v_status <> 'P' THEN
        SET v_valid = 0;
        SET p_message = 'Status pengadaan tidak valid';
    END IF;

    IF v_valid = 1 AND NOT EXISTS (
        SELECT 1
        FROM vendor
        WHERE idvendor = v_vendor_idvendor
          AND status = 'A'
    ) THEN
        SET v_valid = 0;
        SET p_message = 'Vendor tidak valid atau sudah tidak aktif';
    END IF;

    IF v_valid = 1 THEN
        SELECT COUNT(*)
        INTO v_jumlah_detail
        FROM detail_pengadaan
        WHERE pengadaan_idpengadaan = p_idpengadaan;

        IF v_jumlah_detail = 0 THEN
            SET v_valid = 0;
            SET p_message = 'Detail pengadaan belum tersedia';
        END IF;
    END IF;

    IF v_valid = 1 THEN
        SELECT COUNT(*)
        INTO v_invalid
        FROM detail_pengadaan dp
        LEFT JOIN barang b
            ON b.idbarang = dp.barang_idbarang
        WHERE dp.pengadaan_idpengadaan = p_idpengadaan
          AND (
              dp.jumlah IS NULL
              OR dp.jumlah <= 0
              OR dp.harga_satuan IS NULL
              OR dp.harga_satuan <= 0
              OR b.idbarang IS NULL
              OR b.status <> 1
          );

        IF v_invalid > 0 THEN
            SET v_valid = 0;
            SET p_message = 'Detail pengadaan tidak valid atau barang sudah tidak aktif';
        END IF;
    END IF;

    IF v_valid = 1 THEN
        UPDATE detail_pengadaan
        SET sub_total = harga_satuan * jumlah
        WHERE pengadaan_idpengadaan = p_idpengadaan;

        UPDATE pengadaan
        SET subtotal_nilai = (
                SELECT COALESCE(SUM(sub_total), 0)
                FROM detail_pengadaan
                WHERE pengadaan_idpengadaan = p_idpengadaan
            ),
            total_nilai = subtotal_nilai + COALESCE(ppn, 0),
            status = 'S'
        WHERE idpengadaan = p_idpengadaan;

        SET p_status = 'S';
        SET p_message = 'Pengadaan berhasil disetujui';
    END IF;
END ;;
DELIMITER ;

-- PROCEDURE: selesaikan penerimaan
DROP PROCEDURE IF EXISTS `sp_selesai_penerimaan`;
DELIMITER ;;
CREATE PROCEDURE `sp_selesai_penerimaan`(
    IN p_idpenerimaan BIGINT,
    OUT p_status CHAR(1),
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_idpenerimaan BIGINT DEFAULT NULL;
    DECLARE v_status CHAR(1) DEFAULT NULL;
    DECLARE v_idpengadaan BIGINT DEFAULT NULL;
    DECLARE v_jumlah_detail INT DEFAULT 0;
    DECLARE v_invalid INT DEFAULT 0;
    DECLARE v_valid INT DEFAULT 1;

    SET p_status = 'E';
    SET p_message = 'Penerimaan gagal diselesaikan';

    SELECT idpenerimaan, status, pengadaan_idpengadaan
    INTO v_idpenerimaan, v_status, v_idpengadaan
    FROM penerimaan
    WHERE idpenerimaan = p_idpenerimaan
    FOR UPDATE;

    IF v_idpenerimaan IS NULL THEN
        SET v_valid = 0;
        SET p_message = 'Penerimaan tidak ditemukan';
    ELSEIF v_status = 'S' THEN
        SET v_valid = 0;
        SET p_message = 'Penerimaan sudah selesai diproses';
    ELSEIF v_status IS NULL OR v_status <> 'P' THEN
        SET v_valid = 0;
        SET p_message = 'Status penerimaan tidak valid';
    END IF;

    IF v_valid = 1 AND NOT EXISTS (
        SELECT 1
        FROM pengadaan
        WHERE idpengadaan = v_idpengadaan
          AND status = 'S'
    ) THEN
        SET v_valid = 0;
        SET p_message = 'Pengadaan belum selesai';
    END IF;

    IF v_valid = 1 THEN
        SELECT COUNT(*)
        INTO v_jumlah_detail
        FROM detail_penerimaan
        WHERE penerimaan_idpenerimaan = p_idpenerimaan;

        IF v_jumlah_detail = 0 THEN
            SET v_valid = 0;
            SET p_message = 'Detail penerimaan belum tersedia';
        END IF;
    END IF;

    IF v_valid = 1 THEN
        SELECT COUNT(*)
        INTO v_invalid
        FROM detail_penerimaan dpt
        LEFT JOIN detail_pengadaan dp
            ON dp.pengadaan_idpengadaan = v_idpengadaan
           AND dp.barang_idbarang = dpt.barang_idbarang
        LEFT JOIN barang b
            ON b.idbarang = dpt.barang_idbarang
        WHERE dpt.penerimaan_idpenerimaan = p_idpenerimaan
          AND (
              dpt.jumlah_terima IS NULL
              OR dpt.jumlah_terima <= 0
              OR dpt.harga_satuan_terima IS NULL
              OR dpt.harga_satuan_terima < 0
              OR dp.iddetail_pengadaan IS NULL
              OR b.idbarang IS NULL
              OR b.status <> 1
          );

        IF v_invalid > 0 THEN
            SET v_valid = 0;
            SET p_message = 'Detail penerimaan tidak valid atau barang tidak ada dalam pengadaan';
        END IF;
    END IF;

    IF v_valid = 1 THEN
        SELECT COUNT(*)
        INTO v_invalid
        FROM (
            SELECT dpt.barang_idbarang, SUM(dpt.jumlah_terima) AS total_terima
            FROM detail_penerimaan dpt
            JOIN penerimaan pt
                ON pt.idpenerimaan = dpt.penerimaan_idpenerimaan
            WHERE pt.pengadaan_idpengadaan = v_idpengadaan
            GROUP BY dpt.barang_idbarang
        ) AS total_penerimaan
        JOIN (
            SELECT barang_idbarang, SUM(jumlah) AS total_pesan
            FROM detail_pengadaan
            WHERE pengadaan_idpengadaan = v_idpengadaan
            GROUP BY barang_idbarang
        ) AS total_pengadaan
            ON total_pengadaan.barang_idbarang = total_penerimaan.barang_idbarang
        WHERE total_penerimaan.total_terima > total_pengadaan.total_pesan;

        IF v_invalid > 0 THEN
            SET v_valid = 0;
            SET p_message = 'Jumlah penerimaan melebihi jumlah pengadaan';
        END IF;
    END IF;

    IF v_valid = 1 THEN
        UPDATE detail_penerimaan
        SET sub_total_terima = harga_satuan_terima * jumlah_terima
        WHERE penerimaan_idpenerimaan = p_idpenerimaan;

        UPDATE penerimaan
        SET status = 'S'
        WHERE idpenerimaan = p_idpenerimaan;

        SET p_status = 'S';
        SET p_message = 'Penerimaan berhasil diselesaikan';
    END IF;
END ;;
DELIMITER ;

-- PROCEDURE: selesaikan penjualan
DROP PROCEDURE IF EXISTS `sp_selesai_penjualan`;
DELIMITER ;;
CREATE PROCEDURE `sp_selesai_penjualan`(
    IN p_idpenjualan INT,
    OUT p_status CHAR(1),
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_idpenjualan INT DEFAULT NULL;
    DECLARE v_status CHAR(1) DEFAULT NULL;
    DECLARE v_jumlah_detail INT DEFAULT 0;
    DECLARE v_invalid INT DEFAULT 0;
    DECLARE v_valid INT DEFAULT 1;

    SET p_status = 'E';
    SET p_message = 'Penjualan gagal diselesaikan';

    SELECT idpenjualan, status
    INTO v_idpenjualan, v_status
    FROM penjualan
    WHERE idpenjualan = p_idpenjualan
    FOR UPDATE;

    IF v_idpenjualan IS NULL THEN
        SET v_valid = 0;
        SET p_message = 'Penjualan tidak ditemukan';
    ELSEIF v_status = 'S' THEN
        SET v_valid = 0;
        SET p_message = 'Penjualan sudah selesai diproses';
    ELSEIF v_status IS NULL OR v_status <> 'P' THEN
        SET v_valid = 0;
        SET p_message = 'Status penjualan tidak valid';
    END IF;

    IF v_valid = 1 THEN
        SELECT COUNT(*)
        INTO v_jumlah_detail
        FROM detail_penjualan
        WHERE penjualan_idpenjualan = p_idpenjualan;

        IF v_jumlah_detail = 0 THEN
            SET v_valid = 0;
            SET p_message = 'Detail penjualan belum tersedia';
        END IF;
    END IF;

    IF v_valid = 1 THEN
        SELECT COUNT(*)
        INTO v_invalid
        FROM detail_penjualan dp
        LEFT JOIN barang b
            ON b.idbarang = dp.barang_idbarang
        WHERE dp.penjualan_idpenjualan = p_idpenjualan
          AND (
              dp.jumlah IS NULL
              OR dp.jumlah <= 0
              OR dp.harga_satuan IS NULL
              OR dp.harga_satuan < 0
              OR b.idbarang IS NULL
              OR b.status <> 1
          );

        IF v_invalid > 0 THEN
            SET v_valid = 0;
            SET p_message = 'Detail penjualan tidak valid atau barang sudah tidak aktif';
        END IF;
    END IF;

    IF v_valid = 1 THEN
        SELECT COUNT(*)
        INTO v_invalid
        FROM (
            SELECT dp.barang_idbarang, SUM(dp.jumlah) AS total_jual
            FROM detail_penjualan dp
            WHERE dp.penjualan_idpenjualan = p_idpenjualan
            GROUP BY dp.barang_idbarang
        ) AS penjualan_barang
        WHERE penjualan_barang.total_jual > COALESCE((
            SELECT SUM(COALESCE(ks.masuk, 0) - COALESCE(ks.keluar, 0))
            FROM kartu_stok ks
            WHERE ks.barang_idbarang = penjualan_barang.barang_idbarang
        ), 0);

        IF v_invalid > 0 THEN
            SET v_valid = 0;
            SET p_message = 'Stok tidak mencukupi untuk salah satu barang';
        END IF;
    END IF;

    IF v_valid = 1 THEN
        UPDATE detail_penjualan
        SET subtotal = harga_satuan * jumlah
        WHERE penjualan_idpenjualan = p_idpenjualan;

        UPDATE penjualan
        SET subtotal_nilai = (
                SELECT COALESCE(SUM(subtotal), 0)
                FROM detail_penjualan
                WHERE penjualan_idpenjualan = p_idpenjualan
            ),
            ppn = fn_hitung_ppn(subtotal_nilai),
            total_nilai = subtotal_nilai + COALESCE(ppn, 0),
            status = 'S'
        WHERE idpenjualan = p_idpenjualan;

        SET p_status = 'S';
        SET p_message = 'Penjualan berhasil diselesaikan';
    END IF;
END ;;
DELIMITER ;

-- PROCEDURE: proses retur
DROP PROCEDURE IF EXISTS `sp_proses_retur`;
DELIMITER ;;
CREATE PROCEDURE `sp_proses_retur`(
    IN p_idretur BIGINT,
    OUT p_status CHAR(1),
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_idretur BIGINT DEFAULT NULL;
    DECLARE v_status CHAR(1) DEFAULT NULL;
    DECLARE v_idpenerimaan BIGINT DEFAULT NULL;
    DECLARE v_jumlah_detail INT DEFAULT 0;
    DECLARE v_invalid INT DEFAULT 0;
    DECLARE v_valid INT DEFAULT 1;

    SET p_status = 'E';
    SET p_message = 'Retur gagal diproses';

    SELECT idretur, status, penerimaan_idpenerimaan
    INTO v_idretur, v_status, v_idpenerimaan
    FROM retur
    WHERE idretur = p_idretur
    FOR UPDATE;

    IF v_idretur IS NULL THEN
        SET v_valid = 0;
        SET p_message = 'Retur tidak ditemukan';
    ELSEIF v_status = 'S' THEN
        SET v_valid = 0;
        SET p_message = 'Retur sudah selesai diproses';
    ELSEIF v_status IS NULL OR v_status <> 'P' THEN
        SET v_valid = 0;
        SET p_message = 'Status retur tidak valid';
    END IF;

    IF v_valid = 1 AND NOT EXISTS (
        SELECT 1
        FROM penerimaan
        WHERE idpenerimaan = v_idpenerimaan
          AND status = 'S'
    ) THEN
        SET v_valid = 0;
        SET p_message = 'Penerimaan belum selesai';
    END IF;

    IF v_valid = 1 THEN
        SELECT COUNT(*)
        INTO v_jumlah_detail
        FROM detail_retur
        WHERE retur_idretur = p_idretur;

        IF v_jumlah_detail = 0 THEN
            SET v_valid = 0;
            SET p_message = 'Detail retur belum tersedia';
        END IF;
    END IF;

    IF v_valid = 1 THEN
        SELECT COUNT(*)
        INTO v_invalid
        FROM detail_retur dr
        JOIN detail_penerimaan dp
            ON dp.iddetail_penerimaan =
               dr.detail_penerimaan_iddetail_penerimaan
        WHERE dr.retur_idretur = p_idretur
          AND (
              dr.jumlah IS NULL
              OR dr.jumlah <= 0
              OR dp.penerimaan_idpenerimaan <> v_idpenerimaan
              OR (
                  SELECT COALESCE(SUM(dr2.jumlah), 0)
                  FROM detail_retur dr2
                  WHERE dr2.detail_penerimaan_iddetail_penerimaan =
                        dp.iddetail_penerimaan
              ) > dp.jumlah_terima
          );

        IF v_invalid > 0 THEN
            SET v_valid = 0;
            SET p_message = 'Jumlah retur tidak valid atau melebihi jumlah penerimaan';
        END IF;
    END IF;

    IF v_valid = 1 THEN
        SELECT COUNT(*)
        INTO v_invalid
        FROM (
            SELECT dp.barang_idbarang, SUM(dr.jumlah) AS total_retur
            FROM detail_retur dr
            JOIN detail_penerimaan dp
                ON dp.iddetail_penerimaan =
                   dr.detail_penerimaan_iddetail_penerimaan
            WHERE dr.retur_idretur = p_idretur
            GROUP BY dp.barang_idbarang
        ) AS retur_barang
        WHERE retur_barang.total_retur > COALESCE((
            SELECT SUM(COALESCE(ks.masuk, 0) - COALESCE(ks.keluar, 0))
            FROM kartu_stok ks
            WHERE ks.barang_idbarang = retur_barang.barang_idbarang
        ), 0);

        IF v_invalid > 0 THEN
            SET v_valid = 0;
            SET p_message = 'Stok tidak mencukupi untuk melakukan retur';
        END IF;
    END IF;

    IF v_valid = 1 THEN
        UPDATE detail_retur
        SET alasan = CASE
            WHEN alasan IS NULL OR TRIM(alasan) = '' THEN 'Tanpa alasan'
            ELSE alasan
        END
        WHERE retur_idretur = p_idretur;

        UPDATE retur
        SET status = 'S'
        WHERE idretur = p_idretur;

        SET p_status = 'S';
        SET p_message = 'Retur berhasil diproses';
    END IF;
END ;;
DELIMITER ;

-- PROCEDURE: aktifkan satu margin, matikan margin lain yang aktif
DROP PROCEDURE IF EXISTS `sp_aktifkan_margin`;
DELIMITER ;;
CREATE PROCEDURE `sp_aktifkan_margin`(
    IN p_idmargin_penjualan INT,
    IN p_iduser INT
)
BEGIN
    -- Pastikan margin yang dipilih benar-benar ada.
    -- Kalau tidak dicek, ID yang salah bisa mematikan semua margin aktif.
    IF EXISTS (
        SELECT 1
        FROM margin_penjualan
        WHERE idmargin_penjualan = p_idmargin_penjualan
    ) THEN
        -- Matikan semua margin yang sedang aktif,
        -- supaya hanya ada satu margin aktif dalam satu waktu.
        UPDATE margin_penjualan
        SET status = 0,
            updated_at = NOW()
        WHERE status = 1;

        -- Aktifkan margin yang dipilih dan catat user yang mengubahnya.
        -- Kolom persen sengaja tidak diubah.
        UPDATE margin_penjualan
        SET status = 1,
            user_iduser = p_iduser,
            updated_at = NOW()
        WHERE idmargin_penjualan = p_idmargin_penjualan;
    END IF;
END ;;
DELIMITER ;
