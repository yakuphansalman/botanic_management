<?php
    ob_start();
    session_start();
    
    if(!isset($_SESSION['username'])){
        header("Location: login.php?error=unauthorized");
        exit();
    }

    if($_SESSION['role'] != 'organizer' && $_SESSION['role'] != 'admin'){
        header("Location: home.php?error=norole");
        exit();
    }

    include("connectdb.php");

    if(isset($_GET['id']) && !empty($_GET['id'])) {
        $visitor_id = mysqli_real_escape_string($conn, $_GET['id']);

        $sql = "DELETE FROM visitors WHERE visitor_id = '$visitor_id'";
        $received = mysqli_query($conn, $sql);

        if($received) {
            mysqli_close($conn);
            header("Location: visitors.php?msg=deleted");
            exit();
        } else {
            echo "Bilet kaydı silinirken veritabanı hatası oluştu: " . mysqli_error($conn);
            mysqli_close($conn);
        }
    } else {
        mysqli_close($conn);
        header("Location: visitors.php");
        exit();
    }
?>