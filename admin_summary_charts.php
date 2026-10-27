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

// รับค่าค้นหา
$filter_term = isset($_GET['filter_term']) ? $conn->real_escape_string($_GET['filter_term']) : $auto_term;
$filter_year = isset($_GET['filter_year']) ? $conn->real_escape_string($_GET['filter_year']) : $auto_year;
$search_class = isset($_GET['class']) ? $conn->real_escape_string($_GET['class']) : ''; 
$search_room = isset($_GET['room']) ? $conn->real_escape_string($_GET['room']) : '';      
$is_room_selected = ($search_class != '' && $search_room != '');

// ดึงระดับชั้นและห้องเรียนที่มีนักเรียนอยู่จริงในเทอม/ปีที่เลือก
$active_classes = [];
$class_query = $conn->query("SELECT DISTINCT class_level FROM students WHERE class_level IS NOT NULL AND class_level != '' AND semester = '$filter_term' AND academic_year = '$filter_year' ORDER BY CAST(class_level AS UNSIGNED)");
if ($class_query) { while ($c = $class_query->fetch_assoc()) { $active_classes[] = $c['class_level']; } }

$active_rooms = [];
$room_query = $conn->query("SELECT DISTINCT room_number FROM students WHERE room_number IS NOT NULL AND room_number != '' AND semester = '$filter_term' AND academic_year = '$filter_year' ORDER BY CAST(room_number AS UNSIGNED)");
if ($room_query) { while ($r = $room_query->fetch_assoc()) { $active_rooms[] = $r['room_number']; } }

$chart_data = [];
$total_students = 0;

