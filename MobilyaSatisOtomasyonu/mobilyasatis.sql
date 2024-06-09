-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1:3306
-- Üretim Zamanı: 28 May 2024, 21:56:50
-- Sunucu sürümü: 5.7.36
-- PHP Sürümü: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `mobilyasatis`
--

DELIMITER $$
--
-- Yordamlar
--
DROP PROCEDURE IF EXISTS `satislistesi_goruntule`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `satislistesi_goruntule` ()  BEGIN
    SELECT 
        urunler.urunAd AS 'urunAd', 
        CONCAT(musteriler.ad, ' ', musteriler.soyad) AS 'adSoyad', 
        satislistesi.adet, 
        (satislistesi.adet * urunler.satisFiyat) AS 'toplamFiyat', 
        satislistesi.tarih
    FROM 
        satislistesi 
    JOIN 
        urunler ON satislistesi.urunID = urunler.urunID
    JOIN 
        musteriler ON satislistesi.musteriID = musteriler.musteriID;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

DROP TABLE IF EXISTS `kullanicilar`;
CREATE TABLE IF NOT EXISTS `kullanicilar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kullanici_adi` varchar(50) COLLATE utf32_turkish_ci NOT NULL,
  `sifre` varchar(255) COLLATE utf32_turkish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf32 COLLATE=utf32_turkish_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `kullanici_adi`, `sifre`) VALUES
(1, 'Emirhan', '57'),
(2, 'Fatih', '44'),
(3, 'Eren', '05'),
(20, 'SÃ¼leyman', '57'),
(21, 'Volkan', '1905');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `musteriler`
--

