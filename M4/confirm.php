<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "ระบบยืนยันสิทธิ์เข้าศึกษาต่อ";
	$header_desc = "นักเรียนเดิม";

	require($dirPWroot."e/resource/db_connect.php"); require_once($dirPWroot."e/enroll/resource/php/config.php");
	$authuser = $_SESSION['auth']['user'] ?? "";

	// Check right
	$getstatus = $db -> query("SELECT a.choose,a.time,a.ip,b.start,b.stop,c.name,e.name AS new FROM admission_confirm a INNER JOIN admission_timerange b ON a.timerange=b.trid INNER JOIN admission_sgroup c ON a.type=c.code INNER JOIN admission_change d ON a.stdid=d.stdid LEFT JOIN admission_sgroup e ON d.choose=e.code WHERE a.stdid=$authuser");
	$permitted = ($getstatus && $getstatus -> num_rows == 1);
	if ($permitted) {
		$readstatus = $getstatus -> fetch_array(MYSQLI_ASSOC);
		if (empty($readstatus['choose'])) {
		// Check time
		$inTime = inTimerange($readstatus["start"], $readstatus["stop"]);
	} } $db -> close();
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			html body main div.container * { margin: 0px 0px 10px; }
			main form div.box {
				margin-bottom: 10px;
				width: calc(100% - 5px); height: 125px;
				border-radius: 5px; border: 2.5px dashed var(--clr-bs-gray);
				background-color: var(--clr-gg-grey-300); background-size: contain; background-repeat: no-repeat; background-position: center;
				/* display: flex; justify-content: center; */
				overflow: hidden; transition: var(--time-tst-fast);
			}
			main form div.box:after {
				margin: auto;
				position: relative; top: -50%; transform: translateY(-100%);
				text-align: center; text-shadow: 1.25px 1.25px #FFFA;
				display: block; content: "Drag & Drop your file here or Browse";
				pointer-events: none;
			}
			main form input[type="file"] {
				margin: auto;
				width: 100%; height: 100%; transform: translateY(-2.5px);
				opacity: 0%; filter: opacity(0%);
			}
			main form div.box:focus-within {
				border-color: var(--clr-bs-blue);
				box-shadow: 0px 0px 0px 0.25rem rgb(13 110 253 / 25%);
			}
			main a > i.material-icons { transform: translateY(5px); }
			main .form .last { margin-bottom: 0px !important; }
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				seek_param();
				$('form input[name="usf"]').on("change", function() { cnf.validate_file(false); });
			});
			function seek_param() { if (location.hash.length > 1) {
				// Extract hashes
				var hash = {}; location.hash.substring(1, location.hash.length).split("&").forEach((ehs) => {
					let ths = ehs.split("=");
					hash[ths[0]] = ths[1];
				});
				// Let's see
				if (typeof hash.msgID !== "undefined") gainNoti(hash.msgID);
				history.replaceState(null, null, location.pathname);
			} }
			function gainNoti(IDs) {
				const message = [
					[1, "คุณยังไม่เข้าสู่ระบบ. กรุณาเข้าสู่ระบบ."],
					[1, "บัญชีผู้ใช้งานท่าน<u>ไม่ใช่ประเภทนักเรียน</u>. กรุณาเข้าสู่ระบบด้วยบัญชีผู้ใช้งานนักเรียน."],
					[2, "ตัวเลือกไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง."],
					[3, "เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์ กรุณาลองใหม่อีกครั้ง."],
					[1, "นักเรียนไม่มีสิทธิ์ในการเข้าศึกษาต่อ หรือมีมากกว่าหนึ่งสิทธิ์. หากเป็นข้อผิดพลาด กรุณาติดต่อผู้ดูแลระบบ."],
					[2, "คุณได้ทำการใช้สิทธิ์ไปแล้ว."],
					[2, "ขณะนี้หมดเวลาในการยืนยันสิทธิ์ของนักเรียนแล้ว."],
					[2, "นักเรียนไม่ได้เลือกไฟล์หลักฐานสำหรับการอัปโหลด. กรุณาลองใหม่อีกครั้ง."],
					[1, "ไฟล์ที่นักเรียนเลือกมีคุณสมบัติไม่ตรงกับที่กำหนดไว้. กรุณาเลือกไฟล์ใหม่."],
					[3, "เกิดข้อผิดพลาดในการอัปโหลดไฟล์ กรุณาลองใหม่อีกครั้ง."],
					[3, "เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง."],
					[2, "นักเรียนไม่สามารถเปลี่ยนกลุ่มการเรียนเป็นกลุ่มเดิมได้."] // not used
				]; IDs.split("")
					.map(eid => parseInt(eid, 36))
					.forEach(mid => app.ui.notify(1, message[mid]));
			}
			const cnf = function() {
				// const cv = { APIurl: "/e/enroll/resource/php/api" };
				var sv = {};
				var choose = function(select) {
					(function(select) {
						if (!select && !validate_file(true)) $('main form [name="usf"]').focus();
						else if (confirm("คุณต้องการ" + (select ? "ยืนยัน" : "สละสิทธิ์") + "สิทธิ์การเข้าศึกษาต่อใช่หรือไม่ ?")) $(document.forms["rights"])
							.append(addVal("type", "save"))
							.append(addVal("act", "cnf"))
							.append(addVal("param", select ? "Y" : "N"))
							.submit();
					}(select)); return false;
				};
				var addVal = (name, value) => '<input type="hidden" name="'+name+'" value="'+value+'">';
				var validate_file = function(recheck) {
					var f = document.querySelector('.form [name="usf"]').files[0],
						preview = $("main .form div.box"), fname = document.querySelector("main .form div.box + div input[readonly]");
					// if (!recheck && typeof sv.img_link === "string") URL.revokeObjectURL(sv.img_link);
					if (typeof f !== "undefined") {
						let filename = f.name.toLowerCase().split(".");
						if ((["png", "jpg", "jpeg", "heic", "gif", "pdf"].includes(filename[filename.length-1])) && (f.size > 0 && f.size < 10240000)) { // 10 MB
							if (!recheck) {
								fname.value = f.name; try { if (!isSafari) { sv.img_link = URL.createObjectURL(f);
								preview.css("background-image", 'url("'+sv.img_link+'")'); } } catch(ex) {}
							} return true;
						} else app.ui.notify(1, [2, "กรุณาตรวจสอบว่าภาพของคุณเป็นประเภท PNG/JPG/GIF/HEIF/PDF และมีขนาดไม่เกิน 10 MB"]);
					} else {
						fname.value = ""; preview.removeAttr("style");
						if (recheck) app.ui.notify(1, [1, "กรุณาเลือกไฟล์หลักฐาน."]);
					} return false;
				};
				var show_example = function(m, e) {
					(function() {
						// e.preventDefault();
						if (e.ctrlKey) window.open(m.href);
						else app.ui.lightbox.open("mid", {title: m.innerText.substring(12, m.innerText.length-2), allowclose: true, autoclose: 300000,
							html: '<iframe src="'+m.href+'" style="width:90vw;height:80vh;border:none">Loading...</iframe>'
						});
					}()); return false;
				};
				return {
					choose: choose,
					validate_file: validate_file,
					intercept: show_example
				};
			}();
		</script>
	</head>
	<body>
		<?php require($dirPWroot."e/enroll/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<div class="container">
				<h2>ระบบยืนยันสิทธิ์เข้าศึกษาต่อ ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h2>
				<?php if (!$permitted) echo '<center class="message red">นักเรียนไม่มีสิทธิ์ยืนยันสิทธิ์เข้าศึกษาต่อ ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ประเภทห้องเรียนปกติ</center>'; else { ?>
					<center class="message cyan">การยืนยันสิทธิ์เข้าศึกษาต่อ ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ประเภทห้องเรียนปกติ จากนักเรียนที่จบชั้นมัธยมศึกษาปีที่ 3 ของโรงเรียนเดิม ปีการศึกษา 2566<br><?=$_SESSION['auth']['name']['th']['a']?> กลุ่มการเรียน<u><?=$readstatus["name"]?></u>
						<?php if (!empty($readstatus['new'])) echo '<center class="message blue" style="margin: 10px 0px 0px">คำร้องการขอเปลี่ยนเป็นกลุ่มการเรียน<u>'.$readstatus['new'].'</u>จะได้รับการพิจารณา'.($readstatus['choose'] == "Y" ? "ภายหลัง" : "หลังนักเรียนกดยืนยันสิทธิ์").'</center>'; ?>
					</center>
					<?php if (empty($readstatus['choose'])) { ?>
						<?php if ($inTime) { ?>
							<center class="message yellow">นักเรียนสามารถเลือกได้เพียง 1 ครั้งเท่านั้น ภายใน<?=date("วันที่ d/m/Y เวลา H:iน.", strtotime($readstatus["stop"]))?></center>
							<form class="form message blue" name="rights" method="post" enctype="multipart/form-data" action="/e/enroll/resource/php/api">
								<center>กดปุ่มเพื่อยืนยัน หรือ สละสิทธิ์ เข้าศึกษาต่อ ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ประเภทห้องเรียนปกติ จากนักเรียนที่จบชั้นมัธยมศึกษาปีที่ 3 ของโรงเรียนเดิม ปีการศึกษา 2566</center>
								<fieldset>
									<legend>กรณีสละสิทธิ์ กรุณาอัปโหลดไฟล์</legend>
									<div class="box"><input type="file" name="usf" accept=".png, .jpg, .jpeg, .gif, .heic, .pdf" required></div>
									<div class="group last">
										<span>ชื่อไฟล์</span>
										<input type="text" readonly>
									</div>
									<center><a href="/e/enroll/resource/file/dl?name=waiver" target="dlframe" download="ฟอร์มสละสิทธิ์.pdf">[<i class="material-icons">download</i> ฟอร์มสละสิทธิ์ ]</a></center>
								</fieldset>
								<div class="group spread last" name="decide">
									<button class="last green" onClick="return cnf.choose(true)">ยืนยันสิทธิ์การเข้าศึกษาต่อ</button>
									<button class="last red" onClick="return cnf.choose(false)">สละสิทธิ์การเข้าศึกษาต่อ</button>
								</div>
							</form>
						<?php } else { ?>
							<center class="message red">ขณะนี้อยู่นอกช่วงเวลาในการยืนยันสิทธิ์ของนักเรียน</center>
					<?php } } else { ?>
						<center class="message green">นักเรียนได้<b><?=$readstatus['choose']=="Y"?"ยืนยัน":"สละ"?>สิทธิ์</b>เรียบร้อยแล้วเมื่อ<?=date("วันที่ d/m/Y เวลา H:i:s", strtotime($readstatus['time']))?> ผ่านที่อยู่ IP <?=$readstatus['ip']?><?=$readstatus['choose']=="N"?'<br><a href="/e/enroll/resource/upload/view?type=confirm" onClick="return cnf.intercept(this,event)">[<i class="material-icons">visibility</i> ไฟล์หลักฐาน ]</a>':""?></center>
						<?php if ($readstatus['choose'] == "Y") { ?>
						<div class="message gray" name="instruction">
							<center><b>คำชี้แจง</b></center>
							<ol>
								<li>ศึกษาคำชี้แจงเอกสารประกอบการมอบตัวและกำหนดนัดหมายนักเรียน</li>
								<li>พิมพ์ใบมอบตัวนักเรียน สำหรับนักเรียนระดับชั้นมัธยมศึกษาตอนปลาย ประเภทห้องเรียนปกติ ลงบนกระดาษ A4 สีขาว และติดรูปถ่ายนักเรียนพร้อมทั้งกรอกข้อมูล นำมายื่นต่อคณะกรรมการรับมอบตัวในวันอาทิตย์ที่ 2 เมษายน 2566</li>
							</ol>
							<center><a href="/e/enroll/resource/file/dl?name=sef-4n" target="dlframe" download="ใบมอบตัว.pdf">[<i class="material-icons">download</i> ใบมอบตัว ]</a></center>
						</div>
				<?php } } } ?>
				<iframe name="dlframe" hidden></iframe>
			</div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."e/enroll/resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>