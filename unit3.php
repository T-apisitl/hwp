<?php
session_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน่วยการเรียนรู้ที่ 3 - ห้องเรียนครูริว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Sarabun', sans-serif; color: #333; }
        
        .hero-bg { background-color: #ffc928; border-radius: 0 0 40px 40px; padding-bottom: 5rem; padding-top: 1rem; }
        .navbar-custom .nav-link { color: #333 !important; font-weight: 600; margin: 0 5px; }
        .navbar-custom .nav-link:hover { color: #000 !important; }
        .navbar-brand-text { color: #333; font-weight: 700; margin-right: 15px; }
        
        .btn-login { background-color: #1a237e; color: white; border-radius: 20px; padding: 6px 25px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
        .btn-login:hover { background-color: #12185b; color: white; }

        .hero-icon { background-color: #dc3545; color: white; width: 65px; height: 65px; display: inline-flex; align-items: center; justify-content: center; border-radius: 18px; font-size: 2.2rem; margin-bottom: 1rem; box-shadow: 0 5px 15px rgba(220,53,69,0.3); }

        .content-container { background: #ffffff; border-radius: 20px; padding: 3rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-top: -4rem; margin-bottom: 4rem; }

        .section-title { border-left: 6px solid #ffc928; padding-left: 15px; font-weight: 800; font-size: 1.5rem; margin-top: 3rem; margin-bottom: 1.5rem; color: #1a1a1a; letter-spacing: -0.5px; }

        .info-card { border: 1px solid #f0f0f0; border-radius: 12px; padding: 25px; height: 100%; background: #fff; transition: box-shadow 0.2s, transform 0.2s; }
        .info-card:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.08); transform: translateY(-3px); }
        .info-card h6 { font-weight: 800; margin-bottom: 12px; display: flex; align-items: center; gap: 10px; font-size: 1.15rem; }
        .info-card p { color: #555; font-size: 1rem; margin-bottom: 0; line-height: 1.7; }
        
        .highlight-box { background-color: #fff9e6; border-left: 5px solid #ffc107; padding: 20px; border-radius: 0 10px 10px 0; margin-top: 20px; }
        .list-check li { position: relative; padding-left: 25px; margin-bottom: 8px; line-height: 1.6; color: #555; }
        .list-check li::before { content: '\F26A'; font-family: 'bootstrap-icons'; position: absolute; left: 0; color: #198754; font-weight: bold; }
    </style>
</head>
<body>

    <div class="hero-bg">
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container">
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">หน้าแรก</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">บทเรียน</a></li>
                        <li class="nav-item"><a class="nav-link" href="assignment.php">ส่งงาน</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">ผลงานนักเรียน</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">ประวัติครูผู้สอน</a></li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <span class="navbar-brand-text">ห้องเรียนครูริว</span>
                        <a href="login.php" class="btn-login"><i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบ</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container text-center mt-4 pb-3">
            <div class="hero-icon"><i class="bi bi-shield-fill-check"></i></div>
            <h4 class="fw-bold mb-1 text-dark">หน่วยการเรียนรู้ที่ 3</h4>
            <h1 class="fw-bold mb-3 text-dark display-5">การใช้เทคโนโลยีสารสนเทศอย่างปลอดภัย</h1>
            <p class="mb-0 text-dark opacity-75 fs-5 max-w-75 mx-auto">รู้เท่าทันภัยไซเบอร์ เข้าใจกฎหมายดิจิทัล และการปรับตัวเพื่อเป็นพลเมืองดิจิทัลที่มีคุณภาพ</p>
        </div>
    </div>

    <div class="container">
        <div class="content-container">
            
            <!-- 1. ภัยคุกคามทางไซเบอร์ -->
            <div class="section-title text-danger" style="border-left-color: #dc3545;"><i class="bi bi-bug-fill me-2"></i> 1. ภัยคุกคามทางไซเบอร์ (Cyber Threats)</div>
            <p class="text-muted mb-4 fs-6">ภัยคุกคามในโลกดิจิทัลไม่ได้มีแค่ไวรัสคอมพิวเตอร์ แต่ยังรวมถึงการหลอกลวงทางจิตวิทยาที่พุ่งเป้ามาที่ "มนุษย์" โดยตรง</p>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-card border-top border-danger border-4">
                        <h6 class="text-danger"><i class="bi bi-virus"></i> มัลแวร์ (Malware) ประเภทต่างๆ</h6>
                        <ul class="list-check mt-3 small">
                            <li><strong>Virus:</strong> โปรแกรมที่แฝงมากับไฟล์ เมื่อเปิดไฟล์มันจะทำลายระบบคอมพิวเตอร์</li>
                            <li><strong>Worm (หนอนอินเทอร์เน็ต):</strong> กระจายตัวมันเองผ่านเครือข่ายโดยไม่ต้องอาศัยผู้ใช้งาน ดึงแบนด์วิดท์จนเน็ตช้า</li>
                            <li><strong>Trojan:</strong> หลอกว่าเป็นโปรแกรมที่มีประโยชน์ (เช่น แคร็กเกมฟรี) แต่แอบเปิดหลังบ้านให้แฮกเกอร์เข้ามาขโมยข้อมูล</li>
                            <li><strong>Ransomware (มัลแวร์เรียกค่าไถ่):</strong> ล็อกและเข้ารหัสไฟล์ในเครื่องเพื่อกรรโชกทรัพย์ (มักเรียกเป็น Bitcoin)</li>
                            <li><strong>Spyware & Keylogger:</strong> แอบบันทึกการพิมพ์แป้นพิมพ์ รหัสผ่าน และพฤติกรรมการใช้งาน</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card border-top border-warning border-4">
                        <h6 class="text-warning text-darken"><i class="bi bi-person-bounding-box"></i> วิศวกรรมสังคม (Social Engineering)</h6>
                        <p class="small text-muted mb-2">การโจมตีที่ไม่ได้เจาะระบบคอมพิวเตอร์ แต่ "เจาะจุดอ่อนของมนุษย์" อาศัยความโลภ ความกลัว หรือความตื่นตระหนก</p>
                        <ul class="list-check small">
                            <li><strong>Phishing (ฟิชชิง):</strong> หลอกตกปลาด้วยเหยื่อปลอม เช่น ส่งลิงก์เว็บธนาคารปลอมมาทาง SMS หลอกให้กรอกรหัสผ่าน</li>
                            <li><strong>Baiting (การล่อลวง):</strong> ทิ้งแฟลชไดรฟ์ที่ติดไวรัสไว้ในที่สาธารณะ หวังให้คนเก็บไปเสียบกับคอมพิวเตอร์</li>
                            <li><strong>Romance Scam:</strong> หลอกให้รักทางออนไลน์ สร้างโปรไฟล์ดูดี แล้วอ้างว่าเดือดร้อนเพื่อหลอกยืมเงิน</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 2. ความมั่นคงปลอดภัยทางสารสนเทศ (Cybersecurity) -->
            <div class="section-title text-primary" style="border-left-color: #0d6efd;"><i class="bi bi-shield-lock-fill me-2"></i> 2. ความมั่นคงปลอดภัยทางสารสนเทศ (Cybersecurity)</div>
            
            <h6 class="fw-bold mt-4 mb-3">หลักการ 3 ประการ (CIA Triad)</h6>
            <div class="row g-3 text-center mb-4">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <h1 class="text-primary fw-bold">C</h1>
                        <h6 class="fw-bold">Confidentiality (ความลับ)</h6>
                        <p class="small text-muted mb-0">ข้อมูลต้องเข้าถึงได้เฉพาะผู้ที่มีสิทธิ์เท่านั้น เช่น การตั้งรหัสผ่าน หรือการเข้ารหัส (Encryption)</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <h1 class="text-success fw-bold">I</h1>
                        <h6 class="fw-bold">Integrity (ความถูกต้อง)</h6>
                        <p class="small text-muted mb-0">ข้อมูลต้องไม่ถูกแก้ไขดัดแปลงโดยผู้ที่ไม่มีสิทธิ์ เช่น ยอดเงินในบัญชีธนาคารต้องถูกต้องเสมอ</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <h1 class="text-warning fw-bold">A</h1>
                        <h6 class="fw-bold">Availability (ความพร้อมใช้งาน)</h6>
                        <p class="small text-muted mb-0">ระบบต้องพร้อมให้บริการเมื่อต้องการใช้งาน ป้องกันการโจมตีแบบ DDoS ที่ทำให้ระบบล่ม</p>
                    </div>
                </div>
            </div>

            <div class="highlight-box mt-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-file-earmark-person-fill text-warning"></i> กฎหมาย PDPA และสิทธิข้อมูลส่วนบุคคล</h5>
                <p class="text-dark small"><strong>PDPA (Personal Data Protection Act)</strong> หรือ พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล มีเพื่อปกป้องสิทธิของเรา โดยกำหนดให้องค์กรต้องขอความยินยอม (Consent) ก่อนเก็บรวบรวม ใช้ หรือเปิดเผยข้อมูลของเรา สิทธิที่สำคัญ ได้แก่:</p>
                <div class="row small text-dark mt-2">
                    <div class="col-md-6">
                        <ul>
                            <li><strong>สิทธิขอเข้าถึงข้อมูล:</strong> ขอรับสำเนาข้อมูลที่องค์กรมี</li>
                            <li><strong>สิทธิขอให้ลบ (Right to be forgotten):</strong> สั่งให้ลบเมื่อหมดความจำเป็น</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul>
                            <li><strong>สิทธิคัดค้าน:</strong> ปฏิเสธไม่ให้เอาข้อมูลไปใช้ทำการตลาด</li>
                            <li><strong>สิทธิขอแก้ไข:</strong> แจ้งแก้ข้อมูลให้ถูกต้องและเป็นปัจจุบัน</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 3. พลเมืองดิจิทัลที่ดี (Digital Citizenship) -->
            <div class="section-title text-success" style="border-left-color: #198754;"><i class="bi bi-globe-americas me-2"></i> 3. พลเมืองดิจิทัลและมารยาททางอินเทอร์เน็ต</div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-card">
                        <h6 class="text-success"><i class="bi bi-chat-right-quote"></i> มารยาทชาวเน็ต (Netiquette)</h6>
                        <ul class="list-check small">
                            <li><strong>จดจำไว้ว่าคุณกำลังคุยกับมนุษย์:</strong> อย่าพิมพ์ในสิ่งที่กล้าพูดต่อหน้า</li>
                            <li><strong>เคารพความเป็นส่วนตัวผู้อื่น:</strong> ไม่แท็กรูปน่าเกลียด ไม่เปิดเผยความลับเพื่อน</li>
                            <li><strong>ไม่ใช้อารมณ์:</strong> หลีกเลี่ยงการตอบโต้ขณะโกรธ (Flaming) และงดการพิมพ์ตัวพิมพ์ใหญ่ทั้งหมด เพราะถือว่าเป็นการ <strong>ตะโกน</strong></li>
                            <li><strong>ร่องรอยดิจิทัล (Digital Footprint):</strong> สิ่งที่โพสต์ไปแล้วจะคงอยู่ตลอดไป อาจกระทบการเรียนและการทำงานในอนาคต</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <h6 class="text-danger"><i class="bi bi-person-x-fill"></i> การกลั่นแกล้งไซเบอร์ (Cyberbullying)</h6>
                        <p class="small text-muted mb-2">รุนแรงกว่าการแกล้งในโรงเรียน เพราะแพร่กระจายไวและเกิดขึ้นได้ตลอด 24 ชั่วโมง รูปแบบที่พบบ่อย:</p>
                        <ul class="list-check small mb-2">
                            <li><strong>Exclusion:</strong> การจงใจกีดกัน หรือเตะออกจากกลุ่มแชท</li>
                            <li><strong>Outing:</strong> การเอาความลับหรือเรื่องน่าอายของเพื่อนมาแฉ</li>
                            <li><strong>Masquerading:</strong> การสร้างแอคหลุม หรือสวมรอยเป็นคนอื่นเพื่อไปด่าทอ</li>
                        </ul>
                        <strong class="text-dark small">หลักการรับมือ: S-T-O-P (อย่าตอบโต้), แคปหลักฐาน, บล็อก, และแจ้งผู้ใหญ่</strong>
                    </div>
                </div>
            </div>

            <!-- 🌟 4. กฎหมายคอมพิวเตอร์และทรัพย์สินทางปัญญา (เจาะลึก) 🌟 -->
            <div class="section-title text-info" style="border-left-color: #0dcaf0;"><i class="bi bi-file-earmark-ruled-fill me-2"></i> 4. กฎหมายคอมพิวเตอร์และทรัพย์สินทางปัญญา (เจาะลึก)</div>
            
            <h5 class="fw-bold mt-4"><i class="bi bi-gavel text-danger"></i> 4.1 สรุปสาระสำคัญ พ.ร.บ. คอมพิวเตอร์ (ข้อควรระวัง)</h5>
            <p class="small text-muted mb-3">กฎหมายที่ควบคุมการใช้งานคอมพิวเตอร์และอินเทอร์เน็ต เพื่อป้องกันและลงโทษผู้ที่นำเทคโนโลยีไปใช้ในทางที่ผิด ก่อให้เกิดความเสียหายต่อผู้อื่น</p>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="info-card border-top border-danger border-3 bg-light">
                        <h6 class="text-danger"><i class="bi bi-exclamation-octagon-fill"></i> การโพสต์และแชร์ข้อมูล</h6>
                        <ul class="list-check small mb-0">
                            <li><strong>แชร์ข่าวปลอม (Fake News):</strong> โพสต์ข้อมูลเท็จที่ก่อให้เกิดความตื่นตระหนก หรือกระทบความมั่นคง โทษจำคุกสูงสุด 5 ปี หรือปรับไม่เกิน 1 แสนบาท</li>
                            <li><strong>หมิ่นประมาทออนไลน์:</strong> โพสต์ด่าทอ ใส่ความผู้อื่นให้เสื่อมเสียชื่อเสียง (แม้จะเป็นเรื่องจริงแต่ทำให้เขาเสียหายก็ผิด) โทษจำคุกสูงสุด 3 ปี</li>
                            <li><strong>การกด Like / Share:</strong> การกดไลก์ถือเป็นสิทธิส่วนบุคคล แต่หากกดแชร์ข้อมูลที่ผิดกฎหมาย ถือว่ามีส่วนร่วมในการเผยแพร่ มีความผิดร่วมด้วย</li>
                            <li><strong>แอดมินเพจปล่อยปละละเลย:</strong> หากมีคนมาคอมเมนต์ผิดกฎหมายในเพจ แล้วแอดมินไม่ยอมลบออก แอดมินจะมีความผิดด้วย</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card border-top border-warning border-3 bg-light">
                        <h6 class="text-warning text-darken"><i class="bi bi-person-x-fill"></i> การละเมิดสิทธิผู้อื่นและระบบ</h6>
                        <ul class="list-check small mb-0">
                            <li><strong>ตัดต่อภาพ:</strong> นำภาพผู้อื่นมาตัดต่อ ล้อเลียน ทำให้เสื่อมเสียชื่อเสียง โทษจำคุกสูงสุด 3 ปี หรือปรับไม่เกิน 2 แสนบาท</li>
                            <li><strong>แอบเข้าระบบ (Hacking):</strong> แอบเข้า Facebook, อีเมล, หรือโทรศัพท์มือถือของเพื่อนโดยที่เขาไม่อนุญาต โทษจำคุกสูงสุด 6 เดือน</li>
                            <li><strong>สแปม (Spam):</strong> ฝากร้านในคอมเมนต์ หรือส่ง SMS/Email รบกวนผู้อื่นโดยไม่มีปุ่มให้กดยกเลิก โทษปรับสูงสุด 2 แสนบาท</li>
                            <li><strong>โพสต์ภาพลามกอนาจาร:</strong> นำเข้าข้อมูลที่มีลักษณะลามกเข้าสู่ระบบคอมพิวเตอร์ที่ประชาชนเข้าถึงได้ โทษจำคุกสูงสุด 5 ปี</li>
                        </ul>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mt-4"><i class="bi bi-c-circle-fill text-info"></i> 4.2 ทรัพย์สินทางปัญญา (Intellectual Property)</h5>
            <p class="small text-muted mb-3">ผลงานที่เกิดจากความคิดสร้างสรรค์ของมนุษย์ แบ่งออกเป็น 3 ประเภทหลัก ได้แก่ <strong>ลิขสิทธิ์</strong> (ศิลปะ/วรรณกรรม), <strong>สิทธิบัตร</strong> (การประดิษฐ์), และ <strong>เครื่องหมายการค้า</strong> (โลโก้แบรนด์)</p>
            
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="highlight-box mt-0 border-info">
                        <h6 class="fw-bold text-info text-darken"><i class="bi bi-shield-check"></i> ข้อยกเว้นการละเมิดลิขสิทธิ์ (Fair Use - การใช้ลิขสิทธิ์ที่เป็นธรรม)</h6>
                        <p class="small text-dark mb-2">เราสามารถนำผลงานมีลิขสิทธิ์ (เช่น รูปภาพ บทความ) มาใช้ได้โดยไม่ผิดกฎหมาย หากนำมาใช้เพื่อ <strong>การศึกษา, วิจารณ์, หรือรายงานข่าว</strong> โดยต้องพิจารณาจาก 4 ปัจจัยนี้ร่วมกัน:</p>
                        <div class="row small text-muted">
                            <div class="col-md-6">1. <strong>วัตถุประสงค์:</strong> ต้องไม่ใช่เพื่อการค้า หรือหากำไร (ใช้ทำรายงานส่งครูได้)</div>
                            <div class="col-md-6">2. <strong>ลักษณะของงาน:</strong> เป็นงานข้อมูลทั่วไป หรืองานสร้างสรรค์สูง</div>
                            <div class="col-md-6">3. <strong>ปริมาณที่นำมาใช้:</strong> นำมาใช้แค่ส่วนน้อย ไม่ใช่ก๊อปปี้มาทั้งเล่ม หรือทั้งเพลง</div>
                            <div class="col-md-6">4. <strong>ผลกระทบ:</strong> ต้องไม่ทำให้เจ้าของผลงานเสียผลประโยชน์ หรือขาดรายได้</div>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold mt-4 text-dark"><i class="bi bi-badge-cc-fill text-secondary"></i> สัญญาอนุญาตครีเอทีฟคอมมอนส์ (Creative Commons)</h6>
            <p class="small text-muted">สัญลักษณ์ที่เจ้าของผลงานใช้สื่อสารให้สังคมรู้ว่า "อนุญาตให้นำผลงานนี้ไปใช้ได้ฟรี" แต่ผู้ใช้จะต้องปฏิบัติตามเงื่อนไขที่กำหนดไว้ในสัญลักษณ์นั้นๆ:</p>
            <div class="row g-2 text-center small">
                <div class="col-6 col-md-3">
                    <div class="p-3 border rounded bg-white h-100 shadow-sm">
                        <i class="bi bi-person-fill fs-1 text-dark"></i>
                        <h6 class="fw-bold mt-2 mb-1">BY (Attribution)</h6>
                        <span class="text-muted" style="font-size: 0.8rem;">ต้องอ้างอิงและให้เครดิตเจ้าของผลงานเสมอ (สัญลักษณ์พื้นฐานที่ต้องมี)</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 border rounded bg-white h-100 shadow-sm">
                        <i class="bi bi-currency-dollar fs-1 text-dark text-decoration-line-through"></i>
                        <h6 class="fw-bold mt-2 mb-1">NC (Non-Commercial)</h6>
                        <span class="text-muted" style="font-size: 0.8rem;">ห้ามนำผลงานไปใช้เพื่อการค้า หรือแสวงหาผลกำไรโดยเด็ดขาด</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 border rounded bg-white h-100 shadow-sm">
                        <i class="bi bi-x-circle fs-1 text-dark"></i>
                        <h6 class="fw-bold mt-2 mb-1">ND (No Derivatives)</h6>
                        <span class="text-muted" style="font-size: 0.8rem;">ห้ามดัดแปลง ตัดต่อ หรือแก้ไขผลงานต้นฉบับ (ต้องใช้แบบเดิมๆ เท่านั้น)</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 border rounded bg-white h-100 shadow-sm">
                        <i class="bi bi-arrow-repeat fs-1 text-dark"></i>
                        <h6 class="fw-bold mt-2 mb-1">SA (Share Alike)</h6>
                        <span class="text-muted" style="font-size: 0.8rem;">ถ้านำไปดัดแปลง ต้องเผยแพร่ผลงานใหม่ด้วยเงื่อนไข (CC) เดียวกันกับต้นฉบับ</span>
                    </div>
                </div>
            </div>

            <!-- 5. สุขภาวะดิจิทัล -->
            <div class="section-title text-warning" style="border-left-color: #fd7e14;"><i class="bi bi-heart-pulse-fill me-2" style="color:#fd7e14;"></i> 5. สุขภาวะในการใช้เทคโนโลยี (Digital Health & Wellness)</div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="info-card">
                        <h6 class="text-dark"><i class="bi bi-person-wheelchair text-warning"></i> ผลกระทบทางร่างกาย (Physical)</h6>
                        <ul class="list-check small mt-3">
                            <li><strong>CVS (Computer Vision Syndrome):</strong> อาการตาล้า ตาแห้ง จากการจ้องจอ <br><em class="text-success">วิธีแก้: กฎ 20-20-20 (ทุก 20 นาที มองไกล 20 ฟุต นาน 20 วินาที)</em></li>
                            <li><strong>Office Syndrome:</strong> ปวดบ่า ไหล่ หลัง จากการนั่งผิดท่า</li>
                            <li><strong>Text Neck Syndrome:</strong> อาการปวดคอเรื้อรังจากการก้มหน้าเล่นมือถือนานๆ ทำให้กระดูกคอรับน้ำหนักมากเกินไป</li>
                            <li><strong>Smartphone Pinky:</strong> นิ้วก้อยผิดรูปจากการใช้รองฐานสมาร์ทโฟน</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="info-card">
                        <h6 class="text-dark"><i class="bi bi-brain text-warning"></i> ผลกระทบทางจิตใจ (Mental)</h6>
                        <ul class="list-check small mt-3">
                            <li><strong>Nomophobia (No Mobile Phone Phobia):</strong> โรคขาดมือถือไม่ได้ จะรู้สึกกระวนกระวาย หงุดหงิด หรือตื่นตระหนกเมื่อไม่มีเน็ตหรือลืมมือถือ</li>
                            <li><strong>FOMO (Fear Of Missing Out):</strong> อาการกลัวตกกระแส ต้องเช็คโซเชียลตลอดเวลาเพราะกลัวคุยกับเพื่อนไม่รู้เรื่อง</li>
                            <li><strong>Social Media Depression:</strong> ซึมเศร้าจากการเปรียบเทียบชีวิตตัวเองกับภาพลวงตาความสมบูรณ์แบบของคนอื่นบนโซเชียล</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ส่วน Footer ท้ายเพจ -->
    <footer class="text-center py-4 text-muted small bg-white mt-5 border-top">
        © <?php echo date('Y'); ?> Apisit Lun-Ubon - Horwang Pathumthani School | วิชาเทคโนโลยี (วิทยาการคำนวณ)
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>