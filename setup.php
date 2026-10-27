<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'connect.php';

// 1. สร้างตารางเก็บชิ้นงานของครู (ถ้ายังไม่มี)
$sql_assignments = "CREATE TABLE IF NOT EXISTS `assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// 2. สร้างตารางเก็บประวัติการส่งงานของนักเรียน (ตัวที่ทำให้เกิด Error)
$sql_submissions = "CREATE TABLE IF NOT EXISTS `student_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `submit_type` varchar(50) DEFAULT 'individual',
  `group_id` varchar(100) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";

// สั่งรันสร้างทั้ง 2 ตาราง
$check1 = $conn->query($sql_assignments);
$check2 = $conn->query($sql_submissions);

if ($check1 === TRUE && $check2 === TRUE) {
    echo "<h1 style='color: #198754;'>✅ สร้างตารางฐานข้อมูลครบถ้วนแล้ว!</h1>";
    echo "<p>สร้างตารางรองรับการสั่งงาน และตารางเก็บประวัติการส่งงานสำเร็จ</p>";
    echo "<a href='assignment.php' style='padding: 10px 20px; background: #0dcaf0; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;'>กลับไปหน้าส่งงาน (นักเรียน)</a>";
} else {
    echo "<h1 style='color: #dc3545;'>❌ เกิดข้อผิดพลาด</h1>";
    echo "<p><strong>สาเหตุ:</strong> " . $conn->error . "</p>";
}
echo "</div>";
?>