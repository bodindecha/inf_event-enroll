<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "ระบบยืนยันสิทธิ์การเข้าศึกษาต่อ";

	$ann_link = "/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2F2023%2F0";
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			main .wrapper { display: flex; justify-content: space-evenly; flex-wrap: wrap; }
			main .wrapper .card {
				margin: 0px 10px 10px;
				width: 275px; min-height: 300px;
				display: flex; justify-content: space-between; flex-direction: column;
			}
			main .wrapper .card .info > * { margin: 0px 0px 10px; }
			main .wrapper .card .action a {
				width: calc(100% - 22px);
				justify-content: center;
			}
			main .wrapper .card .action a:not(:last-child) { margin-bottom: 5px; }
			main h3[id] { font-weight: 500; }
			main ul li.label {
				margin: 2.5px 0px 1.25px;
				line-height: 1.75;
				list-style-type: none;
			}
			main ul li:not(.label) { line-height: 1.5; }
			@media only screen and (max-width: 768px) {
				main .wrapper { justify-content: center; }
				main .wrapper .card { width: 275px; min-height: 150px; }
			}
		</style>
		<script type="text/javascript">
			
		</script>
	</head>
	<body>
		<?php require($dirPWroot."e/enroll/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>" class="rainbow-bg">
			<div class="container">
				<h2>การเข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h2>
				<p>ติดตามข่าว<a target="_blank" href="/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2Fcategory%2Fการรับนักเรียน-๒๕๖๖">การเข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ในปีการศึกษา 2566</a> ได้ที่นี่</p>
				<br>
				<div class="wrapper">
					<div class="card message cyan">
						<div class="info">
							<h3>นักเรียนใหม่</h3>
							<p>นักเรียนที่สอบเข้ามาใหม่ และมีลำดับใน<a href="#announcement">การประกาศผล</a>ประจำปีการศึกษา 2566</p>
						</div>
						<div class="action">
							<a href="new" role="button" class="dont-ripple">ดำเนินการ</a>
						</div>
					</div>
					<div class="card message blue">
						<div class="info">
							<h3>นักเรียนเดิม</h3>
							<p>นักเรียนที่จบจากชั้นมัธยมศึกษาปีที่ 3 โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ที่มีลำดับใน<a href="#announcement">รายชื่อผู้มีสิทธิ์เข้าศึกษาต่อ</a>ชั้นมัธยมศึกษาปีที่ 4 โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</p>
						</div>
						<div class="action">
							<a <?=isset($_SESSION['auth']) ? 'href="M4/"' : 'onClick="sys.auth.orize(\'e%2Fenroll%2FM4%2F\')" href="javascript:"'?> role="button" class="dont-ripple">ดำเนินการ</a>
						</div>
					</div>
				</div>
				<br>
				<!--h3 id="important">ประกาศสำคัญ</h3>
				<ul>
					<li class="label">ปีการศึกษา 2566</li>
					<li data-release=""><a target="_blank" href="<?=$ann_link?>">การเปิดใช้งานบัญชีผู้ใช้งานเครือข่าย สำหรับนักเรียนใหม่</a></li>
					<li data-release=""><a target="_blank" href="<?=$ann_link?>">กิจกรรมเตรียมความพร้อมความเป็นลูกบดินทร</a></li>
				</ul-->
				<h3 id="announcement">ประกาศผลรายชื่อผู้มีสิทธิ์เข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h3>
				<ul>
					<li class="label">ประกาศผลนักเรียนที่ผ่านการคัดเลือก (สอบเข้าใหม่)</li>
					<li data-release="03-28"><a target="_blank" href="<?=$ann_link?>3%2F28573">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
					<li data-release="03-15"><a target="_blank" href="<?=$ann_link?>3%2F28428">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b> (<b>ความสามารถพิเศษ</b>)</a></li>
					<li data-release="03-07"><a target="_blank" href="<?=$ann_link?>3%2F28317">ประเภท<b>ห้องเรียนพิเศษ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
					<li data-release="03-28"><a target="_blank" href="<?=$ann_link?>3%2F28578">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="03-08"><a target="_blank" href="<?=$ann_link?>3%2F28328">ประเภท<b>ห้องเรียนพิเศษ</b>ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="03-06"><a target="_blank" href="<?=$ann_link?>3%2F28281">ประเภท<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>

					<li class="label">ประกาศผลนักเรียนที่ผ่านการคัดเลือก (เข้าโครงการ)</li>
					<li data-release="04-03"><a target="_blank" href="<?=$ann_link?>4%2F28723"><b>วิทยาศาสตร์พลังสิบ</b>ชั้นมัธยมศึกษา<b>ตอนต้น</b></a></li>

					<li class="label">การเรียกแทนผู้สละสิทธิ์ในปีการศึกษา 2566</li>
					<li data-release="03-09"><a target="_blank" href="<?=$ann_link?>3%2F28349">ประเภท<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> รอบที่ <b>1</b></a></li>
					<li data-release="03-13"><a target="_blank" href="<?=$ann_link?>3%2F28391">ประเภท<b>ห้องเรียนพิเศษ</b> รอบที่ <b>1</b> และ<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> รอบที่ <b>2</b></a></li>
					<li data-release="03-15"><a target="_blank" href="<?=$ann_link?>3%2F28426">ประเภท<b>ห้องเรียนพิเศษ</b> รอบที่ <b>2</b> และ<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> รอบที่ <b>3</b></a></li>
					<li data-release="03-20"><a target="_blank" href="<?=$ann_link?>3%2F28440">ประเภท<b>ห้องเรียนพิเศษ</b> รอบที่ <b>3</b> และ<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> รอบที่ <b>4</b></a></li>
					<li data-release="03-22"><a target="_blank" href="<?=$ann_link?>3%2F28504">ประเภท<b>ห้องเรียนพิเศษ</b> รอบที่ <b>4</b> และ<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> รอบที่ <b>5</b></a></li>
					<li data-release="03-24"><a target="_blank" href="<?=$ann_link?>3%2F28542">ประเภท<b>ห้องเรียนพิเศษ</b> รอบที่ <b>5</b> และ<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> รอบที่ <b>6</b></a></li>
					<li data-release="03-27"><a target="_blank" href="<?=$ann_link?>3%2F28567">ประเภท<b>ห้องเรียนพิเศษ</b> รอบที่ <b>6</b></a></li>
					<li data-release="03-30"><a target="_blank" href="<?=$ann_link?>3%2F28678">ประเภท<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> รอบที่ <b>7</b></a></li>
					<li data-release="04-03"><a target="_blank" href="<?=$ann_link?>4%2F28727"><b>ทุกประเภท</b>ห้องเรียน ชั้นมัธยมศึกษาปีที่ <b>1</b> และ <b>4</b></a></li>
					<li data-release="04-05"><a target="_blank" href="<?=$ann_link?>4%2F28749"><b>ทุกประเภท</b>ห้องเรียน ชั้นมัธยมศึกษาปีที่ <b>1</b> และ <b>4</b></a></li>

					<li class="label">ประกาศผลนักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ 4 โรงเรียนเดิม</li>
					<li data-release="02-14"><a target="_blank" href="<?=$ann_link?>2%2F28045">ประเภทห้องเรียนปกติ (<b>รอบ 1</b>)</a></li>
					<li data-release="03-20"><a target="_blank" href="<?=$ann_link?>3%2F28442">ประเภทห้องเรียนปกติ (<b>รอบ 2</b>)</a></li>
					<li data-release="03-23"><a target="_blank" href="<?=$ann_link?>3%2F28533">ประเภทห้องเรียนปกติ (<b>รอบ 3</b>)</a></li>

					<hr>
					<li data-release="02-28"><a target="_blank" href="<?=$ann_link?>2%2F28223">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="03-29"><a target="_blank" href="<?=$ann_link?>3%2F28607">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ 4 ที่<b>สอบคัดเลือกเข้า</b></a></li>
					<!--li data-release=""><a target="_blank" href="<?=$ann_link?>">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ <b>1</b></a></li-->
				</ul>
				<!--center class="message orange glass">ขณะนี้ยังไม่มีประกาศใดๆ โปรดเข้ามาใหม่ภายหลัง</center>
				<center>.<br>.<br>.</center-->
				<center class="message black">ศึกษารายระเอียดทั้งหมดที่ <a target="_blank" href="/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2Fadmission">งานรับนักเรียน</a><hr><a target="_blank" href="/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2Fcostume">เครื่องแบบและระเบียบการแต่งกาย</a>โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</center>
			</div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."e/enroll/resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>