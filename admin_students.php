<?php
// เปิดแสดง Error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'connect.php';

// ตรวจสอบสถานะแอดมิน
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
    if ($current_d <= 15) {
        $auto_term = '1';
    } else {
        $auto_term = '2';
    }
} else {
    // พ.ย. - ธ.ค. เป็นเทอม 2
    $auto_term = '2'; 
}

// =========================================================
// 🌟 2. อัปเกรดโครงสร้างตารางนักเรียนให้รองรับเทอม/ปีอัตโนมัติ 🌟
// =========================================================
$check_col_term = $conn->query("SHOW COLUMNS FROM students LIKE 'semester'");
if ($check_col_term && $check_col_term->num_rows == 0) {
    $conn->query("ALTER TABLE students ADD semester VARCHAR(10) DEFAULT '$auto_term'");
}
$check_col_year = $conn->query("SHOW COLUMNS FROM students LIKE 'academic_year'");
if ($check_col_year && $check_col_year->num_rows == 0) {
    $conn->query("ALTER TABLE students ADD academic_year VARCHAR(10) DEFAULT '$auto_year'");
}

// ปลดล็อก Primary Key เดิม เพื่อให้รหัสนักเรียนซ้ำกันได้ในคนละเทอม
$check_pk = $conn->query("SHOW KEYS FROM students WHERE Key_name = 'PRIMARY'");
$pk_cols = [];
if ($check_pk) {
    while ($r = $check_pk->fetch_assoc()) {
        $pk_cols[] = $r['Column_name'];
    }
}
if (count($pk_cols) == 1 && $pk_cols[0] == 'student_id') {
    $conn->query("ALTER TABLE students DROP PRIMARY KEY");
    $conn->query("ALTER TABLE students ADD id INT AUTO_INCREMENT PRIMARY KEY FIRST");
    $conn->query("ALTER TABLE students ADD UNIQUE KEY unique_enrollment (student_id, semester, academic_year)");
}

// ----------------- ส่วนของระบบจัดการข้อมูล ----------------- //

// 1. เพิ่ม/แก้ไข นักเรียน
if (isset($_POST['save_student'])) {
    $id = $conn->real_escape_string($_POST['student_id']);
    $name = $conn->real_escape_string($_POST['fullname']);
    $class = $conn->real_escape_string($_POST['class_level']);
    $room = $conn->real_escape_string($_POST['room_number']);
    $num = $conn->real_escape_string($_POST['student_number']);
    $email = $conn->real_escape_string($_POST['email']);
    $semester = $conn->real_escape_string($_POST['semester']);
    $academic_year = $conn->real_escape_string($_POST['academic_year']);
    
    $action = $_POST['action'];

    if ($action == 'add') {
        $sql = "INSERT INTO students (student_id, fullname, class_level, room_number, student_number, email, semester, academic_year) 
                VALUES ('$id', '$name', '$class', '$room', '$num', '$email', '$semester', '$academic_year')
                ON DUPLICATE KEY UPDATE fullname='$name', class_level='$class', room_number='$room', student_number='$num', email='$email'";
        $conn->query($sql);
    } else {
        $old_id = $conn->real_escape_string($_POST['old_id']);
        $old_term = $conn->real_escape_string($_POST['old_term']);
        $old_year = $conn->real_escape_string($_POST['old_year']);
        
        $sql = "UPDATE students SET student_id='$id', fullname='$name', class_level='$class', 
                room_number='$room', student_number='$num', email='$email', semester='$semester', academic_year='$academic_year' 
                WHERE student_id='$old_id' AND semester='$old_term' AND academic_year='$old_year'";
        $conn->query($sql);
    }
    header("Location: admin_students.php?filter_term=$semester&filter_year=$academic_year");
    exit();
}

