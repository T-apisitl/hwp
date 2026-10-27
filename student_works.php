<?php
session_start();
include 'connect.php';

// ระบบตัวกรองสำหรับเลือกดูรายห้อง
$search_class = isset($_GET['class']) ? $_GET['class'] : '';
$search_room = isset($_GET['room']) ? $_GET['room'] : '';

$sql = "SELECT * FROM assignments WHERE 1=1 ";
if ($search_class != '') { $sql .= " AND class_level = '$search_class' "; }
if ($search_room != '') { $sql .= " AND room_number = '$search_room' "; }
$sql .= " ORDER BY uploaded_at DESC"; // เรียงจากงานที่ส่งล่าสุดขึ้นก่อน
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผลงานนักเรียน - ห้องเรียนครูริว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* จัดการขอบตารางบนมือถือ */
        @media (max-width: 576px) {
            .table-responsive {
                border: 1px solid #dee2e6;
                border-radius: 0.5rem;
            }
            .table {
                min-width: 600px; /* บังคับให้ตารางกว้างพอที่จะไม่เบียดกันจนเละ (ใช้นิ้วปัดดูได้) */
            }
        }
    </style>
</head>
<body class="bg-light">

    <?php include 'nav.php'; ?>

    <div class="container my-4 my-md-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold fs-3 fs-md-2"><i class="bi bi-collection-fill text-warning"></i> คลังผลงานนักเรียน</h2>
            <p class="text-muted small">รวมชิ้นงานและโปรเจกต์ Smart Home ของนักเรียนที่ส่งเข้ามาระบบ</p>
        </div>

        <div class="card mb-4 border-0 shadow-sm rounded-3">
            <div class="card-body bg-white py-3">
                <form method="GET" class="row g-2 align-items-end justify-content-center">
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-secondary">ระดับชั้น</label>
                        <select name="class" class="form-select form-select-sm">
                            <option value="">-- ทั้งหมด --</option>
                            <option value="1" <?php if($search_class=='1') echo 'selected'; ?>>ม.1</option>
                            <option value="2" <?php if($search_class=='2') echo 'selected'; ?>>ม.2</option>
                            <option value="3" <?php if($search_class=='3') echo 'selected'; ?>>ม.3</option>
                            <option value="4" <?php if($search_class=='4') echo 'selected'; ?>>ม.4</option>
                            <option value="5" <?php if($search_class=='5') echo 'selected'; ?>>ม.5</option>
                            <option value="6" <?php if($search_class=='6') echo 'selected'; ?>>ม.6</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-secondary">ห้อง</label>
                        <select name="room" class="form-select form-select-sm">
                            <option value="">-- ทั้งหมด --</option>
                            <?php for($i=1; $i<=15; $i++){ 
                                $sel = ($search_room == $i) ? 'selected' : '';
                                echo "<option value='$i' $sel>ห้อง $i</option>"; 
                            } ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold"><i class="bi bi-search"></i> กรองข้อมูล</button>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="student_works.php" class="btn btn-outline-secondary btn-sm w-100">ล้างค่า</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 bg-white">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" width="100">ชั้น/ห้อง</th>
                            <th width="150">ผู้ส่ง/เลขที่</th>
                            <th width="120">ลักษณะงาน</th>
                            <th>หัวข้อ / ชิ้นงาน</th>
                            <th class="text-center" width="130">ไฟล์งาน</th>
                            <th class="text-center" width="120">เวลาที่ส่ง</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) { 
                        ?>
                        <tr>
                            <td class="text-center fw-bold text-secondary">
                                <?php echo $row['class_level']; ?>/<?php echo $row['room_number']; ?>
                            </td>
                            
                            <td>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 150px;"><?php echo $row['student_name']; ?></div>
                                <small class="text-muted">เลขที่ <?php echo $row['student_number']; ?></small>
                            </td>
                            
                            <td>
                                <?php if ($row['work_type'] == 'กลุ่ม'): ?>
                                    <span class="badge bg-info text-dark fw-bold"><i class="bi bi-people-fill"></i> งานกลุ่ม</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary fw-bold"><i class="bi bi-person-fill"></i> งานเดี่ยว</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <div class="fw-bold text-primary fs-6"><?php echo $row['subject_name']; ?></div>
                                <?php if ($row['work_type'] == 'กลุ่ม' && !empty($row['group_members'])): ?>
                                    <div class="p-2 mt-1 rounded bg-light border-start border-3 border-info" style="font-size: 0.85rem;">
                                        <strong class="text-secondary"><i class="bi bi-plus-circle"></i> กลุ่ม:</strong> 
                                        <span class="text-muted"><?php echo $row['group_members']; ?></span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center">
                                <a href="uploads/<?php echo $row['file_name']; ?>" class="btn btn-sm btn-outline-success rounded-pill fw-bold" target="_blank">
                                    <i class="bi bi-download"></i> โหลด
                                </a>
                            </td>
                            
                            <td class="text-center text-muted" style="font-size: 0.75rem;">
                                <?php echo date('d/m/y', strtotime($row['uploaded_at'])); ?><br>
                                <?php echo date('H:i', strtotime($row['uploaded_at'])); ?> น.
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-5 text-muted'><i class='bi bi-folder-x fs-1 d-block mb-2'></i> ยังไม่มีข้อมูลการส่งงานในระบบ</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 py-4 border-top">
        <p class="text-muted mb-0 small">© 2026 Apisit Lun-Ubon - Horwang Pathumthani School</p>
    </footer> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>