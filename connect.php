<?php
// connect.php สำหรับ Plesk (hwp.ac.th)

$servername = "localhost";      // Plesk ส่วนใหญ่ใช้ localhost ได้เลย
$username = "apisit.l";         // ชื่อผู้ใช้ (เอามาจากในรูป)
$password = "UT^h_vzgr6bGoa59";   // <--- **สำคัญ** กดดูจากปุ่ม Connection info ในเว็บครับ
$dbname = "apisit_l_hwp";       // ชื่อฐานข้อมูล (เอามาจากในรูป)

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตั้งค่าภาษาไทย
$conn->set_charset("utf8");

// เช็คว่าต่อติดไหม
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// =======================================================
// ✅ เพิ่ม 2 บรรทัดนี้ เพื่อแก้เวลาให้เป็นประเทศไทย (UTC+7)
// =======================================================
date_default_timezone_set('Asia/Bangkok');  // บอก PHP ว่าเราอยู่ไทย
$conn->query("SET time_zone = '+07:00'");   // บอก Database ว่าขอใช้เวลาไทย (+7 ชม.)
// =======================================================
?>