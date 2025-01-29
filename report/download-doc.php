<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "ดาวน์โหลดไฟล์หลักฐาน (ทั้งหมด)";
	$home_menu = "settings";

	$has_perm = has_perm("admission");
	if (!$has_perm) {
		require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
		TianTcl::http_response_code(901);
	}
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	app[name=main] main .form p { margin: 0; }
	app[name=main] .lightbox .progression h3 { margin: 10px 0; }
	app[name=main] .lightbox .progression .bar { min-width: 375px; width: 30vw; max-width: 80vw; }
</style>
<script type="text/javascript">
	const TRANSLATION = location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+");
	$(document).ready(function() {
		page.init();
	});
	const page = (function(d) {
		const cv = {
			API_URL: AppConfig.baseURL + "e/enroll/report/response/download",
			btn: [
				'<span class="ripple-effect"></span><i class="material-icons">download</i> ดาวน์โหลด',
				'&nbsp;&emsp;<div class="loading small"></div>&emsp;&nbsp;'
			], chunk_size: Math.pow(1e3, 2) * 3, // 3 MB
			TypeAvail: ["prs", "cng", "cnf", "new"],
			barSpeed: .5, barSmoothness: 1.5
		};
		var sv = {
			inited: false,
			processing: false,
			msgID: "",
			barUpdating: null
		};
		var initialize = function() {
			if (sv.inited) return;

			sv.inited = true;
		};
		var verify = function() {
			(function() {
				if (sv.processing) return app.UI.notify(2, "You can only download 1 file a time");
				var send = {
					sys: document.querySelector('main form [name="system"]')
				}; if (!cv.TypeAvail.includes(send.sys.value.trim())) {
					app.UI.notify(1, "หมวดหมู่ไม่ถูกต้อง.");
					$(send.sys).focus();
				} else {
					let msg = "คุณต้องการดาวน์โหลดไฟล์หลักฐานสำหรับการ", name = {
						sys: document.querySelector('main form [name="system"] option:checked')
					}; msg += name.sys.innerText + "ของ" + name.sys.parentNode.label + "ใช่หรือไม่ ?";
					if (confirm(msg)) process(send.sys.value.trim());
				}
			}()); return false;
		},
		process = function(type) {
			sv.processing = true;
			app.nav.confirmLeave();
			$("app[name=main] main .form button").attr("disabled", "").toggleClass("orange green").html(cv.btn[1]);
			sv.msgID = app.UI.notify(1, "Zipping files...", 0);
			app.Util.ajax(cv.API_URL, {act: "merge", cmd: "squash", param: type}).then(function(dat) {
				app.UI.notify.close(sv.msgID);
				if (dat) {
					app.UI.notify(0, "Files zipped.", 10);
					grepFile(dat.token);
				} else {
					$("app[name=main] main .form button").removeAttr("disabled", "").toggleClass("orange green").html(cv.btn[0]);
					app.nav.confirmLeave(false);
					sv.processing = false;
				}
			});
		},
		grepFile = function(token) {
			sv.msgID = app.UI.notify(1, "Downloading zipped files...", 0);
			app.UI.lightbox("center", {allowClose: false}, $("app[name=main] main .progress-template").html());
			app.Util.ajax(cv.API_URL, {act: "get", cmd: "info", param: token}).then(async function(dat) {
				if (!dat) {
					app.UI.lightbox.close();
					app.UI.notify.close(sv.msgID);
					$("app[name=main] main .form button").removeAttr("disabled", "").toggleClass("orange green").html(cv.btn[0]);
					app.nav.confirmLeave(false);
					sv.processing = false;
					return;
				} var chunks = [], bytesRead = 0, offset = 0,
					download = d.querySelector("app[name=main] main [name=dl-link]");
				while (true) {
					let bytesLimit = Math.min(bytesRead + cv.chunk_size, dat.filesize) - 1;
					const file = await fetch(cv.API_URL, {
						method: "POST",
						headers: {Range: `bytes=${bytesRead}-${bytesLimit}`},
						body: new URLSearchParams(`act=get&cmd=file&param[token]=${token}&param[range]=${bytesRead}-${bytesLimit}`)
					}); const chunk = await file.arrayBuffer();
					chunks.push(chunk);
					bytesRead += chunk.byteLength;
					updateProgress(bytesRead / dat.filesize * 100);
					if (bytesRead >= dat.filesize) break;
				} const fileObject = new Blob(chunks, {type: dat.mime});
				const fileURL = URL.createObjectURL(fileObject);
				// real download action
				download.download = dat.filename;
				download.href = fileURL;
				download.click();
				// After download success
				setTimeout(function() {
					app.UI.lightbox.close();
					app.UI.notify.close(sv.msgID);
					app.UI.notify(0, "Zipped file downloaded.");
					setTimeout(function() {
						URL.revokeObjectURL(fileURL);
						$("app[name=main] main .form button").removeAttr("disabled", "").toggleClass("orange green").html(cv.btn[0]);
						$(download).removeAttr("href download");
						app.nav.confirmLeave(false);
						sv.processing = false;
					}, 1e3);
				}, sv.barUpdating != null ? 1e3 * cv.barSpeed : 0);
			});
		},
		updateProgress = function(newValue) {
			var bar = d.querySelector("app[name=main] .lightbox .progression progress");
			var currentValue = bar.value;
			if (sv.barUpdating != null) clearInterval(sv.barUpdating);
			var increment = (newValue - currentValue) / cv.barSmoothness;
			sv.barUpdating = setInterval(function() {
				bar.value += (bar.value + cv.barSmoothness <= newValue ? cv.barSmoothness : newValue - bar.value);
				if (bar.value < newValue) return;
				clearInterval(sv.barUpdating);
				sv.barUpdating = null;
			}, 1e3 * cv.barSpeed / increment);
		};
		return {
			init: initialize,
			verify
		};
	}(document));
