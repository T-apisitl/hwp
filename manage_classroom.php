<?php
session_start();
include 'connect.php';
if (!isset($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit(); }

// ดึงรายชื่อห้องและจำนวนนักเรียน
$rooms = $conn->query("
    SELECT class_level, room_number, COUNT(*) as count 
    FROM students 
    GROUP BY class_level, room_number 
    ORDER BY CAST(class_level AS UNSIGNED), CAST(room_number AS UNSIGNED)
");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการห้องเรียน - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Sarabun', sans-serif; }
        .room-card { border-radius: 12px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: 0.2s; cursor: pointer; }
        .room-card:hover { transform: translateY(-4px); box-shadow: 0 6px 16px rgba(255,193,7,0.25); border-color: #ffc107 !important; }
    </style>
</head>
<body>
    <nav class="navbar py-3 shadow-sm mb-4" style="background:#212529; border-bottom:3px solid #ffc107;">
        <div class="container d-flex justify-content-between">
            <span class="text-white fw-bold"><i class="bi bi-door-open-fill text-warning me-2"></i>จัดการห้องเรียน</span>
            <a href="admin_panel.php" class="btn btn-outline-light btn-sm rounded-pill">← แผงควบคุม</a>
        </div>
    </nav>
    <div class="container pb-5">
        <div class="row g-3">
        <?php while($r = $rooms->fetch_assoc()): ?>
        <div class="col-6 col-md-3">
            <a href="admin_panel.php?class=<?= $r['class_level'] ?>&room=<?= $r['room_number'] ?>" class="text-decoration-none">
            <div class="room-card card p-3 border text-center">
                <div style="font-size:36px">🏫</div>
                <div class="fw-bold fs-5 mt-2">ม.<?= $r['class_level'] ?>/<?= $r['room_number'] ?></div>
                <div class="text-muted small"><?= $r['count'] ?> คน</div>
                <div class="d-flex gap-1 mt-2 justify-content-center">
                    <a href="chat.php?class=<?= $r['class_level'] ?>&room=<?= $r['room_number'] ?>" 
                       onclick="event.stopPropagation()"
                       class="btn btn-sm btn-outline-info rounded-pill">แชท</a>
                    <a href="admin_grades.php?class=<?= $r['class_level'] ?>&room=<?= $r['room_number'] ?>" 
                       onclick="event.stopPropagation()"
                       class="btn btn-sm btn-warning rounded-pill">คะแนน</a>
                </div>
            </div>
            </a>
        </div>
        <?php endwhile; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
