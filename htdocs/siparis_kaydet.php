<?php
session_start();
if (!isset($_SESSION['kullanici'])) {
    header("Location: giris.php");
    exit();
}
include 'db_connect.php';

// Oturumda kayıtlı kullanıcı kimliğini al
$musteri_id = $_SESSION['musteri_id'];

// Satış işlemi detaylarını al
$urunler = $_SESSION['sepet'];

// Her bir ürün için satış işlemini kaydet
foreach($urunler as $urun_id => $miktar) {
    $sql = "INSERT INTO Satislar (musteri_id, urun_id, miktar) VALUES ('$musteri_id', '$urun_id', '$miktar')";
    if ($conn->query($sql) !== TRUE) {
        echo "Satış işlemi eklenirken hata oluştu: " . $conn->error;
    }
}

// Sepeti temizle
unset($_SESSION['sepet']);

echo "Satış işlemi başarıyla tamamlandı. Teşekkürler!";
?>
