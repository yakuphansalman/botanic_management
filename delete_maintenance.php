<?php
    ob_start();
    session_start();
    
    if(!isset($_SESSION['username'])){
        header("Location: login.php?error=unauthorized");
        exit();
    }
    if($_SESSION['role'] != 'staff' && $_SESSION['role'] != 'admin'){
        header("Location: maintenance.php?error=norole");
        exit();
    }

    include("connectdb.php");

    if(isset($_GET['id']) && !empty($_GET['id'])) {
        $m_id = mysqli_real_escape_string($conn, $_GET['id']);

        $sql = "DELETE FROM maintenance_logs WHERE m_id = '$m_id'";
        $received = mysqli_query($conn, $sql);

        if($received) {
            mysqli_close($conn);
            header("Location: maintenance.php?msg=deleted");
            exit();
        } else {
            echo "Kayıt silinirken bir hata oluştu: " . mysqli_error($conn);
            mysqli_close($conn);
        }
    } else {
        mysqli_close($conn);
        header("Location: maintenance.php");
        exit();
    }
?>