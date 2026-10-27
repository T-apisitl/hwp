<?php
include 'connect.php';

header('Content-Type: application/json'); // บอก Browser ว่าข้อมูลที่ส่งกลับเป็น JSON

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // ค้นหานักเรียนจากรหัส
    $sql = "SELECT * FROM students WHERE student_id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // ถ้าเจอ ให้ส่งข้อมูลกลับไป
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        // ถ้าไม่เจอ ส่งค่าว่างกลับไป
        echo json_encode(null);
    }
}
?>