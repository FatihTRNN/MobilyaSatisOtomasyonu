<?php
session_start();
if (!isset($_SESSION['kullanici'])) {
    header("Location: admin_giris.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mobilyasatis";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Veritabanı bağlantısı kurulurken hata oluştu: " . $conn->connect_error);
}

function urunListele($conn) {
    $sql = "SELECT urunID, urunAd, stok, alisFiyat, satisFiyat, (satisFiyat - alisFiyat) AS kar, resim FROM urunler WHERE durum = true"; // Ürünleri veritabanından almak için
    $result = $conn->query($sql);

    if ($result->num_rows > 0) { // Eğer sonuç varsa tabloyu oluştur
        echo "<h2 class='mt-5'>Ürün İşlemleri</h2>";
        echo "<table class='table table-striped'><thead><tr><th>Ürün ID</th><th>Ürün Adı</th><th>Stok</th><th>Alış Fiyatı</th><th>Satış Fiyatı</th><th>Kar</th><th>Fotoğraf</th><th>Güncelle</th><th>Sil</th></tr></thead><tbody>"; // ilk güncelle butonu btn warning tablodaki altta
        while($row = $result->fetch_assoc()) {  // Her ürün için tablo satırı oluştur
            echo "<tr><td>".$row["urunID"]."</td><td>".$row["urunAd"]."</td><td>".$row["stok"]."</td><td>".$row["alisFiyat"]."</td><td>".$row["satisFiyat"]."</td><td>".$row["kar"]."</td>
            <td><img src='".$row["resim"]."' alt='Ürün Fotoğrafı' class='img-thumbnail' style='width:50px;height:50px;'></td>
            <td><form method='post' action='".$_SERVER["PHP_SELF"]."'><input type='hidden' name='guncelle_urun_id' value='".$row["urunID"]."'><input type='submit' name='guncelle_urun' value='Güncelle' class='btn btn-warning'></form></td>
            <td><form method='post' action='".$_SERVER["PHP_SELF"]."'><input type='hidden' name='sil_urun_id' value='".$row["urunID"]."'><input type='submit' name='sil_urun' value='Sil' class='btn btn-danger' onclick='return confirm(\"Bu kaydı silmek istediğinize emin misiniz?\")'></form></td></tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "0 sonuç";
    }
}

function urunKaydet($conn, $urunAd, $stok, $alisFiyat, $satisFiyat, $resim) {// Fotoğrafı yüklemek için dizin oluştur
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($resim["name"]); // Dosya adı ve hedef yol
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if ($resim["size"] > 5000000) { // Dosya boyutunu kontrol et
        echo "Hata: Dosya boyutu 5MB'den küçük olmalıdır.";
        $uploadOk = 0;
    }

    $allowed_file_types = array("jpg", "jpeg", "png", "gif"); // Belirli dosya formatlarına izin ver 
    if (!in_array($imageFileType, $allowed_file_types)) {
        echo "Hata: Sadece JPG, JPEG, PNG & GIF dosya formatlarına izin verilmektedir.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($resim["tmp_name"], $target_file)) { // Dosya yükleme ve veritabanına kaydetme işlemi
            $kar = $satisFiyat - $alisFiyat;
            $sql = "INSERT INTO urunler (urunAd, stok, alisFiyat, satisFiyat, kar, durum, resim) VALUES (?, ?, ?, ?, ?, true, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siddss", $urunAd, $stok, $alisFiyat, $satisFiyat, $kar, $target_file);
            if ($stmt->execute() === TRUE) {
                echo "<script>alert('Kayıt başarıyla eklendi.')</script>";
                urunListele($conn);
            } else {
                echo "Hata: " . $stmt->error;
            }
        } else {
            echo "Hata: Dosya yüklenirken bir sorun oluştu.";
        }
    }
}

