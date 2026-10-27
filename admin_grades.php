<?php
// เปิดระบบแสดง Error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'connect.php';

// --- 1. ระบบบันทึกคะแนน ---
if (isset($_POST['submit_score'])) {
    $sub_id = intval($_POST['sub_id']); 
    $score_val = ($_POST['score'] === '') ? "NULL" : intval($_POST['score']);

    $redir_class = isset($_GET['class']) ? $_GET['class'] : '';
    $redir_room = isset($_GET['room']) ? $_GET['room'] : '';
    $redir_tab = isset($_GET['tab']) ? $_GET['tab'] : 'grading';

    // เช็คก่อนว่าเป็นงานกลุ่มหรือไม่
    $grp_res = $conn->query("SELECT group_id FROM student_submissions WHERE id='$sub_id'");
    if ($grp_res && $grp_row = $grp_res->fetch_assoc()) {
        if (!empty($grp_row['group_id'])) {
            // กระจายคะแนนให้ทุกคนในกลุ่ม
            $g_id = $conn->real_escape_string($grp_row['group_id']);
            $sql_update = "UPDATE student_submissions SET score = $score_val WHERE group_id = '$g_id'";
        } else {
            // ให้คะแนนงานเดี่ยว
            $sql_update = "UPDATE student_submissions SET score = $score_val WHERE id = '$sub_id'";
        }
        $conn->query($sql_update);
    }

    echo "<script>window.location='admin_grades.php?tab=$redir_tab&class=$redir_class&room=$redir_room';</script>";
    exit();
}

