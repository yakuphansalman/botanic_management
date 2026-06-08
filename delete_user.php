<?php
    ob_start();
    session_start();
    
    if(!isset($_SESSION['username'])){
        header("Location: login.php?error=unauthorized");
        exit();
    }

    if($_SESSION['role'] != 'admin'){
        header("Location: home.php?error=norole");
        exit();
    }

    include("connectdb.php");

    if(isset($_GET['id']) && !empty($_GET['id'])) {
        $delete_id = mysqli_real_escape_string($conn, $_GET['id']);

        if($delete_id == $active_admin_id) {
            mysqli_close($conn);
            header("Location: users.php?error=selfdelete");
            exit();
        }

        $sql = "DELETE FROM users WHERE user_id = '$delete_id'";
        $received = mysqli_query($conn, $sql);

        if($received) {
            mysqli_close($conn);
            header("Location: users.php?msg=deleted");
            exit();
        } else {
            echo "Kullanıcı silinirken bir hata oluştu: " . mysqli_error($conn);
            mysqli_close($conn);
        }
    } else {
        mysqli_close($conn);
        header("Location: users.php");
        exit();
    }
?>