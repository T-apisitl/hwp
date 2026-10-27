<?php
// เปิดแสดง Error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'connect.php';

// ตรวจสอบสิทธิ์แอดมิน
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
    $auto_year = $current_y - 1; 
    $auto_term = '2';
} elseif ($current_m >= 5 && $current_m <= 9) {
    $auto_term = '1'; 
} elseif ($current_m == 10) {
    $auto_term = ($current_d <= 15) ? '1' : '2';
} else {
    $auto_term = '2'; 
}

// =========================================================
// 🌟 2. สร้างและอัปเกรดตาราง attendance อัตโนมัติ 🌟
// =========================================================
$conn->query("CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    class_level VARCHAR(20) NOT NULL,
    room_number VARCHAR(20) NOT NULL,
    semester VARCHAR(10) DEFAULT '1',
    academic_year VARCHAR(10) DEFAULT '2569',
    att_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_att (student_id, att_date, semester, academic_year)
)");

$check_att_term = $conn->query("SHOW COLUMNS FROM attendance LIKE 'semester'");
if ($check_att_term && $check_att_term->num_rows == 0) {
    $conn->query("ALTER TABLE attendance ADD semester VARCHAR(10) DEFAULT '$auto_term' AFTER room_number");
    $conn->query("ALTER TABLE attendance ADD academic_year VARCHAR(10) DEFAULT '$auto_year' AFTER semester");
}

// รับค่าตัวกรอง
$filter_term = isset($_GET['filter_term']) ? $conn->real_escape_string($_GET['filter_term']) : $auto_term;
$filter_year = isset($_GET['filter_year']) ? $conn->real_escape_string($_GET['filter_year']) : $auto_year;
$search_class = isset($_GET['class']) ? $conn->real_escape_string($_GET['class']) : ''; 
$search_room = isset($_GET['room']) ? $conn->real_escape_string($_GET['room']) : '';      
$att_date = isset($_GET['att_date']) ? $conn->real_escape_string($_GET['att_date']) : date('Y-m-d');
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'daily';

$is_room_selected = ($search_class != '' && $search_room != '');

