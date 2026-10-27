<?php
// เปิดระบบแสดง Error
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

// --- 🌟 ระบบคำนวณเทอมและปีการศึกษาปัจจุบันอัตโนมัติ 🌟 ---
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
    // พ.ค. - ก.ย. เป็นเทอม 1 แน่นอน
    $auto_term = '1'; 
} elseif ($current_m == 10) {
    // ต.ค. ต้องเช็ควันที่
    if ($current_d <= 15) {
        $auto_term = '1'; // 1 - 15 ต.ค. (เทอม 1)
    } else {
        $auto_term = '2'; // 16 - 31 ต.ค. (เทอม 2)
    }
} else {
    // พ.ย. - ธ.ค. เป็นเทอม 2 แน่นอน
    $auto_term = '2'; 
}

// รับค่าตัวกรอง ถ้าไม่มีให้ใช้ค่าคำนวณอัตโนมัติ
$filter_term = isset($_GET['filter_term']) ? $conn->real_escape_string($_GET['filter_term']) : $auto_term;
$filter_year = isset($_GET['filter_year']) ? $conn->real_escape_string($_GET['filter_year']) : $auto_year;
$search_class = isset($_GET['class']) ? $conn->real_escape_string($_GET['class']) : ''; 
$search_room = isset($_GET['room']) ? $conn->real_escape_string($_GET['room']) : '';      
$is_room_selected = ($search_class != '' && $search_room != '');

