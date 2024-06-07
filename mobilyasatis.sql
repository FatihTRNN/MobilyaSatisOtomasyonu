-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 30 May 2024, 04:49:40
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `satislistesi_goruntule` ()   BEGIN
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

CREATE TABLE `kullanicilar` (
  `id` int(11) NOT NULL,
  `kullanici_adi` varchar(50) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `rol` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `kullanici_adi`, `sifre`, `rol`) VALUES
(1, '\0\0\0E\0\0\0m\0\0\0i\0\0\0r\0\0\0h\0\0\0a\0\0\0n', '\0\0\05\0\0\07', 'user'),
(2, '\0\0\0F\0\0\0a\0\0\0t\0\0\0i\0\0\0h', '\0\0\04\0\0\04', 'user'),
(3, '\0\0\0E\0\0\0r\0\0\0e\0\0\0n', '\0\0\00\0\0\05', 'user'),
(20, 'Süleyman', '57', 'user'),
(21, '\0\0\0V\0\0\0o\0\0\0l\0\0\0k\0\0\0a\0\0\0n', '\0\0\01\0\0\09\0\0\00\0\0\05', 'user'),
(22, 'sinopmobilya', '5757', 'admin'),
(24, 'Ahmet', '123', 'user'),
(25, 'Mehmet', '123', 'user');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `musteriler`
--