</script>
<?php $APP_PAGE -> print -> nav("enroll"); ?>
<main>
	<section class="container">
		<h2><?=$header["title"]?></h2>
		<form class="form form-bs message pink">
			<p>การดาวน์โหลดไฟล์หลักฐาน จะเป็นการรวบรวมไฟล์ทั้งหมดในหมวดหมู่เป็นรูปแบบ .zip เพื่อให้การดาวน์โหลดไฟล์ทำสำเร็จได้ในครั้งเดียว</p>
			<p class="message yellow">ควรใช้งานหน้านี้ผ่านคอมพิวเตอร์หรือโน้ตบุ้ค เนื่องจากประเภทไฟล์นำออกไม่เหมาะสมกับการเปิดบนโทรศัพท์มือถือหรือแท็บเล็ท และไฟล์มีขนาดใหญ่มาก</p>
			<div class="group">
				<label>หมวดหมู่</label>
				<select name="system">
					<option value disabled selected>—กรุณาเลือก—</option>
					<optgroup label="นักเรียนเดิม">
						<option value="prs">รายงานตัว</option>
						<option value="cng">เปลี่ยนกลุ่มการเรียน</option>
						<option value="cnf">ยืนยันสิทธิ์</option>
					</optgroup>
					<optgroup label="นักเรียนใหม่">
						<option value="new">รายงานตัว</option>
					</optgroup>
				</select>
			</div>
			<center><button class="green ripple-click" onClick="return page.verify()">
				<i class="material-icons">download</i>
				<span class="text">ดาวน์โหลด</span>
			</button></center>
		</form>
		<iframe name="dl-frame" hidden></iframe>
		<a name="dl-link" hidden></a>
		<div class="progress-template" hidden>
			<div class="progression">
				<h3 class="center">Do not close this window</h3>
				<div class="bar center">
					<progress class="decor green large stripes" value="0" max="100"></progress>
				</div>
			</div>
		</div>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials(side_panel: "enroll");
	$APP_PAGE -> print -> footer();
?>