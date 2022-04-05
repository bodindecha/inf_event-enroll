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
			main ul li.label {
				margin: 2.5px 0px 1.25px;
				list-style-type: none;
			}
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
							<a <?=isset($_SESSION['auth']) ? 'href="M4/"' : 'onClick="sys.auth.orize(\'e%2Fenroll%2FM4%2F\')" href="javascript:void(0)"'?> role="button" class="dont-ripple">ดำเนินการ</a>
						</div>
					</div>
				</div>
				<br>
				<h3 id="announcement">ประกาศผลรายชื่อผู้มีสิทธิ์เข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h3>
				<ul>
					<li class="label">ประกาศผลนักเรียนที่ผ่านการคัดเลือก (สอบเข้าใหม่)</li>
					<li data-release="03-30"><a target="_blank" href="<?=$ann_link?>24687">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
					<li data-release="03-23"><a target="_blank" href="<?=$ann_link?>24588">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b> (<b>ความสามารถพิเศษ</b>)</a></li>
					<li data-release="03-10"><a target="_blank" href="<?=$ann_link?>24337">ประเภท<b>ห้องเรียนพิเศษ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
					<li data-release="03-31"><a target="_blank" href="<?=$ann_link?>24689">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="03-11"><a target="_blank" href="<?=$ann_link?>24402">ประเภท<b>ห้องเรียนพิเศษ</b>ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="03-09"><a target="_blank" href="<?=$ann_link?>24322">ประเภท<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li class="label">การเรียกแทนผู้สละสิทธิ์ในปีการศึกษา 2565</li>
					<li data-release="03-14"><a target="_blank" href="<?=$ann_link?>24454">ครั้งที่ <b>1</b>: ประเภท<b>ห้องเรียนพิเศษ</b></a></li>
					<li data-release="03-16"><a target="_blank" href="<?=$ann_link?>24500">ครั้งที่ <b>2</b>: ประเภท<b>ห้องเรียนพิเศษ</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="03-21"><a target="_blank" href="<?=$ann_link?>24551">ครั้งที่ <b>3</b>: ประเภท<b>ห้องเรียนพิเศษ</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="03-23"><a target="_blank" href="<?=$ann_link?>24612">ครั้งที่ <b>4</b>: ประเภท<b>ห้องเรียนพิเศษ</b></a></li>
					<li data-release="03-28"><a target="_blank" href="<?=$ann_link?>24672">ครั้งที่ <b>5</b>: ประเภท<b>ห้องเรียนพิเศษ</b></a></li>
					<li data-release="04-03"><a target="_blank" href="<?=$ann_link?>24834">ครั้งที่ <b>6</b>: ชั้นมัธยมศึกษาปีที่ <b>1</b> และประเภท<b>ห้องเรียนพิเศษ</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="04-04"><a target="_blank" href="<?=$ann_link?>24874">ครั้งที่ <b>7</b>: ประเภท<b>ห้องเรียนปกติ</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="04-05"><a target="_blank" href="<?=$ann_link?>24896">ครั้งที่ <b>8</b>: ชั้นมัธยมศึกษาปีที่ <b>1</b> และ <b>4</b> ห้องเรียน<b>ทุกประเภท</b></a></li>
					<li class="label">ประกาศผลนักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ 4 โรงเรียนเดิม</li>
					<li data-release="03-01"><a target="_blank" href="<?=$ann_link?>24209">ประเภทห้องเรียนปกติ (<b>รอบ 1</b>)</a></li>
					<li data-release="03-03"><a target="_blank" href="<?=$ann_link?>24248">ประเภทห้องเรียนปกติ (<b>รอบ 2</b>)</a></li>
					<li data-release="03-16"><a target="_blank" href="<?=$ann_link?>24500">ประเภทห้องเรียนปกติ (<b>รอบ 3</b>)</a></li>
					<hr>
					<li data-release="8"><a target="_blank" href="<?=$ann_link?>24314">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ 4</a></li>
					<li data-release="25"><a target="_blank" href="<?=$ann_link?>24643">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ 4 (<b>ครั้งที่ 2</b>)</a></li>
				</ul>
				<center class="message black">ศึกษารายระเอียดทั้งหมดที่ <a target="_blank" href="/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2Fadmission">งานรับนักเรียน</a><hr><a target="_blank" href="/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2Fcostume">เครื่องแบบและระเบียบการแต่งกาย</a>โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</center>
			</div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."e/enroll/resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>