function urunGuncelleForm($conn, $urunID) {  // Belirli bir ürünü almak için SQL sorgusu
    $sql = "SELECT urunAd, stok, alisFiyat, satisFiyat, resim FROM urunler WHERE urunID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $urunID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) { // Ürün bulunduysa formu doldur
        $row = $result->fetch_assoc();
        echo "<h2 class='mt-5'>Ürün Güncelleme</h2>";
        echo "<form method='post' action='".$_SERVER["PHP_SELF"]."' enctype='multipart/form-data'>";
        echo "<div class='mb-3'><label class='form-label'>Ürün Adı:</label><input type='text' name='urunAd' value='".$row["urunAd"]."' class='form-control' required></div>";
        echo "<div class='mb-3'><label class='form-label'>Stok:</label><input type='text' name='stok' value='".$row["stok"]."' class='form-control' required></div>";
        echo "<div class='mb-3'><label class='form-label'>Alış Fiyatı:</label><input type='text' name='alisFiyat' value='".$row["alisFiyat"]."' class='form-control' required></div>";
        echo "<div class='mb-3'><label class='form-label'>Satış Fiyatı:</label><input type='text' name='satisFiyat' value='".$row["satisFiyat"]."' class='form-control' required></div>";
        echo "<div class='mb-3'><label class='form-label'>Mevcut Fotoğraf:</label><br><img src='".$row["resim"]."' alt='Ürün Fotoğrafı' class='img-thumbnail' style='width:50px;height:50px;'><br></div>";
        echo "<div class='mb-3'><label class='form-label'>Yeni Fotoğraf:</label><input type='file' name='resim' class='form-control'></div>";
        echo "<input type='hidden' name='urunID' value='".$urunID."'>";
        echo "<button type='submit' name='guncelle_urun_submit' class='btn btn-primary'>Güncelle</button>"; // ürünün içine girince gelen güncelle butonu
        echo "</form>";
    } else {
        echo "Ürün bulunamadı.";
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["urunListele"])) {
        urunListele($conn);
    }

    if (isset($_POST["urunKaydet"])) {
        $urunAd = $_POST["urunAd"];
        $stok = $_POST["stok"];
        $alisFiyat = $_POST["alisFiyat"];
        $satisFiyat = $_POST["satisFiyat"];
        $resim = $_FILES["resim"];
        urunKaydet($conn, $urunAd, $stok, $alisFiyat, $satisFiyat, $resim);
    }

    if (isset($_POST["guncelle_urun"])) {
        $urunID = $_POST["guncelle_urun_id"];
        urunGuncelleForm($conn, $urunID);
    }

    if (isset($_POST["guncelle_urun_submit"])) {
        $urunID = $_POST["urunID"];
        $urunAd = $_POST["urunAd"];
        $stok = $_POST["stok"];
        $alisFiyat = $_POST["alisFiyat"];
        $satisFiyat = $_POST["satisFiyat"];
        $kar = $satisFiyat - $alisFiyat;
        $resim = $_FILES["resim"];
        $uploadOk = 1; // Varsayılan olarak yükleme başarılı
        $errorMessage = ""; // Hata mesajını saklamak için
    
        // Mevcut fotoğrafı al
        $sql = "SELECT resim FROM urunler WHERE urunID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $urunID);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $current_photo = $row["resim"];
    
        // Dosya kontrol ve yükleme işlemi
        if (!empty($resim["name"])) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($resim["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
            // Dosya boyutunu kontrol et
            if ($resim["size"] > 5000000) {
                $errorMessage = "Dosya boyutu 5MB'den küçük olmalıdır.";
                $uploadOk = 0;
            }
    
            // İzin verilen dosya türlerini kontrol et
            $allowed_file_types = array("jpg", "jpeg", "png", "gif");
            if (!in_array($imageFileType, $allowed_file_types)) {
                $errorMessage = "Sadece JPG, JPEG, PNG & GIF dosya formatlarına izin verilmektedir.";
                $uploadOk = 0;
            }
    
            // Dosyayı yükle
            if ($uploadOk == 1) {
                if (!move_uploaded_file($resim["tmp_name"], $target_file)) {
                    $errorMessage = "Dosya yüklenirken bir sorun oluştu.";
                    $uploadOk = 0;
                } else {
                    $current_photo = $target_file; // Yeni fotoğrafı güncelle
                }
            }
        }
    
        // Eğer yüklemede bir hata varsa, hata mesajını göster ve işlemi durdur
        if ($uploadOk == 0) {
            echo "<script>alert('$errorMessage'); window.location.href='{$_SERVER["PHP_SELF"]}';</script>";
            exit();
        }
    
        // Veritabanı güncelleme işlemi
        $sql = "UPDATE urunler SET urunAd=?, stok=?, alisFiyat=?, satisFiyat=?, kar=?, resim=? WHERE urunID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siddssi", $urunAd, $stok, $alisFiyat, $satisFiyat, $kar, $current_photo, $urunID);
    
        if ($stmt->execute() === TRUE) {
            echo "<script>alert('Ürün bilgileri başarıyla güncellendi.'); window.location.href='{$_SERVER["PHP_SELF"]}';</script>";
        } else {
            echo "<script>alert('Veritabanı güncelleme hatası: {$stmt->error}'); window.location.href='{$_SERVER["PHP_SELF"]}';</script>";
        }
    }

    if (isset($_POST["sil_urun"])) {
        $urunID = $_POST["sil_urun_id"];
        $sql = "DELETE FROM urunler WHERE urunID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $urunID);
        if ($stmt->execute() === TRUE) {
            echo "<script>alert('Ürün başarıyla silindi.')</script>";
            urunListele($conn);
        } else {
            echo "Hata: " . $sql . "<br>" . $conn->error;
        }
    }
}
// Çıkış yapma işlemi
if(isset($_POST['logout'])) {
    session_unset(); 
    session_destroy();
    header("Location: giris.php?message=logout_success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün İşlemleri</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="musteri_islemleri.php">Müşteri İşlemleri</a></li>
                <li class="nav-item"><a class="nav-link" href="satis_islemleri.php">Satış İşlemleri</a></li>
                <li class="nav-item">
                    <form method="post">
                        <button type="submit" name="logout" class="btn btn-danger">Çıkış Yap</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
    <h2>Ürün İşlemleri</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
        <div class="form-group">
            <label>Ürün Adı:</label>
            <input type="text" name="urunAd" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Stok:</label>
            <input type="text" name="stok" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Alış Fiyatı:</label>
            <input type="text" name="alisFiyat" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Satış Fiyatı:</label>
            <input type="text" name="satisFiyat" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Ürün Fotoğrafı:</label>
            <input type="file" name="resim" class="form-control" required>
        </div>
        <button type="submit" name="urunKaydet" class="btn btn-primary">Kaydet</button> <!-- Kaydet butonu--> 
    </form>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <button type="submit" name="urunListele" class="btn btn-info mt-3">Listele</button> <!-- Listele butonu--> 
    </form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
