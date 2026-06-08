<?php
    ob_start();
    session_start();
    
    if(!isset($_SESSION['username'])){
        header("Location: login.php?error=unauthorized");
        exit();
    }

    include("connectdb.php");
    include("header.php");

    $sql = "SELECT * FROM plants ORDER BY plant_id DESC";
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bitki Koleksiyonu Yönetimi</title>
</head>
<body class="bg-light">

    <div class="container my-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success fw-bold">🌿 Bitki Koleksiyonu</h2>
            <div>
                <a href="home.php" class="btn btn-outline-secondary me-2">Ana Sayfaya Dön</a>
                <?php if ($_SESSION['role'] == 'user' || $_SESSION['role'] == 'admin'): ?>
                    <a href="add_plant.php" class="btn btn-success">+ Yeni Bitki Ekle</a>    
                <?php endif; ?>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'added'): ?>
                <div class="alert alert-success py-2 text-center" role="alert">Bitki başarıyla koleksiyona eklendi!</div>
            <?php elseif($_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-warning py-2 text-center" role="alert">Bitki kaydı başarıyla silindi.</div>
            <?php elseif($_GET['msg'] == 'updated'): ?>
                <div class="alert alert-success py-2 text-center" role="alert">Bitki bilgileri başarıyla güncellendi.</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-success text-dark">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Bilimsel (Latince) Adı</th>
                                <th>Halk Arasındaki Adı</th>
                                <th>Bulunduğu Bölüm</th>
                                <th>Ekilme Tarihi</th>
                                <th class="text-center" style="width: 180px;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-secondary">#<?php echo $row['plant_id']; ?></td>
                                        <td class="fst-italic"><?php echo $row['botanical_name']; ?></td>
                                        <td><?php echo $row['common_name']; ?></td>
                                        <td>
                                            <span class="badge bg-secondary px-2.5 py-1.5"><?php echo $row['garden_section']; ?></span>
                                        </td>
                                        <td><?php echo date('d.m.Y', strtotime($row['planted_date'])); ?></td>
                                        <td class="text-center">
                                            <?php 
                                                $active_user_id = $_SESSION['user_id']; 
                                                
                                                // Bu kod döngünün İÇİNDE olduğu için $row['user_id'] sorunsuz çalışır
                                                if ($_SESSION['role'] == 'admin' || $row['user_id'] == $active_user_id): 
                                            ?>
                                                <a href="edit_plant.php?id=<?php echo $row['plant_id']; ?>" class="btn btn-sm btn-outline-primary me-1">Düzenle</a>
                                                <a href="delete_plant.php?id=<?php echo $row['plant_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Emin misiniz?');">Sil</a>
                                            <?php else: ?>
                                                <span class="text-muted small">🔒 Sadece Sahibi</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <p class="mb-0">Bahçede henüz kayıtlı bir bitki bulunmuyor.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <?php mysqli_close($conn); ?>
</body>
</html>