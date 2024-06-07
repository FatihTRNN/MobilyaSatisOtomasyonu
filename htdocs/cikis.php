<?php
session_start();

// Oturumu sonlandır
session_destroy();

// Kullanıcıyı giriş yapma sayfasına yönlendir
header("Location: giris.php");
exit();
?>
