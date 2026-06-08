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
    include("header.php"); 

    $sql = "SELECT user_id, username, name, role FROM users ORDER BY user_id DESC";
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kullanıcı Yönetimi</title>
</head>
<body class="bg-light">

    <div class="container my-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-danger fw-bold">👤 Sistem Kullanıcıları Yönetimi</h2>
            <div>
                <a href="home.php" class="btn btn-outline-secondary me-2">Ana Sayfaya Dön</a>
                <a href="add_user.php" class="btn btn-danger">+ Yeni Kullanıcı Oluştur</a>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success py-2 text-center" role="alert">Kullanıcı hesabı ve ilişkili tüm verileri başarıyla silindi.</div>
            <?php elseif($_GET['msg'] == 'created'): ?>
                <div class="alert alert-success py-2 text-center shadow-sm" role="alert">🎉 Yeni kullanıcı hesabı başarıyla tanımlandı ve sisteme eklendi.</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if(isset($_GET['error']) && $_GET['error'] == 'selfdelete'): ?>
            <div class="alert alert-danger py-2 text-center fw-bold" role="alert">⚠️ Kendi hesabınızı silemezsiniz!</div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">User ID</th>
                                <th>Kullanıcı Adı</th>
                                <th>Adı Soyadı</th>
                                <th>Sistem Rolü</th>
                                <th class="text-center" style="width: 120px;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-3 text-secondary fw-bold">#<?php echo $row['user_id']; ?></td>
                                    <td class="fw-semibold"><?php echo $row['username']; ?></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td>
                                        <?php 
                                            $role = $row['role'];
                                            $badge = "bg-secondary";
                                            if($role == 'admin') $badge = "bg-danger";
                                            if($role == 'organizer') $badge = "bg-warning text-dark";
                                            if($role == 'staff') $badge = "bg-info text-dark";
                                            if($role == 'user') $badge = "bg-success";
                                        ?>
                                        <span class="badge <?php echo $badge; ?> px-2.5 py-1.5"><?php echo ucfirst($role); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if($row['user_id'] != $_SESSION['user_id']): ?>
                                            <a href="delete_user.php?id=<?php echo $row['user_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz? Kullanıcıya ait tüm bitki kayıtları da otomatik olarak silinecektir!');">
                                                Kullanıcıyı Sil
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">Aktif Hesap</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <?php mysqli_close($conn); ?>
</body>
</html>