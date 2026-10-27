<?php
// เปิดระบบแสดง Error (เอาไว้เช็คปัญหา)
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
    $auto_term = ($current_d <= 15) ? '1' : '2';
} else {
    // พ.ย. - ธ.ค. เป็นเทอม 2
    $auto_term = '2'; 
}

// =========================================================
// 🌟 2. ตรวจสอบและสร้างคอลัมน์ในตาราง assignments อัตโนมัติ 🌟
// =========================================================
$check_col_class = $conn->query("SHOW COLUMNS FROM assignments LIKE 'class_level'");
if ($check_col_class && $check_col_class->num_rows == 0) {
    $conn->query("ALTER TABLE assignments ADD class_level VARCHAR(20) DEFAULT '' AFTER id");
}
$check_col_term = $conn->query("SHOW COLUMNS FROM assignments LIKE 'semester'");
if ($check_col_term && $check_col_term->num_rows == 0) {
    $conn->query("ALTER TABLE assignments ADD semester VARCHAR(10) DEFAULT '$auto_term' AFTER class_level");
}
$check_col_year = $conn->query("SHOW COLUMNS FROM assignments LIKE 'academic_year'");
if ($check_col_year && $check_col_year->num_rows == 0) {
    $conn->query("ALTER TABLE assignments ADD academic_year VARCHAR(10) DEFAULT '$auto_year' AFTER semester");
}

// โฟลเดอร์เก็บไฟล์แนบคำสั่งงาน
$upload_dir = 'uploads/assignments/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

// =========================================================
// 🌟 3. บันทึก / แก้ไข / ลบ การสั่งงาน 🌟
// =========================================================
if (isset($_POST['save_assignment'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $due_date = $conn->real_escape_string($_POST['due_date']);
    $class_level = $conn->real_escape_string($_POST['class_level']); 
    $semester = $conn->real_escape_string($_POST['semester']);
    $academic_year = $conn->real_escape_string($_POST['academic_year']);
    
    $action = $_POST['action'];
    $assignment_id = isset($_POST['assignment_id']) ? intval($_POST['assignment_id']) : 0;

    $file_name = NULL;
    $file_path = NULL;
    $file_sql = "";

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $original_name = $_FILES['attachment']['name'];
        $ext = pathinfo($original_name, PATHINFO_EXTENSION);
        $new_filename = 'task_' . time() . '.' . $ext;
        $target_file = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            $file_name = $conn->real_escape_string($original_name);
            $file_path = $conn->real_escape_string($target_file);
            $file_sql = ", file_name='$file_name', file_path='$file_path'";
        }
    }

    if ($action == 'add') {
        $db_file_name = $file_name ? "'$file_name'" : "NULL";
        $db_file_path = $file_path ? "'$file_path'" : "NULL";
        
        $conn->query("INSERT INTO assignments (class_level, semester, academic_year, title, description, file_name, file_path, due_date) 
                      VALUES ('$class_level', '$semester', '$academic_year', '$title', '$desc', $db_file_name, $db_file_path, '$due_date')");
    } else if ($action == 'edit') {
        $conn->query("UPDATE assignments SET class_level='$class_level', semester='$semester', academic_year='$academic_year', title='$title', description='$desc', due_date='$due_date' $file_sql WHERE id='$assignment_id'");
    }
    
    header("Location: admin_assignments.php?filter_term=$semester&filter_year=$academic_year");
    exit();
}

// ลบชิ้นงาน
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $term = isset($_GET['filter_term']) ? $_GET['filter_term'] : $auto_term;
    $year = isset($_GET['filter_year']) ? $_GET['filter_year'] : $auto_year;

    $res = $conn->query("SELECT file_path FROM assignments WHERE id='$id'");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (!empty($row['file_path']) && file_exists($row['file_path'])) unlink($row['file_path']);
    }
    $conn->query("DELETE FROM assignments WHERE id='$id'");
    header("Location: admin_assignments.php?filter_term=$term&filter_year=$year");
    exit();
}

// =========================================================
// 🌟 4. ระบบกรองข้อมูล (Filter) ตามเทอมและปีการศึกษา 🌟
// =========================================================
$filter_term = isset($_GET['filter_term']) ? $conn->real_escape_string($_GET['filter_term']) : $auto_term;
$filter_year = isset($_GET['filter_year']) ? $conn->real_escape_string($_GET['filter_year']) : $auto_year;
$filter_class = isset($_GET['filter_class']) ? $conn->real_escape_string($_GET['filter_class']) : '';

