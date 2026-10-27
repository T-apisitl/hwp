<?php
session_start();
// สามารถเข้าถึงหน้านี้ได้โดยไม่ต้องล็อกอิน

$fullname = $_SESSION['student_name'] ?? 'นักเรียน';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขผลการเรียน (0, ร, มส) - ห้องเรียนครูริว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #fbfcfe; font-family: 'Sarabun', sans-serif; color: #495057; }
        
        .hero-section { 
            background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%); 
            border-radius: 0 0 30px 30px; 
            padding: 3rem 1rem 4rem 1rem; 
            color: #fff; 
            box-shadow: 0 10px 30px rgba(253, 126, 20, 0.2); 
        }

        .step-card {
            background: #fff;
            border-radius: 20px;
            border: none;
            box-shadow: 0 5px 25px rgba(0,0,0,0.05);
            padding: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
        }

        .step-number {
            position: absolute;
            top: -20px;
            left: 30px;
            background: #dc3545;
            color: white;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.5rem;
            font-weight: 800;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
            border: 4px solid #fff;
        }

        .highlight-box {
            background-color: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 15px 20px;
            border-radius: 0 10px 10px 0;
            margin-top: 15px;
        }

        .btn-action {
            font-weight: 700;
            border-radius: 50px;
            padding: 10px 30px;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .task-list { list-style: none; padding-left: 0; }
        .task-list li {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            margin-bottom: 10px;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .task-list li i { color: #0d6efd; font-size: 1.2rem; }
    </style>
</head>
<body>

    <!-- ดึง Navbar มาใช้งาน -->
    <?php if (file_exists('nav.php')) { include 'nav.php'; } ?>

    <!-- ส่วนหัวเพจ -->
    <div class="hero-section mb-5 position-relative text-center">
        <div class="container mt-4">
            <div class="d-inline-block bg-white text-danger px-4 py-2 rounded-pill fw-bold mb-3 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill"></i> ประชาสัมพันธ์ด่วน
            </div>
            <h1 class="display-5 fw-bold mb-2 text-white">ศูนย์แก้ไขผลการเรียน (0, ร, มส)</h1>
            <p class="fs-5 text-white opacity-75">
                สำหรับนักเรียนที่มีผลการเรียนไม่ผ่านเกณฑ์ ให้อ่านและทำตามขั้นตอนด้านล่างนี้อย่างละเอียด
            </p>
        </div>
    </div>

    <!-- ส่วนเนื้อหาหลัก -->
    <div class="container" style="max-width: 900px;">
        
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-5 p-4 d-flex align-items-center gap-3">
            <i class="bi bi-megaphone-fill fs-1"></i>
            <div>
                <h5 class="fw-bold mb-1">สวัสดี <?php echo htmlspecialchars($fullname); ?></h5>
                <p class="mb-0">กรุณาทำกิจกรรม <strong>AI & DIGITAL Safety 2026</strong> ให้ครบถ้วนเพื่อรับเกียรติบัตร และนำมาอัปโหลดเป็นหลักฐานในการขอแก้ผลการเรียน</p>
                <!-- 🌟 เพิ่มข้อความแจ้งเตือนเวลาตรงนี้ 🌟 -->
                <div class="mt-2">
                    <span class="badge bg-danger fs-6 py-2 px-3 shadow-sm">
                        <i class="bi bi-alarm-fill me-1"></i> ด่วน! กรุณาดำเนินการให้เสร็จสิ้น ภายในเวลา 13:00 น. วันนี้
                    </span>
                </div>
            </div>
        </div>

        <!-- ขั้นตอนที่ 1 -->
        <div class="step-card mt-4">
            <div class="step-number bg-primary">1</div>
            <h4 class="fw-bold text-primary mb-3 ms-2 mt-2"><i class="bi bi-person-vcard"></i> สมัครเข้าร่วมกิจกรรมบน Codekit</h4>
            <p class="text-muted ms-2 fs-6">
                ให้นักเรียนกดเข้าไปที่ลิงก์ด้านล่าง เพื่อเข้าสู่หน้าระบบการสมัคร <br>
                <a href="https://codekit.co/p/1309/582247" target="_blank" class="btn btn-primary btn-action mt-3 mb-3 shadow-sm">
                    <i class="bi bi-link-45deg"></i> เข้าสู่หน้าต่างการสมัคร
                </a>
            </p>
            
            <div class="highlight-box bg-light border-primary mt-2">
                <h6 class="fw-bold text-dark"><i class="bi bi-pencil-square"></i> วิธีการกรอกข้อมูล (มี 5 ช่อง):</h6>
                <ul class="mb-0 mt-2 text-muted" style="line-height: 1.8;">
                    <li><strong>ช่องที่ 1:</strong> กรอก <span class="text-primary fw-bold">อีเมลของโรงเรียน</span> (ตัวอย่าง: @hwp.ac.th)</li>
                    <li><strong>ช่องที่ 2 และ 3 (รหัสผ่าน):</strong> ให้พิมพ์เลขศูนย์ 8 ตัว <span class="badge bg-danger fs-6">00000000</span> ลงไปทั้งสองช่อง</li>
                    <li><strong>ช่องที่ 4:</strong> พิมพ์ <strong>ชื่อจริง</strong> (ภาษาไทย ไม่ต้องใส่คำนำหน้า)</li>
                    <li><strong>ช่องที่ 5:</strong> พิมพ์ <strong>นามสกุล</strong> (ภาษาไทย)</li>
                </ul>
                <div class="mt-3 text-success fw-bold">
                    <i class="bi bi-check-circle-fill"></i> กรอกครบแล้วให้กดปุ่ม <strong>Submit</strong> หรือ <strong>เสร็จสิ้น</strong>
                </div>
            </div>
        </div>

        <!-- ขั้นตอนที่ 2 -->
        <div class="step-card">
            <div class="step-number bg-warning text-dark border-white">2</div>
            <h4 class="fw-bold text-warning-emphasis mb-3 ms-2 mt-2"><i class="bi bi-controller"></i> ทำกิจกรรม AI & DIGITAL Safety 2026</h4>
            <p class="text-muted ms-2">หลังจากสมัครเสร็จแล้ว ให้นักเรียนเข้าทำภารกิจในระบบให้ครบทั้ง 5 ฐานกิจกรรม ดังนี้:</p>
            
            <ul class="task-list ms-2 mt-3">
                <li><i class="bi bi-1-circle-fill"></i> ด่านที่ 1 : AI CEO</li>
                <li><i class="bi bi-2-circle-fill"></i> ด่านที่ 2 : Neo Bangkok 2045</li>
                <li><i class="bi bi-3-circle-fill"></i> ด่านที่ 3 : Scammer Catch</li>
                <li><i class="bi bi-4-circle-fill"></i> ด่านที่ 4 : Prompt Master</li>
                <li><i class="bi bi-5-circle-fill text-danger"></i> ด่านที่ 5 : วิทยาการคำนวณ Challenge (ด่านบอส)</li>
            </ul>
        </div>

        <!-- ขั้นตอนที่ 3 -->
        <div class="step-card">
            <div class="step-number bg-success">3</div>
            <h4 class="fw-bold text-success mb-3 ms-2 mt-2"><i class="bi bi-cloud-arrow-up-fill"></i> อัปโหลดเกียรติบัตรส่งครู</h4>
            <p class="text-muted ms-2">เมื่อทำภารกิจครบทั้ง 5 ฐานแล้ว ระบบจะให้ <strong>"ดาวน์โหลดเกียรติบัตร"</strong> ให้นักเรียนนำไฟล์เกียรติบัตรนั้นมาอัปโหลดลงใน Google Drive ของรายวิชา</p>
            
            <div class="highlight-box bg-danger bg-opacity-10 border-danger mb-4">
                <h6 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-octagon-fill"></i> ข้อควรระวังก่อนอัปโหลด!</h6>
                <p class="mb-0 text-dark">นักเรียนต้อง <strong>"สร้างโฟลเดอร์เป็นชื่อของตัวเอง"</strong> ภายในไดร์ฟก่อน จากนั้นจึงค่อยอัปโหลดไฟล์เกียรติบัตรเข้าไปไว้ในโฟลเดอร์ของตนเองเท่านั้น (เพื่อป้องกันไฟล์ปะปนกับเพื่อน)</p>
            </div>

            <div class="text-center mt-4">
                <a href="https://drive.google.com/drive/folders/1p2Pu8CbUKOiG2epBgDmjolzpjWp57Fzq?usp=drive_link" target="_blank" class="btn btn-success btn-action shadow-sm w-100 py-3">
                    <i class="bi bi-google-play"></i> คลิกที่นี่เพื่อไปหน้า Google Drive สำหรับส่งงาน
                </a>
            </div>
        </div>

        <div class="text-center mb-5 pb-4">
            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                <i class="bi bi-house-door"></i> กลับหน้าแรก
            </a>
        </div>

    </div>

    <!-- Footer -->
    <footer class="text-center py-4 text-muted small mt-auto border-top bg-white">
        © <?php echo date('Y'); ?> Apisit Lun-Ubon - Horwang Pathumthani School | ระบบจัดการผลการเรียน
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>