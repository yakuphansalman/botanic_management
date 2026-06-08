<?php
    include("connectdb.php");
    include("header.php");

    if (isset($_POST['register'])) {
        extract($_POST);

        $sql = "SELECT * FROM users WHERE username = '$uname'";
        $received = mysqli_query($conn, $sql);
        if (mysqli_num_rows($received) > 0) {
            mysqli_close($conn);
            header("Location: register.php?error=taken");
            exit();
        }

        $fullname = $fname . ' '. $lname;
        $hashed = password_hash($pword, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users " . 
        "(username, name, password) ".
        "VALUES ('$uname', '$fullname', '$hashed')";
        
        $received = mysqli_query($conn, $sql);
        if(!$received){
            echo "<br>ERROR: " . mysqli_error($conn);
            mysqli_close($conn);
        }
        else{
            mysqli_close($conn);
            header("Location: login.php?success=1");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html>
    <body class="bg-light">

        <div class="d-flex align-items-center justify-content-center vh-100">
            <div class="card shadow-sm p-4" style="width: 100%; max-width: 400px;">
                
                <h3 class="text-center text-success mb-4">🌿 Kayıt Ol</h3>
                
                <?php if(isset($_GET['error']) && $_GET['error'] == 'taken'): ?>
                    <div class="alert alert-danger py-2 text-center" role="alert">
                        Kullanıcı adı zaten alınmış.
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div class="alert alert-success py-2 text-center" role="alert">
                        Kaydınız başarıyla tamamlandı!
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">İsim</label>
                        <input type="text" class="form-control" name="fname" required/>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Soyisim</label>
                        <input type="text" class="form-control" name="lname" required/>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kullanıcı Adı</label>
                        <input type="text" class="form-control" name="uname" required/>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Şifre</label>
                        <input type="password" class="form-control" name="pword" required/>
                    </div>
                    
                    <input type="submit" name="register" class="btn btn-success w-100 mb-3" value="Kayıt Ol">
                </form>

                <div class="text-center">
                    <a href="login.php" class="text-decoration-none text-success small">Zaten bir hesabın var mı? Giriş Yap</a>
                </div>

            </div>
        </div>

    </body>

</html>