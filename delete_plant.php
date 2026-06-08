<?php
    ob_start();
    session_start();
    
    if(!isset($_SESSION['username'])){
        header("Location: login.php?error=unauthorized");
        exit();
    }

    if($_SESSION['role'] != 'user' && $_SESSION['role'] != 'admin'){
        header("Location: home.php?error=norole");
        exit();
    }

    include("connectdb.php");

    if(isset($_GET['id']) && !empty($_GET['id'])) {
        $plant_id = mysqli_real_escape_string($conn, $_GET['id']);
        $active_user_id = $_SESSION['user_id']; 

       if($_SESSION['role'] == 'admin') {
            $sql = "DELETE FROM plants WHERE plant_id = '$plant_id'";
        } else {
            $sql = "DELETE FROM plants WHERE plant_id = '$plant_id' AND user_id = '$active_user_id'";
        }
        
        $received = mysqli_query($conn, $sql);

        if($received && mysqli_affected_rows($conn) > 0) {
            mysqli_close($conn);
            header("Location: plants.php?msg=deleted");
            exit();
        } else {
            mysqli_close($conn);
            header("Location: plants.php?error=notyourplant");
            exit();
        }
    } else {
        mysqli_close($conn);
        header("Location: plants.php");
        exit();
    }
?>