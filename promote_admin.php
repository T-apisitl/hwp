<?php
// 🌟 เปิดระบบสแกน Error (ถ้าพังอีกรอบนี้จะบอกสาเหตุชัดเจน ไม่ขึ้น 500 แล้วครับ)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'connect.php';

// ตรวจสอบสิทธิ์แอดมิน
if (!isset($_SESSION['admin_logged_in'])) { 
    header("Location: admin_login.php"); 
    exit(); 
}

if (isset($_GET['student_id'])) {
    $student_id = $conn->real_escape_string($_GET['student_id']);
    
    // 🌟 แก้ไข: ค้นหาด้วย student_id อย่างเดียว (ป้องกัน Error Unknown column 'id')
    $sql_get = "SELECT * FROM students WHERE student_id = '$student_id'";
    $res = $conn->query($sql_get);
    
    // ดักจับ Error หากฐานข้อมูลนักเรียนมีปัญหา
    if (!$res) {
        die("<div style='padding:20px; background:#ffdddd; color:red; font-size:18px;'><strong>Error (ตาราง students):</strong><br>" . $conn->error . "</div>");
    }
    
    if ($res->num_rows > 0) {
        $student = $res->fetch_assoc();
        
        // ใช้อีเมลเป็น Username ถ้าไม่มีให้ใช้ รหัสนักเรียนแทน
        $username = !empty($student['email']) ? $student['email'] : $student['student_id']; 
        $password = "123456"; // รหัสผ่านเริ่มต้น
        
        // เช็คว่าเคยเป็นแอดมินหรือยัง
        $check = $conn->query("SELECT * FROM admins WHERE username = '$username'");
        
        // ดักจับ Error หากตาราง admins ไม่มีคอลัมน์ username
        if (!$check) {
            die("<div style='padding:20px; background:#ffdddd; color:red; font-size:18px;'><strong>Error (ตาราง admins):</strong><br>" . $conn->error . "<br><br>💡 <b>คำแนะนำ:</b> รบกวนคุณครูเช็คในฐานข้อมูลว่ามีตารางชื่อ `admins` และมีคอลัมน์ `username`, `password` หรือไม่ครับ</div>");
        }
        
        if ($check->num_rows > 0) {
            echo "<script>alert('❌ นักเรียนคนนี้เป็นผู้ดูแลระบบอยู่แล้วครับ!'); window.location='admin_panel.php';</script>";
        } else {
            // เพิ่มเข้าตาราง admins
            $sql_insert = "INSERT INTO admins (username, password) VALUES ('$username', '$password')";
            
            if ($conn->query($sql_insert)) {
                echo "<script>alert('✅ แต่งตั้งเป็นผู้ดูแลระบบสำเร็จ!\\n\\nชื่อผู้ใช้: $username\\nรหัสผ่านเริ่มต้น: $password'); window.location='admin_panel.php';</script>";
            } else {
                die("<div style='padding:20px; background:#ffdddd; color:red; font-size:18px;'><strong>Error (ตอนเซฟข้อมูลแอดมิน):</strong><br>" . $conn->error . "</div>");
            }
        }
    } else {
        echo "<script>alert('❌ ไม่พบข้อมูลนักเรียนรหัส $student_id ในระบบ'); window.location='admin_panel.php';</script>";
    }
} else {
    header("Location: admin_panel.php");
}
?>