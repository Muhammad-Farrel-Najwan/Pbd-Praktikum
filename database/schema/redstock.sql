/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `barang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barang` (
  `idbarang` int NOT NULL AUTO_INCREMENT,
  `jenis` char(1) NOT NULL,
  `nama` varchar(45) NOT NULL,
  `satuan_idsatuan` int NOT NULL,
  `status` tinyint NOT NULL,
  `harga` int NOT NULL,
  PRIMARY KEY (`idbarang`),
  KEY `fk_barang_satuan_idx` (`satuan_idsatuan`),
  CONSTRAINT `fk_barang_satuan` FOREIGN KEY (`satuan_idsatuan`) REFERENCES `satuan` (`idsatuan`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `detail_penerimaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_penerimaan` (
  `iddetail_penerimaan` bigint NOT NULL AUTO_INCREMENT,
  `barang_idbarang` int NOT NULL,
  `penerimaan_idpenerimaan` bigint NOT NULL,
  `jumlah_terima` int DEFAULT NULL,
  `harga_satuan_terima` int DEFAULT NULL,
  `sub_total_terima` int DEFAULT NULL,
  PRIMARY KEY (`iddetail_penerimaan`),
  KEY `fk_detail_penerimaan_barang1_idx` (`barang_idbarang`),
  KEY `fk_detail_penerimaan_penerimaan1_idx` (`penerimaan_idpenerimaan`),
  CONSTRAINT `fk_detail_penerimaan_barang1` FOREIGN KEY (`barang_idbarang`) REFERENCES `barang` (`idbarang`),
  CONSTRAINT `fk_detail_penerimaan_penerimaan1` FOREIGN KEY (`penerimaan_idpenerimaan`) REFERENCES `penerimaan` (`idpenerimaan`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `detail_pengadaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_pengadaan` (
  `iddetail_pengadaan` bigint NOT NULL AUTO_INCREMENT,
  `harga_satuan` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `sub_total` int DEFAULT NULL,
  `barang_idbarang` int NOT NULL,
  `pengadaan_idpengadaan` bigint NOT NULL,
  PRIMARY KEY (`iddetail_pengadaan`),
  KEY `fk_detail_pengadaan_barang1_idx` (`barang_idbarang`),
  KEY `fk_detail_pengadaan_pengadaan1_idx` (`pengadaan_idpengadaan`),
  CONSTRAINT `fk_detail_pengadaan_barang1` FOREIGN KEY (`barang_idbarang`) REFERENCES `barang` (`idbarang`),
  CONSTRAINT `fk_detail_pengadaan_pengadaan1` FOREIGN KEY (`pengadaan_idpengadaan`) REFERENCES `pengadaan` (`idpengadaan`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `detail_penjualan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_penjualan` (
  `iddetail_penjualan` bigint NOT NULL AUTO_INCREMENT,
  `harga_satuan` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `subtotal` int DEFAULT NULL,
  `penjualan_idpenjualan` int NOT NULL,
  `barang_idbarang` int NOT NULL,
  PRIMARY KEY (`iddetail_penjualan`),
  KEY `fk_detail_penjualan_penjualan1_idx` (`penjualan_idpenjualan`),
  KEY `fk_detail_penjualan_barang1_idx` (`barang_idbarang`),
  CONSTRAINT `fk_detail_penjualan_barang1` FOREIGN KEY (`barang_idbarang`) REFERENCES `barang` (`idbarang`),
  CONSTRAINT `fk_detail_penjualan_penjualan1` FOREIGN KEY (`penjualan_idpenjualan`) REFERENCES `penjualan` (`idpenjualan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `detail_retur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_retur` (
  `iddetail_retur` int NOT NULL AUTO_INCREMENT,
  `jumlah` int DEFAULT NULL,
  `alasan` varchar(200) DEFAULT NULL,
  `detail_penerimaan_iddetail_penerimaan` bigint NOT NULL,
  `retur_idretur` bigint NOT NULL,
  PRIMARY KEY (`iddetail_retur`),
  KEY `fk_detail_retur_detail_penerimaan1_idx` (`detail_penerimaan_iddetail_penerimaan`),
  KEY `fk_detail_retur_retur1_idx` (`retur_idretur`),
  CONSTRAINT `fk_detail_retur_detail_penerimaan1` FOREIGN KEY (`detail_penerimaan_iddetail_penerimaan`) REFERENCES `detail_penerimaan` (`iddetail_penerimaan`),
  CONSTRAINT `fk_detail_retur_retur1` FOREIGN KEY (`retur_idretur`) REFERENCES `retur` (`idretur`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kartu_stok`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kartu_stok` (
  `idkartu_stok` bigint NOT NULL AUTO_INCREMENT,
  `jenis_transaksi` char(1) DEFAULT NULL,
  `masuk` int DEFAULT NULL,
  `keluar` int DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `idtransaksi` int DEFAULT NULL,
  `barang_idbarang` int NOT NULL,
  PRIMARY KEY (`idkartu_stok`),
  KEY `fk_kartu_stok_barang1_idx` (`barang_idbarang`),
  CONSTRAINT `fk_kartu_stok_barang1` FOREIGN KEY (`barang_idbarang`) REFERENCES `barang` (`idbarang`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_hitung_stok_otomatis` BEFORE INSERT ON `kartu_stok` FOR EACH ROW BEGIN
    DECLARE v_saldo INT DEFAULT 0;

    SELECT COALESCE(SUM(COALESCE(masuk, 0) - COALESCE(keluar, 0)), 0)
    INTO v_saldo
    FROM kartu_stok
    WHERE barang_idbarang = NEW.barang_idbarang;

    SET NEW.stock = v_saldo
        + COALESCE(NEW.masuk, 0)
        - COALESCE(NEW.keluar, 0);

    IF NEW.stock < 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stok tidak mencukupi untuk transaksi ini';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `margin_penjualan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `margin_penjualan` (
  `idmargin_penjualan` int NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `persen` double DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `user_iduser` int NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`idmargin_penjualan`),
  KEY `fk_margin_penjualan_user1_idx` (`user_iduser`),
  CONSTRAINT `fk_margin_penjualan_user1` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `penerimaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penerimaan` (
  `idpenerimaan` bigint NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `pengadaan_idpengadaan` bigint NOT NULL,
  `user_iduser` int NOT NULL,
  PRIMARY KEY (`idpenerimaan`),
  KEY `fk_penerimaan_pengadaan1_idx` (`pengadaan_idpengadaan`),
  KEY `fk_penerimaan_user1_idx` (`user_iduser`),
  CONSTRAINT `fk_penerimaan_pengadaan1` FOREIGN KEY (`pengadaan_idpengadaan`) REFERENCES `pengadaan` (`idpengadaan`),
  CONSTRAINT `fk_penerimaan_user1` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_penerimaan_selesai_stok` AFTER UPDATE ON `penerimaan` FOR EACH ROW BEGIN
    IF OLD.status <> 'S' AND NEW.status = 'S' THEN
        INSERT INTO kartu_stok (
            jenis_transaksi,
            masuk,
            keluar,
            stock,
            created_at,
            idtransaksi,
            barang_idbarang
        )
        SELECT
            'P',
            SUM(dp.jumlah_terima),
            0,
            NULL,
            NOW(),
            NEW.idpenerimaan,
            dp.barang_idbarang
        FROM detail_penerimaan dp
        WHERE dp.penerimaan_idpenerimaan = NEW.idpenerimaan
          AND NOT EXISTS (
              SELECT 1
              FROM kartu_stok ks
              WHERE ks.idtransaksi = NEW.idpenerimaan
                AND ks.barang_idbarang = dp.barang_idbarang
                AND ks.jenis_transaksi = 'P'
          )
        GROUP BY dp.barang_idbarang;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `pengadaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengadaan` (
  `idpengadaan` bigint NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `user_iduser` int NOT NULL,
  `status` char(1) DEFAULT NULL,
  `vendor_idvendor` int NOT NULL,
  `subtotal_nilai` int DEFAULT NULL,
  `ppn` int DEFAULT NULL,
  `total_nilai` int DEFAULT NULL,
  PRIMARY KEY (`idpengadaan`),
  KEY `fk_pengadaan_vendor1_idx` (`vendor_idvendor`),
  KEY `fk_pengadaan_user1_idx` (`user_iduser`),
  CONSTRAINT `fk_pengadaan_user1` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`),
  CONSTRAINT `fk_pengadaan_vendor1` FOREIGN KEY (`vendor_idvendor`) REFERENCES `vendor` (`idvendor`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `penjualan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penjualan` (
  `idpenjualan` int NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `subtotal_nilai` int DEFAULT NULL,
  `ppn` int DEFAULT NULL,
  `total_nilai` int DEFAULT NULL,
  `user_iduser` int NOT NULL,
  `margin_penjualan_idmargin_penjualan` int NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'P',
  PRIMARY KEY (`idpenjualan`),
  KEY `fk_penjualan_user1_idx` (`user_iduser`),
  KEY `fk_penjualan_margin_penjualan1_idx` (`margin_penjualan_idmargin_penjualan`),
  CONSTRAINT `fk_penjualan_margin_penjualan1` FOREIGN KEY (`margin_penjualan_idmargin_penjualan`) REFERENCES `margin_penjualan` (`idmargin_penjualan`),
  CONSTRAINT `fk_penjualan_user1` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_penjualan_selesai_stok` AFTER UPDATE ON `penjualan` FOR EACH ROW BEGIN
    IF OLD.status <> 'S' AND NEW.status = 'S' THEN
        INSERT INTO kartu_stok (
            jenis_transaksi,
            masuk,
            keluar,
            stock,
            created_at,
            idtransaksi,
            barang_idbarang
        )
        SELECT
            'J',
            0,
            SUM(dp.jumlah),
            NULL,
            NOW(),
            NEW.idpenjualan,
            dp.barang_idbarang
        FROM detail_penjualan dp
        WHERE dp.penjualan_idpenjualan = NEW.idpenjualan
          AND NOT EXISTS (
              SELECT 1
              FROM kartu_stok ks
              WHERE ks.idtransaksi = NEW.idpenjualan
                AND ks.barang_idbarang = dp.barang_idbarang
                AND ks.jenis_transaksi = 'J'
          )
        GROUP BY dp.barang_idbarang;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `retur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `retur` (
  `idretur` bigint NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `penerimaan_idpenerimaan` bigint NOT NULL,
  `user_iduser` int NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'P',
  PRIMARY KEY (`idretur`),
  KEY `fk_retur_penerimaan1_idx` (`penerimaan_idpenerimaan`),
  KEY `fk_retur_user1_idx` (`user_iduser`),
  CONSTRAINT `fk_retur_penerimaan1` FOREIGN KEY (`penerimaan_idpenerimaan`) REFERENCES `penerimaan` (`idpenerimaan`),
  CONSTRAINT `fk_retur_user1` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_retur_selesai_stok` AFTER UPDATE ON `retur` FOR EACH ROW BEGIN
    IF OLD.status <> 'S' AND NEW.status = 'S' THEN
        INSERT INTO kartu_stok (
            jenis_transaksi,
            masuk,
            keluar,
            stock,
            created_at,
            idtransaksi,
            barang_idbarang
        )
        SELECT
            'R',
            0,
            SUM(dr.jumlah),
            NULL,
            NOW(),
            NEW.idretur,
            dp.barang_idbarang
        FROM detail_retur dr
        JOIN detail_penerimaan dp
            ON dp.iddetail_penerimaan =
               dr.detail_penerimaan_iddetail_penerimaan
        WHERE dr.retur_idretur = NEW.idretur
          AND NOT EXISTS (
              SELECT 1
              FROM kartu_stok ks
              WHERE ks.idtransaksi = NEW.idretur
                AND ks.barang_idbarang = dp.barang_idbarang
                AND ks.jenis_transaksi = 'R'
          )
        GROUP BY dp.barang_idbarang;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role` (
  `idrole` int NOT NULL AUTO_INCREMENT,
  `nama_role` varchar(100) NOT NULL,
  PRIMARY KEY (`idrole`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `satuan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `satuan` (
  `idsatuan` int NOT NULL AUTO_INCREMENT,
  `nama_satuan` varchar(45) NOT NULL,
  `status` tinyint NOT NULL,
  PRIMARY KEY (`idsatuan`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `iduser` int NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role_idrole` int NOT NULL,
  PRIMARY KEY (`iduser`),
  UNIQUE KEY `user_username_unique` (`username`),
  KEY `fk_user_role1_idx` (`role_idrole`),
  CONSTRAINT `fk_user_role1` FOREIGN KEY (`role_idrole`) REFERENCES `role` (`idrole`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vendor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendor` (
  `idvendor` int NOT NULL AUTO_INCREMENT,
  `nama_vendor` varchar(100) NOT NULL,
  `badan_hukum` char(1) NOT NULL,
  `status` char(1) NOT NULL,
  PRIMARY KEY (`idvendor`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `view_barang`;
/*!50001 DROP VIEW IF EXISTS `view_barang`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_barang` AS SELECT 
 1 AS `id`,
 1 AS `kategori`,
 1 AS `nama_barang`,
 1 AS `satuan`,
 1 AS `status_barang`,
 1 AS `harga_barang`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `view_margin`;
/*!50001 DROP VIEW IF EXISTS `view_margin`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_margin` AS SELECT 
 1 AS `id`,
 1 AS `dibuat_pada`,
 1 AS `nilai_margin`,
 1 AS `status_margin`,
 1 AS `nama_pengguna`,
 1 AS `diubah_pada`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `view_role`;
/*!50001 DROP VIEW IF EXISTS `view_role`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_role` AS SELECT 
 1 AS `id`,
 1 AS `nama_peran`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `view_satuan`;
/*!50001 DROP VIEW IF EXISTS `view_satuan`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_satuan` AS SELECT 
 1 AS `id`,
 1 AS `nama`,
 1 AS `status_data`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `view_user`;
/*!50001 DROP VIEW IF EXISTS `view_user`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_user` AS SELECT 
 1 AS `id`,
 1 AS `nama_pengguna`,
 1 AS `peran`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `view_vendor`;
/*!50001 DROP VIEW IF EXISTS `view_vendor`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_vendor` AS SELECT 
 1 AS `id`,
 1 AS `nama`,
 1 AS `jenis_badan_usaha`,
 1 AS `status_vendor`*/;
SET character_set_client = @saved_cs_client;
/*!50003 DROP FUNCTION IF EXISTS `fn_hitung_harga_jual` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_hitung_harga_jual`(
    p_harga INT
) RETURNS decimal(15,2)
    READS SQL DATA
BEGIN
    DECLARE v_margin DECIMAL(5,2) DEFAULT NULL;

    SELECT persen
    INTO v_margin
    FROM margin_penjualan
    WHERE status = 1
    LIMIT 1;

    IF v_margin IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Margin penjualan aktif tidak ditemukan';
    END IF;

    RETURN ROUND(p_harga + (p_harga * v_margin / 100), 0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_aktifkan_margin` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_aktifkan_margin`(
    IN p_idmargin_penjualan INT,
    IN p_iduser INT
)
BEGIN
    DECLARE v_target_exists INT DEFAULT 0;
    DECLARE v_user_exists INT DEFAULT 0;
    DECLARE v_lock_id INT DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Kunci baris pertama sebagai mutex agar dua aktivasi tidak berjalan bersamaan
    SELECT idmargin_penjualan INTO v_lock_id
    FROM margin_penjualan
    ORDER BY idmargin_penjualan
    LIMIT 1
    FOR UPDATE;

    SELECT COUNT(*) INTO v_target_exists
    FROM margin_penjualan
    WHERE idmargin_penjualan = p_idmargin_penjualan;

    SELECT COUNT(*) INTO v_user_exists
    FROM `user`
    WHERE iduser = p_iduser;

    IF v_target_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Margin tidak ditemukan';
    END IF;

    IF v_user_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'User tidak ditemukan';
    END IF;

    UPDATE margin_penjualan
    SET status = 0,
        updated_at = CURRENT_TIMESTAMP
    WHERE status = 1;

    UPDATE margin_penjualan
    SET status = 1,
        user_iduser = p_iduser,
        updated_at = CURRENT_TIMESTAMP
    WHERE idmargin_penjualan = p_idmargin_penjualan;

    COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_proses_retur` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_proses_retur`(
    IN p_idretur BIGINT
)
BEGIN
    DECLARE v_idpenerimaan BIGINT;
    DECLARE v_status CHAR(1);
    DECLARE v_jumlah INT DEFAULT 0;
    DECLARE v_invalid INT DEFAULT 0;

    -- Memastikan retur tersedia
    SELECT penerimaan_idpenerimaan, status
    INTO v_idpenerimaan, v_status
    FROM retur
    WHERE idretur = p_idretur
    FOR UPDATE;

    -- Retur sudah selesai
    IF v_status = 'S' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Retur sudah selesai diproses';
    END IF;

    -- Memastikan penerimaan sudah selesai
    IF NOT EXISTS (
        SELECT 1
        FROM penerimaan
        WHERE idpenerimaan = v_idpenerimaan
          AND status = 'S'
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Penerimaan belum selesai';
    END IF;

    -- Memastikan detail retur tersedia
    SELECT COUNT(*)
    INTO v_jumlah
    FROM detail_retur
    WHERE retur_idretur = p_idretur;

    IF v_jumlah = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Detail retur belum tersedia';
    END IF;

    -- Validasi detail, jumlah, dan batas penerimaan
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
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Jumlah retur tidak valid atau melebihi jumlah penerimaan';
    END IF;

    -- Validasi stok berdasarkan saldo ledger kartu_stok (masuk - keluar)
    SELECT COUNT(*)
    INTO v_invalid
    FROM (
        SELECT
            dp.barang_idbarang,
            SUM(dr.jumlah) AS total_retur,
            COALESCE((
                SELECT SUM(COALESCE(ks.masuk, 0) - COALESCE(ks.keluar, 0))
                FROM kartu_stok ks
                WHERE ks.barang_idbarang = dp.barang_idbarang
            ), 0) AS stok_tersedia
        FROM detail_retur dr
        JOIN detail_penerimaan dp
            ON dp.iddetail_penerimaan =
               dr.detail_penerimaan_iddetail_penerimaan
        WHERE dr.retur_idretur = p_idretur
        GROUP BY dp.barang_idbarang
    ) AS validasi_stok
    WHERE total_retur > stok_tersedia;

    IF v_invalid > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stok tidak mencukupi untuk melakukan retur';
    END IF;

    -- Menyelesaikan retur.
    -- Trigger trg_retur_selesai_stok akan mencatat barang keluar ke kartu_stok.
    UPDATE retur
    SET status = 'S'
    WHERE idretur = p_idretur;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_selesai_penjualan` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_selesai_penjualan`(
    IN p_idpenjualan INT
)
BEGIN
    DECLARE v_status CHAR(1) DEFAULT NULL;
    DECLARE v_jumlah_detail INT DEFAULT 0;
    DECLARE v_invalid INT DEFAULT 0;

    SELECT status
    INTO v_status
    FROM penjualan
    WHERE idpenjualan = p_idpenjualan
    FOR UPDATE;

    IF v_status IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Penjualan tidak ditemukan';
    END IF;

    IF v_status <> 'P' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Penjualan tidak dalam status pending';
    END IF;

    SELECT COUNT(*)
    INTO v_jumlah_detail
    FROM detail_penjualan
    WHERE penjualan_idpenjualan = p_idpenjualan;

    IF v_jumlah_detail = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Detail penjualan belum tersedia';
    END IF;

    SELECT COUNT(*)
    INTO v_invalid
    FROM detail_penjualan
    WHERE penjualan_idpenjualan = p_idpenjualan
      AND (jumlah IS NULL OR jumlah <= 0);

    IF v_invalid > 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Jumlah barang tidak valid';
    END IF;

    SELECT COUNT(*)
    INTO v_invalid
    FROM (
        SELECT
            dp.barang_idbarang,
            SUM(dp.jumlah) AS butuh,
            COALESCE((
                SELECT SUM(COALESCE(ks.masuk, 0) - COALESCE(ks.keluar, 0))
                FROM kartu_stok ks
                WHERE ks.barang_idbarang = dp.barang_idbarang
            ), 0) AS saldo
        FROM detail_penjualan dp
        WHERE dp.penjualan_idpenjualan = p_idpenjualan
        GROUP BY dp.barang_idbarang
    ) AS validasi_stok
    WHERE validasi_stok.butuh > validasi_stok.saldo;

    IF v_invalid > 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stok tidak mencukupi untuk melakukan penjualan';
    END IF;

    -- Trigger trg_penjualan_selesai_stok akan mencatat stok keluar jenis 'J'.
    UPDATE penjualan
    SET status = 'S'
    WHERE idpenjualan = p_idpenjualan;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50001 DROP VIEW IF EXISTS `view_barang`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_barang` AS select `b`.`idbarang` AS `id`,`b`.`jenis` AS `kategori`,`b`.`nama` AS `nama_barang`,`s`.`nama_satuan` AS `satuan`,(case when (`b`.`status` = 1) then 'Aktif' else 'Tidak Aktif' end) AS `status_barang`,`b`.`harga` AS `harga_barang` from (`barang` `b` join `satuan` `s` on((`b`.`satuan_idsatuan` = `s`.`idsatuan`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_margin`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_margin` AS select `margin_penjualan`.`idmargin_penjualan` AS `id`,`margin_penjualan`.`created_at` AS `dibuat_pada`,`margin_penjualan`.`persen` AS `nilai_margin`,(case when (`margin_penjualan`.`status` = 1) then 'Aktif' else 'Tidak Aktif' end) AS `status_margin`,`user`.`username` AS `nama_pengguna`,`margin_penjualan`.`updated_at` AS `diubah_pada` from (`margin_penjualan` join `user` on((`margin_penjualan`.`user_iduser` = `user`.`iduser`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_role`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_role` AS select `role`.`idrole` AS `id`,`role`.`nama_role` AS `nama_peran` from `role` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_satuan`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_satuan` AS select `satuan`.`idsatuan` AS `id`,`satuan`.`nama_satuan` AS `nama`,(case when (`satuan`.`status` = 1) then 'Aktif' else 'Tidak Aktif' end) AS `status_data` from `satuan` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_user`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_user` AS select `u`.`iduser` AS `id`,`u`.`username` AS `nama_pengguna`,`r`.`nama_role` AS `peran` from (`user` `u` join `role` `r` on((`u`.`role_idrole` = `r`.`idrole`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_vendor`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_vendor` AS select `vendor`.`idvendor` AS `id`,`vendor`.`nama_vendor` AS `nama`,(case when (`vendor`.`badan_hukum` = 'P') then 'PT' when (`vendor`.`badan_hukum` = 'C') then 'CV' when (`vendor`.`badan_hukum` = 'U') then 'UD / Perorangan' when (`vendor`.`badan_hukum` = 'K') then 'Koperasi' when (`vendor`.`badan_hukum` = 'Y') then 'Yayasan' else 'Lainnya' end) AS `jenis_badan_usaha`,(case when (`vendor`.`status` = 'A') then 'Aktif' else 'Tidak Aktif' end) AS `status_vendor` from `vendor` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

