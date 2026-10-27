<?php
session_start();
include 'connect.php';
if (!isset($_SESSION['admin_logged_in'])) exit();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT file_name FROM assignments WHERE id = $id";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        unlink("uploads/" . $row['file_name']); // ลบไฟล์จริง
        $conn->query("DELETE FROM assignments WHERE id = $id"); // ลบใน DB
    }
}
header("Location: admin_panel.php");
?>