<?php
    ob_start();
    session_start();
    
    if(!isset($_SESSION['username'])){
        header("Location: login.php?error=unauthorized");
        exit();
    }
    

    
    include("connectdb.php");
    include("header.php"); 

    $sql = "SELECT 
                ml.m_id AS log_id,
                ml.action_type,
                ml.action_date,
                ml.notes,
                p.common_name AS plant_name,
                p.botanical_name AS plant_latin,
                u.name AS staff_name
            FROM maintenance_logs ml
            INNER JOIN plants p ON ml.plant_id = p.plant_id
            INNER JOIN users u ON ml.user_id = u.user_id
            ORDER BY ml.action_date DESC, ml.m_id DESC";
            
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bakım ve Sulama Kayıtları</title>
</head>
<body class="bg-light">

    <div class="container my-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success fw-bold">💧 Bakım & Sulama Günlüğü</h2>
            <div>
                <a href="home.php" class="btn btn-outline-secondary me-2">Ana Sayfaya Dön</a>
                <?php if($_SESSION['role'] == 'staff' || $_SESSION['role'] == 'admin'): ?>
                    <a href="add_maintenance.php" class="btn btn-success">+ Yeni Bakım Kaydı Ekle</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'added'): ?>
                <div class="alert alert-success py-2 text-center" role="alert">Bakım kaydı başarıyla günlüğe eklendi!</div>
            <?php elseif($_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-warning py-2 text-center" role="alert">Bakım kaydı başarıyla silindi.</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-success text-dark">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Bitki Adı</th>
                                <th>İşlem Türü</th>
                                <th>İşlem Tarihi</th>
                                <th>Görevli Personel</th>
                                <th>Notlar</th>
                                <th class="text-center" style="width: 100px;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-secondary">#<?php echo $row['log_id']; ?></td>
                                        <td>
                                            <span class="fw-bold"><?php echo $row['plant_name']; ?></span> <br>
                                            <small class="text-muted fst-italic"><?php echo $row['plant_latin']; ?></small>
                                        </td>
                                        <td>
                                            <?php 
                                                $action = $row['action_type'];
                                                $badge_class = "bg-primary"; 
                                                if($action == 'Sulama') $badge_class = "bg-info text-dark";
                                                if($action == 'Gübreleme') $badge_class = "bg-warning text-dark";
                                                if($action == 'Budama') $badge_class = "bg-success";
                                                if($action == 'İlaçlama') $badge_class = "bg-danger";
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?> px-2 py-1.5"><?php echo $action; ?></span>
                                        </td>
                                        <td><?php echo date('d.m.Y', strtotime($row['action_date'])); ?></td>
                                        <td>
                                            <span class="small fw-semibold text-dark">👤 <?php echo $row['staff_name']; ?></span>
                                        </td>
                                        <td class="text-wrap" style="max-width: 250px;">
                                            <small class="text-secondary"><?php echo !empty($row['notes']) ? $row['notes'] : '-'; ?></small>
                                        </td>
                                        <td class="text-center">
                                            <?php if($_SESSION['role'] == 'staff' || $_SESSION['role'] == 'admin'): ?>
                                                <a href="delete_maintenance.php?id=<?php echo $row['log_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bu bakım kaydını silmek istediğinize emin misiniz?');">Sil</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <p class="mb-0">Henüz yapılmış herhangi bir bakım veya sulama kaydı bulunmuyor.</p>
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