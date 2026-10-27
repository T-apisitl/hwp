<?php
session_start();
include 'connect.php';

// ==========================================
// 🌟 นำ Client ID และ Client Secret มาใส่ตรงนี้ครับ 🌟
// ==========================================
$clientID = '851833553406-d43q8ttggmuj8p4greassdncpqgtkaki.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'https://apisit.sites.hwp.ac.th/google_login.php';

// 1. ถ้าเพิ่งกดปุ่ม Login จะพาไปหน้าต่างของ Google เพื่อขอสิทธิ์
if (!isset($_GET['code'])) {
    $authUrl = "https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=" . $clientID . "&redirect_uri=" . urlencode($redirectUri) . "&scope=email%20profile";
    header('Location: ' . $authUrl);
    exit();
}

// 2. เมื่อนักเรียนกดยืนยัน Google จะส่งรหัส (Code) กลับมา
if (isset($_GET['code'])) {
    // เอา Code ไปแลกเป็นตั๋ว (Access Token)
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $postData = [
        'code' => $_GET['code'],
        'client_id' => $clientID,
        'client_secret' => $clientSecret,
        'redirect_uri' => $redirectUri,
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        $accessToken = $tokenData['access_token'];

        // 3. เอา Access Token ไปถาม Google ว่า "อีเมลของคนนี้คืออะไร?"
        $infoUrl = 'https://www.googleapis.com/oauth2/v3/userinfo';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $infoUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userInfo = curl_exec($ch);
        curl_close($ch);

        $userData = json_decode($userInfo, true);
        
        if (isset($userData['email'])) {
            $userEmail = $userData['email'];

            // 4. เอาอีเมลมาเช็คในฐานข้อมูลนักเรียนของเรา
            $check_sql = "SELECT * FROM students WHERE email = '$userEmail'";
            $result = $conn->query($check_sql);

            if ($result->num_rows > 0) {
                // ถ้าเจออีเมลตรงกัน = ล็อกอินสำเร็จ!
                $student = $result->fetch_assoc();
                
                // สร้าง Session สำหรับจดจำการล็อกอินของนักเรียน
                $_SESSION['student_logged_in'] = true;
                $_SESSION['student_id'] = $student['student_id'];
                $_SESSION['student_name'] = $student['fullname'];
                
                echo "<script>alert('✅ ล็อกอินสำเร็จ! ยินดีต้อนรับ " . $student['fullname'] . "'); window.location='index.php';</script>";
            } else {
                // ถ้าไม่เจออีเมลในระบบ (แอบเนียนใช้อีเมลอื่นมาเข้า)
                echo "<script>alert('❌ ไม่พบอีเมล ($userEmail) ในระบบ กรุณาใช้อีเมลโรงเรียน หรือแจ้งครูครับ'); window.location='index.php';</script>";
            }
        } else {
             echo "<script>alert('❌ ดึงข้อมูลอีเมลจาก Google ไม่สำเร็จ'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('❌ ไม่สามารถเชื่อมต่อกับ Google ได้'); window.location='index.php';</script>";
    }
}
?>