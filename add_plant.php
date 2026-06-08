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
    

    if(isset($_POST['btn_add_plant'])){
        extract($_POST);

        $botanical = mysqli_real_escape_string($conn, $botanical_name);
        $common = mysqli_real_escape_string($conn, $common_name);
        $section = mysqli_real_escape_string($conn, $garden_section);
        $p_date = mysqli_real_escape_string($conn, $planted_date);
        $active_user_id = $_SESSION['user_id'];

        $sql = "INSERT INTO plants (user_id, botanical_name, common_name, garden_section, planted_date) 
        VALUES ('$active_user_id', '$botanical', '$common', '$section', '$p_date')";
        $received = mysqli_query($conn, $sql);

        if($received){
            mysqli_close($conn);
            header("Location: plants.php?msg=added");
            exit();
        } else {
            $error_msg = "Veritabanına ekleme yapılırken bir hata oluştu: " . mysqli_error($conn);
        }
    }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Bitki Ekle</title>
</head>
<body class="bg-light">

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="d-flex align-items-center mb-4">
                    <a href="plants.php" class="btn btn-outline-secondary btn-sm me-3">&larr; Geri Dön</a>
                    <h3 class="text-success fw-bold mb-0">🌿 Yeni Bitki Kaydı</h3>
                </div>

                <?php if(isset($error_msg)): ?>
                    <div class="alert alert-danger py-2 text-center small" role="alert">
                        <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm p-4">
                    <form action="add_plant.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bilimsel (Latince) Adı</label>
                            <input type="text" class="form-control" name="botanical_name" placeholder="Örn: Pinus sylvestris" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Halk Arasındaki Adı</label>
                            <input type="text" class="form-control" name="common_name" placeholder="Örn: Sarıçam" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Bulunduğu Bahçe/Sera Bölümü</label>
                            <select class="form-select" name="garden_section" required>
                                <option value="" disabled selected>Bölüm Seçiniz...</option>
                                <option value="Tropikal Sera">Tropikal Sera</option>
                                <option value="Kaya Bahçesi">Kaya Bahçesi</option>
                                <option value="Japon Bahçesi">Japon Bahçesi</option>
                                <option value="Tıbbi Bitkiler Alanı">Tıbbi Bitkiler Alanı</option>
                                <option value="Açık Karasal Alan">Açık Karasal Alan</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Ekilme/Geliş Tarihi</label>
                            <input type="date" class="form-control" name="planted_date" value="<?php echo date('Y-m-d'); ?>" required />
                        </div>

                        <button type="submit" name="btn_add_plant" class="btn btn-success w-100 py-2">
                            Koleksiyona Ekle
                        </button>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <?php mysqli_close($conn); ?>
</body>
</html>