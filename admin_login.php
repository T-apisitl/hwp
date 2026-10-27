<?php
session_start();
include 'connect.php';

$error = '';

if (isset($_POST['login'])) {
    $pass = $_POST['password'];
    $login_success = false;

    // 1. ดึงข้อมูล admin ทั้งหมดจาก DB เพื่อหารหัสผ่านที่ตรงกัน
    $result = $conn->query("SELECT * FROM admins");
    if ($result && $result->num_rows > 0) {
        while ($admin = $result->fetch_assoc()) {
            // รองรับทั้ง plain text (เก่า) และ hashed (ใหม่)
            $ok = password_verify($pass, $admin['password']) || ($admin['password'] === $pass);
            if ($ok) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $admin['username'];
                $login_success = true;
                break;
            }
        }
    }

    // 2. fallback สำหรับ admin ที่ยังไม่ได้ย้ายไป DB (เช็คเฉพาะรหัสผ่าน)
    if (!$login_success) {
        if ($pass === 'admin') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = 'admin';
            $login_success = true;
        } elseif ($pass === '5628') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = 'ryo';
            $login_success = true;
        }
    }

    // 3. ตรวจสอบผลการเข้าสู่ระบบ
    if ($login_success) {
        header("Location: admin_panel.php");
        exit();
    } else {
        $error = 'รหัสผ่านไม่ถูกต้อง';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบผู้ดูแล - ห้องเรียนครูริว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f8f9fa; }
        .login-card { width: 360px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: none; overflow: hidden; }
        .login-header { background: #ffc107; padding: 28px 24px 20px; text-align: center; }
        .login-header h4 { font-weight: 700; color: #000; margin: 0; }
        .login-header small { color: rgba(0,0,0,0.6); }
        .login-body { padding: 28px 24px; background: white; }
        .form-control:focus { border-color: #ffc107; box-shadow: 0 0 0 0.2rem rgba(255,193,7,0.25); }
        .btn-login { background: #212529; color: #fff; border: none; border-radius: 10px; padding: 12px; font-weight: 600; width: 100%; }
        .btn-login:hover { background: #343a40; color: #fff; }
    </style>
</head>
<body>
    <div class="login-card card">
        <div class="login-header">
            <div style="font-size:36px; margin-bottom:8px">👨‍🏫</div>
            <h4>Admin Login</h4>
            <small>สำหรับครูผู้ดูแลระบบ</small>
        </div>
        <div class="login-body">
            <?php if($error): ?>
            <div class="alert alert-danger py-2 rounded-3 text-center fw-bold"><i class="bi bi-exclamation-circle-fill"></i> <?= $error ?></div>
            <?php endif; ?>
            <form method="post">
                <!-- 🌟 เอาช่อง Username ออกแล้ว เหลือแค่ Password 🌟 -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small">รหัสผ่านผู้ดูแลระบบ</label>
                    <input type="password" name="password" class="form-control rounded-3 py-2" 
                           placeholder="กรอกรหัสผ่าน" required autofocus autocomplete="current-password">
                </div>
                
                <button type="submit" name="login" class="btn-login shadow-sm">
                    เข้าสู่ระบบ
                </button>
                
                <div class="d-flex align-items-center my-3">
                    <hr class="flex-grow-1">
                    <span class="mx-3 text-muted small fw-bold">หรือ</span>
                    <hr class="flex-grow-1">
                </div>

                <a href="admin_google_login.php" class="btn btn-outline-danger w-100 rounded-pill py-2 shadow-sm fw-bold d-flex align-items-center justify-content-center gap-2" style="border-width: 2px;">
                    <i class="bi bi-google fs-5"></i> เข้าสู่ระบบด้วย Google
                </a>
                
                <a href="index.php" class="btn btn-outline-secondary w-100 mt-2 rounded-3 fw-bold border-0">
                    <i class="bi bi-arrow-left-circle"></i> กลับหน้าแรก
                </a>
            </form>
        </div>
    </div>
</body>
</html>