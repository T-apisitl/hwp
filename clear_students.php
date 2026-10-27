<?php
session_start();
include 'connect.php';

if (isset($_SESSION['admin_logged_in'])) {
    $conn->query("TRUNCATE TABLE students"); // TRUNCATE คือล้างข้อมูลทั้งตารางและเริ่มนับใหม่
    header("Location: admin_students.php");
}
?>