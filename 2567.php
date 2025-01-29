<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "ระบบยืนยันสิทธิ์การเข้าศึกษาต่อ";

	if (false && !has_perm("dev")) TianTcl::http_response_code(909);

	$admission = array(
		"year" => "2567",
		"link" => "/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2F2024%2F0"
	);
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	
</style>
<link rel="stylesheet" type="text/css" href="<?=$APP_CONST["baseURL"]?>_resx/static/style/ext/enroll-main.css" />
<script type="text/javascript">
	const TRANSLATION = "e+enroll";
	$(document).ready(function() {
		page.init();
		Spotlight_background.init();
	});
	const page = (function(d) {
		const cv = { API_URL: AppConfig.APIbase + "" };
		var sv = {inited: false};
		var initialize = function() {
			if (sv.inited) return;
			
			sv.inited = true;
		};
		var myFunction = function() {

		};
		return {
			init: initialize,
			myFunction
		};
	}(document));
</script>
<script type="text/javascript" src="<?=$APP_CONST["baseURL"]?>_resx/static/script/core/spotlight-background.js"></script>
<?php $APP_PAGE -> print -> nav("enroll"); ?>
<main class="bg-rainbow-img">
	<section class="container">
		<h2>การเข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h2>
		<p><span class="ref-00001">ติดตามข่าว</span><a target="_blank" href="/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2Fcategory%2Fnewstudent-2567"><span class="ref-00002">การเข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ในปีการศึกษา</span> <?=$admission["year"]?></a> <span class="ref-00003">ได้ที่นี่</span></p>
		<div class="wrapper">
			<div class="card message purple">
				<div class="info">
					<h3>นักเรียนใหม่</h3>
					<p><span class="ref-00004">นักเรียนที่สอบเข้ามาใหม่ และมีลำดับที่ใน</span><a href="#/announcement/new-student">การประกาศผล</a><span class="ref-00005">ประจำปีการศึกษา</span> <?=$admission["year"]?></p>
				</div>
				<div class="action">
					<a href="new" role="button" class="pill ripple-click"><span class="text">ดำเนินการ</span></a>
				</div>
			</div>
			<div class="card message blue">
				<div class="info">
					<h3>นักเรียนเดิม</h3>
					<p><span class="ref-00006">นักเรียนที่จบจากชั้นมัธยมศึกษาปีที่ 3 โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ที่มีลำดับที่ใน</span><a href="#/announcement/current-student">รายชื่อผู้มีสิทธิ์เข้าศึกษาต่อ</a><span class="ref-00007">ชั้นมัธยมศึกษาปีที่ 4 โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</span></p>
				</div>
				<div class="action">
					<a <?=isset($_SESSION["auth"]) ? 'href="M4/"' : 'onClick="sys.auth.orize(\'e%2Fenroll%2FM4%2F\')" href="javascript:"'?> role="button" class="pill ripple-click"><span class="text">ดำเนินการ</span></a>
				</div>
			</div>
		</div>
		<!-- <center class="message orange">ขณะนี้ยังไม่มีประกาศใดๆ กรุณาเข้ามาใหม่ภายหลัง</center>
		<center>.<br>.<br>.</center> -->
		<h3 class="ref-00008">ประกาศทั่วไป</h3>
		<ul class="announcements-list">
			<li ><a href="<?=$admission["year"]?>/statistics">สถิติการสมัครเข้าศึกษาต่อ ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</a></li>
			<li class="label"><span class="ref-00010">ประกาศสำคัญ (ปีการศึกษา</span> <?=$admission["year"]?>)</li>
			<li data-release="03-28"><a target="_blank" href="<?=$admission["link"]?>3%2F32014">กลุ่มการมอบตัวนักเรียนห้องห้องเรียนปกติ</a></li>
			<li data-release="03-29"><a target="_blank" href="<?=$admission["link"]?>3%2F32033">กิจกรรมเตรียมความพร้อมความเป็นลูกบดินทร</a></li>
			<li data-release="04-29"><a target="_blank" href="<?=$admission["link"]?>4%2F32206">การเปิดใช้งานบัญชีผู้ใช้งานเครือข่าย สำหรับนักเรียนใหม่</a></li>
			<li class="label ref-00011">ผลนักเรียนที่ผ่านการคัดเลือก (เข้าโครงการ)</li>
			<li data-release="04-01"><a target="_blank" href="<?=$admission["link"]?>4%2F32063"><b>วิทยาศาสตร์พลังสิบ</b>ชั้นมัธยมศึกษา<b>ตอนต้น</b></a></li>
			<li class="label ref-00012">ข่าวสำหรับนักเรียนชั้นมัธยมศึกษาตอนปลาย</li>
			<li data-release="02-29"><a target="_blank" href="<?=$admission["link"]?>2%2F31626">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ <b>4</b> [รอบที่ <b>1</b>]</a></li>
			<li data-release="03-22"><a target="_blank" href="<?=$admission["link"]?>3%2F31892">ผลการจัด<b>กลุ่มการเรียน</b>นักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ <b>4</b> [รอบที่ <b>2</b>]</a></li>
		</ul>
		<h3 class="ref-00009">ประกาศผลรายชื่อผู้มีสิทธิ์เข้าศึกษาต่อโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h3>
		<ul class="announcements-list">
			<li class="label ref-00013" id="/announcement/new-student">ประกาศผลนักเรียนที่ผ่านการคัดเลือก (สอบเข้าใหม่)</li>
			<li data-release="03-27"><a target="_blank" href="<?=$admission["link"]?>3%2F31952">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
			<li data-release="03-20"><a target="_blank" href="<?=$admission["link"]?>3%2F31864">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b> (<b>ความสามารถพิเศษ</b>)</a></li>
			<li data-release="02-22"><a target="_blank" href="<?=$admission["link"]?>2%2F31558">ประเภท<b>ห้องเรียนพิเศษ</b>ชั้นมัธยมศึกษาปีที่ <b>1</b></a></li>
			<li data-release="03-27"><a target="_blank" href="<?=$admission["link"]?>3%2F31956">ประเภท<b>ห้องเรียนปกติ</b>ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
			<li data-release="02-29"><a target="_blank" href="<?=$admission["link"]?>2%2F31621">ประเภท<b>ห้องเรียนพิเศษ</b>ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
			<li data-release="01-22"><a target="_blank" href="<?=$admission["link"]?>1%2F31238">ประเภท<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b> ชั้นมัธยมศึกษาปีที่ <b>4</b></a></li>
			<li class="label ref-00014" id="/announcement/current-student">ประกาศผลนักเรียนชั้นมัธยมศึกษาปีที่ 3 ที่มีสิทธิ์เข้าเรียนต่อชั้นมัธยมศึกษาปีที่ 4 โรงเรียนเดิม</li>
			<li data-release="02-15"><a target="_blank" href="<?=$admission["link"]?>2%2F31507">ประเภทห้องเรียนปกติ (<b>รอบ 1</b>)</a></li>
			<li data-release="03-20"><a target="_blank" href="<?=$admission["link"]?>3%2F31870">ประเภทห้องเรียนปกติ (<b>รอบ 2</b>)</a></li>
			<!-- <li data-release="__-__"><a target="_blank" href="<?=$admission["link"]?>_%2F_____">ประเภทห้องเรียนปกติ (<b>รอบ 3</b>)</a></li> -->
			<hr>
			<li class="label"><span class="ref-00015">การเรียกแทนผู้สละสิทธิ์ในปีการศึกษา</span> <?=$admission["year"]?></li>
			<li>
				<p>ประเภท<b>ห้องเรียนปกติ</b></p>
				<a data-release="04-01" target="_blank" href="<?=$admission["link"]?>4%2F32057">[รอบที่ <b>1</b>]</a>
				<a data-release="04-03" target="_blank" href="<?=$admission["link"]?>4%2F32073">[รอบที่ <b>2</b>]</a>
				<a data-release="04-24" target="_blank" href="<?=$admission["link"]?>4%2F32133">[รอบที่ <b>3</b>]</a>
			</li>
			<li>
				<p>ประเภท<b>ห้องเรียนพสวท. (สู่ความเป็นเลิศ)</b></p>
				<a data-release="02-02" target="_blank" href="<?=$admission["link"]?>2%2F31387">[รอบที่ <b>1</b>]</a>
				<a data-release="02-06" target="_blank" href="<?=$admission["link"]?>2%2F31410">[รอบที่ <b>2</b>]</a>
				<a data-release="02-09" target="_blank" href="<?=$admission["link"]?>2%2F31426">[รอบที่ <b>3</b>]</a>
				<a data-release="02-13" target="_blank" href="<?=$admission["link"]?>2%2F31453">[รอบที่ <b>4</b>]</a>
				<a data-release="02-16" target="_blank" href="<?=$admission["link"]?>2%2F31541">[รอบที่ <b>5</b>]</a>
				<a data-release="03-20" target="_blank" href="<?=$admission["link"]?>3%2F31880">[รอบที่ <b>6</b>]</a>
				<a data-release="03-22" target="_blank" href="<?=$admission["link"]?>3%2F31917">[รอบที่ <b>7</b>]</a>
				<a data-release="03-26" target="_blank" href="<?=$admission["link"]?>3%2F31944">[รอบที่ <b>8</b>]</a>
				<a data-release="04-01" target="_blank" href="<?=$admission["link"]?>4%2F32057">[รอบที่ <b>9</b>]</a>
				<a data-release="04-03" target="_blank" href="<?=$admission["link"]?>4%2F32073">[รอบที่ <b>10</b>]</a>
				<a data-release="04-25" target="_blank" href="<?=$admission["link"]?>4%2F32163">[รอบที่ <b>11</b>]</a>
			</li>
			<li>
				<p>ประเภท<b>ห้องเรียนพิเศษ</b></p>
				<a data-release="03-07" target="_blank" href="<?=$admission["link"]?>3%2F31688">[รอบที่ <b>1</b>]</a>
				<a data-release="03-12" target="_blank" href="<?=$admission["link"]?>3%2F31767">[รอบที่ <b>2</b>]</a>
				<a data-release="03-20" target="_blank" href="<?=$admission["link"]?>3%2F31880">[รอบที่ <b>3</b>]</a>
				<a data-release="03-22" target="_blank" href="<?=$admission["link"]?>3%2F31917">[รอบที่ <b>4</b>]</a>
				<a data-release="03-26" target="_blank" href="<?=$admission["link"]?>3%2F31944">[รอบที่ <b>5</b>]</a>
				<a data-release="04-01" target="_blank" href="<?=$admission["link"]?>4%2F32057">[รอบที่ <b>6</b>]</a>
				<a data-release="04-03" target="_blank" href="<?=$admission["link"]?>4%2F32073">[รอบที่ <b>7</b>]</a>
				<a data-release="04-25" target="_blank" href="<?=$admission["link"]?>4%2F32163">[รอบที่ <b>8</b>]</a>
			</li>
		</ul>
		<center class="message black"><span class="ref-00016">ศึกษารายระเอียดทั้งหมดที่</span> <a target="_blank" href="/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2Fadmission">งานรับนักเรียน</a><hr><a target="_blank" href="/go?url=https%3A%2F%2Fbodin.ac.th%2Fhome%2Fcostume">เครื่องแบบและระเบียบการแต่งกาย</a><span class="ref-00017">โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</span></center>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials(side_panel: "enroll");
	$APP_PAGE -> print -> footer();
?>