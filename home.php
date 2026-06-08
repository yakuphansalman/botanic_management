<?php
    ob_start();
    session_start();
    include("header.php");

    if(!isset($_SESSION['username'])){
        header("Location: login.php?error=unauthorized");
        exit();
    }
    
    $uname = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Botanik Bahçesi Yönetim Sistemi</title>
    </head>
    <body class="bg-light">
        
        <div class="d-flex align-items-center justify-content-center vh-100">
            <div class="text-center">
                
                <h1 class="display-4 fw-bold text-success mb-4">
                    Merhaba, <?php echo $uname; ?>
                </h1>
                
                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                    <a href="plants.php" class="btn btn-outline-success btn-lg px-4">Bitkiler</a>
                    <a href="maintenance.php" class="btn btn-outline-success btn-lg px-4">Bakımlar</a>
                    <a href="events.php" class="btn btn-outline-success btn-lg px-4">Etkinlikler</a>
                    <?php if($_SESSION['role'] == 'organizer' || $_SESSION['role'] == 'admin'): ?>
                        <a href="visitors.php" class="btn btn-outline-success btn-lg px-4">Ziyaretçiler</a>
                    <?php endif; ?>
                    <?php if($_SESSION['role'] == 'admin'): ?>
                        <a href="users.php" class="btn btn-outline-danger btn-lg px-4">👤 Kullanıcı Hesaplarını Yönet</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-danger btn-lg px-4 shadow-none text-white border-0">Çıkış yap</a>
                </div>

            </div>
        </div>

    </body>
</html>