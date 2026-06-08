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
    include("header.php"); 

    $sql = "SELECT 
                v.visitor_id,
                v.full_name,
                v.visit_date,
                v.ticket_type,
                e.title AS event_title
            FROM visitors v
            LEFT JOIN events e ON v.event_id = e.event_id
            ORDER BY v.visit_date DESC, v.visitor_id DESC";
            
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ziyaretçi ve Bilet Yönetimi</title>
</head>
<body class="bg-light">

    <div class="container my-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success fw-bold">🎫 Ziyaretçi & Bilet Kayıtları</h2>
            <div>
                <a href="home.php" class="btn btn-outline-secondary me-2">Ana Sayfaya Dön</a>
                <a href="add_visitor.php" class="btn btn-success">+ Yeni Bilet/Ziyaretçi Kaydet</a>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-warning py-2 text-center shadow-sm" role="alert">Ziyaretçi / bilet kaydı başarıyla silindi.</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-success text-dark">
                            <tr>
                                <th class="ps-3">Bilet ID</th>
                                <th>Ziyaretçi Adı Soyadı</th>
                                <th>Ziyaret Tarihi</th>
                                <th>Bilet Türü</th>
                                <th>Katıldığı Etkinlik</th>
                                <th class="text-center" style="width: 100px;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-secondary">#<?php echo $row['visitor_id']; ?></td>
                                        <td class="fw-bold text-dark"><?php echo $row['full_name']; ?></td>
                                        <td>📅 <?php echo date('d.m.Y', strtotime($row['visit_date'])); ?></td>
                                        <td>
                                            <?php 
                                                $ticket = $row['ticket_type'];
                                                $badge = "bg-secondary";
                                                if($ticket == 'Tam') $badge = "bg-primary";
                                                if($ticket == 'Öğrenci') $badge = "bg-info text-dark";
                                                if($ticket == 'Ücretsiz' || $ticket == 'Protokol') $badge = "bg-success";
                                            ?>
                                            <span class="badge <?php echo $badge; ?> px-2.5 py-1.5"><?php echo $ticket; ?></span>
                                        </td>
                                        <td>
                                            <span class="text-success fw-semibold">
                                                📌 <?php echo !empty($row['event_title']) ? $row['event_title'] : 'Genel Bahçe Ziyareti'; ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="delete_visitor.php?id=<?php echo $row['visitor_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bu bilet kaydını silmek istediğinize emin misiniz?');">Sil</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <p class="mb-0">Henüz kayıtlı ziyaretçi veya bilet bulunmuyor.</p>
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