DROP TABLE IF EXISTS `musteriler`;
CREATE TABLE IF NOT EXISTS `musteriler` (
  `musteriID` int(11) NOT NULL AUTO_INCREMENT,
  `ad` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `soyad` varchar(25) COLLATE utf8_turkish_ci DEFAULT NULL,
  `adres` varchar(40) COLLATE utf8_turkish_ci DEFAULT NULL,
  `tel` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `durum` bit(1) DEFAULT NULL,
  PRIMARY KEY (`musteriID`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `musteriler`
--

INSERT INTO `musteriler` (`musteriID`, `ad`, `soyad`, `adres`, `tel`, `durum`) VALUES
(1, 'Emirhan', 'Gündoğdu', 'Sinop', '(541) 791-4596', b'1'),
(5, 'Fatih', 'Turan', 'Malatya', '(444) 444-4444', b'1'),
(6, 'Hüseyin Eren', 'Yavuz', 'Amasya', '(555) 555-5555', b'1'),
(7, 'Ahmet Doğan', 'Seçer', 'Hatay', '(333) 333-3333', b'1'),
(8, 'Ali', 'Selçuk', 'Akçadağ', '(444) 444-4444', b'1'),
(10, 'Fatihh', 'Turan', 'Malatya', '(444) 444-4444', b'0'),
(9, 'Fatih', 'Turan', 'Malatya', '(444) 444-4444', b'0'),
(11, 'Ahmet', 'Kaçan', 'Sinop', '(666) 666-6666', b'1'),
(12, 'Mehmet', 'Kal', 'Sinop', '(538) 486-4654', b'1'),
(13, 'Ayşe', 'Kılıç', 'Sinop', '(111) 111-1111', b'1'),
(14, 'Mert', 'Çoban', 'Siirt', '(546) 848-6467', b'1'),
(15, 'Selin', 'Türk', 'Çanakkale', '(567) 484-6542', b'1'),
(16, 'Murat', 'Kayak', 'Erzurum', '(543) 111-1111', b'1'),
(20, 'Mehmet', 'Emin', 'Kıbrıs', '(546) 486-4122', b'1'),
(34, 'mÃ¼slÃ¼m', 'GÃ¼rses', 'sinop', '5417914591', NULL),
(33, 'dadasdadas', 'dadsdad', 'sadasda', '5555555', b'1'),
(32, 'emo', 'xe', 'se', '(555) 555-5555', b'1'),
(31, 'MÃ¼slÃ¼m', 'GÃ¼rses', 'Ankara', '7419516585', b'1'),
(35, 'mÃ¼slÃ¼m', 'Kaï¿½anaaa', 'xgfvxcc', '5748967512', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `satislistesi`
--

DROP TABLE IF EXISTS `satislistesi`;
CREATE TABLE IF NOT EXISTS `satislistesi` (
  `satisID` int(11) NOT NULL AUTO_INCREMENT,
  `urunID` int(11) NOT NULL,
  `urunAd` varchar(255) COLLATE utf32_turkish_ci NOT NULL,
  `musteriID` int(11) NOT NULL,
  `adet` int(11) NOT NULL,
  `toplamFiyat` decimal(10,2) NOT NULL,
  `tarih` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`satisID`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf32 COLLATE=utf32_turkish_ci;

--
-- Tablo döküm verisi `satislistesi`
--

INSERT INTO `satislistesi` (`satisID`, `urunID`, `urunAd`, `musteriID`, `adet`, `toplamFiyat`, `tarih`) VALUES
(42, 5, 'Bilgisayar Masas?', 31, 1, '16999.00', '2024-05-28 17:57:23'),
(41, 11, 'Mutfak Tak?m?', 31, 1, '11000.00', '2024-05-28 17:57:23'),
(40, 3, 'Yemek Masas?', 31, 1, '18999.00', '2024-05-28 17:57:23'),
(43, 11, 'Mutfak Takimi', 7, 1, '11000.00', '2024-05-28 21:00:00'),
(44, 3, 'Yemek Masasi', 8, 1, '18999.00', '2024-05-28 21:00:00'),
(45, 16, 'Televizyon Ünitesi', 13, 1, '10000.00', '2024-05-28 21:51:10'),
(46, 3, 'Yemek Masas?', 34, 3, '56997.00', '2024-05-28 18:53:34'),
(47, 3, 'Yemek Masas?', 35, 2, '37998.00', '2024-05-28 18:56:18');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siparisler`
--

DROP TABLE IF EXISTS `siparisler`;
CREATE TABLE IF NOT EXISTS `siparisler` (
  `siparisID` int(11) NOT NULL AUTO_INCREMENT,
  `ad` varchar(255) COLLATE utf32_turkish_ci NOT NULL,
  `soyad` varchar(255) COLLATE utf32_turkish_ci NOT NULL,
  `adres` text COLLATE utf32_turkish_ci NOT NULL,
  `telefon` varchar(20) COLLATE utf32_turkish_ci NOT NULL,
  `tarih` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `musteriID` int(11) DEFAULT NULL,
  PRIMARY KEY (`siparisID`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf32 COLLATE=utf32_turkish_ci;

--
-- Tablo döküm verisi `siparisler`
--

INSERT INTO `siparisler` (`siparisID`, `ad`, `soyad`, `adres`, `telefon`, `tarih`, `musteriID`) VALUES
(33, 'mÃ¼slÃ¼m', 'Kaï¿½anaaa', 'xgfvxcc', '5748967512', '2024-05-28 21:56:18', 35),
(32, 'mÃ¼slÃ¼m', 'GÃ¼rses', 'sinop', '5417914591', '2024-05-28 21:53:34', 34),
(31, 'MÃ¼slÃ¼m', 'GÃ¼rses', 'Ankara', '7419516585', '2024-05-28 20:57:23', 31);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urunler`
--

DROP TABLE IF EXISTS `urunler`;
CREATE TABLE IF NOT EXISTS `urunler` (
  `urunID` int(11) NOT NULL AUTO_INCREMENT,
  `urunAd` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `stok` int(2) DEFAULT NULL,
  `alisFiyat` int(5) DEFAULT NULL,
  `satisFiyat` int(5) DEFAULT NULL,
  `durum` bit(1) DEFAULT b'1',
  `kar` decimal(10,2) DEFAULT NULL,
  `resim` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  PRIMARY KEY (`urunID`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `urunler`
--

INSERT INTO `urunler` (`urunID`, `urunAd`, `stok`, `alisFiyat`, `satisFiyat`, `durum`, `kar`, `resim`) VALUES
(1, 'Koltuk Takımı', 15, 5000, 8000, b'1', '3000.00', 'resimler/koltuk.jpg'),
(2, 'Oturma Odası', 17, 50000, 63000, b'1', '13000.00', 'resimler/oturmagrubu.jpg'),
(3, 'Yemek Masası', 22, 14000, 18999, b'1', '4999.00', 'resimler/yemekodasi.jpg'),
(4, 'Bilgisayar Koltuğu', 9, 3000, 5199, b'1', '2199.00', 'resimler/bilgisayarkoltugu.jpg'),
(5, 'Bilgisayar Masası', 2, 12000, 16999, b'1', '4999.00', 'resimler/bilgisayarmasasi.jpg'),
(6, 'Çocuk Odası', 15, 20000, 25999, b'1', '5999.00', 'resimler/odasi.jpg'),
(7, 'Genç Odası', 4, 22000, 27999, b'1', '5999.00', 'resimler/genc.jpg'),
(8, 'Gardirop', 6, 4600, 7200, b'1', '2600.00', 'resimler/gardrop.jpg'),
(9, 'Ütü Masası', 1, 3000, 5165, b'1', '2165.00', 'resimler/utu.jpg'),
(10, 'Ayakkabılık', 3, 4050, 5250, b'1', '1200.00', 'resimler/ayakkabilik.jpg'),
(11, 'Mutfak Takımı', 7, 7000, 11000, b'1', '4000.00', 'resimler/mutfaktakimi.jpg'),
(13, 'Kitaplık', 5, 2000, 3499, b'1', '1499.00', 'resimler/kitaplik.jpg'),
(16, 'Televizyon Ünitesi', 4, 7000, 10000, b'1', '3000.00', 'resimler/unite.jpg');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
