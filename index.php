<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "ระบบยืนยันสิทธิ์การเข้าศึกษาต่อ";

	$ann_link = "/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2F2022%2F03%2F";
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
			main #announcement { font-weight: 500; }
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
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<div class="container">
				<h2>การเข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h2>
				<div class="wrapper">
					<div class="card message cyan">
						<div class="info">
							<h3>นักเรียนใหม่</h3>
							<p>นักเรียนที่สอบเข้ามาใหม่ และมีลำดับใน<a href="#announcement">การประกาศผล</a>ประจำปีการศึกษา 2565</p>
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
							<a href="M4/" role="button" class="dont-ripple">ดำเนินการ</a>
						</div>
					</div>
				</div>
				<br>
				<h3 id="announcement">ประกาศผลรายชื่อผู้มีสิทธิ์เข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h3>
				<ul>
					<li disabled data-release="30"><a target="_blank" href="<?=$ann_link?>2____">นักเรียนที่ผ่านการคัดเลือกเข้าเรียน<b>ห้องเรียนทั่วไป</b>ชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
					<li data-release="10"><a target="_blank" href="<?=$ann_link?>24337">นักเรียนที่ผ่านการคัดเลือกเข้าเรียน<b>ห้องเรียนพิเศษ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
					<li disabled data-release="31"><a target="_blank" href="<?=$ann_link?>2____">นักเรียนที่ผ่านการคัดเลือกเข้าเรียน<b>ห้องเรียนทั่วไป</b>ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li disabled data-release="11"><a target="_blank" href="<?=$ann_link?>2____">นักเรียนที่ผ่านการคัดเลือกเข้าเรียน<b>ห้องเรียนพิเศษ</b>ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="9"><a target="_blank" href="<?=$ann_link?>24322">นักเรียนที่ผ่านการคัดเลือกเข้าเรียน<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<hr>
					<li data-release="1"><a target="_blank" href="<?=$ann_link?>24209">นักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ 4 (<b>รอบ 1</b>)</a></li>
					<li data-release="3"><a target="_blank" href="<?=$ann_link?>24248">นักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ 4 (<b>รอบ 2</b>)</a></li>
					<li disabled data-release="17"><a target="_blank" href="<?=$ann_link?>2____">นักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ 4 (<b>รอบ 3</b>)</a></li>
					<li data-release="8"><a target="_blank" href="<?=$ann_link?>24314">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ 4</a></li>
				</ul>
			</div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>