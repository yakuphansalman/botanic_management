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

    if(isset($_POST['btn_add_user'])){
        extract($_POST);

        $uname = mysqli_real_escape_string($conn, $username);
        $pword = mysqli_real_escape_string($conn, $password);
        $real_name = mysqli_real_escape_string($conn, $name);
        $selected_role = mysqli_real_escape_string($conn, $role);

        $check_sql = "SELECT user_id FROM users WHERE username = '$uname'";
        $check_result = mysqli_query($conn, $check_sql);

        if(mysqli_num_rows($check_result) > 0) {
            $error_msg = "⚠️ Bu kullanıcı adı sistemde zaten kayıtlı!";
        } else {
            $hashed_password = password_hash($pword, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, password, name, role) 
                    VALUES ('$uname', '$hashed_password', '$real_name', '$selected_role')";
            
            $received = mysqli_query($conn, $sql);

            if($received){
                mysqli_close($conn);
                header("Location: users.php?msg=created");
                exit();
            } else {
                $error_msg = "Hesap oluşturulurken veritabanı hatası: " . mysqli_error($conn);
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Kullanıcı Tanımla</title>
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="d-flex align-items-center mb-4">
                    <a href="users.php" class="btn btn-outline-secondary btn-sm me-3">&larr; Geri Dön</a>
                    <h3 class="text-danger fw-bold mb-0">👤 Yeni Kullanıcı Hesabı Tanımla</h3>
                </div>

                <?php if(isset($error_msg)): ?>
                    <div class="alert alert-danger py-2 text-center small" role="alert"><?php echo $error_msg; ?></div>
                <?php endif; ?>

                <div class="card shadow-sm p-4">
                    <form action="add_user.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Adı Soyadı</label>
                            <input type="text" class="form-control" name="name" placeholder="Örn: Görkem Çalışkan" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Kullanıcı Adı</label>
                            <input type="text" class="form-control" name="username" placeholder="Giriş yaparken kullanılacak" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Şifre</label>
                            <input type="password" class="form-control" name="password" placeholder="••••••••" required />
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Sistem Yetki Rolü</label>
                            <select class="form-select border-danger fw-semibold text-danger" name="role" required>
                                <option value="user">User (Normal Kullanıcı - Bitki Ekler)</option>
                                <option value="staff">Staff (Bahçe Personeli - Bakım Yapar)</option>
                                <option value="organizer">Organizer (Organizatör - Etkinlik & Bilet Yönetir)</option>
                                <option value="admin">Admin (Tam Yetkili Yönetici)</option>
                            </select>
                        </div>

                        <button type="submit" name="btn_add_user" class="btn btn-danger w-100 py-2 fw-semibold">Hesabı Oluştur ve Yetkilendir</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>
</html>