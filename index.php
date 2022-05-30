<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "ระบบยืนยันสิทธิ์การเข้าศึกษาต่อ";

	$ann_link = "/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2F2022%2F0";
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
				<p>ติดตามข่าว<a target="_blank" href="/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2Fcategory%2Fnewstudent-2565">การเข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ในปีการศึกษา 2565</a> ได้ที่นี่</p>
				<br>
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
				<h3 id="important">ประกาศสำคัญ</h3>
				<ul>
					<li class="label">ปีการศึกษา 2565</li>
					<li data-release="05-01"><a target="_blank" href="<?=$ann_link?>5%2F25061">การเปิดใช้งานบัญชีผู้ใช้งานเครือข่าย สำหรับนักเรียนใหม่ </a></li>
					<li data-release="05-02"><a target="_blank" href="<?=$ann_link?>5%2F25068">กิจกรรมเตรียมความพร้อมความเป็นลูกบดินทร</a></li>
				</ul>
				<h3 id="announcement">ประกาศผลรายชื่อผู้มีสิทธิ์เข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h3>
				<ul>
					<li class="label">ประกาศผลนักเรียนที่ผ่านการคัดเลือก (สอบเข้าใหม่)</li>
					<li data-release="03-30"><a target="_blank" href="<?=$ann_link?>3%2F24687">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
					<li data-release="03-23"><a target="_blank" href="<?=$ann_link?>3%2F24588">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b> (<b>ความสามารถพิเศษ</b>)</a></li>
					<li data-release="03-10"><a target="_blank" href="<?=$ann_link?>3%2F24337">ประเภท<b>ห้องเรียนพิเศษ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
					<li data-release="03-31"><a target="_blank" href="<?=$ann_link?>3%2F24689">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="03-11"><a target="_blank" href="<?=$ann_link?>3%2F24402">ประเภท<b>ห้องเรียนพิเศษ</b>ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="03-09"><a target="_blank" href="<?=$ann_link?>3%2F24322">ประเภท<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li class="label">การเรียกแทนผู้สละสิทธิ์ในปีการศึกษา 2565</li>
					<li data-release="03-14"><a target="_blank" href="<?=$ann_link?>3%2F24454">ครั้งที่ <b>1</b>: ประเภท<b>ห้องเรียนพิเศษ</b></a></li>
					<li data-release="03-16"><a target="_blank" href="<?=$ann_link?>3%2F24500">ครั้งที่ <b>2</b>: ประเภท<b>ห้องเรียนพิเศษ</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="03-21"><a target="_blank" href="<?=$ann_link?>3%2F24551">ครั้งที่ <b>3</b>: ประเภท<b>ห้องเรียนพิเศษ</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="03-23"><a target="_blank" href="<?=$ann_link?>3%2F24612">ครั้งที่ <b>4</b>: ประเภท<b>ห้องเรียนพิเศษ</b></a></li>
					<li data-release="03-28"><a target="_blank" href="<?=$ann_link?>3%2F24672">ครั้งที่ <b>5</b>: ประเภท<b>ห้องเรียนพิเศษ</b></a></li>
					<li data-release="04-03"><a target="_blank" href="<?=$ann_link?>4%2F24834">ครั้งที่ <b>6</b>: ชั้นมัธยมศึกษาปีที่ <b>1</b> และประเภท<b>ห้องเรียนพิเศษ</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="04-04"><a target="_blank" href="<?=$ann_link?>4%2F24874">ครั้งที่ <b>7</b>: ประเภท<b>ห้องเรียนปกติ</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="04-05"><a target="_blank" href="<?=$ann_link?>4%2F24896">ครั้งที่ <b>8</b>: ชั้นมัธยมศึกษาปีที่ <b>1</b> และ <b>4</b> <b>ทุกประเภท</b>ห้องเรียน</a></li>
					<li data-release="04-12"><a target="_blank" href="<?=$ann_link?>4%2F24945">ครั้งที่ <b>9</b>: <b>ทุกประเภท</b>ห้องเรียน ยกเว้นประเภท<b>ห้องเรียนพิเศษวิทยาศาสตร์ฯ</b> ชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
					<li data-release="04-19"><a target="_blank" href="<?=$ann_link?>4%2F24975">ครั้งที่ <b>9</b>: ชั้นมัธยมศึกษาปีที่ <b>1</b> และ <b>4</b> <b>ทุกประเภท</b>ห้องเรียน</a></li>
					<li data-release="04-26"><a target="_blank" href="<?=$ann_link?>4%2F25206">ครั้งที่ <b>10</b>: ประเภท<b>ห้องเรียนปกติ</b> ชั้นมัธยมศึกษาปีที่ <b>1</b> และชั้นมัธยมศึกษาปีที่ <b>4</b> <b>ทุกประเภท</b>ห้องเรียน</a></li>
					<li data-release="05-04"><a target="_blank" href="<?=$ann_link?>5%2F25092">ครั้งที่ <b>11</b>: ชั้นมัธยมศึกษาปีที่ <b>1</b> และ <b>4</b></a></li>
					<li data-release="05-10"><a target="_blank" href="<?=$ann_link?>5%2F25166">ครั้งที่ <b>12</b>: ชั้นมัธยมศึกษาปีที่ <b>1</b> และ <b>4</b> <b>ทุกประเภท</b>ห้องเรียน</a></li>
					<li data-release="05-17"><a target="_blank" href="<?=$ann_link?>5%2F25266">ครั้งที่ <b>13</b>: ชั้นมัธยมศึกษาปีที่ <b>4</b> <b>ทุกประเภท</b>ห้องเรียน</a></li>
					<li data-release="05-24"><a target="_blank" href="<?=$ann_link?>5%2F25315">ครั้งที่ <b>14</b>: ชั้นมัธยมศึกษาปีที่ <b>1</b> และ <b>4</b> ประเภท<b>ห้องเรียนพิเศษ</b></a></li>
					<li data-release="05-30"><a target="_blank" href="<?=$ann_link?>5%2F25347">ครั้งที่ <b>15</b>:  ชั้นมัธยมศึกษาปีที่ <b>1</b> ประเภท<b>ห้องเรียนปกติ</b> และชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li class="label">ประกาศผลนักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ 4 โรงเรียนเดิม</li>
					<li data-release="03-01"><a target="_blank" href="<?=$ann_link?>3%2F24209">ประเภทห้องเรียนปกติ (<b>รอบ 1</b>)</a></li>
					<li data-release="03-03"><a target="_blank" href="<?=$ann_link?>3%2F24248">ประเภทห้องเรียนปกติ (<b>รอบ 2</b>)</a></li>
					<li data-release="03-16"><a target="_blank" href="<?=$ann_link?>3%2F24500">ประเภทห้องเรียนปกติ (<b>รอบ 3</b>)</a></li>
					<hr>
					<li data-release="03-08"><a target="_blank" href="<?=$ann_link?>3%2F24314">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
					<li data-release="03-25"><a target="_blank" href="<?=$ann_link?>3%2F24643">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ <b>4</b> (<b>ครั้งที่ 2</b>)</a></li>
					<li data-release="03-31"><a target="_blank" href="<?=$ann_link?>3%2F24798">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
					<li data-release="04-02"><a target="_blank" href="<?=$ann_link?>4%2F24822">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ <b>4</b> (<b>ครั้งที่ 3</b>)</a></li>
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