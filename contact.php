<?php include 'connect.php'; ?>  </ ต้องมีบรรทัดนี้เป็นบรรทัดแรก! 
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติครูผู้สอน - ห้องเรียนครูอภิสิทธิ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <?php include 'nav.php'; ?>

    <div class="container my-5">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="row g-0">
                <div class="col-md-4 bg-theme text-white text-center p-5 d-flex flex-column justify-content-center align-items-center">
                    <img src="images/IMG_3595.JPG" class="rounded-circle border border-4 border-white shadow mb-3" style="width: 180px; height: 180px; object-fit: cover;" alt="ครูอภิสิทธิ์">
                    <h3 class="fw-bold">ครูอภิสิทธิ์</h3>
                    <p class="mb-0">ครูผู้สอนวิชาคอมพิวเตอร์</p>
                    <p class="small opacity-75">โรงเรียนหอวัง ปทุมธานี</p>
                </div>
                
                <div class="col-md-8 p-5 bg-white">
                    <h3 class="fw-bold text-primary mb-4">👨‍🏫 เกี่ยวกับผู้สอน</h3>
                    
                    <h5 class="fw-bold mt-4"><i class="bi bi-mortarboard-fill text-warning me-2"></i> การศึกษา</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">🎓 ศึกษาศาสตรบัณฑิต สาขาคอมพิวเตอร์ศึกษา</li>
                        <li class="text-muted ms-4">- มหาวิทยาลัยขอนแก่น</li>
                    </ul>

                    <h5 class="fw-bold mt-4"><i class="bi bi-laptop-fill text-info me-2"></i> ความเชี่ยวชาญ</h5>
                    <div>
                        <span class="badge bg-primary rounded-pill me-1">Web Development</span>
                        <span class="badge bg-success rounded-pill me-1">IoT (IPST Board)</span>
                        <span class="badge bg-info text-dark rounded-pill me-1">Graphic Design</span>
                    </div>

                   <h5>✉️ ช่องทางติดต่อ</h5>
                        <p class="ms-3 text-muted">หากนักเรียนมีข้อสงสัย สามารถติดต่อครูได้ที่:</p>
                        
                        <div class="row align-items-center ms-1">
                            
                            <div class="col-sm-7 col-md-8">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><i class="bi bi-line text-success"></i> Line ID: @410duqfb</li>
                                    <li class="mb-2"><i class="bi bi-envelope text-danger"></i> Email: apisit.l@hwp.ac.th</li>
                                    <li class="mb-2"><i class="bi bi-facebook text-primary"></i> Facebook: ห้องเรียนครูอภิสิทธิ์</li>
                                </ul>
                            </div>

                            <div class="col-sm-5 col-md-4 text-center mt-3 mt-sm-0">
                                <img src="images/qr_code.png" alt="QR Code Line" class="img-fluid rounded border p-2 shadow-sm" style="max-width: 130px;">
                                <small class="d-block text-muted mt-1" style="font-size: 0.8rem;">สแกนเพื่อแอดไลน์</small>
                            </div>
                            
                        </div>
                </div>
            </div>
        </div>
		<div class="mt-5 pt-4 border-top">
        <h3 class="fw-bold mb-4">💼 งานของฉัน</h3>
        <div class="row g-3">
            <?php
            // ดึงปุ่มจาก Database มาแสดง
            $buttons = $conn->query("SELECT * FROM contact_buttons");
            
            if ($buttons->num_rows > 0) {
                while($btn = $buttons->fetch_assoc()) {
            ?>
                <div class="col-md-6 col-lg-4">
                    <a href="<?php echo $btn['btn_url']; ?>" target="_blank" 
                       class="btn btn-outline-primary btn-lg w-100 py-3 rounded-pill shadow-sm d-flex align-items-center justify-content-center">
                        <i class="bi bi-link-45deg me-2 fs-4"></i> <span><?php echo $btn['btn_label']; ?></span>
                    </a>
                </div>
            <?php 
                } 
            } else {
                echo '<p class="text-muted">ยังไม่มีข้อมูลลิงก์เพิ่มเติม</p>';
            }
            ?>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	    <footer class="text-center mt-5 py-4 border-top">
        <p class="text-muted mb-0">© 2026 Apisit Lun-Ubon - Horwang Pathumthani School</p>
        <a href="admin_login.php" class="text-decoration-none text-secondary small">สำหรับครูผู้ดูแลระบบ</a>
        <a href="https://plesk.hwp.ac.th:8443/" class="text-decoration-none text-secondary small">จัดการเว็บไซต์</a>
    </footer> 
</body>
</html>