// --- 🌟 2. ระบบลบงานของนักเรียน (ใหม่) 🌟 ---
if (isset($_POST['delete_submission'])) {
    $sub_id = intval($_POST['sub_id']); 
    $redir_class = isset($_GET['class']) ? $_GET['class'] : '';
    $redir_room = isset($_GET['room']) ? $_GET['room'] : '';
    $redir_tab = isset($_GET['tab']) ? $_GET['tab'] : 'grading';

    // ดึงข้อมูลไฟล์และ group_id ก่อนลบ
    $res = $conn->query("SELECT file_path, group_id FROM student_submissions WHERE id='$sub_id'");
    if ($res && $row = $res->fetch_assoc()) {
        $file_path = $row['file_path'];
        $g_id = $row['group_id'];

        // ลบไฟล์ออกจากโฟลเดอร์ในเซิร์ฟเวอร์ (ถ้ามี)
        if (!empty($file_path) && $file_path != 'NULL' && file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลออกจากฐานข้อมูล (ถ้าเป็นงานกลุ่ม ลบทั้งกลุ่ม)
        if (!empty($g_id)) {
            $g_id = $conn->real_escape_string($g_id);
            $conn->query("DELETE FROM student_submissions WHERE group_id = '$g_id'");
        } else {
            $conn->query("DELETE FROM student_submissions WHERE id = '$sub_id'");
        }
    }

    echo "<script>alert('ลบชิ้นงานสำเร็จ! นักเรียนสามารถส่งงานใหม่ได้แล้วครับ'); window.location='admin_grades.php?tab=$redir_tab&class=$redir_class&room=$redir_room';</script>";
    exit();
}

$search_class = isset($_GET['class']) ? $_GET['class'] : ''; 
$search_room = isset($_GET['room']) ? $_GET['room'] : '';      
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'grading'; 

$is_room_selected = ($search_class != '' && $search_room != '');
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบคะแนนและตัดเกรด - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #fbfcfe; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <span class="navbar-brand mb-0 h1 fw-bold"><i class="bi bi-calculator"></i> ระบบคะแนนและตัดเกรดครูริว</span>
            <a href="admin_panel.php" class="btn btn-outline-light btn-sm rounded-pill fw-bold"><i class="bi bi-arrow-left-circle"></i> กลับหน้าหลักแอดมิน</a>
        </div>
    </nav>

    <div class="container my-4">
        
        <div class="card mb-4 border-0 shadow-sm border-top border-primary border-4">
            <div class="card-body bg-white py-4">
                <form method="GET" class="row g-3 align-items-end justify-content-center">
                    <input type="hidden" name="tab" value="<?php echo htmlspecialchars($current_tab); ?>">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">ระดับชั้น</label>
                        <select name="class" class="form-select bg-light" required>
                            <option value="">-- เลือกระดับชั้น --</option>
                            <?php 
                            $classes = ["1", "2", "3", "4", "5", "6"];
                            foreach($classes as $c) {
                                $sel = ($search_class == $c) ? 'selected' : '';
                                echo "<option value='$c' $sel>ม.$c</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-primary">ห้อง</label>
                        <select name="room" class="form-select bg-light" required>
                            <option value="">-- เลือกห้อง --</option>
                            <?php for($i=1; $i<=15; $i++){ 
                                $sel = ($search_room == $i) ? 'selected' : '';
                                echo "<option value='$i' $sel>$i</option>"; 
                            } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm"><i class="bi bi-search"></i> ค้นหา</button>
                    </div>
                </form>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4" id="gradeTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_tab == 'grading' ? 'active fw-bold text-primary border-bottom-0' : 'text-secondary'; ?>" href="admin_grades.php?tab=grading&class=<?php echo urlencode($search_class); ?>&room=<?php echo $search_room; ?>">
                    <i class="bi bi-file-earmark-check"></i> ตรวจให้คะแนนชิ้นงาน
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_tab == 'summary' ? 'active fw-bold text-success border-bottom-0' : 'text-secondary'; ?>" href="admin_grades.php?tab=summary&class=<?php echo urlencode($search_class); ?>&room=<?php echo $search_room; ?>">
                    <i class="bi bi-award"></i> ตารางสรุปคะแนนและตัดเกรด 
                </a>
            </li>
        </ul>

        <div class="tab-content">
            
            <?php if ($current_tab == 'grading'): ?>
                <?php if ($is_room_selected): ?>
                    
                    <div class="card shadow-sm border-0 mb-5 border-top border-4 border-danger">
                        <div class="card-header bg-white text-danger fw-bold py-3">
                            <i class="bi bi-hourglass-split"></i> ชิ้นงานที่นักเรียนส่ง (รอดำเนินการ)
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 bg-white">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80" class="text-center">เลขที่</th>
                                        <th width="200">ชื่อผู้ส่ง</th>
                                        <th>คำสั่งงาน</th>
                                        <th>ข้อความจากนักเรียน</th>
                                        <th class="text-center" width="120">ไฟล์แนบ</th>
                                        <th class="text-center" width="180">ระบุคะแนน / จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $sql_pending = "
                                        SELECT sub.id as sub_id, sub.file_path, sub.comment, sub.submit_type, sub.score, 
                                               a.title as subject_name, 
                                               st.student_number, st.fullname 
                                        FROM student_submissions sub
                                        JOIN assignments a ON sub.assignment_id = a.id
                                        JOIN students st ON sub.student_id = st.student_id
                                        WHERE st.class_level = '$search_class' 
                                        AND st.room_number = '$search_room' 
                                        AND sub.score IS NULL
                                        ORDER BY st.student_number ASC
                                    ";
                                    $query_pending = $conn->query($sql_pending);

                                    if ($query_pending && $query_pending->num_rows > 0) {
                                        while($work = $query_pending->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td class="text-center fw-bold text-secondary fs-5"><?php echo $work['student_number']; ?></td>
                                        <td>
                                            <div class="fw-bold text-primary"><?php echo htmlspecialchars($work['fullname']); ?></div>
                                            <?php if($work['submit_type'] == 'group'): ?>
                                                <span class="badge bg-warning text-dark small"><i class="bi bi-people-fill"></i> งานกลุ่ม</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><div class="text-dark fw-bold"><?php echo htmlspecialchars($work['subject_name']); ?></div></td>
                                        <td><small class="text-muted"><?php echo htmlspecialchars($work['comment'] ?? '-'); ?></small></td>
                                        <td class="text-center">
                                            <?php if(!empty($work['file_path']) && $work['file_path'] != 'NULL'): ?>
                                                <a href="<?php echo htmlspecialchars($work['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-info rounded-pill">
                                                    <i class="bi bi-eye"></i> ดูงาน
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center bg-light">
                                            <form method="POST" class="d-flex align-items-center justify-content-center gap-1 m-0">
                                                <input type="hidden" name="sub_id" value="<?php echo $work['sub_id']; ?>">
                                                <input type="number" name="score" class="form-control form-control-sm text-center fw-bold border-danger" style="width: 70px;" value="" min="0" placeholder="0">
                                                <button type="submit" name="submit_score" class="btn btn-sm btn-success" title="บันทึกคะแนน">
                                                    <i class="bi bi-floppy"></i>
                                                </button>
                                                <button type="submit" name="delete_submission" class="btn btn-sm btn-danger" title="ลบงานนี้ทิ้ง" onclick="return confirm('คุณต้องการลบชิ้นงานนี้ใช่หรือไม่? \n(หากเป็นงานกลุ่ม ระบบจะลบงานของทุกคนในกลุ่มนี้ด้วย)');">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center py-5 text-muted'><i class='bi bi-emoji-sunglasses fs-2 d-block mb-2'></i> ไม่มีชิ้นงานค้างตรวจแล้ว เยี่ยมมากครับ!</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 border-top border-4 border-success">
                        <div class="card-header bg-white text-success fw-bold py-3">
                            <i class="bi bi-check-circle-fill"></i> ชิ้นงานที่ให้คะแนนแล้ว (แก้ไขได้)
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 bg-white opacity-75">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80" class="text-center">เลขที่</th>
                                        <th width="200">ชื่อผู้ส่ง</th>
                                        <th>คำสั่งงาน</th>
                                        <th class="text-center" width="120">ไฟล์แนบ</th>
                                        <th class="text-center" width="180">แก้ไขคะแนน / ลบ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $sql_graded = "
                                        SELECT sub.id as sub_id, sub.file_path, sub.submit_type, sub.score, 
                                               a.title as subject_name, 
                                               st.student_number, st.fullname 
                                        FROM student_submissions sub
                                        JOIN assignments a ON sub.assignment_id = a.id
                                        JOIN students st ON sub.student_id = st.student_id
                                        WHERE st.class_level = '$search_class' 
                                        AND st.room_number = '$search_room' 
                                        AND sub.score IS NOT NULL
                                        ORDER BY st.student_number ASC
                                    ";
                                    $query_graded = $conn->query($sql_graded);

                                    if ($query_graded && $query_graded->num_rows > 0) {
                                        while($work = $query_graded->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td class="text-center fw-bold text-secondary"><?php echo $work['student_number']; ?></td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($work['fullname']); ?></div>
                                            <?php if($work['submit_type'] == 'group'): ?>
                                                <span class="badge bg-warning text-dark small">งานกลุ่ม</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><div class="text-dark fw-bold"><?php echo htmlspecialchars($work['subject_name']); ?></div></td>
                                        <td class="text-center">
                                            <?php if(!empty($work['file_path']) && $work['file_path'] != 'NULL'): ?>
                                                <a href="<?php echo htmlspecialchars($work['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill">
                                                    <i class="bi bi-eye"></i> ดูงาน
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <form method="POST" class="d-flex align-items-center justify-content-center gap-1 m-0">
                                                <input type="hidden" name="sub_id" value="<?php echo $work['sub_id']; ?>">
                                                <input type="number" name="score" class="form-control form-control-sm text-center fw-bold text-success border-success" style="width: 70px;" value="<?php echo $work['score']; ?>" min="0">
                                                <button type="submit" name="submit_score" class="btn btn-sm btn-outline-success" title="อัปเดตคะแนน"><i class="bi bi-arrow-repeat"></i></button>
                                                <button type="submit" name="delete_submission" class="btn btn-sm btn-outline-danger" title="ลบงานนี้ทิ้ง" onclick="return confirm('คุณต้องการลบชิ้นงานนี้ใช่หรือไม่? \n(หากเป็นงานกลุ่ม ระบบจะลบงานของทุกคนในกลุ่มนี้ด้วย)');">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center py-4 text-muted'>ยังไม่มีชิ้นงานที่ตรวจแล้ว</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center py-5 text-muted">
                            <h5 class='mb-0'><i class='bi bi-search fs-1 d-block mb-3 text-primary opacity-50'></i> กรุณาเลือกระดับชั้นและห้องเรียนด้านบนเพื่อเริ่มตรวจงานครับ</h5>
                        </div>
                    </div>
                <?php endif; ?>

            <?php elseif ($current_tab == 'summary'): ?>
                <div class="card shadow-sm border-0 border-top border-success border-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-success"><i class="bi bi-clipboard-data"></i> ตารางประมวลผลเกรด 
                        <?php echo $is_room_selected ? "(ม.$search_class/$search_room)" : ""; ?></h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 bg-white">
                            <thead class="table-light text-center">
                                <tr>
                                    <th width="80">เลขที่</th>
                                    <th width="120">รหัสนักเรียน</th>
                                    <th class="text-start">ชื่อ-นามสกุล</th>
                                    <th width="150">คะแนนรวมทั้งหมด</th>
                                    <th width="150">เกรดที่ได้</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($is_room_selected) {
                                    $query_std = $conn->query("SELECT student_id, fullname, student_number FROM students WHERE class_level='$search_class' AND room_number='$search_room' ORDER BY student_number ASC");
                                    
                                    if ($query_std && $query_std->num_rows > 0) {
                                        while($std = $query_std->fetch_assoc()) {
                                            $std_id = $std['student_id'];
                                            
                                            $score_query = $conn->query("SELECT SUM(score) as total FROM student_submissions WHERE student_id = '$std_id'");
                                            $score_data = $score_query->fetch_assoc();
                                            $total_score = isset($score_data['total']) ? intval($score_data['total']) : 0;

                                            if ($total_score >= 80) { $grade = "4.0"; $bg_grade = "bg-success text-white"; }
                                            elseif ($total_score >= 75) { $grade = "3.5"; $bg_grade = "bg-success text-white bg-opacity-75"; }
                                            elseif ($total_score >= 70) { $grade = "3.0"; $bg_grade = "bg-info text-dark"; }
                                            elseif ($total_score >= 65) { $grade = "2.5"; $bg_grade = "bg-info text-dark bg-opacity-75"; }
                                            elseif ($total_score >= 60) { $grade = "2.0"; $bg_grade = "bg-warning text-dark"; }
                                            elseif ($total_score >= 55) { $grade = "1.5"; $bg_grade = "bg-warning text-dark bg-opacity-75"; }
                                            elseif ($total_score >= 50) { $grade = "1.0"; $bg_grade = "bg-danger text-white bg-opacity-50"; }
                                            else { $grade = "0"; $bg_grade = "bg-danger text-white"; }
                                ?>
                                <tr class="text-center">
                                    <td class="fw-bold fs-5"><?php echo htmlspecialchars($std['student_number']); ?></td>
                                    <td class="text-muted"><?php echo htmlspecialchars($std_id); ?></td>
                                    <td class="text-start fw-bold text-primary"><?php echo htmlspecialchars($std['fullname']); ?></td>
                                    <td class="fw-bold fs-4 text-dark"><?php echo $total_score; ?></td>
                                    <td>
                                        <span class="badge <?php echo $bg_grade; ?> px-4 py-2 fs-6 rounded-pill w-100 shadow-sm">
                                            เกรด <?php echo $grade; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php 
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center py-5 text-muted'>ไม่พบรายชื่อนักเรียนในห้องนี้</td></tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center py-5 text-muted'>
                                            <i class='bi bi-search fs-1 d-block mb-3 text-success opacity-50'></i> กรุณาเลือกระดับชั้นและห้องเรียนก่อนประมวลผลเกรดครับ
                                          </td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>