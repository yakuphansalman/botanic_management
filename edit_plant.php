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
    include("header.php");

    if(isset($_GET['id']) && !empty($_GET['id'])){
        $plant_id = mysqli_real_escape_string($conn, $_GET['id']);
        $active_user_id = $_SESSION['user_id'];

        if($_SESSION['role'] == 'admin') {
            $search_sql = "SELECT * FROM plants WHERE plant_id = '$plant_id'";
        } else {
            $search_sql = "SELECT * FROM plants WHERE plant_id = '$plant_id' AND user_id = '$active_user_id'";
        }
        $search_result = mysqli_query($conn, $search_sql);
        
        if(mysqli_num_rows($search_result) == 1){
            $plant = mysqli_fetch_assoc($search_result);
        } else {
            mysqli_close($conn);
            header("Location: plants.php");
            exit();
        }
    } else {
        header("Location: plants.php");
        exit();
    }

    if(isset($_POST['btn_edit_plant'])){
        extract($_POST);

        $botanical = mysqli_real_escape_string($conn, $botanical_name);
        $common = mysqli_real_escape_string($conn, $common_name);
        $section = mysqli_real_escape_string($conn, $garden_section);
        $p_date = mysqli_real_escape_string($conn, $planted_date);

        $update_sql = "UPDATE plants SET 
                        botanical_name = '$botanical', 
                        common_name = '$common', 
                        garden_section = '$section', 
                        planted_date = '$p_date' 
                       WHERE plant_id = '$plant_id'";
        
        $received = mysqli_query($conn, $update_sql);

        if($received){
            mysqli_close($conn);
            header("Location: plants.php?msg=updated");
            exit();
        } else {
            $error_msg = "Güncelleme yapılırken veritabanı hatası oluştu: " . mysqli_error($conn);
        }
    }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bitki Bilgilerini Düzenle</title>
</head>
<body class="bg-light">

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="d-flex align-items-center mb-4">
                    <a href="plants.php" class="btn btn-outline-secondary btn-sm me-3">&larr; Geri Dön</a>
                    <h3 class="text-success fw-bold mb-0">🌿 Bitki Düzenleme</h3>
                </div>

                <?php if(isset($error_msg)): ?>
                    <div class="alert alert-danger py-2 text-center small" role="alert">
                        <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm p-4">
                    <form action="edit_plant.php?id=<?php echo $plant_id; ?>" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bilimsel (Latince) Adı</label>
                            <input type="text" class="form-control" name="botanical_name" value="<?php echo $plant['botanical_name']; ?>" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Halk Arasındaki Adı</label>
                            <input type="text" class="form-control" name="common_name" value="<?php echo $plant['common_name']; ?>" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Bulunduğu Bahçe/Sera Bölümü</label>
                            <select class="form-select" name="garden_section" required>
                                <option value="Tropikal Sera" <?php if($plant['garden_section'] == 'Tropikal Sera') echo 'selected'; ?>>Tropikal Sera</option>
                                <option value="Kaya Bahçesi" <?php if($plant['garden_section'] == 'Kaya Bahçesi') echo 'selected'; ?>>Kaya Bahçesi</option>
                                <option value="Japon Bahçesi" <?php if($plant['garden_section'] == 'Japon Bahçesi') echo 'selected'; ?>>Japon Bahçesi</option>
                                <option value="Tıbbi Bitkiler Alanı" <?php if($plant['garden_section'] == 'Tıbbi Bitkiler Alanı') echo 'selected'; ?>>Tıbbi Bitkiler Alanı</option>
                                <option value="Açık Karasal Alan" <?php if($plant['garden_section'] == 'Açık Karasal Alan') echo 'selected'; ?>>Açık Karasal Alan</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Ekilme/Geliş Tarihi</label>
                            <input type="date" class="form-control" name="planted_date" value="<?php echo $plant['planted_date']; ?>" required />
                        </div>

                        <button type="submit" name="btn_edit_plant" class="btn btn-success w-100 py-2">
                            Değişiklikleri Kaydet
                        </button>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <?php mysqli_close($conn); ?>
</body>
</html>