<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เนื้อหาบทเรียน - ห้องเรียนครูอภิสิทธิ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> 
    
    <style>
        /* ปรับแต่ง iframe วิดีโอให้เต็มจอสวยงาม */
        .video-container {
            position: relative;
            padding-bottom: 56.25%; /* อัตราส่วน 16:9 */
            height: 0;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .content-box {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
    </style>   
</head>
<body class="bg-light">

    <?php include 'nav.php'; ?>

    <div class="container my-5">
        
        <a href="lessons.php" class="btn btn-outline-secondary mb-3 rounded-pill">
            ⬅️ กลับหน้ารวมบทเรียน
        </a>

        <div class="row">
            <div class="col-lg-8">
                
                <h2 class="fw-bold mb-3">📚 การสร้าง Portfolio (ม.6)</h2>
                
                <!--<div class="video-container">
                    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>-->

                <div class="content-box">
                    <h4 class="fw-bold text-primary">รายละเอียดบทเรียน</h4>
                    <p>
                        ในบทเรียนนี้ นักเรียนจะได้เรียนรู้วิธีการรวบรวมผลงานและการจัดทำแฟ้มสะสมผลงาน (Portfolio) 
                        เพื่อใช้สำหรับการสมัครเข้าศึกษาต่อในระดับมหาวิทยาลัย โดยมีหัวข้อสำคัญดังนี้:
                    </p>
                    <ul>
                        <li>ความสำคัญของ Portfolio ในรอบ TCAS 1</li>
                        <li>การเลือกผลงานที่โดดเด่น</li>
                        <li>เทคนิคการจัดวาง Layout ให้สวยงาม</li>
                        <li>เครื่องมือที่ใช้ในการออกแบบ (Canva, Photoshop)</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="content-box">
                    <h5 class="fw-bold">📂 เอกสารประกอบการเรียน</h5>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-primary rounded-pill">
                            📄 ดาวน์โหลดสไลด์ (PDF)
                        </a>
                        <a href="#" class="btn btn-warning text-white rounded-pill">
                            📝 ใบงานที่ 1 (ส่งท้ายคาบ)
                        </a>
                    </div>
                    
                    <h5 class="fw-bold mt-4">📢 ประกาศจากครู</h5>
                    <div class="alert alert-info mt-2">
                        อย่าลืมส่งงานภายในวันศุกร์นี้นะครับ!
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>