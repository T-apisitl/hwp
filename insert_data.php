<?php
// insert_data.php : เวอร์ชั่นจำกัด 10,000 แถว
require_once 'connect.php'; 

// 1. ตั้งค่าโซนเวลาเป็นไทย
date_default_timezone_set('Asia/Bangkok');
$thai_time = date("Y-m-d H:i:s");

if(isset($_GET['temp']) && isset($_GET['hum'])) {
    
    $temp = $_GET['temp'];
    $hum = $_GET['hum'];
    $ip = $_SERVER['REMOTE_ADDR']; 

    // 2. บันทึกข้อมูลใหม่
    $sql = "INSERT INTO sensor_data (temperature, humidity, local_ip, created_at) 
            VALUES ('$temp', '$hum', '$ip', '$thai_time')";

    if ($conn->query($sql) === TRUE) {
        echo "Saved OK. ";

        // -----------------------------------------------------------
        // 3. ระบบทำความสะอาดอัตโนมัติ (Auto Cleanup)
        // -----------------------------------------------------------
        // ลบข้อมูลเก่าทิ้ง โดยเหลือไว้แค่ 10,000 รายการล่าสุด
        // (SQL นี้จะลบทุก ID ที่ไม่อยู่ในกลุ่ม 10,000 อันดับแรก)
        
        $sql_cleanup = "DELETE FROM sensor_data 
                        WHERE id NOT IN (
                            SELECT id FROM (
                                SELECT id FROM sensor_data ORDER BY id DESC LIMIT 10000
                            ) foo
                        )";
        
        if($conn->query($sql_cleanup) === TRUE) {
            echo " (Cleanup Complete)";
        }
        
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Waiting for data...";
}
?>