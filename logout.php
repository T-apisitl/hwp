<?php
session_start();
session_destroy(); // ล้างข้อมูลการล็อกอิน (Session)

// เปลี่ยนตรงนี้จาก admin_login.php เป็น index.php
header("Location: index.php"); 

exit();
?>