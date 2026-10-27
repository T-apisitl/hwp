<?php
// เปิดระบบแสดง Error เพื่อให้เช็คปัญหาได้ง่ายขึ้น
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'connect.php';

// 🌟 ข้อมูลความลับของระบบ (กุญแจเข้าบ้าน) 🌟
$client_id = '851833553406-d43q8ttggmuj8p4greassdncpqgtkaki.apps.googleusercontent.com';
$client_secret = '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';

// ลิงก์สำหรับกลับมาประมวลผลที่หน้าเว็บนี้ (ต้องตรงกับที่ตั้งใน Google Console)
$redirect_uri = 'https://apisit.sites.hwp.ac.th/admin_google_login.php'; 

// 1. ถ้าไม่มีรหัส code ส่งกลับมา ให้เด้งไปหน้า Google Login
if (!isset($_GET['code'])) {
    $auth_url = "https://accounts.google.com/o/oauth2/v2/auth?client_id=" . $client_id . 
                "&redirect_uri=" . urlencode($redirect_uri) . 
                "&response_type=code&scope=email%20profile";
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    exit();
}

// 2. ถ้า Google ส่ง code กลับมาแล้ว ให้เอาไปแลก Access Token
if (isset($_GET['code'])) {
    $token_url = 'https://oauth2.googleapis.com/token';
    $post_data = [
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code',
        'code' => $_GET['code']
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    $response = curl_exec($ch);
    curl_close($ch);

    $token_data = json_decode($response, true);

    if (isset($token_data['access_token'])) {
        // 3. ดึงข้อมูลอีเมลผู้ใช้
        $info_url = "https://www.googleapis.com/oauth2/v2/userinfo?access_token=" . $token_data['access_token'];
        $user_info = json_decode(file_get_contents($info_url), true);

        if (isset($user_info['email'])) {
            $email = $conn->real_escape_string($user_info['email']);

            // 4. เช็คสิทธิ์ในตาราง admins
            $check_admin = $conn->query("SELECT * FROM admins WHERE username = '$email'");
            
            if ($check_admin && $check_admin->num_rows > 0) {
                // อนุญาตให้เข้าสู่ระบบ
                $admin_data = $check_admin->fetch_assoc();
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $admin_data['username'];
                
                header('Location: admin_panel.php');
                exit();
            } else {
                echo "<script>alert('❌ อีเมล ($email) ของคุณไม่มีสิทธิ์เข้าสู่ระบบผู้ดูแล'); window.location='admin_login.php';</script>";
                exit();
            }
        }
    }
}

// กรณีเชื่อมต่อล้มเหลว
echo "<script>alert('❌ เกิดข้อผิดพลาดในการเชื่อมต่อกับ Google'); window.location='admin_login.php';</script>";
exit();
?>