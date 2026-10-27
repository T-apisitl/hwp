<?php
include 'connect.php';

if (isset($_POST['submit'])) {
    // 1. รับค่าจากฟอร์มให้ครบทุกตัว (สำคัญมาก!)
    $std_id = $_POST['student_id'];      // <-- ตัวต้นเหตุ error ถ้าไม่มีบรรทัดนี้
    $name = $_POST['student_name'];
    $class = $_POST['class_level'];
    $room = $_POST['room_number'];
    $number = $_POST['student_number'];
    $subject = $_POST['subject_name'];
    
    // 2. ตั้งค่าไฟล์
    $target_dir = "uploads/";
    $original_filename = basename($_FILES["work_file"]["name"]);
    $fileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION)); 
    
    // 3. กำหนดนามสกุลที่อนุญาต
    $allowTypes = array('jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx');
    
    if (in_array($fileType, $allowTypes)) {
        
        // ตั้งชื่อไฟล์ใหม่ (กันชื่อซ้ำ)
        $new_filename = time() . "_" . $original_filename;
        $target_file = $target_dir . $new_filename;

        // ย้ายไฟล์ไปที่โฟลเดอร์ uploads
        if (move_uploaded_file($_FILES["work_file"]["tmp_name"], $target_file)) {
            
            // 4. บันทึกลงฐานข้อมูล (ต้องใส่ให้ครบทุกคอลัมน์)
            $sql = "INSERT INTO assignments (student_id, student_name, class_level, room_number, student_number, subject_name, file_name) 
                    VALUES ('$std_id', '$name', '$class', '$room', '$number', '$subject', '$new_filename')";
            
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('✅ ส่งงานเรียบร้อยแล้ว!'); window.location='assignment.php';</script>";
            } else {
                echo "Error Database: " . $conn->error;
            }
        } else {
            echo "<script>alert('❌ เกิดข้อผิดพลาดในการย้ายไฟล์'); window.history.back();</script>";
        }
        
    } else {
        echo "<script>alert('❌ อนุญาตเฉพาะไฟล์ PDF, Word และรูปภาพเท่านั้น!'); window.history.back();</script>";
    }
}
?>