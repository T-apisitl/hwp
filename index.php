<?php
// เปิดระบบแสดง Error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'connect.php';

$error_msg = "";

// 🌟 ระบบเข้าสู่ระบบของนักเรียน 🌟
if (isset($_POST['student_login'])) {
    $std_id = $conn->real_escape_string($_POST['student_id']);
    $std_num = $conn->real_escape_string($_POST['student_number']);

    // ค้นหานักเรียนในฐานข้อมูล
    $sql = "SELECT * FROM students WHERE student_id = '$std_id' AND student_number = '$std_num'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        $_SESSION['student_logged_in'] = true;
        $_SESSION['student_id'] = $user['student_id'];
        $_SESSION['student_name'] = $user['fullname'];
        $_SESSION['student_class'] = $user['class_level'];
        $_SESSION['student_room'] = $user['room_number'];
        
        header("Location: student_dashboard.php");
        exit();
    } else {
        $error_msg = "รหัสนักเรียน หรือ เลขที่ ไม่ถูกต้องครับ";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ครูริว หอวัง ปทุมธานี</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* จัดการขนาดรูปภาพให้พอดีทั้งมือถือและคอม */
        .hero-img {
            width: 250px; /* ขนาดบนมือถือ */
            max-width: 100%;
        }
        @media (min-width: 768px) {
            .hero-img {
                width: 80%; /* ขนาดบนคอม */
                max-width: 400px;
            }
        }
        /* เอฟเฟกต์ป้ายประกาศ */
        .alert-remedial { transition: transform 0.2s; }
        .alert-remedial:hover { transform: translateY(-5px); }
    </style>
</head>
<body class="bg-light">

    <?php include 'nav.php'; ?>

    <div class="container my-4 my-md-5">
        <div class="row align-items-center flex-column-reverse flex-md-row text-center text-md-start">
            
            <div class="col-md-6 mt-4 mt-md-0">
                <h6 class="text-muted text-uppercase ls-2 mb-2">
                    <?php 
                    if (isset($_SESSION['student_logged_in'])) {
                        echo "👋 ยินดีต้อนรับคุณ " . htmlspecialchars($_SESSION['student_name']) . "!";
                    } else {
                        echo "ยินดีต้อนรับสู่!";
                    }
                    ?>
                </h6>
                <h1 class="display-4 fw-bold mb-4">ห้องเรียน<br><span style="color: #ffc107;">ครูริว</span></h1>
                
                <?php if (isset($_SESSION['student_logged_in'])): ?>
                    <!-- 🌟 1. กรณีล็อกอินแล้ว: แสดงปุ่มเมนู (ปรับตามระบบใหม่) 🌟 -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a href="unit3.php" class="btn btn-warning btn-lg shadow-sm fw-bold">เข้าสู่บทเรียน</a>
                        <a href="assignment.php" class="btn btn-primary btn-lg shadow-sm text-white fw-bold">
                            <i class="fa-solid fa-list-check"></i> ฟีดคำสั่งงาน
                        </a>
                        <a href="student_dashboard.php" class="btn btn-outline-secondary btn-lg fw-bold bg-white">หน้าแรกนักเรียน</a>
                    </div>
                <?php else: ?>
                    <!-- 🌟 2. กรณียังไม่ล็อกอิน: แสดงฟอร์มเข้าสู่ระบบ 🌟 -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 mt-3 text-start mx-auto mx-md-0" style="max-width: 400px;">
                        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-user-lock text-primary"></i> เข้าสู่ระบบนักเรียน</h5>
                        
                        <?php if(!empty($error_msg)): ?>
                            <div class="alert alert-danger py-2 small fw-bold rounded-3"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_msg; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">รหัสประจำตัวนักเรียน (5 หลัก)</label>
                                <input type="text" name="student_id" class="form-control bg-light" placeholder="เช่น 11024" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">เลขที่</label>
                                <input type="number" name="student_number" class="form-control bg-light" required>
                            </div>
                            <button type="submit" name="student_login" class="btn btn-warning w-100 fw-bold rounded-pill shadow-sm py-2">เข้าสู่ระบบ</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-6 text-center text-md-end mb-3 mb-md-0">
                <img src="images/COVER.JPG" class="img-fluid rounded-4 shadow-sm border border-5 border-white hero-img"
                     alt="ภาพครูอภิสิทธิ์"> 
            </div>
        </div>

        <!-- 🌟 ป้ายประกาศด่วน แก้ 0, ร, มส (เข้าได้ทุกคน) 🌟 -->
        <div class="row mt-5 pt-3">
            <div class="col-12">
                <a href="remedial.php" class="text-decoration-none">
                    <div class="alert text-white rounded-4 shadow-sm d-flex align-items-center gap-3 p-4 alert-remedial" style="background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 2.5rem;"></i>
                        <div>
                            <h4 class="fw-bold mb-1">ศูนย์แก้ไขผลการเรียน (0, ร, มส)</h4>
                            <p class="mb-0 text-white opacity-75">สำหรับนักเรียนที่มีผลการเรียนไม่ผ่านเกณฑ์ กดที่นี่เพื่อดูขั้นตอน (ภายใน 13:00 น. วันนี้)</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </div>

    <footer class="text-center mt-5 py-4 border-top">
      <!--  <p class="text-muted mb-0 small">© 2026 Apisit Lun-Ubon - Horwang Pathumthani School</p> -->
        <a href="admin_login.php" class="text-decoration-none text-secondary small">© 2026 Apisit Lun-Ubon</a>
        <a href="https://plesk.hwp.ac.th:8443/" class="text-decoration-none text-secondary small">- Horwang Pathumthani School</a>
    </footer> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>