$where_sql = "WHERE semester = '$filter_term' AND academic_year = '$filter_year'";
if ($filter_class !== '') {
    $where_sql .= " AND class_level = '$filter_class'";
}

$result = $conn->query("SELECT * FROM assignments $where_sql ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการชิ้นงาน - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #fbfcfe; font-family: 'Sarabun', sans-serif; }
        .navbar-main { background-color: #ffffff; border-bottom: 4px solid #ffc107; }
        .card-bright { border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.03); background-color: #ffffff; }
        .btn-yellow { background-color: #ffc107; color: #000; font-weight: bold; }
        .btn-yellow:hover { background-color: #ffca28; color: #000; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light navbar-main py-3 mb-4">
        <div class="container-fluid px-4 d-flex justify-content-between">
            <span class="navbar-brand fw-bold text-dark"><i class="bi bi-journal-code text-warning"></i> จัดการชิ้นงาน (Admin)</span>
            <div>
                <a href="admin_panel.php" class="btn btn-outline-secondary rounded-pill fw-bold">กลับหน้าแผงควบคุม</a>
                <button class="btn btn-yellow rounded-pill ms-2 shadow-sm" onclick="openModal('add')"><i class="bi bi-plus-lg"></i> สั่งงานใหม่</button>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        
        <!-- 🌟 แถบกรองข้อมูล เทอม/ปีการศึกษา/ระดับชั้น 🌟 -->
        <div class="card card-bright mb-4 border-start border-warning border-5">
            <div class="card-body p-3">
                <form method="GET" class="row g-2 align-items-center">
                    <div class="col-auto"><span class="fw-bold"><i class="bi bi-filter"></i> เลือกดูชิ้นงาน:</span></div>
                    <div class="col-auto">
                        <select name="filter_term" class="form-select form-select-sm fw-bold border-primary">
                            <option value="1" <?= ($filter_term == '1') ? 'selected' : '' ?>>เทอม 1</option>
                            <option value="2" <?= ($filter_term == '2') ? 'selected' : '' ?>>เทอม 2</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="filter_year" class="form-select form-select-sm fw-bold border-primary">
                            <?php for($y = 2568; $y <= 2575; $y++): ?>
                                <option value="<?= $y ?>" <?= ($filter_year == strval($y)) ? 'selected' : '' ?>>ปีการศึกษา <?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="filter_class" class="form-select form-select-sm">
                            <option value="">-- ทุกระดับชั้น --</option>
                            <?php for($c = 1; $c <= 6; $c++): ?>
                                <option value="<?= $c ?>" <?= ($filter_class == strval($c)) ? 'selected' : '' ?>>ม.<?= $c ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">แสดงข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card-bright border-top border-warning border-4">
            <div class="card-body p-4">
                
                <h5 class="fw-bold text-primary mb-3">
                    <i class="bi bi-folder2-open"></i> รายการสั่งงาน (เทอม <?= htmlspecialchars($filter_term . '/' . $filter_year) ?>)
                </h5>

                <?php if ($result === false): ?>
                    <div class="alert alert-danger">ตารางฐานข้อมูลมีปัญหา กรุณาตรวจสอบว่าสร้างตาราง assignments แล้วหรือไม่</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="12%">ระดับชั้น</th>
                                    <th width="25%">หัวข้องาน</th>
                                    <th width="25%">คำอธิบาย</th>
                                    <th width="13%">ไฟล์แนบ</th>
                                    <th width="15%">กำหนดส่ง</th>
                                    <th width="10%" class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($result->num_rows == 0): ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">ยังไม่มีการสั่งงานในเทอม <?= htmlspecialchars($filter_term . '/' . $filter_year) ?> ครับ</td></tr>
                                <?php endif; ?>

                                <?php while($row = $result->fetch_assoc()): 
                                    $js_data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                ?>
                                <tr>
                                    <td>
                                        <?php if(!empty($row['class_level'])): ?>
                                            <span class="badge bg-primary fs-6">ม.<?= htmlspecialchars($row['class_level']); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">ทุกระดับชั้น</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['title']); ?></td>
                                    <td><small class="text-muted text-truncate d-inline-block" style="max-width: 250px;"><?= htmlspecialchars($row['description']); ?></small></td>
                                    <td>
                                        <?php if(!empty($row['file_path']) && $row['file_path'] != 'NULL'): ?>
                                            <a href="<?= htmlspecialchars($row['file_path']); ?>" target="_blank" class="badge bg-info text-dark text-decoration-none"><i class="bi bi-paperclip"></i> มีไฟล์แนบ</a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-danger"><?= (!empty($row['due_date']) && $row['due_date'] != '0000-00-00') ? date('d/m/Y', strtotime($row['due_date'])) : 'ไม่มีกำหนด'; ?></span></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-light text-primary border" onclick="openModal('edit', <?= $js_data; ?>)"><i class="bi bi-pencil"></i></button>
                                        <a href="?delete=<?= $row['id']; ?>&filter_term=<?= urlencode($filter_term); ?>&filter_year=<?= urlencode($filter_year); ?>" class="btn btn-sm btn-light text-danger border" onclick="return confirm('ลบงานนี้ใช่หรือไม่?');"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- 🌟 Modal สร้าง/แก้ไขคำสั่งงาน 🌟 -->
    <div class="modal fade" id="taskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-warning bg-opacity-25 border-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">สร้างคำสั่งงานใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST" enctype="multipart/form-data" id="taskForm">
                        <input type="hidden" name="action" id="action" value="add">
                        <input type="hidden" name="assignment_id" id="assignment_id">
                        
                        <div class="row bg-light p-3 rounded-3 mb-3 border">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label class="form-label fw-bold text-primary small">ภาคเรียนที่ <span class="text-danger">*</span></label>
                                <select name="semester" id="inpSemester" class="form-select border-primary fw-bold" required>
                                    <option value="1">เทอม 1</option>
                                    <option value="2">เทอม 2</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label class="form-label fw-bold text-primary small">ปีการศึกษา <span class="text-danger">*</span></label>
                                <input type="number" name="academic_year" id="inpYear" class="form-control border-primary fw-bold" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-primary small">ระดับชั้น <span class="text-danger">*</span></label>
                                <select name="class_level" id="inpClassLevel" class="form-select border-primary fw-bold">
                                    <option value="">-- ใช้กับทุกระดับชั้น --</option>
                                    <option value="1">ม.1</option>
                                    <option value="2">ม.2</option>
                                    <option value="3">ม.3</option>
                                    <option value="4">ม.4</option>
                                    <option value="5">ม.5</option>
                                    <option value="6">ม.6</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">หัวข้องาน <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="inpTitle" class="form-control bg-light" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">อธิบายชิ้นงาน / คำสั่ง <span class="text-danger">*</span></label>
                            <textarea name="description" id="inpDesc" rows="4" class="form-control bg-light" placeholder="พิมพ์คำอธิบายรายละเอียดงานที่นี่..." required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">ไฟล์แนบประกอบ (ไม่บังคับ)</label>
                                <input type="file" name="attachment" class="form-control bg-light">
                                <small class="text-muted">เช่น ไฟล์ใบงาน PDF, รูปภาพตัวอย่าง</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">กำหนดส่ง (ไม่บังคับ)</label>
                                <input type="date" name="due_date" id="inpDate" class="form-control bg-light">
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" name="save_assignment" class="btn btn-yellow rounded-pill px-4 shadow-sm">บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openModal(action, data = null) {
            var modal = new bootstrap.Modal(document.getElementById('taskModal'));
            document.getElementById('taskForm').reset();
            document.getElementById('action').value = action;
            
            if (action === 'add') {
                document.getElementById('modalTitle').innerText = 'สร้างคำสั่งงานใหม่';
                document.getElementById('inpSemester').value = '<?= htmlspecialchars($filter_term) ?>';
                document.getElementById('inpYear').value = '<?= htmlspecialchars($filter_year) ?>';
                document.getElementById('inpClassLevel').value = '<?= htmlspecialchars($filter_class) ?>';
            } else {
                document.getElementById('modalTitle').innerText = 'แก้ไขคำสั่งงาน';
                document.getElementById('assignment_id').value = data.id;
                document.getElementById('inpSemester').value = data.semester || '<?= htmlspecialchars($auto_term) ?>';
                document.getElementById('inpYear').value = data.academic_year || '<?= htmlspecialchars($auto_year) ?>';
                document.getElementById('inpClassLevel').value = data.class_level || '';
                document.getElementById('inpTitle').value = data.title;
                document.getElementById('inpDesc').value = data.description;
                document.getElementById('inpDate').value = data.due_date;
            }
            modal.show();
        }
    </script>
</body>
</html>