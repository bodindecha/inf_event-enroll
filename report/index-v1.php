<?php
	$dirPWroot = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "แผงควบคุม - งานรับนักเรียน";

	if (!isset($_SESSION["auth"])) header("Location: /$my_url");
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<link rel="stylesheet" href="/resource/css/extend/all-index.css">
		<script type="text/javascript" src="/resource/js/extend/all-index.js"></script>
	</head>
	<body>
		<?php require($dirPWroot."e/enroll/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<div class="container">
				<p><?php echo ($_COOKIE["set_lang"]=="en"?"Welcome ":"ยินดีต้อนรับ ").$_SESSION["auth"]["name"][$_COOKIE["set_lang"]]["a"]; ?></p>
				<p><?php echo ($_COOKIE["set_lang"]=="en"?"to Bodindecha (Sing Singhaseni) School admission system":"เข้าสู่ระบบจัดการงานรับนักเรียนโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)"); ?></p><br>
				<p>คุณสามารถเลือกดูรายงานการตอบกลับได้จากเมนูด้านบนหรือตัวเลือกด้านล่าง</p>
				<input name="response" type="checkbox" id="ref_menu-a"><label for="ref_menu-a">การตอบกลับ</label><ul>
					<li class="dt">นักเรียนเดิม</li>
					<li><a href="response/M4-present-v2">รายงานตัว</a></li>
					<li><a href="response/M4-change-v2">เปลี่ยนกลุ่มการเรียน</a></li>
					<li><a href="response/M4-confirm-v2">ยืนยันสิทธิ์</a></li>
					<li><a href="response/M4-switch">เปลี่ยนแปลงสิทธิ์</a></li>
					<li class="dt">นักเรียนใหม่</li>
					<li><a href="response/new-student-v2">รายงานตัว</a></li>
				</ul>
				<input name="manage" type="checkbox" id="ref_menu-b"><label for="ref_menu-b">จัดการข้อมูล</label><ul>
					<li><a href="print-form">พิมพ์เอกสารใบมอบตัว</a></li>
					<li><a href="delete-response">ลบรายการการตอบกลับ</a></li>
				</ul>
				<input name="settings" type="checkbox" id="ref_menu-c"><label for="ref_menu-c">กระทำการ</label><ul>
					<li><a href="time-control">ตั้งค่าเวลา</a></li>
					<li><a href="edit-direction">แก้ไขคำชี้แจง</a></li>
					<li class="dl">&nbsp;</li>
					<li><a href="import-data">นำเข้าข้อมูล</a></li>
					<li><a href="export-result">นำออกข้อมูล</a></li>
					<li><a href="download-doc">ดาวน์โหลดไฟล์หลักฐาน</a></li>
				</ul>
			</div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."e/enroll/resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>