// --- 🌟 1. ระบบ AJAX สำหรับอัปเดตคะแนนอัตโนมัติ 🌟 ---
if (isset($_POST['action']) && $_POST['action'] == 'update_score') {
    ob_clean(); 
    
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $assignment_id = $conn->real_escape_string($_POST['assignment_id']);
    $score = $conn->real_escape_string($_POST['score']);
    
    $check = $conn->query("SELECT id FROM student_submissions WHERE student_id='$student_id' AND assignment_id='$assignment_id'");
    
    if ($check->num_rows > 0) {
        $score_val = ($score === '') ? 'NULL' : "'$score'";
        $conn->query("UPDATE student_submissions SET score=$score_val WHERE student_id='$student_id' AND assignment_id='$assignment_id'");
    } else {
        if ($score !== '') {
            $conn->query("INSERT INTO student_submissions (student_id, assignment_id, score, submit_type) 
                          VALUES ('$student_id', '$assignment_id', '$score', 'manual_grade')");
        }
    }
    echo "success";
    exit();
}

// --- 🌟 2. ระบบเพิ่มช่องงานด่วน (Quick Add) 🌟 ---
if (isset($_POST['quick_add_assignment'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $class_level = $conn->real_escape_string($_POST['class_level']);
    $q_term = $conn->real_escape_string($_POST['q_term']);
    $q_year = $conn->real_escape_string($_POST['q_year']);
    
    $conn->query("INSERT INTO assignments (class_level, semester, academic_year, title, description) 
                  VALUES ('$class_level', '$q_term', '$q_year', '$title', 'งานนี้สร้างจากหน้าสรุปคะแนน')");
    
    $redirect_room = isset($_GET['room']) ? $_GET['room'] : '';
    header("Location: admin_student_scores.php?filter_term=$q_term&filter_year=$q_year&class=$class_level&room=$redirect_room");
    exit();
}

// ตรวจสอบคอลัมน์
$check_column = $conn->query("SHOW COLUMNS FROM assignments LIKE 'class_level'");
if($check_column && $check_column->num_rows == 0){
    $conn->query("ALTER TABLE assignments ADD class_level VARCHAR(20) DEFAULT '' AFTER id");
}

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
    <title>สมุด ปพ. ออนไลน์ - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #fbfcfe; }
        .navbar-admin { background: #212529; border-bottom: 3px solid #dc3545; }
        .table-responsive { max-width: 100%; overflow-x: auto; white-space: nowrap; }
        .sticky-col { position: sticky; left: 0; background-color: white; z-index: 2; box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1); }
        .sticky-col-header { position: sticky; left: 0; z-index: 3; }
        
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; transition: all 0.3s ease; }
        
        .score-input { font-weight: bold; width: 60px !important; margin: 0 auto; }
        .score-input:focus { box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25); border-color: #198754; }
    </style>
</head>
<body>

    <nav class="navbar navbar-admin py-3 mb-4 shadow-sm">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <span class="text-white fw-bold fs-5">
                <i class="bi bi-clipboard-data-fill text-danger me-2"></i> ระบบสมุด ปพ. ออนไลน์
            </span>
            <div class="d-flex gap-2 align-items-center">
                <a href="admin_panel.php" class="btn btn-outline-light btn-sm rounded-pill fw-bold">
                    <i class="bi bi-arrow-left-circle"></i> กลับหน้าแผงควบคุม
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 my-4">
        
        <div class="card mb-4 border-0 shadow-sm border-top border-danger border-4">
            <div class="card-body bg-white py-4">
                <form method="GET" class="row g-3 align-items-end justify-content-center">
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark small">ภาคเรียนที่</label>
                        <select name="filter_term" class="form-select bg-light border-secondary fw-bold">
                            <!-- 🌟 ลบภาคฤดูร้อนออก 🌟 -->
                            <option value="1" <?= ($filter_term=='1')?'selected':'' ?>>เทอม 1</option>
                            <option value="2" <?= ($filter_term=='2')?'selected':'' ?>>เทอม 2</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark small">ปีการศึกษา</label>
                        <select name="filter_year" class="form-select bg-light border-secondary fw-bold">
                            <?php for($y = 2568; $y <= 2575; $y++): ?>
                                <option value="<?=$y?>" <?= ($filter_year==strval($y))?'selected':'' ?>>ปี <?=$y?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">ระดับชั้น <span class="text-danger">*</span></label>
                        <select name="class" class="form-select bg-light border-primary" required>
                            <option value="">-- เลือกระดับชั้น --</option>
                            <?php 
                            foreach($active_classes as $c) {
                                $sel = ($search_class == $c) ? 'selected' : '';
                                echo "<option value='$c' $sel>ม.$c</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">ห้อง <span class="text-danger">*</span></label>
                        <select name="room" class="form-select bg-light border-primary" required>
                            <option value="">-- เลือกห้อง --</option>
                            <?php 
                            foreach($active_rooms as $r) { 
                                $sel = ($search_room == $r) ? 'selected' : '';
                                echo "<option value='$r' $sel>$r</option>"; 
                            } 
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-danger w-100 fw-bold shadow-sm">
                            <i class="bi bi-search"></i> ดูคะแนน
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($is_room_selected): ?>
            <?php 
            $sql_assignments = "SELECT id, title FROM assignments 
                                WHERE (class_level = '$search_class' OR class_level = '' OR class_level IS NULL) 
                                AND semester = '$filter_term' AND academic_year = '$filter_year' 
                                ORDER BY created_at ASC";
            $res_assignments = $conn->query($sql_assignments);
            $assignments = [];
            if ($res_assignments && $res_assignments->num_rows > 0) {
                while($a = $res_assignments->fetch_assoc()) { $assignments[] = $a; }
            }

            $sql_scores = "
                SELECT sub.student_id, sub.assignment_id, sub.score 
                FROM student_submissions sub
                JOIN students s ON sub.student_id = s.student_id
                WHERE s.class_level = '$search_class' AND s.room_number = '$search_room'
                AND s.semester = '$filter_term' AND s.academic_year = '$filter_year'
            ";
            $res_scores = $conn->query($sql_scores); 
            $student_scores = [];
            if ($res_scores && $res_scores->num_rows > 0) {
                while($score_row = $res_scores->fetch_assoc()) {
                    $student_scores[$score_row['student_id']][$score_row['assignment_id']] = $score_row['score'];
                }
            }

            $sql_students = "SELECT student_id, fullname, student_number FROM students 
                             WHERE class_level = '$search_class' AND room_number = '$search_room' 
                             AND semester = '$filter_term' AND academic_year = '$filter_year'
                             ORDER BY CAST(student_number AS UNSIGNED) ASC";
            $query_students = $conn->query($sql_students);
            ?>

            <div class="card shadow-sm border-0 border-top border-4 border-dark mb-5">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-pencil-square me-2 text-success"></i> 
                        ให้คะแนนนักเรียน ม.<?php echo htmlspecialchars($search_class.'/'.$search_room); ?> 
                        <span class="badge bg-secondary fs-6 ms-2">เทอม <?php echo htmlspecialchars($filter_term.'/'.$filter_year); ?></span>
                    </h5>
                    <div>
                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm me-2 fw-bold" data-bs-toggle="modal" data-bs-target="#quickAddModal">
                            <i class="bi bi-plus-circle"></i> เพิ่มช่องงานใหม่
                        </button>
                        <button onclick="window.print()" class="btn btn-sm btn-outline-dark rounded-pill px-3 shadow-sm">
                            <i class="bi bi-printer"></i> พิมพ์
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive pb-4" style="max-height: 70vh;">
                    <table class="table table-bordered table-hover align-middle mb-0 bg-white">
                        <thead class="table-light align-middle text-center">
                            <tr>
                                <th width="60" rowspan="2" class="sticky-col sticky-col-header text-secondary">เลขที่</th>
                                <th width="120" rowspan="2" class="sticky-col sticky-col-header text-secondary" style="left: 60px;">รหัสนักเรียน</th>
                                <th width="220" rowspan="2" class="sticky-col sticky-col-header text-start text-dark" style="left: 180px;">ชื่อ-นามสกุล</th>
                                
                                <?php if (count($assignments) > 0): ?>
                                    <th colspan="<?php echo count($assignments); ?>" class="text-primary bg-light border-bottom-0">
                                        <i class="bi bi-journal-bookmark-fill"></i> ชิ้นงานของ ม.<?php echo htmlspecialchars($search_class); ?> 
                                        <small class="text-muted fw-normal">(พิมพ์คะแนนเพื่อบันทึกอัตโนมัติ)</small>
                                    </th>
                                <?php else: ?>
                                    <th rowspan="2" class="text-muted">ยังไม่มีช่องคะแนน กดปุ่ม "เพิ่มช่องงานใหม่" ด้านบนได้เลยครับ</th>
                                <?php endif; ?>
                                
                                <th width="70" rowspan="2" class="bg-dark text-white shadow-sm border-start">รวม</th>
                                <th width="70" rowspan="2" class="bg-dark text-warning shadow-sm">เกรด</th>
                            </tr>
                            <tr>
                                <?php foreach($assignments as $index => $ass): ?>
                                    <th style="font-size: 0.85rem; min-width: 80px; max-width: 150px;" class="text-secondary bg-light pb-2 text-truncate" title="<?php echo htmlspecialchars($ass['title']); ?>">
                                        งานที่ <?php echo $index + 1; ?><br>
                                        <small class="text-muted fw-normal text-truncate d-inline-block w-100"><?php echo htmlspecialchars($ass['title']); ?></small>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($query_students && $query_students->num_rows > 0) {
                                while($std = $query_students->fetch_assoc()) {
                                    $sid = $std['student_id'];
                                    $total_score = 0;
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-secondary sticky-col"><?php echo htmlspecialchars($std['student_number']); ?></td>
                                <td class="text-center text-muted fs-6 sticky-col" style="left: 60px;"><code class="text-primary"><?php echo htmlspecialchars($sid); ?></code></td>
                                <td class="text-start fw-bold text-dark sticky-col text-truncate" style="left: 180px; max-width: 220px;"><?php echo htmlspecialchars($std['fullname']); ?></td>
                                
                                <?php foreach($assignments as $ass): 
                                    $a_id = $ass['id'];
                                    $score_val = '';
                                    $input_class = 'score-input';
                                    $icon_html = '';
                                    
                                    if (isset($student_scores[$sid]) && array_key_exists($a_id, $student_scores[$sid])) {
                                        $score = $student_scores[$sid][$a_id];
                                        if ($score === null) {
                                            $input_class .= ' bg-warning bg-opacity-10 border-warning';
                                            $icon_html = "<span class='position-absolute top-0 start-100 translate-middle p-1 bg-warning border border-light rounded-circle' title='ส่งงานแล้ว รอตรวจ'></span>";
                                        } else {
                                            $score_val = $score;
                                            $total_score += (float)$score;
                                            $input_class .= ' text-success';
                                        }
                                    } else {
                                        $input_class .= ' bg-light'; 
                                    }
                                ?>
                                    <td class="text-center bg-white position-relative p-2">
                                        <input type="number" step="any" 
                                               class="form-control form-control-sm text-center <?php echo $input_class; ?>" 
                                               data-sid="<?php echo htmlspecialchars($sid); ?>" 
                                               data-aid="<?php echo htmlspecialchars($a_id); ?>" 
                                               value="<?php echo htmlspecialchars($score_val); ?>" 
                                               placeholder="-">
                                        <?php echo $icon_html; ?>
                                    </td>
                                <?php endforeach; ?>
                                
                                <?php if (count($assignments) == 0): ?>
                                    <td class="text-center text-muted bg-light">-</td>
                                <?php endif; ?>

                                <?php 
                                    if ($total_score >= 80) { $grade = "4.0"; $g_class = "text-success"; }
                                    elseif ($total_score >= 75) { $grade = "3.5"; $g_class = "text-success"; }
                                    elseif ($total_score >= 70) { $grade = "3.0"; $g_class = "text-primary"; }
                                    elseif ($total_score >= 65) { $grade = "2.5"; $g_class = "text-primary"; }
                                    elseif ($total_score >= 60) { $grade = "2.0"; $g_class = "text-warning"; }
                                    elseif ($total_score >= 55) { $grade = "1.5"; $g_class = "text-warning"; }
                                    elseif ($total_score >= 50) { $grade = "1.0"; $g_class = "text-danger"; }
                                    else { $grade = "0"; $g_class = "text-danger"; }
                                ?>
                                <td class="text-center fw-bold fs-5 bg-light text-dark shadow-sm border-start total-score">
                                    <?php echo $total_score; ?>
                                </td>
                                <td class="text-center fw-bold fs-5 bg-light shadow-sm grade-result <?php echo $g_class; ?>">
                                    <?php echo $grade; ?>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                                $colspan = 5 + count($assignments);
                                echo "<tr><td colspan='$colspan' class='text-center py-5 text-muted'>ไม่พบข้อมูลนักเรียนครับ (ลองตรวจสอบตัวกรองเทอมและปีการศึกษา)</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5 text-muted">
                    <h5 class='mb-0'><i class='bi bi-funnel-fill fs-1 d-block mb-3 text-danger opacity-50'></i> กรุณาเลือกระดับชั้นและห้องเรียนด้านบนเพื่อดูและแก้ไขคะแนนครับ</h5>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <div class="modal fade" id="quickAddModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle"></i> เพิ่มช่องงาน (ม.<?php echo htmlspecialchars($search_class); ?>)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST">
                        <input type="hidden" name="class_level" value="<?php echo htmlspecialchars($search_class); ?>">
                        <input type="hidden" name="q_term" value="<?php echo htmlspecialchars($filter_term); ?>">
                        <input type="hidden" name="q_year" value="<?php echo htmlspecialchars($filter_year); ?>">
                        
                        <div class="alert alert-info py-2 small fw-bold">
                            <i class="bi bi-info-circle-fill"></i> งานนี้จะถูกเพิ่มเข้าไปใน เทอม <?php echo htmlspecialchars($filter_term.'/'.$filter_year); ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อชิ้นงาน / ภาระงาน</label>
                            <input type="text" name="title" class="form-control" placeholder="เช่น สอบเก็บคะแนนบทที่ 1, สมุดจด" required>
                            <small class="text-muted mt-2 d-block">* หลังจากสร้างแล้วสามารถมากดพิมพ์คะแนนในตารางได้เลย</small>
                        </div>
                        <div class="text-end">
                            <button type="submit" name="quick_add_assignment" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">บันทึกช่องงาน</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .navbar, form, .btn, .card-header button, .badge, .modal { display: none !important; }
            .card { border: none !important; box-shadow: none !important; margin-bottom: 0 !important; }
            body { background-color: white; padding: 0 !important; }
            .sticky-col { position: static; box-shadow: none; left: auto !important; }
            .table-responsive { overflow-x: visible; max-height: none !important; }
            input[type=number] { border: none !important; background: transparent !important; box-shadow: none !important; padding: 0 !important; text-align: center; font-weight: bold; }
            input[type=number]::placeholder { color: transparent; }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.querySelectorAll('.score-input').forEach(input => {
            input.addEventListener('change', function() {
                let sid = this.getAttribute('data-sid');
                let aid = this.getAttribute('data-aid');
                let score = this.value;
                
                this.style.backgroundColor = '#e9ecef';
                
                let formData = new FormData();
                formData.append('action', 'update_score');
                formData.append('student_id', sid);
                formData.append('assignment_id', aid);
                formData.append('score', score);
                
                fetch('admin_student_scores.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if(data.trim() === 'success') {
                        this.style.backgroundColor = '#d1e7dd';
                        this.classList.remove('bg-light', 'bg-warning', 'bg-opacity-10', 'border-warning');
                        this.classList.add('text-success');
                        
                        let badge = this.parentNode.querySelector('.bg-warning.rounded-circle');
                        if (badge) badge.remove();

                        setTimeout(() => { this.style.backgroundColor = ''; }, 800);
                        
                        updateTotalRow(this.closest('tr'));
                    } else {
                        alert('เกิดข้อผิดพลาดในการบันทึกคะแนน กรุณาลองใหม่ครับ');
                        this.style.backgroundColor = '#f8d7da';
                    }
                });
            });
        });

        function updateTotalRow(row) {
            let inputs = row.querySelectorAll('.score-input');
            let total = 0;
            inputs.forEach(inp => {
                let val = parseFloat(inp.value);
                if(!isNaN(val)) total += val;
            });
            
            row.querySelector('.total-score').innerText = total;
            
            let grade = '0';
            let gClass = 'text-danger';
            
            if (total >= 80) { grade = '4.0'; gClass = 'text-success'; }
            else if (total >= 75) { grade = '3.5'; gClass = 'text-success'; }
            else if (total >= 70) { grade = '3.0'; gClass = 'text-primary'; }
            else if (total >= 65) { grade = '2.5'; gClass = 'text-primary'; }
            else if (total >= 60) { grade = '2.0'; gClass = 'text-warning'; }
            else if (total >= 55) { grade = '1.5'; gClass = 'text-warning'; }
            else if (total >= 50) { grade = '1.0'; gClass = 'text-danger'; }
            
            let gradeTd = row.querySelector('.grade-result');
            gradeTd.innerText = grade;
            gradeTd.className = 'text-center fw-bold fs-5 bg-light shadow-sm grade-result ' + gClass;
        }
    </script>
</body>
</html>