<?php
    session_start();
    include("connectdb.php");
    include("header.php");

    if(isset($_POST['login'])){
        extract($_POST);

        $sql = "SELECT * FROM users WHERE username = '$uname'";
        $received = mysqli_query($conn, $sql);

        if(mysqli_num_rows($received) > 0){
            $user = mysqli_fetch_assoc($received);

            if(password_verify($pword, $user['password'])){
                $_SESSION['username'] = $user['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_id'] = $user['user_id'];
                mysqli_close($conn);

                header("Location: home.php?success=1");
                exit();
            }
            else{
                header("Location: login.php?error=incpw");
            }
        }
        else{
            header("Location: login.php?error=incun");
        }
        mysqli_close($conn);
    }

    

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Log in</title>
    </head>
    <body class="bg-light">

        <div class="d-flex align-items-center justify-content-center vh-100">
            <div class="card shadow-sm p-4" style="width: 100%; max-width: 400px;">
                
                <h3 class="text-center text-success mb-4">🌿 Giriş Yap</h3>
                
                <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div class="alert alert-success py-2 text-center" role="alert">
                        Kaydınız başarılı! Giriş yapabilirsiniz.
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['error'])): ?>
                    <?php if($_GET['error'] == 'unauthorized'): ?>
                        <div class="alert alert-danger shadow-sm text-center mb-3 py-2" role="alert">
                            <h6 class="fw-bold mb-1">Lütfen giriş yapın!</h6>
                            <span class="small">Yönetim paneline erişmek için önce giriş yapmalısınız.</span>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger py-2 text-center small" role="alert">
                            <?php 
                                if($_GET['error'] == 'incun') echo "Geçersiz kullanıcı adı.";
                                if($_GET['error'] == 'incpw') echo "Hatalı şifre.";
                            ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Kullanıcı Adı</label>
                        <input type="text" class="form-control" name="uname" required/>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Şifre</label>
                        <input type="password" class="form-control" name="pword" required/>
                    </div>
                    
                    <input type="submit" name="login" class="btn btn-success w-100 mb-3" value="Giriş Yap">
                </form>

                <div class="text-center">
                    <a href="register.php" class="text-decoration-none text-success small">Hesabın yok mu? Kayıt Ol</a>
                </div>

            </div>
        </div>

    </body>



</html>