// 2. ลบนักเรียนคนเดียว
if (isset($_GET['delete'])) {
    $id = $conn->real_escape_string($_GET['delete']);
    $term = $conn->real_escape_string($_GET['term']);
    $year = $conn->real_escape_string($_GET['year']);
    $conn->query("DELETE FROM students WHERE student_id='$id' AND semester='$term' AND academic_year='$year'");
    header("Location: admin_students.php?filter_term=$term&filter_year=$year");
    exit();
}

// 3. ลบหลายคน (เช็คบ็อกซ์)
if (isset($_POST['delete_selected']) && !empty($_POST['selected_students'])) {
    $term = $conn->real_escape_string($_POST['current_term']);
    $year = $conn->real_escape_string($_POST['current_year']);
    
    $ids = $_POST['selected_students'];
    foreach ($ids as $id) {
        $id = $conn->real_escape_string($id);
        $conn->query("DELETE FROM students WHERE student_id='$id' AND semester='$term' AND academic_year='$year'");
    }
    header("Location: admin_students.php?filter_term=$term&filter_year=$year");
    exit();
}

// 4. นำเข้า CSV
if (isset($_POST['import_csv'])) {
    $imp_semester = $conn->real_escape_string($_POST['import_semester']);
    $imp_year = $conn->real_escape_string($_POST['import_year']);
    
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        
        // ข้ามบรรทัดหัวตาราง
        fgetcsv($handle); 
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) < 5) continue; // ข้ามบรรทัดที่ข้อมูลไม่ครบ
            
            $id = $conn->real_escape_string(trim($data[0]));
            $name = $conn->real_escape_string(trim($data[1]));
            $class = $conn->real_escape_string(trim($data[2]));
            $room = $conn->real_escape_string(trim($data[3]));
            $num = $conn->real_escape_string(trim($data[4]));
            $email = isset($data[5]) ? $conn->real_escape_string(trim($data[5])) : '';
            
            if ($id != '') {
                $conn->query("INSERT INTO students (student_id, fullname, class_level, room_number, student_number, email, semester, academic_year) 
                              VALUES ('$id', '$name', '$class', '$room', '$num', '$email', '$imp_semester', '$imp_year')
                              ON DUPLICATE KEY UPDATE fullname='$name', class_level='$class', room_number='$room', student_number='$num', email='$email'");
            }
        }
        fclose($handle);
        echo "<script>alert('✅ นำเข้าข้อมูลนักเรียน เทอม $imp_semester/$imp_year สำเร็จ'); window.location='admin_students.php?filter_term=$imp_semester&filter_year=$imp_year';</script>";
        exit();
    }
}

// ----------------- ส่วนของระบบค้นหาและฟิลเตอร์ ----------------- //
$where = [];
$filter_term = isset($_GET['filter_term']) ? $conn->real_escape_string($_GET['filter_term']) : $auto_term;
$filter_year = isset($_GET['filter_year']) ? $conn->real_escape_string($_GET['filter_year']) : $auto_year;
$search_class = isset($_GET['filter_class']) ? $conn->real_escape_string($_GET['filter_class']) : '';
$search_room = isset($_GET['filter_room']) ? $conn->real_escape_string($_GET['filter_room']) : '';
$search_keyword = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : '';

$where[] = "semester = '$filter_term'";
$where[] = "academic_year = '$filter_year'";

if ($search_class != '') $where[] = "class_level = '$search_class'";
if ($search_room != '') $where[] = "room_number = '$search_room'";
if ($search_keyword != '') $where[] = "(fullname LIKE '%$search_keyword%' OR student_id LIKE '%$search_keyword%' OR email LIKE '%$search_keyword%')";

