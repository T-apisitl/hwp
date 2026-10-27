<?php
// เอาส่วน session_start และการ check login ออกแล้วครับ
// แต่ยังคงเรียกไฟล์เชื่อมต่อฐานข้อมูลไว้หากในอนาคตต้องการดึงชื่อบทเรียนจาก DB
include 'connect.php'; 
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บทเรียน - ห้องเรียนครูริว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .lesson-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            cursor: pointer;
        }
        .lesson-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .lesson-img {
            width: 100%;
            height: auto;
            border-radius: 15px;
        }
    </style>
</head>
<body class="bg-light">

    <?php include 'nav.php'; ?>

    <div class="container my-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">เลือกหน่วยการเรียนรู้</h2>
            <p class="text-muted">เลือกบทเรียนที่ต้องการเข้าศึกษาได้เลยครับ</p>
        </div>

        <div class="row g-4">
            <div class="col-6 col-md-3">
                <a href="unit1.php" class="text-decoration-none">
                    <div class="card lesson-card bg-transparent">
                        <img src="images/unit1_btn.png" class="lesson-img" alt="หน่วยการเรียนรู้ที่ 1">
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-3">
                <a href="unit2.php" class="text-decoration-none">
                    <div class="card lesson-card bg-transparent">
                        <img src="images/unit2_btn.png" class="lesson-img" alt="หน่วยการเรียนรู้ที่ 2">
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-3">
                <a href="unit3.php" class="text-decoration-none">
                    <div class="card lesson-card bg-transparent">
                        <img src="images/unit3_btn.png" class="lesson-img" alt="หน่วยการเรียนรู้ที่ 3">
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-3">
                <a href="unit4.php" class="text-decoration-none">
                    <div class="card lesson-card bg-transparent">
                        <img src="images/unit4_btn.png" class="lesson-img" alt="หน่วยการเรียนรู้ที่ 4">
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>