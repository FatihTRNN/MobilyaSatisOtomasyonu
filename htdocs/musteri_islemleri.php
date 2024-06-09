<?php
session_start();
if (!isset($_SESSION['kullanici'])) { // Kullanıcı kontrolu
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

function listele($conn) {
    $sql = "SELECT musteriID, ad, soyad, adres, tel FROM musteriler WHERE durum = true ORDER BY musteriID"; // durumu true olan müşterileri MüşteriID ye göre vt den çek 
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2 class='mt-5'>Müşteri İşlemleri</h2>";
        echo "<table class='table table-striped'><thead><tr><th>Müşteri ID</th><th>Ad</th><th>Soyad</th><th>Adres</th><th>Telefon</th><th>Güncelle</th><th>Sil</th></tr></thead><tbody>"; // Güncelle butonu warning yazan tablodaki.
        while($row = $result->fetch_assoc()) {  // Her müşteri için tablo satırı oluşturur
            echo "<tr><td>".$row["musteriID"]."</td><td>".$row["ad"]."</td><td>".$row["soyad"]."</td><td>".$row["adres"]."</td><td>".$row["tel"]."</td>
            <td><form method='post' action='".$_SERVER["PHP_SELF"]."'><input type='hidden' name='guncelle_musteri_id' value='".$row["musteriID"]."'><input type='submit' name='guncelle_musteri' value='Güncelle' class='btn btn-warning'></form></td>
            <td><form method='post' action='".$_SERVER["PHP_SELF"]."'><input type='hidden' name='sil_musteri_id' value='".$row["musteriID"]."'><input type='submit' name='sil_musteri' value='Sil' class='btn btn-danger' onclick='return confirm(\"Bu kaydı silmek istediğinize emin misiniz?\")'></form></td></tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "0 sonuç";
    }
}

function guncelleForm($conn, $musteriID) { // Müşteri güncelleme formu
    $sql = "SELECT ad, soyad, adres, tel FROM musteriler WHERE musteriID = '$musteriID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h2 class='mt-5'>Müşteri Güncelleme</h2>";
        echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
        echo "<div class='mb-3'><label class='form-label'>Ad:</label><input type='text' name='ad' value='".$row["ad"]."' class='form-control' required></div>";
        echo "<div class='mb-3'><label class='form-label'>Soyad:</label><input type='text' name='soyad' value='".$row["soyad"]."' class='form-control' required></div>";
        echo "<div class='mb-3'><label class='form-label'>Adres:</label><input type='text' name='adres' value='".$row["adres"]."' class='form-control' required></div>";
        echo "<div class='mb-3'><label class='form-label'>Telefon:</label><input type='text' name='tel' value='".$row["tel"]."' class='form-control' pattern='[0-9]{11}' title='11 haneli telefon numarası girin' maxlength='11' required></div>";
        echo "<input type='hidden' name='musteriID' value='".$musteriID."'>";
        echo "<button type='submit' name='guncelle_musteri_submit' class='btn btn-primary'>Güncelle</button>"; // verileri getir dediğimizdeki güncelle butonu
        echo "</form>";
    } else {
        echo "Müşteri bulunamadı.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST["listele"])) {
        listele($conn); // Müşterileri listeleme işlemi
    }

    if(isset($_POST["kaydet"])) {
        $ad = $_POST["ad"];
        $soyad = $_POST["soyad"];
        $adres = $_POST["adres"];
        $tel = $_POST["tel"];

        if (empty($ad) || empty($soyad) || empty($adres) || empty($tel)) { // Alanların boş olup olmadığını kontrol eder
            echo "<script>alert('Lütfen tüm alanları doldurun.')</script>";
        } else {
            if (strlen($tel) == 11 && ctype_digit($tel)) {
                $sql = "INSERT INTO musteriler (ad, soyad, adres, tel, durum) VALUES ('$ad', '$soyad', '$adres', '$tel', true)"; // Yeni müşteri ekleme sorgusu
                if ($conn->query($sql) === TRUE) {
                    echo "<script>alert('Kayıt başarıyla eklendi.')</script>";
                    listele($conn);
                } else {
                    echo "Hata: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "<script>alert('Telefon numarası 11 haneli olmalı ve sadece rakamlardan oluşmalıdır.')</script>";
            }
        }
    }

    if(isset($_POST["guncelle_musteri"])) {
        $musteriID = $_POST["guncelle_musteri_id"];
        guncelleForm($conn, $musteriID); // Güncelleme formunu gösterir
    }

    if(isset($_POST["guncelle_musteri_submit"])) {
        $musteriID = $_POST["musteriID"];
        $ad = $_POST["ad"];
        $soyad = $_POST["soyad"];
        $adres = $_POST["adres"];
        $tel = $_POST["tel"];
        if (strlen($tel) == 11 && ctype_digit($tel)) {
            $sql = "UPDATE musteriler SET ad='$ad', soyad='$soyad', adres='$adres', tel='$tel' WHERE musteriID='$musteriID'"; // Müşteri güncelleme sorgusu
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Müşteri bilgileri başarıyla güncellendi.')</script>";
                listele($conn);
            } else {
                echo "Hata: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "<script>alert('Telefon numarası 11 haneli olmalı ve sadece rakamlardan oluşmalıdır.')</script>";
        }
    }

    if(isset($_POST["sil_musteri"])) {
        $musteriID = $_POST["sil_musteri_id"];
        $sql = "DELETE FROM musteriler WHERE musteriID = '$musteriID'"; // Müşteri silme sorgusu
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Müşteri başarıyla silindi.')</script>";
            listele($conn);
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
    <title>Müşteri İşlemleri</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="urun_islemleri.php">Ürün İşlemleri</a></li> <!-- Ürün İşlemleri butonu -->
                <li class="nav-item"><a class="nav-link" href="satis_islemleri.php">Satış İşlemleri</a></li> <!-- Satış İşlemleri butonu -->
                <li class="nav-item">
                    <form method="post">
                        <button type="submit" name="logout" class="btn btn-danger">Çıkış Yap</button> <!-- Çıkış Yap butonu -->
                    </form>
                </li>
            </ul>
        </div>
    </div>
    <h2>Müşteri İşlemleri</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="form-group">
            <label>Müşteri ID:</label>
            <input type="number" name="musteriID" value="1" class="form-control">
        </div>
        <button type="submit" name="getir" class="btn btn-info">Verileri Getir</button> <!-- Verileri Getir butonu -->
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["getir"])) {
        $musteriID = $_POST["musteriID"];
        $sql = "SELECT ad, soyad, adres, tel FROM musteriler WHERE musteriID='$musteriID'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
            echo "<div class='form-group'><label class='form-label'>Ad:</label><input type='text' name='ad' value='".$row["ad"]."' class='form-control'></div>";
            echo "<div class='form-group'><label class='form-label'>Soyad:</label><input type='text' name='soyad' value='".$row["soyad"]."' class='form-control'></div>";
            echo "<div class='form-group'><label class='form-label'>Adres:</label><input type='text' name='adres' value='".$row["adres"]."' class='form-control'></div>";
            echo "<div class='form-group'><label class='form-label'>Telefon:</label><input type='text' name='tel' value='".$row["tel"]."' class='form-control' pattern='[0-9]{11}' title='11 haneli telefon numarası girin' maxlength='11' required></div>";
            echo "<input type='hidden' name='musteriID' value='".$musteriID."'>"; //gizli müşteri id 
            echo "<button type='submit' name='guncelle_musteri_submit' class='btn btn-primary'>Güncelle</button>"; // ikinci güncelle butonu
            echo "</form>";
        } else {
            echo "Müşteri bulunamadı.";
        }
    }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="form-group">
            <label>Ad:</label>
            <input type="text" name="ad" class="form-control">
        </div>
        <div class="form-group">
            <label>Soyad:</label>
            <input type="text" name="soyad" class="form-control">
        </div>
        <div class="form-group">
            <label>Adres:</label>
            <input type="text" name="adres" class="form-control">
        </div>
        <div class="form-group">
            <label>Telefon:</label>
            <input type="text" name="tel" class="form-control" pattern="[0-9]{11}" title="11 haneli telefon numarası girin" maxlength="11" required>   
        </div>
        <button type="submit" name="kaydet" class="btn btn-primary">Kaydet</button> <!-- Kaydet butonu--> 
    </form>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <button type="submit" name="listele" class="btn btn-info mt-3">Listele</button> <!-- Listele butonu--> 
    </form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