if ($is_room_selected) {
    // 1. นับจำนวนนักเรียนทั้งหมดในห้องนี้ (เฉพาะเทอม/ปีที่เลือก)
    $count_stu = $conn->query("SELECT COUNT(*) as c FROM students WHERE class_level = '$search_class' AND room_number = '$search_room' AND semester = '$filter_term' AND academic_year = '$filter_year'");
    if ($count_stu) {
        $total_students = intval($count_stu->fetch_assoc()['c']);
    }

    if ($total_students > 0) {
        // 2. ดึงชิ้นงานของระดับชั้นและเทอมนี้
        $sql_assign = "SELECT id, title FROM assignments 
                       WHERE (class_level = '$search_class' OR class_level = '' OR class_level IS NULL) 
                       AND semester = '$filter_term' AND academic_year = '$filter_year' 
                       ORDER BY id ASC";
        $res_assign = $conn->query($sql_assign);
        
        if ($res_assign && $res_assign->num_rows > 0) {
            while ($assign = $res_assign->fetch_assoc()) {
                $a_id = $assign['id'];
                
                // 3. นับจำนวนคนที่ส่งงานแล้วในห้องและเทอมนี้
                $sql_sub = "SELECT COUNT(DISTINCT sub.student_id) as c 
                            FROM student_submissions sub 
                            JOIN students s ON sub.student_id = s.student_id 
                            WHERE sub.assignment_id = '$a_id' 
                              AND s.class_level = '$search_class' 
                              AND s.room_number = '$search_room'
                              AND s.semester = '$filter_term'
                              AND s.academic_year = '$filter_year'";
                
                $res_sub = $conn->query($sql_sub);
                $submitted = $res_sub ? intval($res_sub->fetch_assoc()['c']) : 0;
                
                $missing = max(0, $total_students - $submitted);
                $percent = round(($submitted / $total_students) * 100, 1);
                
                $chart_data[] = [
                    'id' => $a_id,
                    'title' => $assign['title'],
                    'submitted' => $submitted,
                    'missing' => $missing,
                    'total' => $total_students,
                    'percent' => $percent
                ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สรุปเปอร์เซ็นต์การส่งงาน - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #fbfcfe; }
        .navbar-admin { background: #212529; border-bottom: 3px solid #0dcaf0; }
        .chart-card { border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.3s; }
        .chart-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .percent-text { font-size: 2rem; font-weight: 800; line-height: 1; }
        .chart-container { position: relative; height: 200px; width: 100%; margin: 0 auto; display: flex; justify-content: center; }
    </style>
</head>
<body>

    <nav class="navbar navbar-admin py-3 mb-4 shadow-sm">
        <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
            <span class="text-white fw-bold fs-5">
                <i class="bi bi-pie-chart-fill text-info me-2"></i> สรุปเปอร์เซ็นต์การส่งงาน (รายห้อง)
            </span>
            <a href="admin_panel.php" class="btn btn-outline-light btn-sm rounded-pill fw-bold">
                <i class="bi bi-arrow-left-circle"></i> กลับหน้าแผงควบคุม
            </a>
        </div>
    </nav>

    <div class="container-fluid px-4 my-4">
        
        <!-- 🌟 กล่องค้นหา / เลือกเทอม ปี ชั้น ห้อง 🌟 -->
        <div class="card mb-5 border-0 shadow-sm border-top border-info border-4">
            <div class="card-body bg-white py-4">
                <form method="GET" class="row g-3 align-items-end justify-content-center">
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark small">ภาคเรียนที่</label>
                        <select name="filter_term" class="form-select bg-light border-secondary fw-bold">
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
                            <?php foreach($active_classes as $c) {
                                $sel = ($search_class == $c) ? 'selected' : '';
                                echo "<option value='$c' $sel>ม.$c</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-primary">ห้อง <span class="text-danger">*</span></label>
                        <select name="room" class="form-select bg-light border-primary" required>
                            <option value="">-- เลือกห้อง --</option>
                            <?php foreach($active_rooms as $r) { 
                                $sel = ($search_room == $r) ? 'selected' : '';
                                echo "<option value='$r' $sel>$r</option>"; 
                            } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-info text-dark w-100 fw-bold shadow-sm">
                            <i class="bi bi-graph-up"></i> วิเคราะห์ข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($is_room_selected): ?>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-dark">
                    <i class="bi bi-bar-chart-steps text-info"></i> รายงานการส่งงานชั้น ม.<?= htmlspecialchars($search_class.'/'.$search_room); ?>
                    <span class="badge bg-primary fs-6 ms-2">เทอม <?= htmlspecialchars($filter_term.'/'.$filter_year); ?></span>
                </h4>
                <span class="badge bg-secondary fs-6 rounded-pill px-3 shadow-sm">
                    <i class="bi bi-people-fill"></i> จำนวนนักเรียนทั้งหมด: <?= $total_students; ?> คน
                </span>
            </div>

            <?php if ($total_students > 0 && count($chart_data) > 0): ?>
                <div class="row g-4">
                    <?php foreach($chart_data as $data): 
                        $text_color = "text-danger";
                        if ($data['percent'] >= 80) $text_color = "text-success";
                        elseif ($data['percent'] >= 50) $text_color = "text-warning";
                    ?>
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="card chart-card h-100">
                                <div class="card-header bg-white border-0 pt-3 pb-0 text-center">
                                    <h6 class="fw-bold text-dark text-truncate" title="<?= htmlspecialchars($data['title']); ?>">
                                        <?= htmlspecialchars($data['title']); ?>
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="chart-container">
                                        <canvas id="chart_<?= $data['id']; ?>"></canvas>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <div class="percent-text <?= $text_color; ?>"><?= $data['percent']; ?>%</div>
                                        <small class="text-muted fw-bold">อัตราการส่งงาน</small>
                                    </div>
                                </div>
                                <div class="card-footer bg-light border-0 rounded-bottom-4 py-2">
                                    <div class="row text-center small fw-bold">
                                        <div class="col-6 text-success border-end">
                                            <i class="bi bi-check-circle-fill"></i> ส่งแล้ว<br><?= $data['submitted']; ?> คน
                                        </div>
                                        <div class="col-6 text-danger">
                                            <i class="bi bi-x-circle-fill"></i> ค้างส่ง<br><?= $data['missing']; ?> คน
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <script>
                    <?php foreach($chart_data as $data): ?>
                    new Chart(document.getElementById('chart_<?= $data['id']; ?>'), {
                        type: 'doughnut',
                        data: {
                            labels: ['ส่งแล้ว', 'ยังไม่ส่ง'],
                            datasets: [{
                                data: [<?= $data['submitted']; ?>, <?= $data['missing']; ?>],
                                backgroundColor: ['#198754', '#dc3545'],
                                borderWidth: 2,
                                borderColor: '#ffffff',
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + context.raw + ' คน';
                                        }
                                    }
                                }
                            }
                        }
                    });
                    <?php endforeach; ?>
                </script>

            <?php elseif ($total_students == 0): ?>
                <div class="alert alert-warning text-center py-4 border-0 shadow-sm rounded-4">
                    <i class="bi bi-exclamation-triangle-fill fs-1 text-warning d-block mb-2"></i>
                    <h5 class="fw-bold">ไม่พบรายชื่อนักเรียนในเทอม <?= htmlspecialchars($filter_term.'/'.$filter_year); ?></h5>
                    <p class="mb-0 text-muted">กรุณาตรวจสอบว่าได้นำเข้ารายชื่อนักเรียนชั้น ม.<?= htmlspecialchars($search_class.'/'.$search_room); ?> ของเทอมนี้แล้วหรือยัง</p>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center py-4 border-0 shadow-sm rounded-4">
                    <i class="bi bi-inbox fs-1 text-info d-block mb-2"></i>
                    <h5 class="fw-bold">ยังไม่มีการสั่งงานในเทอม <?= htmlspecialchars($filter_term.'/'.$filter_year); ?></h5>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="card shadow-sm border-0 bg-transparent">
                <div class="card-body text-center py-5 text-muted">
                    <h5 class='mb-0'><i class='bi bi-pie-chart fs-1 d-block mb-3 text-info opacity-50'></i> กรุณาเลือกระดับชั้นและห้องเรียนเพื่อดูรายงานครับ</h5>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>