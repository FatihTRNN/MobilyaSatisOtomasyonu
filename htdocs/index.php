<?php
session_start();

// Kullanıcı oturumu kontrol et
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: giris.php"); // Kullanıcı oturumu yoksa giriş sayfasına yönlendir
    exit();
} else {
    // Kullanıcı oturumu var, giriş yapmıştır
    $kullanici_adi = $_SESSION['kullanici_adi'];
    $hosgeldiniz_mesaji = "Hoş geldiniz, $kullanici_adi!";
}

// Veritabanı bağlantısını içe aktar
include 'db_connect.php';

// Ürün sepete eklendiğinde
if (isset($_POST['urun_id'])) {
    $urun_id = $_POST['urun_id'];
    
    // Ürün miktarını kontrol etmek için varsayılan miktarı belirleyin
    $miktar = 1;
    
    // Eğer daha önce bu ürün sepete eklenmişse, miktarı arttır
    if (isset($_SESSION['sepet'][$urun_id])) {
        $_SESSION['sepet'][$urun_id] += $miktar;
    } else {
        $_SESSION['sepet'][$urun_id] = $miktar;
    }
}

// Sepetteki ürün sayısını hesapla
$sepet_sayisi = 0;
if (isset($_SESSION['sepet'])) {
    foreach ($_SESSION['sepet'] as $miktar) {
        $sepet_sayisi += $miktar;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anasayfa</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .product-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin: 10px;
            text-align: center;
        }
        .product-card img {
            width: 100%;
            height: auto;
        }
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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Anasayfa</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
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
        <h1><?php echo $hosgeldiniz_mesaji; ?></h1>
        <h2>Popüler Ürünler</h2>
        <div class="row">
            <?php
            // Veritabanından ürünleri al
            $sql = "SELECT * FROM Urunler";
            $result = $conn->query($sql);
            
            // Ürünleri listele
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='col-md-4'>";
                    echo "<div class='product-card'>";
                    echo "<img src='" . $row["resim"] . "' alt='" . $row["urunAd"] . "'>";
                    echo "<h3>" . $row["urunAd"] . "</h3>";
                    echo "<p>Fiyat: ₺" . $row["satisFiyat"] . "</p>";
                    echo "<form action='' method='post' class='add-to-cart-form'>";
                    echo "<input type='hidden' name='urun_id' value='" . $row["urunID"] . "'>";
                    echo "<input type='submit' class='btn btn-primary' value='Sepete Ekle'>"; // sepete ekle butonu
                    echo "</form>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<div class='col-12'><p>Üzgünüz, hiç ürün bulunamadı.</p></div>";
            }
            ?>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function(){
        $('.add-to-cart-form').on('submit', function(e){
            e.preventDefault();
            var form = $(this);
            $.post('', form.serialize(), function(){
                var cartCount = parseInt($('#cart-count').text());
                $('#cart-count').text(cartCount + 1);
            });
        });
    });
    </script>
</body>
</html>
