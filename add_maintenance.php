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
    include("header.php"); 

    $session_username = $_SESSION['username'];
    $user_query = "SELECT user_id FROM users WHERE username = '$session_username'";
    $user_result = mysqli_query($conn, $user_query);
    $user_row = mysqli_fetch_assoc($user_result);
    $active_user_id = $user_row['user_id'];

    if(isset($_POST['btn_add_log'])){
        extract($_POST);

        $p_id = mysqli_real_escape_string($conn, $plant_id);
        $action = mysqli_real_escape_string($conn, $action_type);
        $date = mysqli_real_escape_string($conn, $action_date);
        $note = mysqli_real_escape_string($conn, $notes);

        $sql = "INSERT INTO maintenance_logs (plant_id, user_id, action_type, action_date, notes) 
                VALUES ('$p_id', '$active_user_id', '$action', '$date', '$note')";
        
        $received = mysqli_query($conn, $sql);

        if($received){
            mysqli_close($conn);
            header("Location: maintenance.php?msg=added");
            exit();
        } else {
            $error_msg = "Kayıt eklenirken bir hata oluştu: " . mysqli_error($conn);
        }
    }

    $plants_sql = "SELECT plant_id, common_name, botanical_name FROM plants ORDER BY common_name ASC";
    $plants_result = mysqli_query($conn, $plants_sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Bakım Kaydı Ekle</title>
</head>
<body class="bg-light">

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="d-flex align-items-center mb-4">
                    <a href="maintenance.php" class="btn btn-outline-secondary btn-sm me-3">&larr; Geri Dön</a>
                    <h3 class="text-success fw-bold mb-0">💧 Yeni Bakım Girişi</h3>
                </div>

                <?php if(isset($error_msg)): ?>
                    <div class="alert alert-danger py-2 text-center small" role="alert">
                        <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm p-4">
                    <form action="add_maintenance.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bakım Yapılan Bitki</label>
                            <select class="form-select" name="plant_id" required>
                                <option value="" disabled selected>Bitki Seçiniz...</option>
                                <?php if(mysqli_num_rows($plants_result) > 0): ?>
                                    <?php while($p_row = mysqli_fetch_assoc($plants_result)): ?>
                                        <option value="<?php echo $p_row['plant_id']; ?>">
                                            <?php echo $p_row['common_name'] . " (" . $p_row['botanical_name'] . ")"; ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <option value="" disabled>Önce bitki koleksiyonuna veri eklemelisiniz!</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Yapılan İşlem Türü</label>
                            <select class="form-select" name="action_type" required>
                                <option value="" disabled selected>İşlem Seçiniz...</option>
                                <option value="Sulama">Sulama</option>
                                <option value="Gübreleme">Gübreleme</option>
                                <option value="Budama">Budama</option>
                                <option value="İlaçlama">İlaçlama</option>
                                <option value="Toprak Değişimi">Toprak Değişimi</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">İşlem Tarihi</label>
                            <input type="date" class="form-control" name="action_date" value="<?php echo date('Y-m-d'); ?>" required />
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Yapılan İşlemle İlgili Notlar (Opsiyonel)</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Örn: 2 Litre su verildi, yapraklar kontrol edildi..."></textarea>
                        </div>

                        <button type="submit" name="btn_add_log" class="btn btn-success w-100 py-2">
                            Bakım Günlüğüne Kaydet
                        </button>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <?php mysqli_close($conn); ?>
</body>
</html>