CREATE TABLE `musteriler` (
  `musteriID` int(11) NOT NULL,
  `ad` varchar(20) DEFAULT NULL,
  `soyad` varchar(25) DEFAULT NULL,
  `adres` varchar(40) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `durum` bit(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `musteriler`
--

INSERT INTO `musteriler` (`musteriID`, `ad`, `soyad`, `adres`, `tel`, `durum`) VALUES
(1, 'Emirhan', 'Gündoğdu', 'Sinop', '(541) 791-4596', b'1'),
(5, 'Fatih', 'Turan', 'Malatya', '(444) 444-4444', b'1'),
(6, 'Hüseyin Eren', 'Yavuz', 'Amasya', '(555) 555-5555', b'1'),
(7, 'Ahmet Doğan', 'Seçer', 'Hatay', '(333) 333-3333', b'1'),
(8, 'Ali', 'Selçuk', 'Akçadağ', '(444) 444-4444', b'1'),
(43, 'Deneme2', 'Deneme2', 'Deneme2', '5555555555', b'1'),
(42, 'Deneme', 'Deneme', 'Deneme', '5554446655', b'1'),
(11, 'Ahmet', 'Kaçan', 'Sinop', '(666) 666-6666', b'1'),
(12, 'Mehmet', 'Kal', 'Sinop', '(538) 486-4654', b'1'),
(13, 'Ayşe', 'Kılıç', 'Sinop', '(111) 111-1111', b'1'),
(14, 'Mert', 'Çoban', 'Siirt', '(546) 848-6467', b'1'),
(15, 'Selin', 'Türk', 'Çanakkale', '(567) 484-6542', b'1'),
(16, 'Murat', 'Kayak', 'Erzurum', '(543) 111-1111', b'1'),
(20, 'Mehmet', 'Emin', 'Kıbrıs', '(546) 486-4122', b'1'),
(41, 'Muzaffer', 'Ülger', 'Malatya', '4444444444', b'1'),
(39, 'Ahmet', 'Mehmet', 'Sinop', '5555555555', b'1'),
(31, 'Müslüm', 'Gürses', 'Ankara', '5555555555', b'1'),
(40, 'Ahmet', 'Murat', 'Sinop', '5555555555', b'1'),
(38, 'Mehmet', 'Kayacan', 'Sinop', '5314498741', b'1');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `satislistesi`
--

CREATE TABLE `satislistesi` (
  `satisID` int(11) NOT NULL,
  `urunID` int(11) NOT NULL,
  `urunAd` varchar(255) NOT NULL,
  `musteriID` int(11) NOT NULL,
  `adet` int(11) NOT NULL,
  `toplamFiyat` decimal(10,2) NOT NULL,
  `tarih` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `satislistesi`
--

INSERT INTO `satislistesi` (`satisID`, `urunID`, `urunAd`, `musteriID`, `adet`, `toplamFiyat`, `tarih`) VALUES
(42, 5, '\0\0\0B\0\0\0i\0\0\0l\0\0\0g\0\0\0i\0\0\0s\0\0\0a\0\0\0y\0\0\0a\0\0\0r\0\0\0 \0\0\0M\0\0\0a\0\0\0s\0\0\0a\0\0\0s\0\0\0?', 31, 1, 16999.00, '2024-05-28 17:57:23'),
(41, 11, '\0\0\0M\0\0\0u\0\0\0t\0\0\0f\0\0\0a\0\0\0k\0\0\0 \0\0\0T\0\0\0a\0\0\0k\0\0\0?\0\0\0m\0\0\0?', 31, 1, 11000.00, '2024-05-28 17:57:23'),
(40, 3, '\0\0\0Y\0\0\0e\0\0\0m\0\0\0e\0\0\0k\0\0\0 \0\0\0M\0\0\0a\0\0\0s\0\0\0a\0\0\0s\0\0\0?', 31, 1, 18999.00, '2024-05-28 17:57:23'),
(43, 11, '\0\0\0M\0\0\0u\0\0\0t\0\0\0f\0\0\0a\0\0\0k\0\0\0 \0\0\0T\0\0\0a\0\0\0k\0\0\0i\0\0\0m\0\0\0i', 7, 1, 11000.00, '2024-05-28 21:00:00'),
(44, 3, '\0\0\0Y\0\0\0e\0\0\0m\0\0\0e\0\0\0k\0\0\0 \0\0\0M\0\0\0a\0\0\0s\0\0\0a\0\0\0s\0\0\0i', 8, 1, 18999.00, '2024-05-28 21:00:00'),
(45, 16, '\0\0\0T\0\0\0e\0\0\0l\0\0\0e\0\0\0v\0\0\0i\0\0\0z\0\0\0y\0\0\0o\0\0\0n\0\0\0 \0\0\0?\0\0\0n\0\0\0i\0\0\0t\0\0\0e\0\0\0s\0\0\0i', 13, 1, 10000.00, '2024-05-28 21:51:10'),
(46, 3, '\0\0\0Y\0\0\0e\0\0\0m\0\0\0e\0\0\0k\0\0\0 \0\0\0M\0\0\0a\0\0\0s\0\0\0a\0\0\0s\0\0\0?', 34, 3, 56997.00, '2024-05-28 18:53:34'),
(47, 3, '\0\0\0Y\0\0\0e\0\0\0m\0\0\0e\0\0\0k\0\0\0 \0\0\0M\0\0\0a\0\0\0s\0\0\0a\0\0\0s\0\0\0?', 35, 2, 37998.00, '2024-05-28 18:56:18'),
(51, 23, 'Üçgen Koltuk', 5, 2, 20000.00, '2024-05-30 01:49:14'),
(50, 1, 'Koltuk Takımı', 5, 1, 8000.00, '2024-05-30 01:28:02'),
(52, 1, 'Koltuk Takımı', 39, 1, 8000.00, '2024-05-30 01:14:15'),
(53, 2, 'Oturma Odası', 39, 1, 63000.00, '2024-05-30 01:14:15'),
(54, 4, 'Bilgisayar Koltuğu', 39, 1, 5199.00, '2024-05-30 01:14:15'),
(55, 1, 'Koltuk Takımı', 40, 1, 8000.00, '2024-05-30 01:21:22'),
(56, 23, 'Üçgen Koltuk', 40, 1, 10000.00, '2024-05-30 01:21:22'),
(57, 1, 'Koltuk Takımı', 41, 1, 8000.00, '2024-05-30 01:28:23'),
(58, 23, 'Üçgen Koltuk', 41, 1, 10000.00, '2024-05-30 01:28:23'),
(59, 23, 'Üçgen Koltuk', 42, 1, 10000.00, '2024-05-30 01:33:47'),
(60, 23, 'Üçgen Koltuk', 43, 1, 10000.00, '2024-05-30 01:40:19');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siparisler`
--

CREATE TABLE `siparisler` (
  `siparisID` int(11) NOT NULL,
  `ad` varchar(255) NOT NULL,
  `soyad` varchar(255) NOT NULL,
  `adres` text NOT NULL,
  `telefon` varchar(20) NOT NULL,
  `tarih` timestamp NULL DEFAULT current_timestamp(),
  `musteriID` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `siparisler`
--

INSERT INTO `siparisler` (`siparisID`, `ad`, `soyad`, `adres`, `telefon`, `tarih`, `musteriID`) VALUES
(31, 'Müslüm', 'Gürses', 'Ankara', '646555555', '2024-05-28 20:57:23', 31),
(34, 'sdfg', 'sdfa', 'asdf', '1231241234', '2024-05-28 22:17:47', NULL),
(35, 'Ahmet', 'Mehmet', 'Sinop', '5555555555', '2024-05-30 02:14:15', 39),
(36, 'Ahmet', 'Murat', 'Sinop', '5555555555', '2024-05-30 02:21:22', 40),
(37, 'Muzaffer', 'Ülger', 'Malatya', '4444444444', '2024-05-30 02:28:23', 41),
(38, 'Deneme', 'Deneme', 'Deneme', '5554446655', '2024-05-30 02:33:47', 42),
(39, 'Deneme2', 'Deneme2', 'Deneme2', '5555555555', '2024-05-30 02:40:19', 43);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urunler`
--

CREATE TABLE `urunler` (
  `urunID` int(11) NOT NULL,
  `urunAd` varchar(20) DEFAULT NULL,
  `stok` int(2) DEFAULT NULL,
  `alisFiyat` int(5) DEFAULT NULL,
  `satisFiyat` int(5) DEFAULT NULL,
  `durum` bit(1) DEFAULT b'1',
  `kar` decimal(10,2) DEFAULT NULL,
  `resim` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `urunler`
--

INSERT INTO `urunler` (`urunID`, `urunAd`, `stok`, `alisFiyat`, `satisFiyat`, `durum`, `kar`, `resim`) VALUES
(1, 'Koltuk Takımı', 12, 5000, 8000, b'1', 3000.00, 'resimler/koltuk.jpg'),
(2, 'Oturma Odası', 16, 50000, 63000, b'1', 13000.00, 'resimler/oturmagrubu.jpg'),
(3, 'Yemek Masası', 22, 14000, 18999, b'1', 4999.00, 'resimler/yemekodasi.jpg'),
(4, 'Bilgisayar Koltuğu', 8, 3000, 5199, b'1', 2199.00, 'resimler/bilgisayarkoltugu.jpg'),
(5, 'Bilgisayar Masası', 2, 12000, 16999, b'1', 4999.00, 'resimler/bilgisayarmasasi.jpg'),
(6, 'Çocuk Odası', 15, 20000, 25999, b'1', 5999.00, 'resimler/odasi.jpg'),
(7, 'Genç Odası', 4, 22000, 27999, b'1', 5999.00, 'resimler/genc.jpg'),
(8, 'Gardirop', 6, 4600, 7200, b'1', 2600.00, 'resimler/gardrop.jpg'),
(9, 'Ütü Masası', 1, 3000, 5165, b'1', 2165.00, 'resimler/utu.jpg'),
(10, 'Ayakkabılık', 3, 4050, 5250, b'1', 1200.00, 'resimler/ayakkabilik.jpg'),
(11, 'Mutfak Takımı', 7, 7000, 11000, b'1', 4000.00, 'resimler/mutfaktakimi.jpg'),
(13, 'Kitaplık', 5, 2000, 3499, b'1', 1499.00, 'resimler/kitaplik.jpg'),
(16, 'Televizyon Ünitesi', 4, 7000, 10000, b'1', 3000.00, 'resimler/unite.jpg'),
(23, 'Üçgen Koltuk', 6, 5000, 10000, b'1', 5000.00, 'uploads/background.PNG');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `musteriler`
--
ALTER TABLE `musteriler`
  ADD PRIMARY KEY (`musteriID`);

--
-- Tablo için indeksler `satislistesi`
--
ALTER TABLE `satislistesi`
  ADD PRIMARY KEY (`satisID`);

--
-- Tablo için indeksler `siparisler`
--
ALTER TABLE `siparisler`
  ADD PRIMARY KEY (`siparisID`);

--
-- Tablo için indeksler `urunler`
--
ALTER TABLE `urunler`
  ADD PRIMARY KEY (`urunID`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Tablo için AUTO_INCREMENT değeri `musteriler`
--
ALTER TABLE `musteriler`
  MODIFY `musteriID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Tablo için AUTO_INCREMENT değeri `satislistesi`
--
ALTER TABLE `satislistesi`
  MODIFY `satisID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Tablo için AUTO_INCREMENT değeri `siparisler`
--
ALTER TABLE `siparisler`
  MODIFY `siparisID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Tablo için AUTO_INCREMENT değeri `urunler`
--
ALTER TABLE `urunler`
  MODIFY `urunID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
