<?php
session_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน่วยการเรียนรู้ที่ 2 - ห้องเรียนครูริว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Sarabun', sans-serif; 
            color: #333; 
        }
        
        /* โทนสีเหลืองตามต้นฉบับ */
        .hero-bg {
            background-color: #ffc928; /* สีเหลืองโทนเดียวกับภาพ */
            border-radius: 0 0 40px 40px;
            padding-bottom: 5rem;
            padding-top: 1rem;
        }

        /* แถบเมนูด้านบน */
        .navbar-custom .nav-link {
            color: #333 !important;
            font-weight: 600;
            margin: 0 5px;
        }
        .navbar-custom .nav-link:hover {
            color: #000 !important;
        }
        .navbar-brand-text {
            color: #333;
            font-weight: 700;
            margin-right: 15px;
        }
        .btn-login {
            background-color: #1a237e; /* สีน้ำเงินเข้ม */
            color: white;
            border-radius: 20px;
            padding: 6px 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-login:hover {
            background-color: #12185b;
            color: white;
        }

        /* โลโก้ไอคอนตรงกลาง */
        .hero-icon {
            background-color: #448aff;
            color: white;
            width: 45px;
            height: 45px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* กล่องเนื้อหาหลักสีขาว */
        .content-container {
            background: #ffffff;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            margin-top: -4rem; /* ดันขึ้นไปทับสีเหลือง */
            margin-bottom: 4rem;
        }

        /* สไตล์หัวข้อหลัก (มีเส้นสีเหลืองด้านหน้า) */
        .section-title {
            border-left: 5px solid #ffc928;
            padding-left: 15px;
            font-weight: 700;
            font-size: 1.4rem;
            margin-top: 2.5rem;
            margin-bottom: 1.5rem;
            color: #212529;
        }

        /* สไตล์การ์ดเนื้อหา */
        .info-card {
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            padding: 20px;
            height: 100%;
            background: #fff;
            transition: box-shadow 0.2s;
        }
        .info-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .info-card h6 {
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-card p {
            color: #555;
            font-size: 0.95rem;
            margin-bottom: 0;
            line-height: 1.6;
        }

        /* ตาราง */
        .table-custom {
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            overflow: hidden;
        }
        .table-custom th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #eaeaea;
        }
        .table-custom td {
            color: #555;
            vertical-align: middle;
        }
    </style>
</head>
<body>

    <div class="hero-bg">
        <!-- 🌟 เมนูด้านบน 🌟 -->
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container">
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">หน้าแรก</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">บทเรียน</a></li>
                        <li class="nav-item"><a class="nav-link" href="assignment.php">ส่งงาน</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">ผลงานนักเรียน</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">ประวัติครูผู้สอน</a></li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <span class="navbar-brand-text">ห้องเรียนครูริว</span>
                        <a href="login.php" class="btn-login"><i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบ</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- 🌟 ส่วนหัวข้อ (Hero Title) 🌟 -->
        <div class="container text-center mt-4">
            <div class="hero-icon">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <h4 class="fw-bold mb-1">หน่วยการเรียนรู้ที่ 2</h4>
            <h2 class="fw-bold mb-3">การเปลี่ยนแปลงของเทคโนโลยีสารสนเทศ</h2>
            <p class="mb-0 text-dark opacity-75">วิชาเทคโนโลยี (วิทยาการคำนวณ) · โรงเรียนหอวัง ปทุมธานี</p>
        </div>
    </div>

    <!-- 🌟 กล่องเนื้อหาหลักสีขาว 🌟 -->
    <div class="container">
        <div class="content-container">
            
            <p class="text-muted text-center mb-4">
                สรุปเนื้อหาสำคัญเกี่ยวกับวิวัฒนาการและบทบาทของเทคโนโลยีสารสนเทศในยุคดิจิทัล โดยครอบคลุมตั้งแต่การเปลี่ยนแปลงของระบบเว็บ ความก้าวหน้าของปัญญาประดิษฐ์ ไปจนถึงนวัตกรรมความจริงเสริม และการประมวลผลแบบคลาวด์[cite: 2]
            </p>

            <!-- 1. วิวัฒนาการเว็บ -->
            <div class="section-title">วิวัฒนาการของเทคโนโลยีเว็บและสารสนเทศ</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="info-card">
                        <h6 class="text-info"><i class="bi bi-display"></i> Web 1.0 (ยุคเริ่มต้น)</h6>
                        <p>เน้นการสื่อสารทางเดียว ผู้ใช้รับข้อมูลเพียงอย่างเดียวผ่านเว็บไซต์หรืออีเมล เป็นจุดเปลี่ยนจากโลกออฟไลน์สู่โลกออนไลน์[cite: 2]</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <h6 class="text-success"><i class="bi bi-chat-square-text"></i> Web 2.0 (ยุคโซเชียลมีเดีย)</h6>
                        <p>เริ่มประมาณปี ค.ศ. 2000 เน้นการสื่อสารสองทาง (2-Way Communication) ผู้ใช้สามารถสร้างเนื้อหาเองได้ (User-Generated Content)[cite: 2]</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <h6 class="text-primary"><i class="bi bi-globe"></i> Web 3.0 (ยุคข้อมูลขนาดใหญ่)</h6>
                        <p>เน้นการนำข้อมูลจำนวนมหาศาล (Big Data) มาบริหารจัดการและวิเคราะห์พฤติกรรมผู้บริโภค เพื่อสร้างประสบการณ์ที่ตรงความต้องการรายบุคคล[cite: 2]</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <h6 class="text-danger"><i class="bi bi-phone-vibrate"></i> Web 4.0 (ยุคปัจจุบัน)</h6>
                        <p>เป็นยุคที่ AI และ Internet of Things (IoT) เข้ามามีบทบาทสำคัญ อุปกรณ์ต่างๆ สามารถเชื่อมต่อและสื่อสารกันเองได้อย่างอัจฉริยะ[cite: 2]</p>
                    </div>
                </div>
            </div>

            <!-- 2. AI -->
            <div class="section-title">ปัญญาประดิษฐ์ (Artificial Intelligence: AI)</div>
            <p class="text-muted mb-3">ปัญญาประดิษฐ์คือศาสตร์ที่พยายามทำให้เครื่องจักรมีกระบวนการคิด การเรียนรู้ และการตัดสินใจคล้ายมนุษย์[cite: 2]</p>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="info-card bg-light border-0">
                        <h6 class="text-dark">1. ANI (Narrow)</h6>
                        <p class="small">เชี่ยวชาญเฉพาะด้าน เช่น ระบบแปลภาษา, Google Maps[cite: 2]</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card bg-light border-0">
                        <h6 class="text-dark">2. AGI (General)</h6>
                        <p class="small">มีความฉลาดเทียบเท่าสมองมนุษย์ วางแผนและแก้ปัญหาซับซ้อนได้[cite: 2]</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card bg-light border-0">
                        <h6 class="text-dark">3. ASI (Super)</h6>
                        <p class="small">มีความสามารถเหนือกว่ามนุษย์ในทุกด้าน ทั้งความคิดสร้างสรรค์และการตัดสินใจ[cite: 2]</p>
                    </div>
                </div>
            </div>

            <!-- 3 & 4. หุ่นยนต์และ AR -->
            <div class="row mt-3">
                <div class="col-lg-6">
                    <div class="section-title mt-4">เทคโนโลยีหุ่นยนต์ (Robotic)</div>
                    <div class="info-card">
                        <p>หุ่นยนต์ (Robot) มาจากภาษาเช็กว่า "Robota" หมายถึง การทำงานเหมือนทาส[cite: 2]</p>
                        <ul class="text-muted small mb-0 mt-2">
                            <li><b>Humanoid Robot:</b> รูปร่างคล้ายมนุษย์[cite: 2]</li>
                            <li><b>Actroid:</b> หุ่นยนต์ที่เลียนแบบมนุษย์อย่างสมจริง[cite: 2]</li>
                            <li><b>Cyborg:</b> การผสมผสานระหว่างมนุษย์และเทคโนโลยี[cite: 2]</li>
                            <li><b>Nanorobot:</b> หุ่นยนต์ขนาดจิ๋วระดับนาโนเมตร[cite: 2]</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="section-title mt-4">ความจริงเสริม (AR)</div>
                    <div class="info-card">
                        <p>การนำภาพจำลอง 2 มิติ หรือ 3 มิติ มาซ้อนทับบนโลกแห่งความเป็นจริงผ่านอุปกรณ์[cite: 2]</p>
                        <ul class="text-muted small mb-0 mt-2">
                            <li><b>Marker-Based:</b> ใช้สัญลักษณ์ เช่น QR Code[cite: 2]</li>
                            <li><b>Marker-Less:</b> ไม่ใช้สัญลักษณ์ ใช้ GPS อ้างอิงสถานที่[cite: 2]</li>
                            <li>ตัวอย่าง: เกม Pokemon GO และแคมเปญโฆษณา Burn That Ad[cite: 2]</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 5. Cloud -->
            <div class="section-title">คลาวด์คอมพิวติ้ง (Cloud Computing)</div>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="info-card">
                        <h6 class="text-success">SaaS (Software)</h6>
                        <p class="small">การใช้งานซอฟต์แวร์ผ่านอินเทอร์เน็ต เช่น Microsoft 365, Google Workspace[cite: 2]</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <h6 class="text-primary">PaaS (Platform)</h6>
                        <p class="small">บริการแพลตฟอร์มสำหรับพัฒนาระบบ เช่น บริการ Database[cite: 2]</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <h6 class="text-danger">IaaS (Infrastructure)</h6>
                        <p class="small">บริการโครงสร้างพื้นฐาน เช่น หน่วยประมวลผลเสมือน (Google Compute Engine)[cite: 2]</p>
                    </div>
                </div>
            </div>

            <!-- 6. สายงานอาชีพ -->
            <div class="section-title">สายงานอาชีพทางด้านเทคโนโลยีสารสนเทศ</div>
            <div class="table-responsive table-custom">
                <table class="table table-borderless mb-0">
                    <thead>
                        <tr>
                            <th width="30%" class="text-center">กลุ่มสายงาน</th>
                            <th>ตำแหน่งอาชีพที่สำคัญ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center fw-bold border-end">การพัฒนาซอฟต์แวร์</td>
                            <td>IT Project Manager, System Analyst, Programmer, Software Engineer[cite: 2]</td>
                        </tr>
                        <tr class="bg-light">
                            <td class="text-center fw-bold border-end">เครือข่ายและความปลอดภัย</td>
                            <td>Network Engineer, Network Administrator, System Administrator[cite: 2]</td>
                        </tr>
                        <tr>
                            <td class="text-center fw-bold border-end">ด้านข้อมูล</td>
                            <td>Database Administrator, Data Scientist[cite: 2]</td>
                        </tr>
                        <tr class="bg-light">
                            <td class="text-center fw-bold border-end">สื่อและสังคมออนไลน์</td>
                            <td>Content Writer, Blogger, YouTuber[cite: 2]</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- 7. บทสรุป -->
            <div class="section-title">บทสรุปการประยุกต์ใช้เทคโนโลยี</div>
            <div class="info-card bg-light border-0">
                <p class="mb-0">
                    การนำเทคโนโลยีมาประยุกต์ใช้ช่วยเพิ่มประสิทธิภาพการทำงานอย่างเห็นได้ชัด ดังตัวอย่างการจัดการห้องสมุด ที่เปลี่ยนจากการเขียนแบบสอบถามกระดาษ มาใช้ระบบฐานข้อมูลบนคลาวด์[cite: 2]<br><br>
                    <i>"การมีความเข้าใจในเทคโนโลยีสารสนเทศอย่างถูกต้องและมีจริยธรรม จึงเป็นสิ่งจำเป็นสำหรับเยาวชนและคนในยุคปัจจุบันเพื่อการพัฒนาตนเองและสังคมในอนาคต"</i>[cite: 2]
                </p>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>