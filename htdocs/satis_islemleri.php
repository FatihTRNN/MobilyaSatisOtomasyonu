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

function urunListele($conn) {
    $sql = "SELECT urunID, urunAd FROM Urunler WHERE durum = true AND stok > 0"; // 'durum' true ve 'stok' 0'dan büyük olan ürünleri veritabanından seç
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) { // Eğer sonuçlar varsa, ürünleri dropdown menüde listele
        echo "<option value=''>Ürün Seçiniz</option>";
        while($row = $result->fetch_assoc()) {
            echo "<option value='" . $row["urunID"] . "'>" . $row["urunAd"] . "</option>";
        }
    } else {
        echo "<option value=''>Ürün Bulunamadı</option>";
    }
}

function musteriListele($conn) {
    $sql = "SELECT musteriID, CONCAT(ad, ' ', soyad) AS adSoyad FROM Musteriler WHERE durum = true"; // 'durum' true olan müşterileri veritabanından listele
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) { // Eğer sonuçlar varsa, müşterileri dropdown menüde(seçim yapılan menü) listeler
        echo "<option value=''>Müşteri Seçiniz</option>";
        while($row = $result->fetch_assoc()) {
            echo "<option value='" . $row["musteriID"] . "'>" . $row["adSoyad"] . "</option>";
        }
    } else {
        echo "<option value=''>Müşteri Bulunamadı</option>";
    }
}

if(isset($_POST["calculateSale"])) { // Satış fiyatı hesaplama 
    $urunID = $_POST["urunID"];
    $adet = $_POST["adet"];

    $sql = "SELECT satisFiyat FROM Urunler WHERE urunID = '$urunID'"; // veritabanından satış fiyatını al
    $result = $conn->query($sql);

    if ($result->num_rows > 0) { // Eğer ürün bulunduysa, toplam fiyatı hesapla ve JSON formatında döndür
        $row = $result->fetch_assoc();
        $satisFiyat = $row["satisFiyat"];
        $toplamFiyat = $satisFiyat * $adet;
        echo json_encode(array("success" => true, "toplamFiyat" => $toplamFiyat));
    } else {
        echo json_encode(array("success" => false, "error" => "Ürün bulunamadı"));
    }
    exit();
}

if(isset($_POST["confirmSale"])) { // Satış işlemini onaylama
    $urunID = $_POST["urunID"];
    $musteriID = $_POST["musteriID"];
    $adet = $_POST["adet"];

    $sql = "SELECT * FROM SatisListesi WHERE urunID = '$urunID' AND musteriID = '$musteriID'";  // Veritabanından aynı ürün ve müşteri için daha önce yapılmış bir satış olup olmadığını kontrol et
    $result = $conn->query($sql);

    if ($result->num_rows > 0) { // Eğer daha önce yapılmış bir satış varsa, mevcut satış miktarını güncelle
        $row = $result->fetch_assoc();
        $yeniAdet = $row["adet"] + $adet;
        $sql = "UPDATE SatisListesi SET adet = '$yeniAdet', toplamFiyat = toplamFiyat + (SELECT satisFiyat FROM Urunler WHERE urunID = '$urunID') * '$adet' WHERE urunID = '$urunID' AND musteriID = '$musteriID'";
    } else { // yoksa yeni satış kaydı ekle
        $sql = "INSERT INTO SatisListesi (urunID, urunAd, musteriID, adet, toplamFiyat, tarih) VALUES ('$urunID', (SELECT urunAd FROM Urunler WHERE urunID = '$urunID'), '$musteriID', '$adet', (SELECT satisFiyat FROM Urunler WHERE urunID = '$urunID') * '$adet', NOW())";
    }

    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("success" => false, "error" => $conn->error));
    }
    exit();
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
    <title>Satış İşlemleri</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="musteri_islemleri.php">Müşteri İşlemleri</a></li>
                <li class="nav-item"><a class="nav-link" href="urun_islemleri.php">Ürün İşlemleri</a></li>
                <li class="nav-item">
                    <form method="post">
                        <button type="submit" name="logout" class="btn btn-danger">Çıkış Yap</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <form id="saleForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="musteriID">Müşteri Seçin:</label>
            <select id="musteriID" name="musteriID" class="form-control" required>
                <?php musteriListele($conn); ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="urunID">Ürün Seçin:</label>
            <select id="urunID" name="urunID" class="form-control" required>
                <?php urunListele($conn); ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="adet">Adet:</label>
            <input type="number" id="adet" name="adet" min="1" value="1" class="form-control">
        </div>
        
        <button type="button" id="calculateBtn" class="btn btn-info">Hesapla</button>
        <div class="form-group mt-2">
            <label>Toplam Fiyat:</label>
            <span id="toplamFiyat" class="form-control-plaintext"></span>
        </div>
        <button type="button" id="confirmSaleBtn" class="btn btn-success">Satış Yap</button>
    </form>
    
    <h2 class="mt-5">Satış Listesi</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Ürün Adı</th>
                <th>Müşteri Adı</th>
                <th>Adet</th>
                <th>Toplam Fiyat</th>
                <th>Tarih</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT u.urunAd, CONCAT(m.ad, ' ', m.soyad) AS adSoyad, s.adet, s.toplamFiyat, s.tarih 
                    FROM SatisListesi s
                    JOIN Urunler u ON s.urunID = u.urunID
                    JOIN Musteriler m ON s.musteriID = m.musteriID";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["urunAd"] . "</td>";
                    echo "<td>" . $row["adSoyad"] . "</td>";
                    echo "<td>" . $row["adet"] . "</td>";
                    echo "<td>" . $row["toplamFiyat"] . "</td>";
                    echo "<td>" . $row["tarih"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Satış bulunamadı.</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
    var hesaplamaYapildi = false;

    $("#calculateBtn").click(function(){ //Hesapla butonu 
        var urunID = $("#urunID").val();
        var adet = $("#adet").val();
        $.post("<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>", {
            calculateSale: true,
            urunID: urunID,
            adet: adet
        }, function(data, status){
            var response = JSON.parse(data);
            if(response.success) {
                $("#toplamFiyat").text(response.toplamFiyat + " TL");
                hesaplamaYapildi = true;
            } else {
                $("#toplamFiyat").text("Hesaplama başarısız.");
                hesaplamaYapildi = false;
            }
        });
    });

    $("#confirmSaleBtn").click(function(){ // Satış yap butonu
        if (!hesaplamaYapildi) {
            alert("Lütfen önce hesaplama işlemini yapın.");
            return;
        }

        var urunID = $("#urunID").val();
        var musteriID = $("#musteriID").val();
        var adet = $("#adet").val();
        $.post("<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>", {
            confirmSale: true,
            urunID: urunID,
            musteriID: musteriID,
            adet: adet
        }, function(data, status){
            var response = JSON.parse(data);
            if(response.success) {
                alert("Satış başarıyla gerçekleştirildi.");
                location.reload();
            } else {
                alert("Satış yapılırken bir hata oluştu: " + response.error);
            }
        });
    });
});
</script>

</body>
</html>
