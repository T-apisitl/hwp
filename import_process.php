<?php
include 'connect.php';

if (isset($_POST['import'])) {
    
    // ตรวจสอบไฟล์
    if ($_FILES['csv_file']['name']) {
        $filename = explode(".", $_FILES['csv_file']['name']);
        
        if ($filename[1] == 'csv') {
            $handle = fopen($_FILES['csv_file']['tmp_name'], "r");
            
            // วนลูปอ่านทีละบรรทัด
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // $data[0] คือคอลัมน์แรก, $data[1] คอลัมน์สอง...
                
                // ข้ามบรรทัดหัวตาราง (ถ้ามีคำว่า 'Student ID' หรือ 'รหัส')
                if (!is_numeric($data[0])) continue; 

                $id = $conn->real_escape_string($data[0]);
                $name = $conn->real_escape_string($data[1]);
                $class = $conn->real_escape_string($data[2]);
                $room = $conn->real_escape_string($data[3]);
                $number = $conn->real_escape_string($data[4]);

                // คำสั่ง SQL (ใช้ INSERT IGNORE เพื่อข้ามถ้ารหัสซ้ำ)
                $sql = "INSERT IGNORE INTO students (student_id, fullname, class_level, room_number, student_number) 
                        VALUES ('$id', '$name', '$class', '$room', '$number')";
                
                $conn->query($sql);
            }
            
            fclose($handle);
            echo "<script>alert('✅ นำเข้าข้อมูลสำเร็จ!'); window.location='admin_students.php';</script>";
        } else {
            echo "<script>alert('❌ กรุณาเลือกไฟล์ .csv เท่านั้น'); window.history.back();</script>";
        }
    }
}
?>