$sql = "SELECT * FROM students";
if (count($where) > 0) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY CAST(class_level AS UNSIGNED) ASC, CAST(room_number AS UNSIGNED) ASC, CAST(student_number AS UNSIGNED) ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการฐานข้อมูลนักเรียน - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #fbfcfe; font-family: 'Sarabun', sans-serif; color: #495057; }
        .navbar-main { background-color: #ffffff; border-bottom: 4px solid #ffc107; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .btn-yellow { background-color: #ffc107; color: #000; border: none; font-weight: 600; }
        .btn-yellow:hover { background-color: #ffca28; color: #000; }
        .card-bright { border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.02); background-color: #ffffff; }
        .table-bright th { background-color: #f8f9fa; color: #495057; font-weight: 700; border-bottom: 2px solid #ffc107; }
        .table-bright tbody tr:hover { background-color: #fffdf5; }
        .form-bright { border-radius: 10px; border: 1px solid #e9ecef; background-color: #fbfcfe; }
        .form-bright:focus { border-color: #ffc107; box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.15); }
        .modal-bright .modal-content { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .modal-bright .modal-header { background-color: #fff9e6; color: #7f6000; border-radius: 20px 20px 0 0; border-bottom: 2px solid #ffc107; }
        .btn-close:focus { box-shadow: none; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light navbar-main py-3 mb-4">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <span class="navbar-brand mb-0 h1 fw-bold text-dark">
                <i class="bi bi-people-fill text-warning"></i> ห้องเรียนครูริว - <span class="text-secondary fw-normal fs-6">จัดการฐานข้อมูลนักเรียน</span>
            </span>
            <div class="d-flex gap-2 align-items-center">
                <a href="admin_panel.php" class="btn btn-outline-secondary btn-sm fw-bold rounded-pill px-3">
                    <i class="bi bi-speedometer2"></i> แผงควบคุม
                </a>
                <!-- ปุ่มเพิ่มนักเรียน -->
                <button type="button" class="btn btn-yellow btn-sm fw-bold shadow-sm rounded-pill px-3" onclick="openStudentModal('add')">
                    <i class="bi bi-person-plus-fill"></i> เพิ่มนักเรียน
                </button>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4"> 
        
        <!-- แถบกรองข้อมูล -->
        <div class="card card-bright mb-4 border-start border-primary border-5">
            <div class="card-body p-4 p-md-3">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-primary">ภาคเรียนที่</label>
                        <select name="filter_term" class="form-select form-bright form-select-sm fw-bold border-primary">
                            <option value="1" <?= ($filter_term=='1')?'selected':'' ?>>เทอม 1</option>
                            <option value="2" <?= ($filter_term=='2')?'selected':'' ?>>เทอม 2</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-primary">ปีการศึกษา</label>
                        <select name="filter_year" class="form-select form-bright form-select-sm fw-bold border-primary">
                            <?php for($y = 2568; $y <= 2575; $y++): ?>
                                <option value="<?=$y?>" <?= ($filter_year==strval($y))?'selected':'' ?>>ปี <?=$y?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-4 col-md-1">
                        <label class="form-label small fw-bold text-secondary">ระดับชั้น</label>
                        <select name="filter_class" class="form-select form-bright form-select-sm">
                            <option value="">ทั้งหมด</option>
                            <?php for($c=1; $c<=6; $c++) echo "<option value='$c' ".($search_class==strval($c)?'selected':'').">ม.$c</option>"; ?>
                        </select>
                    </div>
                    <div class="col-4 col-md-1">
                        <label class="form-label small fw-bold text-secondary">ห้อง</label>
                        <select name="filter_room" class="form-select form-bright form-select-sm">
                            <option value="">ทั้งหมด</option>
                            <?php for($i=1; $i<=15; $i++) echo "<option value='$i' ".($search_room==strval($i)?'selected':'').">$i</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-secondary">ค้นหารายชื่อ (ชื่อ/รหัส/อีเมล)</label>
                        <input type="text" name="keyword" class="form-control form-control-sm form-bright" placeholder="พิมพ์คำค้น..." value="<?= htmlspecialchars($search_keyword); ?>">
                    </div>
                    <div class="col-6 col-md-2">
                        <button type="submit" class="btn btn-yellow w-100 fw-bold btn-sm rounded-pill shadow-sm"><i class="bi bi-filter"></i> กรองข้อมูล</button>
                    </div>
                    <div class="col-6 col-md-1">
                        <a href="admin_students.php" class="btn btn-outline-secondary btn-sm w-100 fw-bold rounded-pill">ล้างค่า</a>
                    </div>
                </form>
            </div>
        </div>

        <form method="POST" id="deleteForm">
            <input type="hidden" name="current_term" value="<?= htmlspecialchars($filter_term) ?>">
            <input type="hidden" name="current_year" value="<?= htmlspecialchars($filter_year) ?>">
            <input type="hidden" name="delete_selected" value="1">

            <div class="card card-bright shadow-sm mb-5 overflow-hidden border-top border-warning border-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-0">
                    <span class="fw-bold text-dark fs-5">
                        <i class="bi bi-list-check text-warning"></i> ตารางรายชื่อนักเรียน (เทอม <?= htmlspecialchars($filter_term.'/'.$filter_year) ?>)
                    </span>
                    <div class="d-flex gap-2">
                        <!-- ปุ่มนำเข้า CSV -->
                        <button type="button" class="btn btn-outline-dark btn-sm fw-bold rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="bi bi-file-earmark-arrow-up"></i> นำเข้า CSV
                        </button>
                        <button type="button" class="btn btn-danger btn-sm fw-bold shadow-sm rounded-pill px-3" onclick="confirmMultiDelete()">
                            <i class="bi bi-trash3"></i> ลบที่เลือก
                        </button>
                    </div>
                </div>
                <div class="table-responsive bg-white">
                    <table class="table table-bright align-middle mb-0">
                        <thead>
                            <tr class="text-center">
                                <th width="50">
                                    <input class="form-check-input" type="checkbox" id="selectAll" style="border-color: #ffc107;">
                                </th>
                                <th width="100">รหัส นร.</th>
                                <th class="text-start">ชื่อ-นามสกุล</th>
                                <th width="80">ชั้น/ห้อง</th>
                                <th width="80">เลขที่</th>
                                <th>อีเมลโรงเรียน</th>
                                <th width="140">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result &&$result->num_rows > 0) {
                                while($row =$result->fetch_assoc()) { 
                                    $js_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            ?>
                            <tr class="text-center">
                                <td>
                                    <input class="form-check-input chk-student" type="checkbox" name="selected_students[]" value="<?= htmlspecialchars($row['student_id']); ?>">
                                </td>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($row['student_id']); ?></td>
                                <td class="text-start fw-bold text-dark"><?= htmlspecialchars($row['fullname']); ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-bold">ม.<?= htmlspecialchars($row['class_level']); ?>/<?= htmlspecialchars($row['room_number']); ?></span>
                                </td>
                                <td><?= htmlspecialchars($row['student_number']); ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm rounded-pill overflow-hidden border">
                                        <button type="button" class="btn btn-white text-primary fw-bold border-end" onclick="openStudentModal('edit', <?= $js_data; ?>)">แก้ไข</button>
                                        <a href="admin_students.php?delete=<?= urlencode($row['student_id']); ?>&term=<?= urlencode($filter_term); ?>&year=<?= urlencode($filter_year); ?>" class="btn btn-white text-danger fw-bold" onclick="return confirm('ลบข้อมูลของ <?= htmlspecialchars($row['fullname']); ?> ในเทอม <?= htmlspecialchars($filter_term.'/'.$filter_year); ?> ใช่หรือไม่?');">ลบ</a>
                                    </div>
                                </td>
                            </tr>
                            <?php } } else { ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted bg-light bg-opacity-50">
                                        <i class="bi bi-person-x fs-1 d-block mb-3 text-warning opacity-50"></i> ไม่พบรายชื่อนักเรียนในเทอม <?= htmlspecialchars($filter_term.'/'.$filter_year) ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>

    <!-- 🌟 Modal เพิ่ม/แก้ไข นักเรียนรายคน 🌟 -->
    <div class="modal fade modal-bright" id="studentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header pb-3 bg-warning bg-opacity-25 border-0">
                    <h5 class="modal-title fw-bold text-dark" id="modalTitle"><i class="bi bi-person-badge"></i> เพิ่มนักเรียนใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST" id="studentForm">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="old_id" id="oldId">
                        <input type="hidden" name="old_term" id="oldTerm">
                        <input type="hidden" name="old_year" id="oldYear">
                        
                        <div class="row g-3 bg-light p-2 rounded-3 mb-3 border">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-primary mb-1">ภาคเรียนที่</label>
                                <select name="semester" id="inpSemester" class="form-select border-primary fw-bold form-select-sm" required>
                                    <option value="1">เทอม 1</option>
                                    <option value="2">เทอม 2</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-primary mb-1">ปีการศึกษา</label>
                                <input type="number" name="academic_year" id="inpYear" class="form-control border-primary fw-bold form-control-sm" required>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-7">
                                <label class="form-label small fw-bold text-secondary mb-1">รหัสนักเรียน (5 หลัก)</label>
                                <input type="text" name="student_id" id="inpId" class="form-control form-bright form-control-sm" required>
                            </div>
                            <div class="col-5">
                                <label class="form-label small fw-bold text-secondary mb-1">เลขที่</label>
                                <input type="number" name="student_number" id="inpNum" class="form-control form-bright form-control-sm" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary mb-1">ชื่อ-นามสกุล</label>
                                <input type="text" name="fullname" id="inpName" class="form-control form-bright form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary mb-1">ระดับชั้น (1-6)</label>
                                <input type="number" name="class_level" id="inpClass" class="form-control form-bright form-control-sm" required min="1" max="6">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary mb-1">ห้อง (1-15)</label>
                                <input type="number" name="room_number" id="inpRoom" class="form-control form-bright form-control-sm" required min="1" max="15">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary mb-1">อีเมลโรงเรียน (ไม่บังคับ)</label>
                                <input type="email" name="email" id="inpEmail" class="form-control form-bright form-control-sm" placeholder="@hwp.ac.th">
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" name="save_student" class="btn btn-yellow fw-bold rounded-pill px-4 shadow-sm w-100 py-2">บันทึกข้อมูลนักเรียน</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 🌟 Modal นำเข้า CSV 🌟 -->
    <div class="modal fade modal-bright" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header pb-3 bg-dark text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-arrow-up text-warning"></i> นำเข้ารายชื่อนักเรียน (CSV File)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    
                    <form method="POST" enctype="multipart/form-data">
                        
                        <div class="row g-2 mb-4 text-start bg-light p-3 rounded-3 border">
                            <div class="col-12"><h6 class="fw-bold text-primary mb-2"><i class="bi bi-gear-fill"></i> ตั้งค่าการนำเข้า</h6></div>
                            <div class="col-6">
                                <label class="form-label small fw-bold mb-1">นำเข้าข้อมูลสู่ภาคเรียนที่</label>
                                <select name="import_semester" class="form-select form-select-sm fw-bold border-primary" required>
                                    <option value="1" <?= ($filter_term=='1')?'selected':'' ?>>เทอม 1</option>
                                    <option value="2" <?= ($filter_term=='2')?'selected':'' ?>>เทอม 2</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold mb-1">ปีการศึกษา</label>
                                <input type="number" name="import_year" class="form-control form-control-sm fw-bold border-primary" value="<?= htmlspecialchars($filter_year) ?>" required>
                            </div>
                        </div>

                        <div class="text-start alert alert-warning rounded-3 small mb-4 border-0">
                            <strong><i class="bi bi-info-circle-fill"></i> รูปแบบไฟล์ CSV ที่ถูกต้อง:</strong><br>
                            คอลัมน์ต้องเรียงตามนี้: 1.รหัส นร., 2.ชื่อ-สกุล, 3.ชั้น(ตัวเลข), 4.ห้อง, 5.เลขที่, 6.อีเมล
                            <div class="mt-3 text-center">
                                <a href="sample_students.csv" download class="btn btn-outline-dark btn-sm rounded-pill fw-bold bg-white">
                                    <i class="bi bi-download text-warning"></i> โหลดไฟล์ตัวอย่าง (sample_students.csv)
                                </a>
                            </div>
                        </div>
                        
                        <input type="file" name="csv_file" class="form-control form-bright form-control-lg shadow-sm mb-4" accept=".csv" required style="font-size: 1rem;">
                        <button type="submit" name="import_csv" class="btn btn-success w-100 fw-bold text-white py-3 rounded-pill shadow-sm fs-5">
                            <i class="bi bi-cloud-arrow-up-fill"></i> อัปโหลดและบันทึก
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 🌟 โหลด Bootstrap Bundle JS เพื่อให้ Modal ทำงาน 🌟 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ระบบ Checkbox ทั้งหมด
        document.getElementById('selectAll').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.chk-student');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        // ฟังก์ชันยืนยันการลบ
        function confirmMultiDelete() {
            let checked = document.querySelectorAll('.chk-student:checked').length;
            if (checked === 0) {
                alert('⚠️ กรุณาเลือกนักเรียนที่ต้องการลบอย่างน้อย 1 คนครับ');
                return;
            }
            if (confirm(`‼️ ยืนยันการลบข้อมูลนักเรียนที่เลือกจำนวน ${checked} คน ออกจากเทอมนี้ใช่หรือไม่?`)) {
                document.getElementById('deleteForm').submit();
            }
        }

        // ฟังก์ชันเปิด Modal พร้อมจัดการข้อมูลให้ถูกต้อง
        function openStudentModal(action, data = null) {
            // ใช้ getOrCreateInstance ป้องกันปัญหากดแล้ว Popup ไม่เด้ง
            var modalEl = document.getElementById('studentModal');
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            var form = document.getElementById('studentForm');
            
            form.reset();
            
            if (action === 'add') {
                document.getElementById('modalTitle').innerHTML = '<i class="bi bi-person-plus-fill text-warning"></i> เพิ่มนักเรียนใหม่';
                document.getElementById('formAction').value = 'add';
                document.getElementById('oldId').value = '';
                document.getElementById('inpId').readOnly = false;
                
                // ดึงค่าเทอมจากตัวกรองมาใส่ให้เลย
                document.getElementById('inpSemester').value = '<?= htmlspecialchars($filter_term) ?>';
                document.getElementById('inpYear').value = '<?= htmlspecialchars($filter_year) ?>';
            } else {
                document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square text-primary"></i> แก้ไขข้อมูลนักเรียน';
                document.getElementById('formAction').value = 'edit';
                
                document.getElementById('oldId').value = data.student_id;
                document.getElementById('oldTerm').value = data.semester;
                document.getElementById('oldYear').value = data.academic_year;
                
                document.getElementById('inpId').value = data.student_id;
                document.getElementById('inpName').value = data.fullname;
                document.getElementById('inpClass').value = data.class_level;
                document.getElementById('inpRoom').value = data.room_number;
                document.getElementById('inpNum').value = data.student_number;
                document.getElementById('inpEmail').value = data.email;
                
                document.getElementById('inpSemester').value = data.semester || '<?= htmlspecialchars($auto_term) ?>';
                document.getElementById('inpYear').value = data.academic_year || '<?= htmlspecialchars($auto_year) ?>';
                
                // ไม่ให้แก้รหัสนักเรียนตอนแก้ไข (เพราะเป็น Key อ้างอิง)
                document.getElementById('inpId').readOnly = true;
            }
            modal.show();
        }
    </script>
</body>
</html>