<?php
session_start();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บทที่ 1: การนำเสนอและแบ่งปันข้อมูล - ห้องเรียนครูริว</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* 🌟 ปรับขนาดตัวอักษรพื้นฐานเป็น 18px 🌟 */
        body { background-color: #fbfcfe; font-family: 'Sarabun', sans-serif; color: #495057; font-size: 18px; }
        
        /* สไตล์โทนเหลืองโมเดิร์น */
        .hero-section { background: linear-gradient(135deg, #ffc107 0%, #ffca28 100%); border-radius: 0 0 30px 30px; padding: 3rem 1rem; color: #000; }
        .card-bright { border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.03); background-color: #ffffff; }
        .btn-yellow { background-color: #ffc107; color: #000; border: none; font-weight: 600; }
        .btn-yellow:hover { background-color: #e0a800; color: #000; }
        
        /* ตกแต่งเนื้อหาและปรับขนาดตัวอักษรให้ใหญ่ขึ้น */
        .content-section h4 { 
            border-left: 5px solid #ffc107; 
            padding-left: 12px; 
            margin-top: 2.5rem; 
            margin-bottom: 1.2rem; 
            color: #212529; 
            font-weight: bold; 
            font-size: 26px; /* ขยายหัวข้อหลัก */
        }
        .content-section h5 { color: #2c3e50; margin-top: 1.5rem; font-weight: 600; font-size: 22px; /* ขยายหัวข้อย่อย */ }
        .content-section p { line-height: 1.7; font-size: 18px; }
        .content-section ul, .content-section ol { line-height: 1.7; font-size: 18px; }
        .content-section li { margin-bottom: 8px; }
        
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #0dcaf0;
            padding: 15px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 20px;
        }
        .info-box h6 { font-size: 18px; }
        .info-box p.small { font-size: 16px !important; }
        
        .data-type-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            height: 100%;
            transition: transform 0.2s;
        }
        .data-type-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-color: #ffc107; }
        .data-type-card h6 { font-size: 18px; }
        .data-type-card small { font-size: 16px; }
        
        /* ตกแต่งหัวข้อย่อย */
        .sub-header {
            background-color: #fff9e6;
            padding: 8px 15px;
            border-radius: 8px;
            display: inline-block;
            color: #d48806;
            margin-bottom: 15px;
            font-size: 20px; /* ขยายขนาด */
        }

        /* ตาราง */
        .table-responsive { font-size: 17px; }
    </style>
</head>
<body>

    <?php include 'nav.php'; ?>

    <!-- 🌟 ส่วนหัวบทเรียน (Hero Section) 🌟 -->
    <div class="hero-section mb-5 text-center shadow-sm">
        <div class="container-fluid px-4">
            <span class="badge bg-dark text-warning px-3 py-2 rounded-pill mb-3 fw-bold" style="font-size: 16px;">วิชาระบบสารสนเทศ ม.6</span>
            <h1 class="display-5 fw-bold mb-3">การใช้เทคโนโลยีสารสนเทศนำเสนอและแบ่งปันข้อมูล</h1>
            <p class="lead mb-0 fw-semibold opacity-75">การจัดเรียง การเลือกใช้เครื่องมือ และความปลอดภัยในยุคดิจิทัล</p>
        </div>
    </div>

    <!-- 🌟 ขยายให้เต็มจอด้วย container-fluid และเพิ่ม padding ด้านข้าง 🌟 -->
    <div class="container-fluid px-4 px-xl-5 mb-5">
        <div class="row">
            <div class="col-12">
                <div class="card card-bright p-4 p-md-5">
                    
                    <div class="content-section mt-0">
                        
                        <!-- ================== หัวข้อที่ 1 ================== -->
                        <h4 class="mt-0"><i class="bi bi-diagram-3-fill text-primary me-2"></i> 1. การนำเสนอและแบ่งปันข้อมูลสารสนเทศ</h4>
                        <p>การจัดเรียงข้อมูลที่ได้รวบรวมมาให้อยู่ในรูปแบบที่เป็นระเบียบ เพื่อให้ผู้รับสารสามารถเข้าใจ เปรียบเทียบ และนำข้อมูลไปใช้ได้อย่างถูกต้อง</p>
                        
                        <div class="sub-header fw-bold"><i class="bi bi-easel2-fill me-2"></i> การนำเสนอข้อมูลแบ่งออกเป็น 2 ลักษณะ</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="info-box border-secondary mb-0 h-100">
                                    <h6 class="fw-bold text-dark">1. อย่างไม่เป็นแบบแผน (Informal)</h6>
                                    <p class="small text-muted mb-0">การสื่อสารในลักษณะที่ไม่เป็นทางการ มักใช้ภาษาพูดหรือรูปแบบที่เข้าใจง่าย เหมาะสำหรับการแลกเปลี่ยนความคิดเห็นหรือสถานการณ์ที่ผ่อนคลาย</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box border-success mb-0 h-100">
                                    <h6 class="fw-bold text-dark">2. อย่างเป็นแบบแผน (Formal)</h6>
                                    <p class="small text-muted mb-0">การสื่อสารที่เป็นทางการ มีโครงสร้างชัดเจน ใช้ภาษาทางวิชาการ และมักใช้สื่อประกอบ (เช่น สไลด์) เพื่อให้มีความน่าเชื่อถือ</p>
                                </div>
                            </div>
                        </div>

                        <div class="sub-header fw-bold"><i class="bi bi-tags-fill me-2"></i> ลักษณะของข้อมูลสารสนเทศ 5 ประเภท</div>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6 col-md-4 col-xl-2">
                                <div class="data-type-card">
                                    <h6 class="fw-bold text-primary"><i class="bi bi-123 me-1"></i> 1. ข้อมูลตัวเลข</h6>
                                    <small class="text-muted">ข้อมูลที่นำไปคำนวณหรือวิเคราะห์ได้ เช่น คะแนนสอบ, ส่วนสูงและน้ำหนัก</small>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-2">
                                <div class="data-type-card">
                                    <h6 class="fw-bold text-primary"><i class="bi bi-fonts me-1"></i> 2. ข้อมูลตัวอักขระ</h6>
                                    <small class="text-muted">ตัวอักษรหรือข้อความที่ไม่เน้นการคำนวณ เช่น ชื่อ-นามสกุล, พจนานุกรม</small>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-2">
                                <div class="data-type-card">
                                    <h6 class="fw-bold text-primary"><i class="bi bi-image me-1"></i> 3. ข้อมูลกราฟิก</h6>
                                    <small class="text-muted">รูปภาพหรือสัญลักษณ์ เพื่อให้เข้าใจง่าย เช่น ภาพถ่าย, ภาพวาด, แผนที่</small>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-xl-3">
                                <div class="data-type-card">
                                    <h6 class="fw-bold text-primary"><i class="bi bi-music-note-beamed me-1"></i> 4. ข้อมูลเสียง</h6>
                                    <small class="text-muted">เสียงพูดหรือเสียงอื่นๆ ที่บันทึกผ่านอุปกรณ์ เช่น เสียงสัมภาษณ์, เสียงดนตรี</small>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-xl-3">
                                <div class="data-type-card">
                                    <h6 class="fw-bold text-primary"><i class="bi bi-play-btn-fill me-1"></i> 5. ข้อมูลวิดีโอ</h6>
                                    <small class="text-muted">ภาพเคลื่อนไหวพร้อมเสียงประกอบ ช่วยให้เห็นภาพรวมและเข้าใจลำดับเหตุการณ์ได้อย่างชัดเจน</small>
                                </div>
                            </div>
                        </div>

                        <div class="sub-header fw-bold"><i class="bi bi-share-fill me-2"></i> รูปแบบการแบ่งปันข้อมูล 5 รูปแบบ</div>
                        <ul class="list-group list-group-flush mb-4 border rounded">
                            <li class="list-group-item py-3"><strong class="text-dark">1. การแบ่งปันข้อมูล (Data Sharing):</strong> แชร์ข้อมูลดิบ (Raw Data) หรือไฟล์เบื้องต้น เพื่อให้นำไปวิเคราะห์ต่อ</li>
                            <li class="list-group-item py-3"><strong class="text-dark">2. การแบ่งปันข่าวสาร (News Sharing):</strong> กระจายข้อมูลเหตุการณ์หรือประกาศแจ้งเตือนใหม่ๆ</li>
                            <li class="list-group-item py-3"><strong class="text-dark">3. การแบ่งปันความรู้ (Knowledge Sharing):</strong> ถ่ายทอดทักษะ ประสบการณ์ หรือบทเรียน</li>
                            <li class="list-group-item py-3"><strong class="text-dark">4. การแบ่งปันความคิด (Idea Sharing):</strong> แลกเปลี่ยนมุมมองหรือระดมสมอง (Brainstorming)</li>
                            <li class="list-group-item py-3"><strong class="text-dark">5. การแบ่งปันสารสนเทศ (Information Sharing):</strong> ส่งต่อข้อมูลที่ผ่านการประมวลผลและจัดรูปแบบแล้ว เช่น อินโฟกราฟิก</li>
                        </ul>

                        <!-- ================== หัวข้อที่ 2 ================== -->
                        <h4 class="mt-5"><i class="bi bi-laptop text-info me-2"></i> 2. การใช้เทคโนโลยีสารสนเทศนำเสนอและแบ่งปันข้อมูล</h4>
                        <p>การเลือกใช้เทคโนโลยีที่เหมาะสม จะช่วยให้ข้อมูลชัดเจน เข้าใจง่าย และสื่อสารได้อย่างมีประสิทธิภาพ ปัจจุบันมี 3 รูปแบบหลัก ดังนี้</p>
                        
                        <h5 class="text-primary"><i class="bi bi-globe me-2"></i> 2.1 บล็อก (Blog)</h5>
                        <p>ย่อมาจาก Web-log หมายถึง เว็บไซต์ที่ใช้เผยแพร่ข้อมูลหรือความคิดเห็นส่วนตัว สามารถอัปเดตได้อย่างต่อเนื่อง</p>
                        <ul>
                            <li><strong>ซอฟต์แวร์ที่ใช้:</strong> WordPress, Joomla, Drupal (มักพัฒนาด้วย PHP/MySQL)</li>
                            <li><strong>ผู้ให้บริการบล็อก:</strong> Blogger, WordPress, MySpace</li>
                        </ul>
                        
                        <div class="bg-light p-4 rounded-3 mb-4">
                            <h6 class="fw-bold text-dark mb-3" style="font-size: 19px;"><i class="bi bi-list-ol text-warning"></i> แนวคิดและวิธีการเขียนบทความ 10 ขั้นตอน:</h6>
                            <ol class="mb-0 text-muted" style="columns: 2; font-size: 17px;">
                                <li>กำหนดเป้าหมาย/วัตถุประสงค์</li>
                                <li>ค้นหาข้อมูลก่อนลงมือเขียน</li>
                                <li>ตั้งชื่อหัวข้อให้น่าสนใจ</li>
                                <li>กำหนดเค้าโครงบทความ</li>
                                <li>การเขียนบทนำ</li>
                                <li>กำหนดขนาดบทความ</li>
                                <li>เรียนรู้คำพื้นฐานโครงสร้างเว็บ</li>
                                <li>ตกแต่งบทความให้น่าอ่าน</li>
                                <li>รู้จักโฆษณาเผยแพร่</li>
                                <li>เขียนอย่างต่อเนื่องและสม่ำเสมอ</li>
                            </ol>
                        </div>

                        <h6 class="fw-bold mb-3" style="font-size: 20px;">เปรียบเทียบ บล็อก กับ เว็บไซต์</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th width="20%" class="py-3">ความแตกต่าง</th>
                                        <th width="40%" class="text-primary py-3">บล็อก (Blog)</th>
                                        <th width="40%" class="text-success py-3">เว็บไซต์ (Website)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold bg-light text-center">เนื้อหาและภาษา</td>
                                        <td>ไม่เป็นทางการ เน้นแสดงความเห็นและประสบการณ์ส่วนตัว</td>
                                        <td>ทางการ เน้นให้ข้อมูล ข่าวสาร หรือบริการขององค์กร</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold bg-light text-center">ความเป็นปัจจุบัน</td>
                                        <td>อัปเดตสม่ำเสมอ ตอบสนองเหตุการณ์ปัจจุบัน</td>
                                        <td>ข้อมูลคงที่ เปลี่ยนแปลงไม่บ่อย มักใช้เป็นแหล่งอ้างอิงถาวร</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold bg-light text-center">หน้าที่หลัก</td>
                                        <td>ถ่ายทอดแนวคิดและมุมมองส่วนบุคคล</td>
                                        <td>ให้ข้อมูลผลิตภัณฑ์ บริการ หรือกิจกรรมขององค์กร</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold bg-light text-center">ด้านเทคนิค</td>
                                        <td>สร้างและจัดการง่าย ไม่ต้องใช้ความรู้เชิงเทคนิคมาก</td>
                                        <td>ต้องอาศัยความรู้ด้านเทคโนโลยีและการเขียนโปรแกรม</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h5 class="text-primary"><i class="bi bi-broadcast me-2"></i> 2.2 สตรีมมิง (Streaming)</h5>
                        <p>กระบวนการถ่ายทอดข้อมูลมัลติมีเดียผ่านอินเทอร์เน็ตอย่างต่อเนื่อง ผู้ใช้สามารถรับชมได้ทันทีโดยไม่ต้องดาวน์โหลดจนเสร็จ เช่น การไลฟ์ขายสินค้าออนไลน์, รับชมภาพยนตร์ออนไลน์ (Netflix)</p>

                        <h5 class="text-primary"><i class="bi bi-bar-chart-steps me-2"></i> 2.3 อินโฟกราฟิก (Infographic)</h5>
                        <p>การนำเสนอข้อมูลหรือความรู้ผ่านรูปภาพ แผนภูมิ และข้อความอย่างสั้นๆ เพื่อให้เข้าใจง่าย เห็นภาพรวมชัดเจน มีหลักการออกแบบ 4 ขั้นตอน:</p>
                        <ol>
                            <li><strong>เตรียมข้อมูล:</strong> กำหนดวัตถุประสงค์และเลือกข้อมูลจากแหล่งที่น่าเชื่อถือ</li>
                            <li><strong>วางแผนนำเสนอ:</strong> จัดลำดับเนื้อหาให้กระชับและเข้าใจง่าย</li>
                            <li><strong>ลงมือออกแบบ:</strong> เลือกใช้ภาพ สี และฟอนต์ที่เหมาะสม ดึงดูดความสนใจ</li>
                            <li><strong>ปรับปรุงผลงาน:</strong> ตรวจสอบความถูกต้องและแก้ไขให้สมบูรณ์ก่อนเผยแพร่</li>
                        </ol>

                        <!-- ================== หัวข้อที่ 3 ================== -->
                        <h4 class="mt-5"><i class="bi bi-shield-check text-success me-2"></i> 3. การนำเสนอและแบ่งปันข้อมูลอย่างปลอดภัย</h4>
                        <p>การนำเสนอและแบ่งปันข้อมูลควรทำด้วยความระมัดระวัง โดยเฉพาะข้อมูลส่วนบุคคล ต้องได้รับความยินยอมจากเจ้าของข้อมูล และไม่ละเมิดสิทธิผู้อื่น</p>
                        
                        <h5 class="text-danger"><i class="bi bi-lock-fill me-2"></i> ความเป็นส่วนตัว (Privacy)</h5>
                        <p>สิทธิของบุคคลในการควบคุมข้อมูลเกี่ยวกับตนเอง โดยไม่ให้ผู้อื่นเข้าถึงหรือเปิดเผยโดยไม่ได้รับอนุญาต ได้รับการคุ้มครองตาม <strong>กฎหมายคุ้มครองข้อมูลส่วนบุคคล (PDPA - Personal Data Protection Act)</strong></p>

                        <h5 class="text-danger"><i class="bi bi-person-vcard-fill me-2"></i> ข้อมูลส่วนบุคคล (Personal Data)</h5>
                        <p>ข้อมูลที่สามารถใช้ระบุตัวบุคคลได้ทั้งทางตรงและทางอ้อม แบ่งเป็น 2 ประเภท:</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-1-circle-fill text-secondary me-2"></i> <strong class="text-dark">ข้อมูลส่วนบุคคลทั่วไป:</strong> เช่น ชื่อ-นามสกุล, ที่อยู่, หมายเลขโทรศัพท์, อีเมล, เลขประจำตัวประชาชน</li>
                            <li class="mb-3"><i class="bi bi-2-circle-fill text-danger me-2"></i> <strong class="text-dark text-danger">ข้อมูลส่วนบุคคลที่อ่อนไหว (Sensitive Data):</strong> เช่น ความเชื่อทางศาสนา, ข้อมูลสุขภาพ, ประวัติอาชญากรรม, ข้อมูลทางพันธุกรรม (หากถูกเปิดเผยอาจส่งผลกระทบร้ายแรงหรือนำไปสู่การเลือกปฏิบัติได้)</li>
                        </ul>

                        <h5 class="text-danger"><i class="bi bi-bookmark-star-fill me-2"></i> จริยธรรมอื่นๆ ที่เกี่ยวข้อง</h5>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item px-0 py-2 border-0 bg-transparent">
                                <i class="bi bi-c-circle text-primary me-2"></i> <strong class="text-dark">ลิขสิทธิ์และทรัพย์สินทางปัญญา:</strong> การใช้งานสื่อภาพ/เสียงอย่างถูกต้อง รู้จัก Creative Commons (CC) และการอ้างอิงแหล่งที่มา
                            </li>
                            <li class="list-group-item px-0 py-2 border-0 bg-transparent">
                                <i class="bi bi-shoe-prints text-secondary me-2"></i> <strong class="text-dark">รอยเท้าดิจิทัล (Digital Footprint):</strong> ตระหนักว่าข้อมูลที่แชร์จะคงอยู่ตลอดไปและอาจส่งผลกระทบในอนาคต
                            </li>
                            <li class="list-group-item px-0 py-2 border-0 bg-transparent">
                                <i class="bi bi-search text-warning me-2"></i> <strong class="text-dark">การรู้เท่าทันสื่อ (Media Literacy):</strong> ตรวจสอบข้อเท็จจริง (Fact-check) เสมอก่อนทำการแชร์หรือแบ่งปันข้อมูล
                            </li>
                        </ul>
                    </div>

                    <!-- 🌟 กล่องส่งงาน 🌟 -->
                    <?php if (isset($_SESSION['student_logged_in'])): ?>
                    <div class="bg-light rounded-4 p-4 text-center mt-5 mb-2 border border-warning shadow-sm mx-auto" style="max-width: 800px;">
                        <h5 class="fw-bold mb-2"><i class="bi bi-cloud-upload-fill text-warning"></i> ส่งงานประจำบทเรียน</h5>
                        <p class="text-muted mb-3" style="font-size: 16px;">เรียนจบบทนี้แล้ว อย่าลืมเข้าไปทำใบงานและส่งผลงานกันด้วยนะครับ</p>
                        <a href="assignment.php" class="btn btn-yellow rounded-pill fw-bold shadow-sm px-5 py-2" style="font-size: 18px;">
                            เข้าสู่หน้าส่งงาน
                        </a>
                    </div>
                    <?php endif; ?>

                    <!-- ปุ่มนำทางส่วนล่างเนื้อหา -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                        <a href="lessons.php" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold" style="font-size: 18px;">
                            <i class="bi bi-arrow-left"></i> กลับหน้ารวม
                        </a>
                        <a href="#" class="btn btn-dark rounded-pill px-4 py-2 fw-bold" style="font-size: 18px;">
                            บทต่อไป <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- ส่วนท้ายเว็บ (Footer) -->
    <footer class="text-center py-4 bg-white border-top">
        <p class="text-muted mb-0" style="font-size: 16px;">© 2026 Apisit Lun-Ubon - Horwang Pathumthani School</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>