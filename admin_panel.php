<?php
// เปิดแสดง Error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'connect.php';

// ตรวจสอบสิทธิ์ว่าล็อกอินเป็นแอดมินหรือยัง
if (!isset($_SESSION['admin_logged_in'])) { 
    header("Location: admin_login.php"); 
    exit(); 
}

// =========================================================
// 🌟 1. ระบบคำนวณเทอมและปีการศึกษาปัจจุบันอัตโนมัติ 🌟
// =========================================================
$current_m = (int)date('m');
$current_d = (int)date('d');
$current_y = date('Y') + 543;

$auto_term = '2';
$auto_year = $current_y;

if ($current_m < 5) {
    // ม.ค. - เม.ย. ถือเป็นเทอม 2 ของปีการศึกษาที่แล้ว
    $auto_year = $current_y - 1; 
    $auto_term = '2';
} elseif ($current_m >= 5 && $current_m <= 9) {
    // พ.ค. - ก.ย. เป็นเทอม 1
    $auto_term = '1'; 
} elseif ($current_m == 10) {
    // ต.ค. เช็ควันที่ (1-15 เป็นเทอม 1, 16-31 เป็นเทอม 2)
    $auto_term = ($current_d <= 15) ? '1' : '2';
} else {
    // พ.ย. - ธ.ค. เป็นเทอม 2
    $auto_term = '2'; 
}

// ตรวจสอบคอลัมน์ในตารางพื้นฐานเพื่อป้องกัน Error
$check_stu_term = $conn->query("SHOW COLUMNS FROM students LIKE 'semester'");
if ($check_stu_term && $check_stu_term->num_rows == 0) {
    $conn->query("ALTER TABLE students ADD semester VARCHAR(10) DEFAULT '$auto_term'");
    $conn->query("ALTER TABLE students ADD academic_year VARCHAR(10) DEFAULT '$auto_year'");
}
$check_ass_term = $conn->query("SHOW COLUMNS FROM assignments LIKE 'semester'");
if ($check_ass_term && $check_ass_term->num_rows == 0) {
    $conn->query("ALTER TABLE assignments ADD semester VARCHAR(10) DEFAULT '$auto_term'");
    $conn->query("ALTER TABLE assignments ADD academic_year VARCHAR(10) DEFAULT '$auto_year'");
}

// รับค่าตัวกรอง เทอม/ปีการศึกษา/ชั้น/ห้อง/คำค้นหา
$filter_term = isset($_GET['filter_term']) ? $conn->real_escape_string($_GET['filter_term']) : $auto_term;
$filter_year = isset($_GET['filter_year']) ? $conn->real_escape_string($_GET['filter_year']) : $auto_year;
$filter_room = isset($_GET['room']) ? $conn->real_escape_string($_GET['room']) : '';
$filter_class = isset($_GET['class']) ? $conn->real_escape_string($_GET['class']) : '';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// =========================================================
// 🌟 2. นับสถิติภาพรวมเฉพาะเทอมและปีการศึกษาที่เลือก 🌟
// =========================================================
$total_students = $conn->query("SELECT COUNT(*) as c FROM students WHERE semester='$filter_term' AND academic_year='$filter_year'")->fetch_assoc()['c'] ?? 0;
$total_rooms = $conn->query("SELECT COUNT(DISTINCT CONCAT(class_level, '/', room_number)) as c FROM students WHERE room_number != '' AND semester='$filter_term' AND academic_year='$filter_year'")->fetch_assoc()['c'] ?? 0;
$total_assignments = $conn->query("SELECT COUNT(*) as c FROM assignments WHERE semester='$filter_term' AND academic_year='$filter_year'")->fetch_assoc()['c'] ?? 0;

$sql_sub_count = "SELECT COUNT(*) as c FROM student_submissions sub 
                  JOIN assignments a ON sub.assignment_id = a.id 
                  WHERE a.semester='$filter_term' AND a.academic_year='$filter_year'";
$total_submissions = $conn->query($sql_sub_count)->fetch_assoc()['c'] ?? 0;

$sql_pending_count = "SELECT COUNT(*) as c FROM student_submissions sub 
                      JOIN assignments a ON sub.assignment_id = a.id 
                      WHERE sub.score IS NULL AND a.semester='$filter_term' AND a.academic_year='$filter_year'";
$pending_grading = $conn->query($sql_pending_count)->fetch_assoc()['c'] ?? 0;

// =========================================================
// 🌟 3. ดึงรายชื่อนักเรียนตามตัวกรอง 🌟
// =========================================================
$where = [];
$where[] = "semester = '$filter_term'";
$where[] = "academic_year = '$filter_year'";
if ($filter_class) $where[] = "class_level = '$filter_class'";
if ($filter_room)  $where[] = "room_number = '$filter_room'";
if ($search)       $where[] = "(fullname LIKE '%$search%' OR student_id LIKE '%$search%')";
$where_sql = 'WHERE ' . implode(' AND ', $where);

$query = $conn->query("SELECT * FROM students $where_sql ORDER BY CAST(class_level AS UNSIGNED) ASC, CAST(room_number AS UNSIGNED) ASC, CAST(student_number AS UNSIGNED) ASC");

