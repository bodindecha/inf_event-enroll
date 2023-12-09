<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "อัปโหลดไฟล์หลักฐาน";

	if (!isset($_REQUEST["a"]) || !preg_match("/^[0-9A-Za-z]{4,7}$/", $_REQUEST["a"])) $error = "902";
	else {
		require($dirPWroot."e/resource/db_connect.php"); require_once($dirPWroot."e/enroll/resource/php/config.php");
		require_once($dirPWroot."resource/php/lib/TianTcl/virtual-token.php");
		$datid = $db -> real_escape_string($vToken -> read(trim($_REQUEST["a"])));
		$getinfo = $db -> query("SELECT a.choose,a.filetype,b.start,b.stop FROM admission_newstd a INNER JOIN admission_timerange b ON a.timerange=b.trid WHERE a.datid=$datid");
		if (!$getinfo) $error = "905";
		else if ($getinfo -> num_rows <> 1) $error = "900";
		else {
			$readinfo = $getinfo -> fetch_array(MYSQLI_ASSOC);
			$inTime = inTimerange($readinfo["start"], $readinfo["stop"]);
			$hasFile = (!empty($readinfo["filetype"]));
			$allowFile = ($readinfo["choose"] <> "Y" && !$hasFile);
		}
	}
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
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
			<?php if ($hasFile) echo 'top.cnf.recieved("'.$readinfo["filetype"].'");'; else { ?>
			$(document).ready(function() {
				seek_param();
				$('form input[name="usf"]').on("change", function() { gsef.validate_file(false); });
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
					[3, "เกิดขอ้ผิดพลาด. กรุณาปิดแล้วเปิดหน้านี้และลองใหม่อีกครั้ง."],
					[3, "Unable to get time range."],
					[2, "นักเรียนได้ทำการอัปโหลดไฟล์ไว้แล้ว"],
					[2, "ขณะนี้อยู่นอกช่วงเวลาในการยืนยันสิทธิ์ของนักเรียน"],
					[2, "นักเรียนได้เลือกยืนยันสิทธิ์หรือนักเรียนได้ทำการอัปโหลดไฟล์ไว้แล้ว"],
					[2, "นักเรียนไม่ได้เลือกไฟล์หลักฐานสำหรับการอัปโหลด. กรุณาลองใหม่อีกครั้ง."],
					[1, "ไฟล์ที่นักเรียนเลือกมีคุณสมบัติไม่ตรงกับที่กำหนดไว้. กรุณาเลือกไฟล์ใหม่."],
					[3, "เกิดข้อผิดพลาดในการอัปโหลดไฟล์ กรุณาลองใหม่อีกครั้ง."],
				]; IDs.split("")
					.map(eid => parseInt(eid, 36))
					.forEach(mid => app.ui.notify(1, message[mid]));
			}
			const gsef = (function() {
				var upload = function() {
					(function() {
						if (!validate_file(true)) $('main form [name="usf"]').focus();
						else $("main form")
							.append(addVal("type", "new"))
							.append(addVal("act", "record"))
							.append(addVal("param", "<?=$_REQUEST["a"]?>"))
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
				return {
					out: upload,
					validate_file: validate_file,
				};
			}()); <?php } ?>
		</script>
	</head>
	<body class="nohbar">
		<?php require($dirPWroot."e/enroll/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<?php if (isset($error)) echo '<iframe src="/error/'.$error.'">Error: '.$error.'</iframe>'; else { ?>
			<div class="container">
				<?php if ($hasFile) { ?>
					<center class="message yellow">นักเรียนได้ทำการอัปโหลดไฟล์ไว้แล้ว</center>
				<?php } else if (!$inTime) { ?>
					<center class="message red">ขณะนี้อยู่นอกช่วงเวลาในการยืนยันสิทธิ์ของนักเรียน</center>
				<?php } else if (!$allowFile) { ?>
					<center class="message cyan">นักเรียนได้เลือกยืนยันสิทธิ์หรือนักเรียนได้ทำการอัปโหลดไฟล์ไว้แล้ว</center>
				<?php } else { ?>
					<center style="margin-bottom: 37.5px;"><a href="/e/enroll/resource/file/dl?name=waiver" target="dlframe" download="ฟอร์มสละสิทธิ์.pdf">[<i class="material-icons">download</i> ฟอร์มคำร้องขอสละสิทธิ์ ]</a></center>
					<form class="form" method="post" enctype="multipart/form-data" action="/e/enroll/resource/php/api">
						<div class="box"><input type="file" name="usf" accept=".png, .jpg, .jpeg, .gif, .heic, .pdf" required></div>
						<div class="group last">
							<span>ชื่อไฟล์</span>
							<input type="text" readonly>
							<button class="blue" onClick="return gsef.out()" style="white-space: nowrap;">อัปโหลด</button>
						</div>
					</form>
					<iframe name="dlframe" hidden></iframe>
				<?php } ?>
			</div><?php } ?>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."e/enroll/resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>