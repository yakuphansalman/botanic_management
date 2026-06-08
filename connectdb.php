<?php
    $server = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'db';
    $conn = mysqli_connect($server,$user,$password,$database);
    if (!$conn) {
        echo "MySQL sunucu ile baglanti kurulamadi! </br>";
        echo "HATA: " . mysqli_connect_error();
        exit;
    }
?>