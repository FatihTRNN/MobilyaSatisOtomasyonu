<?php
session_start();
if (!isset($_SESSION['kullanici'])) {
    header("Location: giris.php");
    exit();
}
include 'db_connect.php';

// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri alın
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $adres = $_POST['adres'];
    $telefon = $_POST['telefon'];

    // Müşteri bilgilerini veritabanına ekle
    $sql_musteri = "INSERT INTO Musteriler (ad, soyad, adres, tel, durum) VALUES ('$ad', '$soyad', '$adres', '$telefon', 1)";
    if ($conn->query($sql_musteri) === TRUE) {
        // Son eklenen müşterinin ID'sini al
        $musteriID = $conn->insert_id;

        // Sipariş bilgilerini veritabanına ekle
        $sql_siparis = "INSERT INTO Siparisler (musteriID, ad, soyad, adres, telefon) VALUES ('$musteriID', '$ad', '$soyad', '$adres', '$telefon')";
        if ($conn->query($sql_siparis) === TRUE) {
            // Son eklenen siparişin ID'sini al
            $siparisID = $conn->insert_id;

            // Sepetteki her ürünü SatisListesi tablosuna ekle ve stoktan düş
            if (isset($_SESSION['sepet'])) {
                foreach ($_SESSION['sepet'] as $urun_id => $miktar) {
                    // Ürün bilgilerini veritabanından al
                    $sql_urun = "SELECT * FROM Urunler WHERE urunID = $urun_id";
                    $result_urun = $conn->query($sql_urun);
                    if ($result_urun->num_rows > 0) {
                        $row_urun = $result_urun->fetch_assoc();
                        $urunAd = $row_urun['urunAd'];
                        $fiyat = $row_urun['satisFiyat'];
                        $stok = $row_urun['stok']; // Ürünün stok miktarını al

                        // Stok yeterli mi kontrol et
                        if ($stok >= $miktar) {
                            $toplamFiyat = $fiyat * $miktar;
                            $tarih = date("Y-m-d H:i:s");

                            // Stoktan düş ve satışı kaydet
                            $yeni_stok = $stok - $miktar;
                            $sql_update_stok = "UPDATE Urunler SET stok = $yeni_stok WHERE urunID = $urun_id";
                            $sql_satis = "INSERT INTO SatisListesi (urunID, urunAd, musteriID, adet, toplamFiyat, tarih) VALUES ('$urun_id', '$urunAd', '$musteriID', '$miktar', '$toplamFiyat', '$tarih')";
                            if ($conn->query($sql_update_stok) === TRUE && $conn->query($sql_satis) === TRUE) {
                                // Satış başarılı, devam et
                            } else {
                                echo "Hata: " . $conn->error . "<br>";
                            }
                        } else {
                            echo "Stok yetersiz: $urunAd<br>";
                        }
                    } else {
                        echo "Ürün bulunamadı: ID $urun_id<br>";
                    }
                }
            } else {
                echo "Sepet boş.<br>";
            }

            // Sepeti boşalt
            unset($_SESSION['sepet']);

            // Teşekkürler sayfasına yönlendir
            header("Location: tesekkurler.php");
            exit();
        } else {
            echo "Hata: " . $sql_siparis . "<br>" . $conn->error . "<br>";
        }
    } else {
        echo "Hata: " . $sql_musteri . "<br>" . $conn->error . "<br>";
    }

    // Veritabanı bağlantısını kapatın
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Onayla</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Sipariş Onayla</h1>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="ad">Ad:</label>
                <input type="text" id="ad" name="ad" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="soyad">Soyad:</label>
                <input type="text" id="soyad" name="soyad" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="adres">Adres:</label>
                <textarea id="adres" name="adres" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="telefon">Telefon:</label>
                <input type="text" id="telefon" name="telefon" class="form-control" required>
            </div>
            
            <h2>Sepetinizdeki Ürünler</h2>
            <div class="list-group">
            <?php
            // Sepetteki her ürünü listele
            if (isset($_SESSION['sepet'])) {
                foreach ($_SESSION['sepet'] as $urun_id => $miktar) {
                    // Ürün bilgilerini veritabanından al
                    $sql_urun = "SELECT * FROM Urunler WHERE urunID = $urun_id";
                    $result_urun = $conn->query($sql_urun);
                    if ($result_urun->num_rows > 0) {
                        $row_urun = $result_urun->fetch_assoc();
                        // Ürünü listele
                        echo "<div class='list-group-item'>";
                        echo "<img src='" . $row_urun["resim"] . "' alt='" . $row_urun["urunAd"] . "' width='100' class='img-thumbnail'>";
                        echo "<p>" . $row_urun["urunAd"] . " - Miktar: " . $miktar . " - Fiyat: ₺" . $row_urun["satisFiyat"] . "</p>";
                        echo "</div>";
                    }
                }
            }
            ?>
            </div>
            
            <button type="submit" class="btn btn-primary mt-3">Siparişi Onayla</button> <!-- Siparişi onayla butonu-->
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
