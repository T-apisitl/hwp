<?php
// แก้ชื่อ Database ให้ตรงกับของคุณ (เช่น esp32_db หรือ my_database)
$servername = "localhost";
$username = "apisit.l";
$password = "UT^h_vzgr6bGoa59";
$dbname = "apisit_l_hwp"; // <-- เช็คตรงนี้

// เชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// คำสั่งเพิ่มช่อง local_ip
$sql = "ALTER TABLE sensor_data ADD COLUMN local_ip VARCHAR(50) DEFAULT '-'";

if ($conn->query($sql) === TRUE) {
  echo "✅ เพิ่มช่องเก็บ IP สำเร็จแล้ว! (Column 'local_ip' added)";
} else {
  echo "⚠️ แจ้งเตือน: " . $conn->error; 
  echo "<br>ถ้ามันบอกว่า Duplicate column แปลว่ามีอยู่แล้ว (ใช้ได้เลย ไม่ต้องทำอะไร)";
}

$conn->close();
?>