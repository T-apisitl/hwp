<?php
// ตรวจสอบว่า session_start() ถูกเรียกใช้งานแล้วหรือยัง
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg py-3 shadow-sm" style="background-color: #ffc107;">
    <div class="container">
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="mainMenu">
            
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-bold">
				
                <li class="nav-item"><a class="nav-link" href="index.php">หน้าแรก</a></li>
                <li class="nav-item"><a class="nav-link" href="lessons.php">บทเรียน</a></li>
                <li class="nav-item"><a class="nav-link" href="assignment.php">ส่งงาน</a></li>
                <li class="nav-item"><a class="nav-link" href="student_works.php">ผลงานนักเรียน</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">ประวัติครูผู้สอน</a></li>
				<a class="nav-link text-danger fw-bold" href="remedial.php">
        			<i class="bi bi-exclamation-triangle-fill"></i> แก้ 0, ร, มส
    			</a>
            </ul>
            
            <div class="d-flex align-items-center">
                <span class="fw-bold me-3 text-dark fs-5">ห้องเรียนครูริว</span>
                
                <?php if(isset($_SESSION['student_logged_in'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-dark border-dark rounded-pill dropdown-toggle fw-bold px-3" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            สวัสดี, <?php echo htmlspecialchars($_SESSION['student_name']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userMenu">
                            <li><a class="dropdown-item py-2" href="student_dashboard.php"><i class="bi bi-person-vcard text-primary me-2"></i> ข้อมูลและคะแนนของฉัน</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger fw-bold" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> ออกจากระบบ</a></li>
                        </ul>
                    </div>
                
                <?php elseif(isset($_SESSION['admin_logged_in'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-dark rounded-pill dropdown-toggle fw-bold px-3" type="button" id="adminMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            👨‍🏫 โหมดผู้สอน (Admin)
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="adminMenu">
                            <li><a class="dropdown-item py-2" href="admin_panel.php"><i class="bi bi-gear text-primary me-2"></i> แผงควบคุม (Admin Panel)</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger fw-bold" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> ออกจากระบบ</a></li>
                        </ul>
                    </div>

                <?php else: ?>
                    <a href="google_login.php" class="btn btn-dark rounded-pill fw-bold px-4 shadow-sm">
                        <i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบ
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</nav>