// ดึง room list เฉพาะที่มีในเทอมนั้นสำหรับใช้ใน Dropdown
$rooms = $conn->query("SELECT DISTINCT room_number FROM students WHERE room_number != '' AND semester='$filter_term' AND academic_year='$filter_year' ORDER BY CAST(room_number AS UNSIGNED)");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แผงควบคุมผู้สอน - ห้องเรียนครูริว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Sarabun', sans-serif; }
        .navbar-admin { background: #212529; border-bottom: 3px solid #ffc107; }
        .stat-card { border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: 0.2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.1); }
        .stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .menu-btn { border-radius: 12px; border: 1px solid #dee2e6; padding: 16px; text-align: center; text-decoration: none; color: #212529; display: block; transition: 0.2s; background: white; height: 100%; }
        .menu-btn:hover { border-color: #ffc107; box-shadow: 0 4px 12px rgba(255,193,7,0.2); color: #212529; transform: translateY(-2px); }
        .menu-btn i { font-size: 28px; display: block; margin-bottom: 8px; }
        .table th { font-size: 13px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.04em; }
        .badge-room { font-size: 12px; }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-admin py-3 mb-0">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <span class="text-white fw-bold fs-5">
                <i class="bi bi-speedometer2 text-warning me-2"></i>แผงควบคุมผู้สอน 
                <span class="badge bg-warning text-dark fs-6 ms-2">เทอม <?= htmlspecialchars($filter_term . '/' . $filter_year) ?></span>
            </span>
            <div class="d-flex gap-2 align-items-center">
                <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill"><i class="bi bi-house"></i> หน้าเว็บ</a>
                <a href="logout.php" class="btn btn-danger btn-sm rounded-pill"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">

        <!-- 🌟 ส่วนสถิติด้านบน (อิงตามเทอมและปีการศึกษาที่เลือก) 🌟 -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-2">
                <div class="stat-card card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people-fill"></i></div>
                        <div><div class="text-muted small">นักเรียน (เทอมนี้)</div><div class="fw-bold fs-4"><?= $total_students ?></div></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-door-open-fill"></i></div>
                        <div><div class="text-muted small">ห้องเรียน</div><div class="fw-bold fs-4"><?= $total_rooms ?></div></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-journal-check"></i></div>
                        <div><div class="text-muted small">ชิ้นงาน</div><div class="fw-bold fs-4"><?= $total_assignments ?></div></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-send-check-fill"></i></div>
                        <div><div class="text-muted small">ส่งงานแล้ว</div><div class="fw-bold fs-4"><?= $total_submissions ?></div></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-hourglass-split"></i></div>
                        <div><div class="text-muted small">รอตรวจ</div><div class="fw-bold fs-4"><?= $pending_grading ?></div></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card card p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-calendar-check"></i></div>
                        <div><div class="text-muted small">ภาคเรียนปัจจุบัน</div><div class="fw-bold text-primary" style="font-size:15px;margin-top:2px"><?= $auto_term . '/' . $auto_year ?></div></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🌟 ส่วนเมนูลัด 6 กล่อง (ส่งพารามิเตอร์เทอม/ปีที่เลือกไปด้วย) 🌟 -->
        <?php $term_query_str = "?filter_term=" . urlencode($filter_term) . "&filter_year=" . urlencode($filter_year); ?>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg-2">
                <a href="admin_students.php<?= $term_query_str ?>" class="menu-btn">
                    <i class="bi bi-person-lines-fill text-primary"></i>
                    <strong>จัดการนักเรียน</strong>
                    <div class="text-muted small mt-1">เพิ่ม ลบ แก้ไข นำเข้า CSV</div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="admin_assignments.php<?= $term_query_str ?>" class="menu-btn">
                    <i class="bi bi-journal-plus text-warning"></i>
                    <strong>ระบบสั่งชิ้นงาน</strong>
                    <div class="text-muted small mt-1">สร้างคำสั่งงานให้แต่ละ ม.</div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="admin_attendance.php<?= $term_query_str ?>" class="menu-btn">
                    <i class="bi bi-calendar-check-fill text-primary"></i>
                    <strong>เช็คชื่อเข้าเรียน</strong>
                    <div class="text-muted small mt-1">มา สาย ลา ขาด รายเทอม</div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="admin_grades.php<?= $term_query_str ?>" class="menu-btn">
                    <i class="bi bi-calculator text-success"></i>
                    <strong>ตรวจงานและให้คะแนน</strong>
                    <div class="text-muted small mt-1">ตรวจไฟล์งานที่นักเรียนส่ง</div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="admin_student_scores.php<?= $term_query_str ?>" class="menu-btn border-danger">
                    <i class="bi bi-clipboard-data-fill text-danger"></i>
                    <strong>สมุด ปพ. ออนไลน์</strong>
                    <div class="text-muted small mt-1">ตารางคะแนนเก็บและตัดเกรด</div>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="admin_summary_charts.php<?= $term_query_str ?>" class="menu-btn border-info">
                    <i class="bi bi-pie-chart-fill text-info"></i>
                    <strong>กราฟสรุปงาน</strong>
                    <div class="text-muted small mt-1">% การส่งงานรายห้อง</div>
                </a>
            </div>
        </div>

        <!-- 🌟 ตารางรายชื่อนักเรียน 🌟 -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex flex-wrap gap-2 justify-content-between align-items-center border-bottom border-warning border-3">
                <h5 class="mb-0"><i class="bi bi-table text-warning me-2"></i>รายชื่อนักเรียนในระบบ (ภาคเรียนที่ <?= htmlspecialchars($filter_term . '/' . $filter_year) ?>)</h5>
                <div class="d-flex gap-2">
                    <a href="admin_students.php<?= $term_query_str ?>" class="btn btn-primary btn-sm rounded-pill shadow-sm">
                        <i class="bi bi-person-plus"></i> จัดการนักเรียน / นำเข้า CSV
                    </a>
                </div>
            </div>

            <div class="card-body border-bottom pb-3 pt-3">
                <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                    <select name="filter_term" class="form-select form-select-sm rounded-pill border-primary fw-bold" style="max-width:110px">
                        <option value="1" <?= $filter_term == '1' ? 'selected' : '' ?>>เทอม 1</option>
                        <option value="2" <?= $filter_term == '2' ? 'selected' : '' ?>>เทอม 2</option>
                    </select>
                    <select name="filter_year" class="form-select form-select-sm rounded-pill border-primary fw-bold" style="max-width:120px">
                        <?php for($y = 2568; $y <= 2575; $y++): ?>
                        <option value="<?= $y ?>" <?= $filter_year == strval($y) ? 'selected' : '' ?>>ปี <?= $y ?></option>
                        <?php endfor; ?>
                    </select>

                    <input type="text" name="search" class="form-control form-control-sm rounded-pill" 
                           style="max-width:200px" placeholder="🔍 ค้นหาชื่อ / รหัส" 
                           value="<?= htmlspecialchars($search) ?>">
                    <select name="class" class="form-select form-select-sm rounded-pill" style="max-width:130px">
                        <option value="">ทุกระดับชั้น</option>
                        <?php for($i = 1; $i <= 6; $i++): ?>
                        <option value="<?= $i ?>" <?= $filter_class == strval($i) ? 'selected' : '' ?>>ม.<?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="room" class="form-select form-select-sm rounded-pill" style="max-width:120px">
                        <option value="">ทุกห้อง</option>
                        <?php if ($rooms): while($r = $rooms->fetch_assoc()): ?>
                        <option value="<?= $r['room_number'] ?>" <?= $filter_room == $r['room_number'] ? 'selected' : '' ?>>
                            ห้อง <?= $r['room_number'] ?>
                        </option>
                        <?php endwhile; endif; ?>
                    </select>
                    <button type="submit" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold">กรองข้อมูล</button>
                    <?php if($filter_class || $filter_room || $search || $filter_term != $auto_term || $filter_year != $auto_year): ?>
                    <a href="admin_panel.php" class="btn btn-outline-secondary btn-sm rounded-pill">รีเซ็ต</a>
                    <?php endif; ?>
                    <span class="text-muted small ms-auto fw-bold">พบ <?= $query ? $query->num_rows : 0 ?> คน</span>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:130px" class="ps-4">รหัสนักเรียน</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th style="width:100px" class="text-center">ชั้น/ห้อง</th>
                                <th style="width:80px" class="text-center">เลขที่</th>
                                <th>อีเมล</th>
                                <th style="width:180px" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!$query || $query->num_rows == 0): ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>ไม่พบข้อมูลนักเรียนในภาคเรียนที่ <?= htmlspecialchars($filter_term . '/' . $filter_year) ?></td></tr>
                        <?php else: ?>
                        <?php while($row = $query->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4"><code class="text-primary fs-6 fw-bold"><?= htmlspecialchars($row['student_id']) ?></code></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($row['fullname']) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark badge-room px-2 py-1 shadow-sm">
                                        ม.<?= htmlspecialchars($row['class_level']) ?>/<?= htmlspecialchars($row['room_number']) ?>
                                    </span>
                                </td>
                                <td class="text-center fw-bold"><?= htmlspecialchars($row['student_number']) ?></td>
                                <td><small class="text-info"><?= htmlspecialchars($row['email'] ?? '-') ?></small></td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="admin_students.php?filter_term=<?= urlencode($filter_term) ?>&filter_year=<?= urlencode($filter_year) ?>&keyword=<?= urlencode($row['student_id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">แก้ไข</a>
                                        
                                        <a href="promote_admin.php?student_id=<?= urlencode($row['student_id']) ?>" 
                                           class="btn btn-sm btn-dark rounded-pill shadow-sm fw-bold"
                                           title="แต่งตั้งเป็น Admin"
                                           onclick="return confirm('ต้องการแต่งตั้ง <?= htmlspecialchars($row['fullname']) ?> เป็นผู้ดูแลระบบ (Admin) ใช่หรือไม่?');">
                                           <i class="bi bi-star-fill text-warning"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>