<?php
session_start();
include 'connect.php';

if (isset($_POST['work_title'])) {
    $sender_id = $_POST['sender_id'];
    $type = $_POST['work_type'];
    $title = $_POST['work_title'];
    
    // 1. ดึงข้อมูล ชั้น/ห้อง/เลขที่ ของผู้ส่งมาจากฐานข้อมูลก่อน
    $user_query = $conn->query("SELECT class_level, room_number, student_number FROM students WHERE student_id = '$sender_id'");
    $user_data = $user_query->fetch_assoc();
    
    $my_class = $user_data['class_level'];
    $my_room = $user_data['room_number'];
    $my_num = $user_data['student_number'];

    // 2. จัดการรายชื่อสมาชิกกลุ่ม
    $members = "";
    if ($type == "กลุ่ม" && isset($_POST['members'])) {
        $members = implode(", ", array_filter($_POST['members'])); 
    }

    // 3. จัดการอัปโหลดไฟล์
    $file_original_name = $_FILES['file_work']['name'];
    $file_name = time() . "_" . $file_original_name;
    $target = "uploads/" . $file_name;

    if (!is_dir('uploads')) mkdir('uploads', 0777, true);

    if (move_uploaded_file($_FILES['file_work']['tmp_name'], $target)) {
        // 4. บันทึกลงตาราง assignments (แก้ไข $$sql เป็น $sql และใส่ตัวแปรให้ครบ)
        $sql = "INSERT INTO assignments (student_id, student_name, class_level, room_number, student_number, work_type, subject_name, group_members, file_name) 
                VALUES ('$sender_id', '{$_SESSION['student_name']}', '$my_class', '$my_room', '$my_num', '$type', '$title', '$members', '$file_name')";
        
        if ($conn->query($sql)) {
            echo "<script>alert('✅ ส่งงานสำเร็จแล้วครับ'); window.location='student_works.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "<script>alert('❌ อัปโหลดไฟล์ไม่สำเร็จ'); window.history.back();</script>";
    }
}
?>