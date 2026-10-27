<?php session_start(); ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน่วยที่ 4: Board IPST - ห้องเรียนครูริว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body { background-color: #fbfcfe; font-family: 'Sarabun', sans-serif; font-size: 18px; color: #495057; }
        .hero-section { background: linear-gradient(135deg, #212529 0%, #343a40 100%); border-radius: 0 0 30px 30px; padding: 3rem 1rem; color: white; }
        .card-bright { border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.03); background: white; }
        .content-section h4 { border-left: 5px solid #ffc107; padding-left: 12px; margin-top: 2.5rem; margin-bottom: 1.2rem; color: #212529; font-weight: bold; font-size: 26px; }
        .content-section h5 { color: #2c3e50; margin-top: 1.5rem; font-weight: 600; font-size: 22px; }
        .content-section p, .content-section ul { line-height: 1.8; font-size: 18px; }
        .content-section li { margin-bottom: 8px; }
        .code-block { background: #1e1e2e; color: #cdd6f4; border-radius: 10px; padding: 18px 20px; font-family: 'Source Code Pro', monospace; font-size: 16px; line-height: 1.7; overflow-x: auto; margin-bottom: 16px; }
        .code-block .kw { color: #cba6f7; }
        .code-block .fn { color: #89dceb; }
        .code-block .str { color: #a6e3a1; }
        .code-block .cm { color: #6c7086; font-style: italic; }
        .pin-table td, .pin-table th { font-size: 15px; padding: 8px 12px; }
        .badge-pin { font-size: 13px; padding: 4px 10px; border-radius: 6px; }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="hero-section mb-4">
        <div class="container text-center">
            <div style="font-size:52px; margin-bottom:12px">🤖</div>
            <h2 class="fw-bold mb-2 text-warning">หน่วยการเรียนรู้ที่ 4</h2>
            <h3 class="mb-1">Board IPST — โปรแกรมไมโครคอนโทรลเลอร์</h3>
            <p class="mb-0" style="opacity:0.7">วิชาเทคโนโลยี (วิทยาการคำนวณ) · โรงเรียนหอวัง ปทุมธานี</p>
        </div>
    </div>
    <div class="container pb-5">
        <div class="card card-bright p-4 p-md-5 content-section">

            <h4>Board IPST คืออะไร?</h4>
            <p>Board IPST (สสวท.) คือไมโครคอนโทรลเลอร์ที่สร้างโดยสถาบันส่งเสริมการสอนวิทยาศาสตร์และเทคโนโลยี ออกแบบสำหรับการเรียนการสอน IoT และ Robotics ในประเทศไทย รองรับการเขียนโปรแกรมด้วย MicroPython และ C/C++</p>

            <h4>ส่วนประกอบสำคัญ</h4>
            <div class="table-responsive">
            <table class="table table-bordered pin-table">
                <thead class="table-warning">
                    <tr><th>ส่วนประกอบ</th><th>คำอธิบาย</th><th>การใช้งาน</th></tr>
                </thead>
                <tbody>
                    <tr><td><span class="badge bg-danger badge-pin">ขา Digital</span></td><td>D0–D7</td><td>อ่าน/เขียนสัญญาณ HIGH/LOW (0V/3.3V)</td></tr>
                    <tr><td><span class="badge bg-primary badge-pin">ขา Analog</span></td><td>A0–A3</td><td>อ่านค่าความต้านทาน 0–1023</td></tr>
                    <tr><td><span class="badge bg-success badge-pin">I2C</span></td><td>SDA / SCL</td><td>เชื่อมต่อ Display, Sensor หลายตัว</td></tr>
                    <tr><td><span class="badge bg-info badge-pin">Serial</span></td><td>TX / RX</td><td>สื่อสารกับ Computer และ Module</td></tr>
                    <tr><td><span class="badge bg-warning text-dark badge-pin">ไฟ</span></td><td>VCC / GND</td><td>จ่ายไฟ 3.3V และกราวด์</td></tr>
                </tbody>
            </table>
            </div>

            <h4>ตัวอย่างโปรแกรม MicroPython</h4>
            <h5>กระพริบ LED</h5>
            <div class="code-block">
<span class="kw">from</span> machine <span class="kw">import</span> Pin
<span class="kw">import</span> time

led = <span class="fn">Pin</span>(<span class="str">2</span>, Pin.OUT)  <span class="cm"># ขา D2 เป็น Output</span>

<span class="kw">while True</span>:
    led.<span class="fn">on</span>()       <span class="cm"># เปิด LED</span>
    time.<span class="fn">sleep</span>(<span class="str">0.5</span>)
    led.<span class="fn">off</span>()      <span class="cm"># ปิด LED</span>
    time.<span class="fn">sleep</span>(<span class="str">0.5</span>)
            </div>

            <h5>อ่านค่าเซนเซอร์อุณหภูมิ DHT11</h5>
            <div class="code-block">
<span class="kw">from</span> machine <span class="kw">import</span> Pin
<span class="kw">import</span> dht

sensor = dht.<span class="fn">DHT11</span>(<span class="fn">Pin</span>(<span class="str">4</span>))  <span class="cm"># เซนเซอร์ที่ขา D4</span>
sensor.<span class="fn">measure</span>()

temp = sensor.<span class="fn">temperature</span>()   <span class="cm"># อุณหภูมิ (°C)</span>
humi = sensor.<span class="fn">humidity</span>()      <span class="cm"># ความชื้น (%)</span>

<span class="fn">print</span>(<span class="str">f"อุณหภูมิ: {temp}°C | ความชื้น: {humi}%"</span>)
            </div>

            <h5>ส่งข้อมูลขึ้น Server (HTTP POST)</h5>
            <div class="code-block">
<span class="kw">import</span> urequests

data = {<span class="str">"temperature"</span>: temp, <span class="str">"humidity"</span>: humi}
url  = <span class="str">"https://apisit.sites.hwp.ac.th/insert_data.php"</span>

res = urequests.<span class="fn">post</span>(url, json=data)
<span class="fn">print</span>(res.text)
res.<span class="fn">close</span>()
            </div>

            <h4>แบบฝึกหัด</h4>
            <ol>
                <li>เขียนโปรแกรมให้ LED กระพริบเร็ว-ช้าสลับกัน 5 รอบ แล้วดับ</li>
                <li>อ่านค่าอุณหภูมิแสดงผลบน Serial Monitor ทุก 2 วินาที</li>
                <li>ถ้าอุณหภูมิเกิน 35°C ให้ LED ติดค้าง และส่งข้อมูลขึ้น Server</li>
            </ol>

            <div class="mt-4 d-flex gap-2 flex-wrap">
                <a href="unit3.php" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> หน่วยที่ 3</a>
                <a href="lessons.php" class="btn btn-warning rounded-pill fw-bold">กลับหน้าบทเรียน</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
