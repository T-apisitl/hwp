<?php
// เปิดระบบแสดง Error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'connect.php';

// ตรวจสอบการเข้าสู่ระบบของนักเรียน
if (!isset($_SESSION['student_logged_in'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อนครับ'); window.location='index.php';</script>";
    exit();
}

$my_id = $_SESSION['student_id'];

// =========================================================
// 🌟 1. ระบบคำนวณเทอมและปีการศึกษาปัจจุบันอัตโนมัติ 🌟
// =========================================================
$current_m = (int)date('m');
$current_d = (int)date('d');
$current_y = date('Y') + 543;

$auto_term = '2';
$auto_year = $current_y;

if ($current_m < 5) {
    $auto_year = $current_y - 1; 
    $auto_term = '2';
} elseif ($current_m >= 5 && $current_m <= 9) {
    $auto_term = '1'; 
} elseif ($current_m == 10) {
    $auto_term = ($current_d <= 15) ? '1' : '2';
} else {
    $auto_term = '2'; 
}

// 🌟 ระบบยกเลิกการส่งงานของนักเรียน (ลบได้เฉพาะที่ครูยังไม่ตรวจ) 🌟
if (isset($_POST['delete_my_work'])) {
    $sub_id = intval($_POST['sub_id']);
    
    $check_sub = $conn->query("SELECT file_path, group_id, score FROM student_submissions WHERE id='$sub_id' AND student_id='$my_id'");
    
    if ($check_sub && $row = $check_sub->fetch_assoc()) {
        if ($row['score'] !== NULL) {
            echo "<script>alert('❌ ไม่สามารถยกเลิกงานที่ครูตรวจแล้วได้ครับ'); window.location='student_dashboard.php';</script>";
            exit();
        }
        
        $file_path = $row['file_path'];
        $g_id = $row['group_id'];

        if (!empty($file_path) && $file_path != 'NULL' && file_exists($file_path)) {
            unlink($file_path);
        }

        if (!empty($g_id)) {
            $g_id = $conn->real_escape_string($g_id);
            $conn->query("DELETE FROM student_submissions WHERE group_id = '$g_id'");
        } else {
            $conn->query("DELETE FROM student_submissions WHERE id = '$sub_id'");
        }
        
        echo "<script>alert('✅ ยกเลิกการส่งงานสำเร็จ! คุณสามารถกดส่งงานใหม่ได้อีกครั้งครับ'); window.location='student_dashboard.php';</script>";
        exit();
    } else {
        echo "<script>alert('❌ ไม่พบข้อมูล หรือคุณไม่มีสิทธิ์ยกเลิกงานนี้'); window.location='student_dashboard.php';</script>";
        exit();
    }
}

// 2. ดึงข้อมูลส่วนตัวของนักเรียนในเทอมปัจจุบัน (หากไม่พบให้ดึงข้อมูลล่าสุดของนักเรียนคนนี้)
$sql_student = "SELECT * FROM students WHERE student_id = '$my_id' AND semester = '$auto_term' AND academic_year = '$auto_year' LIMIT 1";
$res_student = $conn->query($sql_student);

if (!$res_student || $res_student->num_rows == 0) {
    $res_student = $conn->query("SELECT * FROM students WHERE student_id = '$my_id' ORDER BY id DESC LIMIT 1");
}

$student = $res_student ? $res_student->fetch_assoc() : null;

if (!$student) {
    echo "<script>alert('ไม่พบข้อมูลนักเรียนในระบบ'); window.location='logout.php';</script>";
    exit();
}

$fullname = $student['fullname'];
$class_level = $student['class_level'] ?? $student['class'];
$room_number = $student['room_number'] ?? $student['room'];
$student_number = $student['student_number'];
$active_term = $student['semester'] ?? $auto_term;
$active_year = $student['academic_year'] ?? $auto_year;

// 3. คำนวณสถิติสรุปผลงาน (เฉพาะเทอมและปีการศึกษาปัจจุบัน)
$total_assignments = 0;
$total_submitted = 0;

$sql_total_tasks = "SELECT COUNT(*) as total FROM assignments 
                    WHERE (class_level = '$class_level' OR class_level = '' OR class_level IS NULL)
                    AND semester = '$active_term' AND academic_year = '$active_year'";
$res_all_tasks = $conn->query($sql_total_tasks);
if ($res_all_tasks && $row = $res_all_tasks->fetch_assoc()) {
    $total_assignments = intval($row['total']);
}

$sql_my_subs = "SELECT COUNT(DISTINCT sub.assignment_id) as submitted 
                FROM student_submissions sub
                JOIN assignments a ON sub.assignment_id = a.id
                WHERE sub.student_id = '$my_id' 
                AND a.semester = '$active_term' AND a.academic_year = '$active_year'";
$res_my_subs = $conn->query($sql_my_subs);
if ($res_my_subs && $row = $res_my_subs->fetch_assoc()) {
    $total_submitted = intval($row['submitted']);
}

$missing_tasks = max(0, $total_assignments - $total_submitted);

// 4. ดึงรายการ "งานที่ยังไม่ได้ส่ง" (To-Do List) เฉพาะเทอมปัจจุบัน
$sql_pending = "SELECT * FROM assignments 
                WHERE (class_level = '$class_level' OR class_level = '' OR class_level IS NULL)
                AND semester = '$active_term' AND academic_year = '$active_year'
                AND id NOT IN (SELECT assignment_id FROM student_submissions WHERE student_id = '$my_id') 
                ORDER BY id DESC";
$query_pending = $conn->query($sql_pending);

// 5. ดึงรายการ "งานที่ส่งแล้ว" เฉพาะเทอมปัจจุบัน (ซ่อนคะแนน)
$sql_submitted = "SELECT a.title, sub.id as sub_id, sub.score, sub.submitted_at, sub.submit_type 
                  FROM student_submissions sub
                  JOIN assignments a ON sub.assignment_id = a.id
                  WHERE sub.student_id = '$my_id'
                  AND a.semester = '$active_term' AND a.academic_year = '$active_year'
                  ORDER BY sub.submitted_at DESC";
$query_submitted = $conn->query($sql_submitted);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าแรกนักเรียน - ห้องเรียนครูริว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #fbfcfe; font-family: 'Sarabun', sans-serif; color: #495057; }
        .hero-section { 
            background: linear-gradient(135deg, #0d6efd 0%, #00d2ff 100%); 
            border-radius: 0 0 40px 40px; 
            padding: 3rem 1rem 4rem 1rem; 
            color: #fff; 
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.15); 
        }
        .stat-card { border: none; border-radius: 15px; transition: transform 0.2s; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .stat-card:hover { transform: translateY(-5px); }
        .card-main { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .nav-btn { font-weight: 600; border-radius: 20px; padding: 8px 20px; transition: 0.3s; }
        .nav-btn:hover { background-color: rgba(255,255,255,0.2); }
        .btn-delete-sub { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm" style="background: linear-gradient(135deg, #0b5ed7 0%, #0d6efd 100%);">
        <div class="container">
            <span class="navbar-brand fw-bold mb-0 h1"><i class="bi bi-mortarboard-fill text-warning me-2"></i> ห้องเรียนครูริว</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-2 mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-white nav-btn active" href="student_dashboard.php"><i class="bi bi-house-door"></i> หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white nav-btn" href="assignment.php"><i class="bi bi-card-list"></i> ฟีดคำสั่งงาน</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a href="logout.php" class="btn btn-danger rounded-pill fw-bold btn-sm px-4 py-2 shadow-sm"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="hero-section mb-5 position-relative">
        <div class="container">
            <div class="row align-items-center bg-white bg-opacity-10 p-4 rounded-4 shadow-sm border border-white border-opacity-25 backdrop-blur justify-content-center">
                <div class="col-md-8 text-center">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($fullname); ?>&background=random&color=fff&size=100" alt="Profile" class="rounded-circle border border-4 border-white shadow-sm mb-3">
                    <div class="mb-2">
                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                            ภาคเรียนที่ <?= htmlspecialchars($active_term . '/' . $active_year); ?>
                        </span>
                    </div>
                    <h2 class="fw-bold mb-2">สวัสดี, <?= htmlspecialchars($fullname); ?></h2>
                    <p class="fs-5 opacity-75 mb-0">
                        <i class="bi bi-building"></i> ชั้น ม.<?= htmlspecialchars($class_level . '/' . $room_number); ?> | 
                        <i class="bi bi-list-ol"></i> เลขที่ <?= htmlspecialchars($student_number); ?> | 
                        <i class="bi bi-person-badge"></i> รหัสประจำตัว: <?= htmlspecialchars($my_id); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5 mt-n4">
        
        <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-bar-chart-line-fill me-2"></i> สถิติการส่งงานของคุณ (เทอม <?= htmlspecialchars($active_term . '/' . $active_year); ?>)</h5>
        <div class="row g-4 mb-5 justify-content-center">
            <div class="col-md-4">
                <div class="card stat-card bg-primary text-white h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-journal-text fs-1 opacity-50 mb-2 d-block"></i>
                        <h2 class="fw-bold mb-0"><?= $total_assignments; ?></h2>
                        <small>ภาระงานทั้งหมด</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-success text-white h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-check-circle-fill fs-1 opacity-50 mb-2 d-block"></i>
                        <h2 class="fw-bold mb-0"><?= $total_submitted; ?></h2>
                        <small>ส่งงานแล้ว</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card bg-danger text-white h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-exclamation-triangle-fill fs-1 opacity-50 mb-2 d-block"></i>
                        <h2 class="fw-bold mb-0"><?= $missing_tasks; ?></h2>
                        <small>ค้างส่ง (รอส่ง)</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-lg-6">
                <div class="card card-main border-top border-danger border-4 h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-bell-fill me-2"></i> งานที่ค้างส่ง (To-Do List)</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($query_pending && $query_pending->num_rows > 0): ?>
                            <div class="list-group list-group-flush rounded-bottom">
                                <?php while($task = $query_pending->fetch_assoc()): ?>
                                    <div class="list-group-item p-4 border-bottom bg-danger bg-opacity-10">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-journal-x text-danger"></i> <?= htmlspecialchars($task['title']); ?></h6>
                                                <?php if(!empty($task['due_date']) && $task['due_date'] != '0000-00-00'): ?>
                                                    <small class="text-danger fw-bold"><i class="bi bi-calendar-x"></i> กำหนดส่ง: <?= date('d/m/Y', strtotime($task['due_date'])); ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <a href="assignment.php" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm">ดูคำสั่งงาน</a>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-emoji-sunglasses text-success opacity-75" style="font-size: 4rem;"></i>
                                <h5 class="fw-bold mt-3 text-success">ยอดเยี่ยมมาก!</h5>
                                <p class="text-muted">คุณเคลียร์งานในเทอมนี้ครบทุกชิ้นแล้ว</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-main border-top border-success border-4 h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0 text-success"><i class="bi bi-check2-all me-2"></i> ประวัติการส่งงาน</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($query_submitted && $query_submitted->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">ชิ้นงาน</th>
                                            <th class="text-center">เวลาที่ส่ง</th>
                                            <th class="text-center pe-4">สถานะ / จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($sub = $query_submitted->fetch_assoc()): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <span class="fw-bold text-dark d-block"><?= htmlspecialchars($sub['title']); ?></span>
                                                    <?php if($sub['submit_type'] == 'group'): ?>
                                                        <span class="badge bg-info text-dark" style="font-size: 0.7rem;">งานกลุ่ม</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <small class="text-muted"><?= date('d/m/y H:i', strtotime($sub['submitted_at'])); ?></small>
                                                </td>
                                                <td class="text-center pe-4">
                                                    <?php if(is_null($sub['score'])): ?>
                                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                                            <span class="badge bg-warning text-dark px-2 py-1 rounded-pill shadow-sm"><i class="bi bi-hourglass-split"></i> รอตรวจ</span>
                                                            <form method="POST" class="m-0" onsubmit="return confirm('ต้องการยกเลิกการส่งงานชิ้นนี้ใช่หรือไม่? \n(หากเป็นงานกลุ่ม เพื่อนในกลุ่มจะถูกยกเลิกด้วย)');">
                                                                <input type="hidden" name="sub_id" value="<?= $sub['sub_id']; ?>">
                                                                <button type="submit" name="delete_my_work" class="btn btn-outline-danger btn-delete-sub rounded-circle" title="ยกเลิกการส่งงาน">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill"></i> ครูตรวจแล้ว</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox text-muted opacity-50" style="font-size: 4rem;"></i>
                                <h5 class="fw-bold mt-3 text-muted">ยังไม่มีประวัติการส่งงานในเทอมนี้</h5>
                                <p class="text-muted small">เมื่องานถูกส่งแล้ว รายการสถานะจะแสดงที่นี่ครับ</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="text-center py-4 text-muted small mt-auto border-top bg-white">
        © 2026 Apisit Lun-Ubon - Horwang Pathumthani School
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>