// =========================================================
// 🌟 3. บันทึกข้อมูลการเช็คชื่อรายวัน 🌟
// =========================================================
if (isset($_POST['save_attendance'])) {
    $p_term = $conn->real_escape_string($_POST['semester']);
    $p_year = $conn->real_escape_string($_POST['academic_year']);
    $p_class = $conn->real_escape_string($_POST['class_level']);
    $p_room = $conn->real_escape_string($_POST['room_number']);
    $p_date = $conn->real_escape_string($_POST['att_date']);
    
    if (isset($_POST['status']) && is_array($_POST['status'])) {
        foreach ($_POST['status'] as $sid => $st) {
            $sid = $conn->real_escape_string($sid);
            $st = $conn->real_escape_string($st);
            
            // ลบของเก่าในวันและเทอมเดียวกันก่อน แล้วบันทึกใหม่ (ป้องกันปัญหา Key ซ้ำ)
            $conn->query("DELETE FROM attendance WHERE student_id='$sid' AND att_date='$p_date' AND semester='$p_term' AND academic_year='$p_year'");
            $conn->query("INSERT INTO attendance (student_id, class_level, room_number, semester, academic_year, att_date, status) 
                          VALUES ('$sid', '$p_class', '$p_room', '$p_term', '$p_year', '$p_date', '$st')");
        }
    }
    echo "<script>alert('✅ บันทึกการเช็คชื่อวันที่ " . date('d/m/Y', strtotime($p_date)) . " เรียบร้อยแล้วครับ!'); window.location='admin_attendance.php?filter_term=$p_term&filter_year=$p_year&class=$p_class&room=$p_room&att_date=$p_date&tab=grid';</script>";
    exit();
}

// ลบข้อมูลเช็คชื่อทั้งวัน (กรณีเลือกวันผิด)
if (isset($_GET['delete_date']) && $is_room_selected) {
    $del_date = $conn->real_escape_string($_GET['delete_date']);
    $conn->query("DELETE FROM attendance WHERE class_level='$search_class' AND room_number='$search_room' AND semester='$filter_term' AND academic_year='$filter_year' AND att_date='$del_date'");
    header("Location: admin_attendance.php?filter_term=$filter_term&filter_year=$filter_year&class=$search_class&room=$search_room&att_date=$att_date&tab=grid");
    exit();
}

// ดึงระดับชั้นและห้องเรียนที่มีนักเรียนอยู่จริงในเทอม/ปีที่เลือก
$active_classes = [];
$class_query = $conn->query("SELECT DISTINCT class_level FROM students WHERE class_level != '' AND semester='$filter_term' AND academic_year='$filter_year' ORDER BY CAST(class_level AS UNSIGNED)");
if ($class_query) { while ($c = $class_query->fetch_assoc()) { $active_classes[] = $c['class_level']; } }

$active_rooms = [];
$room_query = $conn->query("SELECT DISTINCT room_number FROM students WHERE room_number != '' AND semester='$filter_term' AND academic_year='$filter_year' ORDER BY CAST(room_number AS UNSIGNED)");
if ($room_query) { while ($r = $room_query->fetch_assoc()) { $active_rooms[] = $r['room_number']; } }
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบเช็คชื่อเข้าเรียน - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #fbfcfe; }
        .navbar-admin { background: #212529; border-bottom: 3px solid #0d6efd; }
        .table-responsive { max-width: 100%; overflow-x: auto; white-space: nowrap; }
        .sticky-col { position: sticky; left: 0; background-color: white; z-index: 2; box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1); }
        .sticky-col-header { position: sticky; left: 0; z-index: 3; }
        
        /* ปุ่มเลือกสถานะเช็คชื่อ */
        .btn-check:checked + .btn-outline-success { background-color: #198754; color: #fff; box-shadow: 0 3px 8px rgba(25,135,84,0.3); }
        .btn-check:checked + .btn-outline-warning { background-color: #ffc107; color: #000; box-shadow: 0 3px 8px rgba(255,193,7,0.3); }
        .btn-check:checked + .btn-outline-info { background-color: #0dcaf0; color: #000; box-shadow: 0 3px 8px rgba(13,202,240,0.3); }
        .btn-check:checked + .btn-outline-danger { background-color: #dc3545; color: #fff; box-shadow: 0 3px 8px rgba(220,53,69,0.3); }
        .status-btn { width: 65px; font-weight: bold; border-radius: 20px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-admin py-3 mb-4 shadow-sm">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <span class="text-white fw-bold fs-5">
                <i class="bi bi-calendar-check-fill text-primary me-2"></i> ระบบเช็คชื่อเข้าเรียน
            </span>
            <a href="admin_panel.php?filter_term=<?= urlencode($filter_term) ?>&filter_year=<?= urlencode($filter_year) ?>" class="btn btn-outline-light btn-sm rounded-pill fw-bold">
                <i class="bi bi-arrow-left-circle"></i> กลับหน้าแผงควบคุม
            </a>
        </div>
    </nav>

    <div class="container-fluid px-4 my-4">
        
        <!-- 🌟 แถบกรองข้อมูล เทอม / ปีการศึกษา / ชั้น / ห้อง / วันที่ 🌟 -->
        <div class="card mb-4 border-0 shadow-sm border-top border-primary border-4">
            <div class="card-body bg-white py-4">
                <form method="GET" class="row g-3 align-items-end justify-content-center">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($active_tab) ?>">
                    
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-bold text-dark small">ภาคเรียนที่</label>
                        <select name="filter_term" class="form-select bg-light border-secondary fw-bold">
                            <option value="1" <?= ($filter_term=='1')?'selected':'' ?>>เทอม 1</option>
                            <option value="2" <?= ($filter_term=='2')?'selected':'' ?>>เทอม 2</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-bold text-dark small">ปีการศึกษา</label>
                        <select name="filter_year" class="form-select bg-light border-secondary fw-bold">
                            <?php for($y = 2568; $y <= 2575; $y++): ?>
                                <option value="<?=$y?>" <?= ($filter_year==strval($y))?'selected':'' ?>>ปี <?=$y?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label fw-bold text-primary">ระดับชั้น <span class="text-danger">*</span></label>
                        <select name="class" class="form-select bg-light border-primary" required>
                            <option value="">-- เลือกชั้น --</option>
                            <?php foreach($active_classes as $c): ?>
                                <option value="<?= $c ?>" <?= ($search_class == $c) ? 'selected' : '' ?>>ม.<?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-bold text-primary">ห้อง <span class="text-danger">*</span></label>
                        <select name="room" class="form-select bg-light border-primary" required>
                            <option value="">-- เลือกห้อง --</option>
                            <?php foreach($active_rooms as $r): ?>
                                <option value="<?= $r ?>" <?= ($search_room == $r) ? 'selected' : '' ?>>ห้อง <?= $r ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label fw-bold text-success">วันที่เช็คชื่อ</label>
                        <input type="date" name="att_date" class="form-control bg-light border-success fw-bold" value="<?= htmlspecialchars($att_date) ?>" required>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                            <i class="bi bi-search"></i> เปิดรายชื่อ
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($is_room_selected): ?>
            <?php
            // ดึงรายชื่อนักเรียนในห้องและเทอมนี้
            $sql_students = "SELECT student_id, fullname, student_number FROM students 
                             WHERE class_level = '$search_class' AND room_number = '$search_room' 
                             AND semester = '$filter_term' AND academic_year = '$filter_year'
                             ORDER BY CAST(student_number AS UNSIGNED) ASC";
            $res_students = $conn->query($sql_students);
            $students = [];
            if ($res_students) {
                while ($s = $res_students->fetch_assoc()) {
                    $students[] = $s;
                }
            }

            // ดึงข้อมูลการเช็คชื่อของวันที่เลือก (สำหรับหน้าเช็คชื่อรายวัน)
            $daily_records = [];
            $res_daily = $conn->query("SELECT student_id, status FROM attendance WHERE class_level='$search_class' AND room_number='$search_room' AND semester='$filter_term' AND academic_year='$filter_year' AND att_date='$att_date'");
            if ($res_daily) {
                while ($d = $res_daily->fetch_assoc()) {
                    $daily_records[$d['student_id']] = $d['status'];
                }
            }

            // ดึงวันที่ทั้งหมดที่เคยเช็คชื่อในเทอมนี้ (สำหรับตารางสรุปเวลาเรียน)
            $att_dates = [];
            $res_dates = $conn->query("SELECT DISTINCT att_date FROM attendance WHERE class_level='$search_class' AND room_number='$search_room' AND semester='$filter_term' AND academic_year='$filter_year' ORDER BY att_date ASC");
            if ($res_dates) {
                while ($dt = $res_dates->fetch_assoc()) {
                    $att_dates[] = $dt['att_date'];
                }
            }

            // ดึงข้อมูลการเช็คชื่อทั้งหมดของเทอมนี้มาเก็บใน Array 2 มิติ
            $grid_records = [];
            $res_all_att = $conn->query("SELECT student_id, att_date, status FROM attendance WHERE class_level='$search_class' AND room_number='$search_room' AND semester='$filter_term' AND academic_year='$filter_year'");
            if ($res_all_att) {
                while ($row = $res_all_att->fetch_assoc()) {
                    $grid_records[$row['student_id']][$row['att_date']] = $row['status'];
                }
            }
            ?>

            <!-- ปุ่มสลับโหมด: เช็คชื่อรายวัน vs ตารางสมุดเช็คชื่อ -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div class="btn-group shadow-sm">
                    <a href="?filter_term=<?= urlencode($filter_term) ?>&filter_year=<?= urlencode($filter_year) ?>&class=<?= urlencode($search_class) ?>&room=<?= urlencode($search_room) ?>&att_date=<?= urlencode($att_date) ?>&tab=daily" 
                       class="btn <?= ($active_tab == 'daily') ? 'btn-primary fw-bold' : 'btn-white bg-white text-dark border' ?> px-4">
                        <i class="bi bi-check2-square"></i> 1. เช็คชื่อประจำวัน (<?= date('d/m/Y', strtotime($att_date)) ?>)
                    </a>
                    <a href="?filter_term=<?= urlencode($filter_term) ?>&filter_year=<?= urlencode($filter_year) ?>&class=<?= urlencode($search_class) ?>&room=<?= urlencode($search_room) ?>&att_date=<?= urlencode($att_date) ?>&tab=grid" 
                       class="btn <?= ($active_tab == 'grid') ? 'btn-primary fw-bold' : 'btn-white bg-white text-dark border' ?> px-4">
                        <i class="bi bi-grid-3x3-gap-fill"></i> 2. ตารางสรุปเวลาเรียน (เทอม <?= htmlspecialchars($filter_term.'/'.$filter_year) ?>)
                    </a>
                </div>

                <span class="badge bg-dark fs-6 px-3 py-2 rounded-pill">
                    ชั้น ม.<?= htmlspecialchars($search_class.'/'.$search_room) ?> | เทอม <?= htmlspecialchars($filter_term.'/'.$filter_year) ?>
                </span>
            </div>

            <?php if ($active_tab == 'daily'): ?>
                <!-- ========================================== -->
                <!-- 🌟 โหมดที่ 1: เช็คชื่อรายวัน 🌟 -->
                <!-- ========================================== -->
                <div class="card shadow-sm border-0 border-top border-4 border-primary mb-5">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-calendar-event text-primary me-2"></i> เช็คชื่อวันที่ <?= date('d/m/Y', strtotime($att_date)) ?>
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill fw-bold px-3" onclick="markAllPresent()">
                            <i class="bi bi-check-all"></i> เช็ค "มาเรียน" ทั้งห้อง
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <?php if (count($students) > 0): ?>
                            <form method="POST">
                                <input type="hidden" name="semester" value="<?= htmlspecialchars($filter_term) ?>">
                                <input type="hidden" name="academic_year" value="<?= htmlspecialchars($filter_year) ?>">
                                <input type="hidden" name="class_level" value="<?= htmlspecialchars($search_class) ?>">
                                <input type="hidden" name="room_number" value="<?= htmlspecialchars($search_room) ?>">
                                <input type="hidden" name="att_date" value="<?= htmlspecialchars($att_date) ?>">

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th width="80">เลขที่</th>
                                                <th width="130">รหัสนักเรียน</th>
                                                <th class="text-start">ชื่อ-นามสกุล</th>
                                                <th width="340">สถานะการเข้าเรียน</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($students as $std): 
                                                $sid = $std['student_id'];
                                                // ค่าเริ่มต้นเป็น 'present' (มาเรียน) หากยังไม่เคยเช็ควันนี้
                                                $cur_status = $daily_records[$sid] ?? 'present';
                                            ?>
                                            <tr class="text-center">
                                                <td class="fw-bold text-secondary"><?= htmlspecialchars($std['student_number']) ?></td>
                                                <td><code class="text-primary fw-bold fs-6"><?= htmlspecialchars($sid) ?></code></td>
                                                <td class="text-start fw-bold text-dark"><?= htmlspecialchars($std['fullname']) ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <input type="radio" class="btn-check radio-present" name="status[<?= htmlspecialchars($sid) ?>]" id="p_<?= $sid ?>" value="present" <?= ($cur_status=='present')?'checked':'' ?>>
                                                        <label class="btn btn-outline-success btn-sm status-btn" for="p_<?= $sid ?>">มา</label>

                                                        <input type="radio" class="btn-check" name="status[<?= htmlspecialchars($sid) ?>]" id="l_<?= $sid ?>" value="late" <?= ($cur_status=='late')?'checked':'' ?>>
                                                        <label class="btn btn-outline-warning btn-sm status-btn" for="l_<?= $sid ?>">สาย</label>

                                                        <input type="radio" class="btn-check" name="status[<?= htmlspecialchars($sid) ?>]" id="lv_<?= $sid ?>" value="leave" <?= ($cur_status=='leave')?'checked':'' ?>>
                                                        <label class="btn btn-outline-info btn-sm status-btn" for="lv_<?= $sid ?>">ลา</label>

                                                        <input type="radio" class="btn-check" name="status[<?= htmlspecialchars($sid) ?>]" id="ab_<?= $sid ?>" value="absent" <?= ($cur_status=='absent')?'checked':'' ?>>
                                                        <label class="btn btn-outline-danger btn-sm status-btn" for="ab_<?= $sid ?>">ขาด</label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer bg-white p-3 text-end border-top">
                                    <button type="submit" name="save_attendance" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                        <i class="bi bi-save-fill me-1"></i> บันทึกการเช็คชื่อ
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="text-center py-5 text-muted">ไม่พบรายชื่อนักเรียนในห้องนี้สำหรับเทอม <?= htmlspecialchars($filter_term.'/'.$filter_year) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <!-- ========================================== -->
                <!-- 🌟 โหมดที่ 2: ตารางสมุดเช็คชื่อรายเทอม (Grid) 🌟 -->
                <!-- ========================================== -->
                <div class="card shadow-sm border-0 border-top border-4 border-dark mb-5">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-journal-check text-success me-2"></i> สมุดสรุปเวลาเรียน ม.<?= htmlspecialchars($search_class.'/'.$search_room) ?> (เทอม <?= htmlspecialchars($filter_term.'/'.$filter_year) ?>)
                        </h5>
                        <div>
                            <span class="badge bg-light text-dark border me-2">
                                ✅ มา | 🕒 สาย | 📝 ลา | ❌ ขาด
                            </span>
                            <button onclick="window.print()" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                <i class="bi bi-printer"></i> พิมพ์
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive pb-3" style="max-height: 70vh;">
                        <table class="table table-bordered table-hover align-middle mb-0 bg-white text-center">
                            <thead class="table-light align-middle">
                                <tr>
                                    <th width="55" rowspan="2" class="sticky-col sticky-col-header">เลขที่</th>
                                    <th width="110" rowspan="2" class="sticky-col sticky-col-header" style="left: 55px;">รหัส</th>
                                    <th width="200" rowspan="2" class="sticky-col sticky-col-header text-start" style="left: 165px;">ชื่อ-นามสกุล</th>
                                    
                                    <?php if (count($att_dates) > 0): ?>
                                        <th colspan="<?= count($att_dates) ?>" class="bg-light text-primary">วันที่เช็คชื่อ (ทั้งหมด <?= count($att_dates) ?> ครั้ง)</th>
                                    <?php else: ?>
                                        <th rowspan="2" class="text-muted">ยังไม่มีประวัติการเช็คชื่อในเทอมนี้</th>
                                    <?php endif; ?>

                                    <th colspan="4" class="bg-dark text-white">สรุปรวม (ครั้ง)</th>
                                </tr>
                                <tr>
                                    <?php foreach ($att_dates as $dt): ?>
                                        <th style="min-width: 65px; font-size: 0.8rem;" class="bg-light">
                                            <?= date('d/m', strtotime($dt)) ?>
                                            <div class="mt-1">
                                                <a href="?filter_term=<?= urlencode($filter_term) ?>&filter_year=<?= urlencode($filter_year) ?>&class=<?= urlencode($search_class) ?>&room=<?= urlencode($search_room) ?>&delete_date=<?= urlencode($dt) ?>" 
                                                   class="text-danger text-decoration-none" title="ลบการเช็คชื่อวันนี้"
                                                   onclick="return confirm('ต้องการลบข้อมูลเช็คชื่อของวันที่ <?= date('d/m/Y', strtotime($dt)) ?> ทั้งหมดใช่หรือไม่?');">
                                                    <i class="bi bi-x-circle"></i>
                                                </a>
                                            </div>
                                        </th>
                                    <?php endforeach; ?>
                                    <th width="50" class="bg-success text-white small">มา</th>
                                    <th width="50" class="bg-warning text-dark small">สาย</th>
                                    <th width="50" class="bg-info text-dark small">ลา</th>
                                    <th width="50" class="bg-danger text-white small">ขาด</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($students) > 0): ?>
                                    <?php foreach ($students as $std): 
                                        $sid = $std['student_id'];
                                        $c_present = 0; $c_late = 0; $c_leave = 0; $c_absent = 0;
                                    ?>
                                    <tr>
                                        <td class="fw-bold text-secondary sticky-col"><?= htmlspecialchars($std['student_number']) ?></td>
                                        <td class="sticky-col" style="left: 55px;"><code class="text-primary"><?= htmlspecialchars($sid) ?></code></td>
                                        <td class="text-start fw-bold text-dark sticky-col text-truncate" style="left: 165px; max-width: 200px;"><?= htmlspecialchars($std['fullname']) ?></td>

                                        <?php if (count($att_dates) > 0): ?>
                                            <?php foreach ($att_dates as $dt): 
                                                $st = $grid_records[$sid][$dt] ?? '';
                                                $icon = '-';
                                                if ($st == 'present') { $icon = '✅'; $c_present++; }
                                                elseif ($st == 'late') { $icon = '🕒'; $c_late++; }
                                                elseif ($st == 'leave') { $icon = '📝'; $c_leave++; }
                                                elseif ($st == 'absent') { $icon = '❌'; $c_absent++; }
                                            ?>
                                                <td><?= $icon ?></td>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <td class="text-muted">-</td>
                                        <?php endif; ?>

                                        <td class="fw-bold text-success bg-light"><?= $c_present ?></td>
                                        <td class="fw-bold text-warning bg-light"><?= $c_late ?></td>
                                        <td class="fw-bold text-info bg-light"><?= $c_leave ?></td>
                                        <td class="fw-bold text-danger bg-light"><?= $c_absent ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="<?= 7 + count($att_dates) ?>" class="py-5 text-muted">ไม่พบรายชื่อนักเรียน</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5 text-muted">
                    <h5 class="mb-0"><i class="bi bi-calendar2-check fs-1 d-block mb-3 text-primary opacity-50"></i> กรุณาเลือกระดับชั้นและห้องเรียนด้านบนเพื่อเริ่มเช็คชื่อครับ</h5>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <style>
        @media print {
            .navbar, form, .btn, .btn-group, .badge, a.text-danger { display: none !important; }
            .card { border: none !important; box-shadow: none !important; }
            .sticky-col { position: static; box-shadow: none; }
            .table-responsive { overflow-x: visible; max-height: none !important; }
        }
    </style>

    <script>
        function markAllPresent() {
            document.querySelectorAll('.radio-present').forEach(radio => {
                radio.checked = true;
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>