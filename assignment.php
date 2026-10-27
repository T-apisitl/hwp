<?php
// เปิดแสดง Error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
@include 'connect.php';

// บังคับล็อกอิน
if (!isset($_SESSION['student_logged_in'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อนครับ'); window.location='index.php';</script>";
    exit();
}

$my_id = $_SESSION['student_id'] ?? 0;
$fullname = $_SESSION['student_name'] ?? 'นักเรียน';

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

// ดึงข้อมูลชั้น ห้อง เทอม และปีการศึกษาของนักเรียน
$stmt = $conn->query("SELECT class_level, room_number, semester, academic_year FROM students WHERE student_id = '$my_id' AND semester = '$auto_term' AND academic_year = '$auto_year' LIMIT 1");
if (!$stmt || $stmt->num_rows == 0) {
    $stmt = $conn->query("SELECT class_level, room_number, semester, academic_year FROM students WHERE student_id = '$my_id' ORDER BY id DESC LIMIT 1");
}

if ($stmt && $stmt->num_rows > 0) {
    $std_info = $stmt->fetch_assoc();
    $my_class = $std_info['class_level'];
    $my_room = $std_info['room_number'];
    $active_term = $std_info['semester'] ?? $auto_term;
    $active_year = $std_info['academic_year'] ?? $auto_year;
} else {
    $my_class = '';
    $my_room = '';
    $active_term = $auto_term;
    $active_year = $auto_year;
}

// 🌟 ระบบสร้างตารางคอมเมนต์อัตโนมัติ 🌟
$conn->query("CREATE TABLE IF NOT EXISTS assignment_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT,
    sender_id VARCHAR(50),
    sender_name VARCHAR(100),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// =========================================================
// 2. ส่งคอมเมนต์ใต้โพสต์คำสั่งงาน
// =========================================================
if (isset($_POST['post_comment'])) {
    $a_id = intval($_POST['assignment_id']);
    $msg = $conn->real_escape_string($_POST['comment_msg']);
    
    if (trim($msg) != '') {
        $conn->query("INSERT INTO assignment_comments (assignment_id, sender_id, sender_name, message) 
                      VALUES ('$a_id', '$my_id', '$fullname', '$msg')");
    }
    header("Location: assignment.php");
    exit();
}

// =========================================================
// 3. ส่งงาน (รองรับงานเดี่ยว และงานกลุ่ม 1-5 คน)
// =========================================================
if (isset($_POST['submit_work'])) {
    $assignment_id = intval($_POST['assignment_id']);
    $submit_type = $_POST['submit_type']; 
    $comment = $conn->real_escape_string($_POST['student_comment']);
    
    $file_uploaded = NULL;
    $db_file_path = "NULL"; 
    
    if (isset($_FILES['student_file']) && $_FILES['student_file']['error'] == 0) {
        $max_size = 20 * 1024 * 1024;
        if ($_FILES['student_file']['size'] > $max_size) {
            echo "<script>alert('❌ ไฟล์มีขนาดใหญ่เกิน 20 MB กรุณาอัปโหลดใหม่ครับ'); window.history.back();</script>";
            exit();
        }

        $upload_dir = 'uploads/submissions/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $ext = pathinfo($_FILES['student_file']['name'], PATHINFO_EXTENSION);
        $new_filename = 'work_' . $my_id . '_' . time() . '.' . $ext;
        $target_file = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['student_file']['tmp_name'], $target_file)) {
            $db_file_path = "'" . $conn->real_escape_string($target_file) . "'";
        }
    }
    
    if ($submit_type === 'individual') {
        $conn->query("INSERT INTO student_submissions (assignment_id, student_id, file_path, comment, submit_type) 
                      VALUES ('$assignment_id', '$my_id', $db_file_path, '$comment', 'individual')");
    } else {
        $group_id = 'GRP_' . time() . '_' . $my_id; 
        $conn->query("INSERT INTO student_submissions (assignment_id, student_id, file_path, comment, submit_type, group_id) 
                      VALUES ('$assignment_id', '$my_id', $db_file_path, '$comment', 'group', '$group_id')");
        
        if (isset($_POST['group_members']) && is_array($_POST['group_members'])) {
            $members = array_slice($_POST['group_members'], 0, 5); 
            foreach ($members as $member_id) {
                $member_id = $conn->real_escape_string($member_id);
                $conn->query("INSERT INTO student_submissions (assignment_id, student_id, file_path, comment, submit_type, group_id) 
                               VALUES ('$assignment_id', '$member_id', $db_file_path, '$comment', 'group', '$group_id')");
            }
        }
    }
    echo "<script>alert('✅ ส่งงานสำเร็จเรียบร้อยแล้วครับ!'); window.location='assignment.php';</script>";
    exit();
}

// =========================================================
// 4. ดึงข้อมูลเพื่อนร่วมห้อง และ โพสต์คำสั่งงาน (เฉพาะเทอม/ปีปัจจุบัน)
// =========================================================

// ดึงรายชื่อเพื่อนในห้อง (เฉพาะเทอมและปีการศึกษาเดียวกัน)
$classmates = [];
if (!empty($my_class) && !empty($my_room)) {
    $sql_mates = "SELECT student_id, fullname, student_number FROM students 
                  WHERE class_level = '$my_class' AND room_number = '$my_room' 
                  AND semester = '$active_term' AND academic_year = '$active_year' 
                  AND student_id != '$my_id' 
                  ORDER BY CAST(student_number AS UNSIGNED) ASC";
    $res_mates = $conn->query($sql_mates);
    if ($res_mates) {
        while ($r = $res_mates->fetch_assoc()) {
            $classmates[] = $r;
        }
    }
}

// ดึงโพสต์งานทั้งหมดของระดับชั้นและเทอมนี้
$assignments = [];
$sql_tasks = "SELECT a.*, sub.id as sub_id 
              FROM assignments a
              LEFT JOIN student_submissions sub ON a.id = sub.assignment_id AND sub.student_id = '$my_id'
              WHERE (a.class_level = '$my_class' OR a.class_level = '' OR a.class_level IS NULL)
              AND a.semester = '$active_term' AND a.academic_year = '$active_year'
              ORDER BY a.id DESC";
$res_tasks = $conn->query($sql_tasks);
if ($res_tasks) {
    while ($row = $res_tasks->fetch_assoc()) {
        $assignments[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ฟีดคำสั่งงาน - ห้องเรียนครูริว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Sarabun', sans-serif; color: #1c1e21; }
        .hero-section { background: #ffffff; border-bottom: 1px solid #ddd; padding: 1.5rem; text-align: center; }
        .post-card { border-radius: 12px; border: 1px solid #ddd; box-shadow: 0 1px 2px rgba(0,0,0,0.08); background-color: #ffffff; margin-bottom: 1.5rem; }
        .teacher-avatar { width: 45px; height: 45px; border-radius: 50%; background-color: #0d6efd; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .post-content { background-color: #f8f9fa; border-left: 4px solid #0d6efd; padding: 15px; border-radius: 0 8px 8px 0; margin-top: 10px; }
        .comment-section { background-color: #f0f2f5; padding: 15px; border-radius: 0 0 12px 12px; }
        .comment-bubble { background-color: #ffffff; padding: 10px 15px; border-radius: 18px; display: inline-block; max-width: 90%; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .btn-submit-work { font-weight: bold; border-radius: 8px; }
        .member-select-box { background-color: #fff; border: 1px solid #ced4da; border-radius: 10px; padding: 15px; max-height: 200px; overflow-y: auto; }
    </style>
</head>
<body>

    <?php if (file_exists('nav.php')) { include 'nav.php'; } ?>

    <div class="hero-section mb-4 shadow-sm">
        <div class="container">
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">
                ภาคเรียนที่ <?= htmlspecialchars($active_term . '/' . $active_year); ?>
            </span>
            <h2 class="fw-bold mb-1 text-primary"><i class="bi bi-card-list"></i> ฟีดคำสั่งงานห้อง ม.<?= htmlspecialchars($my_class . '/' . $my_room); ?></h2>
            <p class="text-muted mb-0">ดูคำสั่งงาน แสดงความคิดเห็นสอบถาม และกดส่งงานได้ที่นี่</p>
        </div>
    </div>

    <div class="container mb-5" style="max-width: 700px;">
        
        <?php if (count($assignments) > 0): ?>
            <?php foreach($assignments as $task): 
                $a_id = $task['id'];
                $is_submitted = !is_null($task['sub_id']);
                
                // ดึงคอมเมนต์ของโพสต์นี้
                $comments = [];
                $res_com = $conn->query("SELECT * FROM assignment_comments WHERE assignment_id = '$a_id' ORDER BY created_at ASC");
                if ($res_com) { while($c = $res_com->fetch_assoc()) { $comments[] = $c; } }
            ?>
            <div class="post-card">
                <!-- ส่วนหัวโพสต์ -->
                <div class="p-3 d-flex justify-content-between align-items-center border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="teacher-avatar shadow-sm me-3"><i class="bi bi-person-fill"></i></div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">ครูริว <span class="fw-normal text-muted">ได้โพสต์คำสั่งงาน:</span> <?= htmlspecialchars($task['title']); ?></h6>
                            <small class="text-muted">
                                <?php if(!empty($task['due_date']) && $task['due_date'] != '0000-00-00'): ?>
                                    <span class="text-danger fw-bold"><i class="bi bi-calendar-event"></i> กำหนดส่ง: <?= date('d/m/Y', strtotime($task['due_date'])); ?></span>
                                <?php else: ?>
                                    <i class="bi bi-clock"></i> ไม่ระบุวันครบกำหนด
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- เนื้อหาโพสต์ -->
                <div class="p-4">
                    <div class="post-content">
                        <?= nl2br(htmlspecialchars($task['description'])); ?>
                    </div>
                    
                    <?php if(!empty($task['file_path']) && $task['file_path'] != 'NULL'): ?>
                        <div class="mt-3">
                            <a href="<?= htmlspecialchars($task['file_path']); ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill fw-bold">
                                <i class="bi bi-paperclip"></i> ดาวน์โหลดไฟล์แนบจากครู
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <span class="text-muted small"><i class="bi bi-chat-dots"></i> ความคิดเห็น <?= count($comments); ?> รายการ</span>
                        <?php if($is_submitted): ?>
                            <button class="btn btn-success btn-submit-work px-4 py-2" disabled><i class="bi bi-check-circle-fill"></i> ส่งงานเรียบร้อยแล้ว</button>
                        <?php else: ?>
                            <button class="btn btn-primary btn-submit-work px-4 py-2 shadow-sm" onclick="openSubmitModal('<?= $a_id; ?>', '<?= htmlspecialchars($task['title'], ENT_QUOTES); ?>')">
                                <i class="bi bi-send-fill"></i> ส่งงานชิ้นนี้
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ช่องคอมเมนต์ใต้โพสต์ -->
                <div class="comment-section">
                    <?php if (count($comments) > 0): ?>
                        <div class="mb-3" style="max-height: 250px; overflow-y: auto;">
                            <?php foreach($comments as $c): 
                                $is_me = ($c['sender_id'] == $my_id);
                            ?>
                                <div class="mb-2 d-flex <?= $is_me ? 'justify-content-end' : 'justify-content-start'; ?>">
                                    <div class="comment-bubble <?= $is_me ? 'bg-primary text-white' : ''; ?>">
                                        <?php if(!$is_me): ?>
                                            <div class="small fw-bold <?= ($c['sender_id'] == 'admin') ? 'text-danger' : 'text-primary'; ?> mb-1" style="font-size: 0.75rem;">
                                                <?= htmlspecialchars($c['sender_name']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="mb-0" style="font-size: 0.95rem;"><?= nl2br(htmlspecialchars($c['message'])); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="assignment_id" value="<?= $a_id; ?>">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($fullname); ?>&background=random&color=fff&size=40" class="rounded-circle" style="width: 38px; height: 38px;">
                        <input type="text" name="comment_msg" class="form-control rounded-pill bg-white" placeholder="เพิ่มความคิดเห็นในชั้นเรียน..." required autocomplete="off">
                        <button type="submit" name="post_comment" class="btn btn-light text-primary rounded-circle border shadow-sm" style="width:38px; height:38px; flex-shrink: 0;"><i class="bi bi-send-fill"></i></button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                <i class="bi bi-inbox text-muted opacity-50" style="font-size: 4rem;"></i>
                <h4 class="fw-bold mt-3 text-muted">ยังไม่มีคำสั่งงานในภาคเรียนนี้</h4>
                <a href="student_dashboard.php" class="btn btn-outline-primary rounded-pill mt-3 px-4 fw-bold">กลับหน้าแรก</a>
            </div>
        <?php endif; ?>

    </div>

    <!-- 🌟 Modal สำหรับส่งงาน 🌟 -->
    <div class="modal fade" id="submitModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-cloud-arrow-up-fill"></i> ส่งงาน: <span id="modalTaskTitle"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <input type="hidden" name="assignment_id" id="modalTaskId">

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark"><i class="bi bi-people-fill text-warning"></i> รูปแบบการส่งงาน</label>
                            <select class="form-select bg-white fw-bold text-primary" name="submit_type" id="submitType" onchange="toggleGroup()" required>
                                <option value="individual" selected>🙋‍♂️ ส่งงานเดี่ยว</option>
                                <option value="group">👥 ส่งงานแบบกลุ่ม</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="groupMembersBox">
                            <label class="form-label fw-bold text-primary">เลือกสมาชิกกลุ่ม (เลือกเพื่อน 1 - 5 คน) <span class="text-danger">*</span></label>
                            <div class="member-select-box">
                                <div class="row g-2">
                                    <?php if (!empty($classmates)): ?>
                                        <?php foreach ($classmates as $mate): ?>
                                            <div class="col-sm-6">
                                                <div class="form-check p-2 border rounded bg-white">
                                                    <input class="form-check-input ms-1 peer-checkbox" type="checkbox" name="group_members[]" value="<?= htmlspecialchars($mate['student_id']); ?>" id="mate_<?= htmlspecialchars($mate['student_id']); ?>">
                                                    <label class="form-check-label text-dark ms-2 w-100" style="cursor:pointer;" for="mate_<?= htmlspecialchars($mate['student_id']); ?>">
                                                        <strong>เลขที่ <?= htmlspecialchars($mate['student_number']); ?></strong> - <?= htmlspecialchars($mate['fullname']); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12 text-muted text-center py-2">ไม่มีข้อมูลเพื่อนในห้องนี้สำหรับเทอม <?= htmlspecialchars($active_term . '/' . $active_year) ?> ครับ</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <small class="text-danger mt-1 d-block" id="groupAlert" style="display:none;"><i class="bi bi-exclamation-circle"></i> เลือกเพื่อนได้สูงสุด 5 คนครับ</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark"><i class="bi bi-card-text text-primary"></i> พิมพ์ข้อความอธิบายผลงาน หรือวางลิงก์งาน <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="student_comment" rows="3" placeholder="วางลิงก์ Canva, เว็บไซต์ หรือพิมพ์คำอธิบายที่นี่..." required></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark"><i class="bi bi-paperclip text-success"></i> แนบไฟล์ประกอบ (ไม่บังคับ)</label>
                            <input class="form-control" type="file" name="student_file" id="fileUpload">
                            <small class="text-muted mt-1 d-block">รองรับไฟล์ขนาดไม่เกิน 20 MB</small>
                        </div>

                        <button type="submit" name="submit_work" class="btn btn-primary w-100 rounded-pill py-3 fw-bold fs-5 shadow-sm">
                            <i class="bi bi-send-check-fill"></i> ยืนยันการส่งงาน
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openSubmitModal(id, title) {
            document.getElementById('modalTaskId').value = id;
            document.getElementById('modalTaskTitle').innerText = title;
            new bootstrap.Modal(document.getElementById('submitModal')).show();
        }

        function toggleGroup() {
            const submitType = document.getElementById('submitType').value;
            const groupBox = document.getElementById('groupMembersBox');
            if (submitType === 'group') {
                groupBox.classList.remove('d-none');
            } else {
                groupBox.classList.add('d-none');
                document.querySelectorAll('.peer-checkbox').forEach(cb => cb.checked = false);
            }
        }

        const checkboxes = document.querySelectorAll('.peer-checkbox');
        const alertBox = document.getElementById('groupAlert');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if (document.querySelectorAll('.peer-checkbox:checked').length > 5) {
                    this.checked = false;
                    alertBox.style.display = 'block';
                } else {
                    alertBox.style.display = 'none';
                }
            });
        });

        function validateForm() {
            if (document.getElementById('submitType').value === 'group') {
                if (document.querySelectorAll('.peer-checkbox:checked').length === 0) {
                    alert('❌ คุณเลือกส่งงานกลุ่ม กรุณาติ๊กเลือกเพื่อน 1-5 คนครับ');
                    return false;
                }
            }
            const fileInput = document.getElementById('fileUpload');
            if (fileInput.files.length > 0 && fileInput.files[0].size > (20 * 1024 * 1024)) {
                alert('❌ ไฟล์มีขนาดใหญ่เกิน 20 MB กรุณาลดขนาดไฟล์ครับ');
                return false;
            }
            return true;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>