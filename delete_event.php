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
        $event_id = mysqli_real_escape_string($conn, $_GET['id']);

        $sql = "DELETE FROM events WHERE event_id = '$event_id'";
        $received = mysqli_query($conn, $sql);

        if($received) {
            mysqli_close($conn);
            header("Location: events.php?msg=deleted");
            exit();
        } else {
            echo "Etkinlik silinirken veritabanı hatası oluştu: " . mysqli_error($conn);
            mysqli_close($conn);
        }
    } else {
        mysqli_close($conn);
        header("Location: events.php");
        exit();
    }
?>