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

    // Form Gönderildiğinde
    if(isset($_POST['btn_add_visitor'])){
        extract($_POST);

        $name = mysqli_real_escape_string($conn, $full_name);
        $v_date = mysqli_real_escape_string($conn, $visit_date);
        $t_type = mysqli_real_escape_string($conn, $ticket_type);
        
        $e_id = (!empty($event_id)) ? "'".mysqli_real_escape_string($conn, $event_id)."'" : "NULL";

        $sql = "INSERT INTO visitors (full_name, visit_date, ticket_type, event_id) 
                VALUES ('$name', '$v_date', '$t_type', $e_id)";
        
        $received = mysqli_query($conn, $sql);

        if($received){
            mysqli_close($conn);
            header("Location: visitors.php");
            exit();
        } else {
            $error_msg = "Bilet oluşturulurken hata çıktı: " . mysqli_error($conn);
        }
    }

    $events_sql = "SELECT event_id, title FROM events ORDER BY start_date DESC";
    $events_result = mysqli_query($conn, $events_sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Yeni Ziyaretçi & Bilet Kaydı</title>
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="d-flex align-items-center mb-4">
                    <a href="visitors.php" class="btn btn-outline-secondary btn-sm me-3">&larr; Geri Dön</a>
                    <h3 class="text-success fw-bold mb-0">🎫 Bilet Satış / Giriş Formu</h3>
                </div>

                <?php if(isset($error_msg)): ?>
                    <div class="alert alert-danger py-2 text-center small" role="alert"><?php echo $error_msg; ?></div>
                <?php endif; ?>

                <div class="card shadow-sm p-4">
                    <form action="add_visitor.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ziyaretçi Adı Soyadı</label>
                            <input type="text" class="form-control" name="full_name" placeholder="Örn: Ahmet Yılmaz" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Bilet Türü</label>
                            <select class="form-select" name="ticket_type" required>
                                <option value="Tam">Tam Bilet</option>
                                <option value="Öğrenci">Öğrenci Bileti (%50 İndirimli)</option>
                                <option value="Ücretsiz">Ücretsiz / Görevli Kartı</option>
                                <option value="Protokol">Protokol / Özel Davetli</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Kayıtlı Olduğu Etkinlik (Opsiyonel)</label>
                            <select class="form-select" name="event_id">
                                <option value="">-- Etkinlik Harici (Genel Bahçe Girişi) --</option>
                                <?php while($e_row = mysqli_fetch_assoc($events_result)): ?>
                                    <option value="<?php echo $e_row['event_id']; ?>">
                                        <?php echo $e_row['title']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Giriş / Ziyaret Tarihi</label>
                            <input type="date" class="form-control" name="visit_date" value="<?php echo date('Y-m-d'); ?>" required />
                        </div>

                        <button type="submit" name="btn_add_visitor" class="btn btn-success w-100 py-2">Bilet Kes ve Giriş Ver</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>
</html>