<?php
session_start();
if (!isset($_SESSION['kullanici'])) {
    header("Location: giris.php");
    exit();
}

// Kullanıcı oturumu kontrol et
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: giris.php"); // Kullanıcı oturumu yoksa giriş sayfasına yönlendir
    exit();
}

// Veritabanı bağlantısını içe aktar
include 'db_connect.php';

// Sepetten ürün silme 
if (isset($_POST['urun_id']) && isset($_POST['action']) && $_POST['action'] == 'sil') {
    $urun_id = $_POST['urun_id'];
    if (isset($_SESSION['sepet'][$urun_id])) {
        unset($_SESSION['sepet'][$urun_id]);
    }
}

// Ürün miktarını güncelleme 
if (isset($_POST['urun_id']) && isset($_POST['action']) && $_POST['action'] == 'guncelle') {
    $urun_id = $_POST['urun_id'];
    $yeni_miktar = $_POST['miktar'];
    if ($yeni_miktar > 0) {
        $_SESSION['sepet'][$urun_id] = $yeni_miktar;
    } else {
        unset($_SESSION['sepet'][$urun_id]);
    }
}

// Sepeti boşaltma işlemi
if (isset($_POST['bosalt'])) {
    unset($_SESSION['sepet']);
}

// Sepet içeriğini al
$sepet_urunler = [];
if (isset($_SESSION['sepet'])) {
    foreach ($_SESSION['sepet'] as $urun_id => $miktar) {
        $sql = "SELECT * FROM Urunler WHERE urunID = $urun_id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $urun = $result->fetch_assoc();
            $urun['miktar'] = $miktar;
            $sepet_urunler[] = $urun;
        }
    }
}

// Sepetteki toplam ürün sayısını ve fiyatını hesapla
$sepet_sayisi = 0;
$toplam_fiyat = 0;
if (isset($_SESSION['sepet'])) {
    foreach ($_SESSION['sepet'] as $urun_id => $miktar) {
        $sql = "SELECT satisFiyat FROM Urunler WHERE urunID = $urun_id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $urun = $result->fetch_assoc();
            $sepet_sayisi += $miktar;
            $toplam_fiyat += $urun['satisFiyat'] * $miktar;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepet</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .cart-icon {
            position: relative;
            display: inline-block;
        }
        .cart-icon .badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
        }
        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Sepet</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Anasayfa</a> <!-- anasayfa butonu -->
            </li>
            <li class="nav-item">
                <a class="nav-link" href="sepet.php"> <!-- sağdaki sepet butonu -->
                    <span class="cart-icon">
                        🛒
                        <span class="badge" id="cart-count"><?php echo $sepet_sayisi; ?></span>
                    </span>
                </a>
            </li>
            <li class="nav-item"><a class="nav-link" href="cikis.php">Çıkış Yap</a></li> <!-- sağdaki çıkış yap butonu -->
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <h2>Sepetiniz</h2>
    <?php if (!empty($sepet_urunler)): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Ürün Resmi</th>
                    <th>Ürün Adı</th>
                    <th>Fiyat</th>
                    <th>Miktar</th>
                    <th>Toplam</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sepet_urunler as $urun): ?>
                    <tr>
                        <td><img src="<?php echo $urun['resim']; ?>" alt="<?php echo $urun['urunAd']; ?>" class="product-img"></td>
                        <td><?php echo $urun['urunAd']; ?></td>
                        <td>₺<?php echo $urun['satisFiyat']; ?></td>
                        <td>
                            <input type="number" name="miktar" value="<?php echo $urun['miktar']; ?>" class="form-control miktar-input" data-urun-id="<?php echo $urun['urunID']; ?>" style="width: 60px;">
                        </td>
                        <td class="toplam-fiyat">₺<?php echo $urun['satisFiyat'] * $urun['miktar']; ?></td>
                        <td>
                            <form action="sepet.php" method="post">
                                <input type="hidden" name="urun_id" value="<?php echo $urun['urunID']; ?>">
                                <input type="hidden" name="action" value="sil">
                                <input type="submit" class="btn btn-danger" value="Sil"> <!-- Sil butonu-->
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="text-right">
            <h4>Toplam Fiyat: ₺<span id="toplam-fiyat"><?php echo number_format($toplam_fiyat, 2); ?></span></h4>
        </div>
        <form action="sepet.php" method="post">
            <input type="submit" name="bosalt" class="btn btn-warning" value="Sepeti Boşalt"> <!-- sepeti boşalt butonu -->
        </form>
        <a href="siparis_onayla.php" class="btn btn-success mt-3">Siparişi Onayla</a> <!-- siparişi onayla butonu -->
    <?php else: ?>
        <p>Sepetinizde ürün bulunmamaktadır.</p>
    <?php endif; ?>
    <a href="index.php" class="btn btn-primary mt-3">Anasayfaya Dön</a> <!-- anasayfaya dön butonu -->
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
    var toplamFiyat = <?php echo $toplam_fiyat; ?>; // Toplam fiyatı hesaplama

    $('.miktar-input').on('input', function() { // Miktar girişi yani input alanındaki değişiklikleri izler
        var urunID = $(this).data('urun-id');
        var yeniMiktar = $(this).val();
        var $row = $(this).closest('tr');
        var satisFiyat = parseFloat($row.find('td:eq(2)').text().replace('₺', ''));
        
        if (yeniMiktar > 0) { // Yeni toplam fiyatı hesaplar ve günceller
            var yeniToplamFiyat = satisFiyat * yeniMiktar;
            $row.find('.toplam-fiyat').text('₺' + yeniToplamFiyat.toFixed(2)); // Toplam fiyatı günceller
            toplamFiyat += satisFiyat * (yeniMiktar - $row.find('.miktar-input').attr('value'));
            $row.find('.miktar-input').attr('value', yeniMiktar);
        } else {
            toplamFiyat -= satisFiyat * $row.find('.miktar-input').attr('value'); // Miktar sıfır ise, ürün satırını sepetten çıkarır
            $row.remove();
        }

        $('#toplam-fiyat').text(toplamFiyat.toFixed(2)); // Toplam fiyatı ekranda günceller

        $.post("sepet.php", { // Ajax ile sepeti günceller
            urun_id: urunID,
            action: 'guncelle',
            miktar: yeniMiktar
        }, function(response) {
            // Sepet güncellendiğinde toplam ürün sayısını günceller
            var toplamUrunSayisi = 0;
            $('.miktar-input').each(function() {
                toplamUrunSayisi += parseInt($(this).val());
            });
            $('#cart-count').text(toplamUrunSayisi);
        });
    });
});
</script>
</body>
</html>
