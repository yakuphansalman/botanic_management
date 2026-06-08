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

    if(isset($_POST['btn_add_event'])){
        extract($_POST);

        $e_title = mysqli_real_escape_string($conn, $title);
        $e_desc = mysqli_real_escape_string($conn, $description);
        $e_start = mysqli_real_escape_string($conn, $start_date);
        $e_end = mysqli_real_escape_string($conn, $end_date);
        $e_loc = mysqli_real_escape_string($conn, $location);

        $sql = "INSERT INTO events (title, description, start_date, end_date, location) 
                VALUES ('$e_title', '$e_desc', '$e_start', '$e_end', '$e_loc')";
        
        $received = mysqli_query($conn, $sql);

        if($received){
            mysqli_close($conn);
            header("Location: events.php?msg=added");
            exit();
        } else {
            $error_msg = "Etkinlik planlanırken veritabanı hatası oluştu: " . mysqli_error($conn);
        }
    }
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Etkinlik Planla</title>
</head>
<body class="bg-light">

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="d-flex align-items-center mb-4">
                    <a href="events.php" class="btn btn-outline-secondary btn-sm me-3">&larr; Geri Dön</a>
                    <h3 class="text-success fw-bold mb-0">📅 Yeni Etkinlik Planla</h3>
                </div>

                <?php if(isset($error_msg)): ?>
                    <div class="alert alert-danger py-2 text-center small" role="alert">
                        <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm p-4">
                    <form action="add_event.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Etkinlik / Faaliyet Başlığı</label>
                            <input type="text" class="form-control" name="title" placeholder="Örn: Tıbbi Bitkiler Hasat Festivali" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Düzenleneceği Yer (Konum)</label>
                            <input type="text" class="form-control" name="location" placeholder="Örn: Kuzey Sera Alanı veya Konferans Salonu" required />
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Başlangıç Tarihi & Saati</label>
                                <input type="datetime-local" class="form-control" name="start_date" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bitiş Tarihi & Saati</label>
                                <input type="datetime-local" class="form-control" name="end_date" required />
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Etkinlik Detayları / Açıklama</label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Etkinliğin amacı, katılacak görevliler ve yapılacak işler hakkında kısa bilgi yazın..."></textarea>
                        </div>

                        <button type="submit" name="btn_add_event" class="btn btn-success w-100 py-2">
                            Takvime Ekle ve Yayınla
                        </button>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <?php mysqli_close($conn); ?>
</body>
</html>