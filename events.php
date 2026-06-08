<?php
    ob_start();
    session_start();
    
    if(!isset($_SESSION['username'])){
        header("Location: login.php?error=unauthorized");
        exit();
    }

    include("connectdb.php");
    include("header.php"); 

    $sql = "SELECT event_id, title, description, start_date, end_date, location 
            FROM events 
            ORDER BY start_date ASC, event_id DESC";
            
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bahçe Etkinlikleri ve Faaliyetler</title>
</head>
<body class="bg-light">

    <div class="container my-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success fw-bold">📅 Etkinlikler & Faaliyetler</h2>
            <div>
                <a href="home.php" class="btn btn-outline-secondary me-2">Ana Sayfaya Dön</a>
                <?php if($_SESSION['role'] == 'organizer' || $_SESSION['role'] == 'admin'): ?>
                    <a href="add_event.php" class="btn btn-success">+ Yeni Etkinlik Planla</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'added'): ?>
                <div class="alert alert-success py-2 text-center" role="alert">Etkinlik başarıyla takvime eklendi!</div>
            <?php elseif($_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-warning py-2 text-center" role="alert">Etkinlik kaydı başarıyla silindi.</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-success text-dark">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Etkinlik Başlığı</th>
                                <th>Başlangıç Tarihi</th>
                                <th>Bitiş Tarihi</th>
                                <th>Düzenlendiği Yer</th>
                                <th>Açıklama</th>
                                <th class="text-center" style="width: 100px;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-secondary">#<?php echo $row['event_id']; ?></td>
                                        <td>
                                            <span class="fw-bold text-dark"><?php echo $row['title']; ?></span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-primary">
                                                📅 <?php echo date('d.m.Y H:i', strtotime($row['start_date'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-secondary">
                                                🏁 <?php echo date('d.m.Y H:i', strtotime($row['end_date'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary px-2 py-1.5"><?php echo $row['location']; ?></span>
                                        </td>
                                        <td class="text-wrap" style="max-width: 250px;">
                                            <small class="text-secondary"><?php echo !empty($row['description']) ? $row['description'] : '-'; ?></small>
                                        </td>
                                        <td class="text-center">
                                            <?php if($_SESSION['role'] == 'organizer' || $_SESSION['role'] == 'admin'): ?>
                                                <a href="delete_event.php?id=<?php echo $row['event_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bu etkinlik kaydını silmek istediğinize emin misiniz?');">Sil</a>
                                            <?php endif; ?>
                                        </td>
                                        
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <p class="mb-0">Planlanmış herhangi bir etkinlik veya faaliyet bulunmuyor.</p>
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