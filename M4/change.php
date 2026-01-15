<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "ระบบยื่นคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน";
	$header_desc = "นักเรียนเดิม";

	require($dirPWroot."e/resource/db_connect.php"); require_once($dirPWroot."e/enroll/resource/php/config.php");
	$authuser = $_SESSION["auth"]["user"] ?? "0";

	// Check right
	if (!empty($authuser) && $_SESSION["auth"]["type"] == "s") $getstatus = $db -> query("SELECT a.choose,a.time,a.ip,b.start,b.stop,c.name AS name1,d.name AS name2 FROM admission_change a INNER JOIN admission_timerange b ON a.timerange=b.trid INNER JOIN admission_sgroup c ON a.type=c.code LEFT JOIN admission_sgroup d ON a.choose=d.code WHERE a.stdid=$authuser");
	$permitted = (!empty($authuser) && $_SESSION["auth"]["type"] == "s" && $getstatus && $getstatus -> num_rows == 1);
	if ($permitted) {
		$readstatus = $getstatus -> fetch_array(MYSQLI_ASSOC);
		$inTime = inTimerange($readstatus["start"], $readstatus["stop"]);
		if ($inTime) $openGroup = $db -> query("SELECT code,name FROM admission_sgroup WHERE NOT name='".$readstatus["name1"]."' ORDER BY code");
	} $db -> close();
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
				$('form input[name="usf"]').on("change", function() { cng.validate_file(false); });
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
					[2, "คุณได้ทำการใช้สิทธิ์ไปแล้ว."], // not used
					[2, "ขณะนี้หมดเวลาในการยื่นคำร้องขอเปลี่ยนแปลงกลุ่มการเรียนของนักเรียนแล้ว."],
					[2, "นักเรียนไม่ได้เลือกไฟล์หลักฐานสำหรับการอัปโหลด. กรุณาลองใหม่อีกครั้ง."],
					[1, "ไฟล์ที่นักเรียนเลือกมีคุณสมบัติไม่ตรงกับที่กำหนดไว้. กรุณาเลือกไฟล์ใหม่."],
					[3, "เกิดข้อผิดพลาดในการอัปโหลดไฟล์ กรุณาลองใหม่อีกครั้ง."],
					[3, "เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง."],
					[2, "นักเรียนไม่สามารถเปลี่ยนกลุ่มการเรียนเป็นกลุ่มเดิมได้."]
				]; IDs.split("")
					.map(eid => parseInt(eid, 36))
					.forEach(mid => app.ui.notify(1, message[mid]));
			}
			const cng = function() {
				// const cv = { APIurl: "/e/enroll/resource/php/api" };
				var sv = {};
				var send = function() {
					(function() {
						var openGroup = [<?php if (isset($openGroup)) { while ($eg = $openGroup -> fetch_assoc()) echo '"'.$eg["code"].'",'; } ?>];
						if (!openGroup.includes(document.querySelector('main form [name="param"] option:checked').value.trim())) {
							app.ui.notify(1, [1, "กลุ่มการเรียนไม่ถูกต้อง."]);
							$('main form [name="param"]').focus();
						} else if (!validate_file(true)) $('main form [name="usf"]').focus();
						else if (confirm("คุณต้องการยื่นคำร้องขอเปลี่ยนแปลงกลุ่มการเรียนใช่หรือไม่ ?")) $(document.forms["rights"])
							.append(addVal("type", "save"))
							.append(addVal("act", "cng"))
							.submit();
					}()); return false;
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
					request: send,
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
				<h2>ระบบยื่นคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน</h2>
				<?php if (!$permitted) echo '<center class="message red">นักเรียนไม่มีสิทธิ์ยื่นคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน ประเภทห้องเรียนปกติ</center>'; else { ?>
					<center class="message cyan">การยื่นคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน ประเภทห้องเรียนปกติ จากนักเรียนที่จบชั้นมัธยมศึกษาปีที่ 3 ของโรงเรียนเดิม ปีการศึกษา 2569<br><?=$_SESSION["auth"]["name"]["th"]["a"]?> กลุ่มการเรียน<u><?=$readstatus["name1"]?></u></center>
					<?php if (!empty($readstatus["choose"])) { ?>
						<center class="message green">นักเรียนได้ยื่นคำร้องขอเปลี่ยนแปลงกลุ่มการเรียนเป็น<b><?=$readstatus["name2"]?></b>เรียบร้อยแล้ว ครั้งล่าสุดเมื่อ<?=date("วันที่ d/m/Y เวลา H:i:s", strtotime($readstatus["time"]))?> ผ่านที่อยู่ IP <?=$readstatus["ip"]?><br><a href="/e/enroll/resource/upload/view?type=change" onClick="return cng.intercept(this,event)">[<i class="material-icons">visibility</i> ไฟล์หลักฐาน ]</a></center>
					<?php } if ($inTime) { ?>
						<?php if (empty($readstatus["choose"])) { ?><center class="message yellow">นักเรียนสามารถยื่นคำร้องได้ภายใน<?=date("วันที่ d/m/Y เวลา H:i น.", strtotime($readstatus["stop"]))?></center><?php }
						include($dirPWroot."e/enroll/resource/upload/direction/change.html"); ?>
						<form class="form message blue" name="rights" method="post" enctype="multipart/form-data" action="/e/enroll/resource/php/api">
							<center>เลือกกลุ่มการเรียนที่ต้องการและอัปโหลดคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน กดปุ่ม "ยืนยันการขอเปลี่ยนแปลงกลุ่มการเรียน"</center>
							<div class="group last">
								<span>กลุ่มการเรียน</span>
								<select name="param">
									<option value disabled selected>---กรุณาเลือก---</option>
									<?php
										mysqli_data_seek($openGroup, 0);
										while ($eg = $openGroup -> fetch_assoc())
											echo '<option value="'.$eg["code"].'">'.$eg["name"].'</option>';
									?>
								</select>
							</div>
							<div class="box"><input type="file" name="usf" accept=".png, .jpg, .jpeg, .gif, .heic, .pdf" required></div>
							<div class="group last">
								<span>ชื่อไฟล์</span>
								<input type="text" readonly>
							</div>
							<center><a href="/e/enroll/resource/file/dl?name=csgrf" target="dlframe" download="ฟอร์มคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน.pdf">[<i class="material-icons">download</i> ฟอร์มคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน ]</a></center>
							<div class="group spread last" name="decide">
								<button class="last yellow" onClick="return cng.request()">ยืนยันการขอเปลี่ยนแปลงกลุ่มการเรียน</button>
							</div>
						</form>
						<iframe name="dlframe" hidden></iframe>
				<?php } else if (empty($readstatus["choose"])) { ?>
					<center class="message red">ขณะนี้อยู่นอกช่วงเวลาในการยื่นคำร้องขอเปลี่ยนแปลงกลุ่มการเรียนของนักเรียน</center>
				<?php } } ?>
			</div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."e/enroll/resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>