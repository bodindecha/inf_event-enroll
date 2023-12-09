<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "ดาวน์โหลดไฟล์หลักฐาน (ทั้งหมด)";
	$home_menu = "settings";

	require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
	if (!has_perm("dev", false)) $TCL -> http_response_code(903);

	$has_perm = has_perm("admission");
	if (!$has_perm) {
		require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
		$TCL -> http_response_code(901);
	}
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($APP_RootDir."private/block/core/heading.php"); require($APP_RootDir."private/script/start/CSS-JS.php"); ?>
		<style type="text/css">
			app[name=main] main .form p { margin: 0; }
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
					], chunk_size: Math.pow(1024, 2) * 5, // 5 MB
					TypeAvail: ["prs", "cng", "cnf", "new"]
				};
				var sv = {inited: false, processing: false, msgID: ""};
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
					$("app[name=main] main .form button").attr("disabled", "").toggleClass("orange green").html(cv.btn[1]);
					sv.msgID = app.UI.notify(1, "Zipping files...", 0);
					app.Util.ajax(cv.API_URL, {act: "merge", cmd: "squash", param: type}).then(function(dat) {
						app.UI.notify.close(sv.msgID);
						if (dat) {
							app.UI.notify(0, "Files zipped.", 10);
							grepFile(dat.token);
						} else {
							$("app[name=main] main .form button").removeAttr("disabled", "").toggleClass("orange green").html(cv.btn[0]);
							sv.processing = false;
						}
					});
				},
				grepFile = function(token) {
					sv.msgID = app.UI.notify(1, "Downloading zipped files...", 0);
					const fetcher = new XMLHttpRequest(),
						command = new FormData();
					command.append("act", "get");
					command.append("cmd", "info");
					command.append("param[token]", token);
					fetcher.open("POST", cv.API_URL);
					fetcher.responseType = "blob";
					fetcher.onload = function() {
						const blob = fetcher.response;
						const fileSize = blob.size;
						const chunks = Math.ceil(fileSize / cv.chunk_size);
						let chunk = 0;
						const getChunk = function() {
							var byte = {start: chunk * cv.chunk_size};
							byte.end = Math.min(byte.start + cv.chunk_size, fileSize);
							const data = blob.slice(byte.start, byte.end),
								grepChunk = new XMLHttpRequest(),
								buffer = new FormData();
							buffer.append("act", "get");
							buffer.append("cmd", "part");
							buffer.append("param[token]", token);
							buffer.append("param[part]", data);
							buffer.append("param[chunk]", chunk);
							buffer.append("param[all]", chunks);
							grepChunk.open("POST", cv.API_URL);
							grepChunk.onload = function() {
								chunk += 1;
								if (chunk < chunks) return getChunk();
								// real download action
								var download = d.querySelector("app[name=main] main [name=dlframe]");
								download.src = cv.API_URL + "?act=get&cmd=file&param[token]=" + token;
								// After download success
								app.UI.notify.close(sv.msgID);
								app.UI.notify(0, "Zipped file downloaded.");
								setTimeout(function() {
									$("app[name=main] main .form button").removeAttr("disabled", "").toggleClass("orange green").html(cv.btn[0]);
									$(download).removeAttr("src");
									sv.processing = false;
								}, 1e3);
							}; grepChunk.send(buffer);
						}; getChunk();
					}; fetcher.send(command);
				};
				return {
					init: initialize,
					verify
				};
			}(document));
		</script>
	</head>
	<body>
		<app name="main">
			<?php require($APP_RootDir."private/block/core/top-panel/enroll.php"); ?>
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
					<iframe name="dlframe" hidden></iframe>
				</section>
			</main>
			<?php
				$resourcePath["navtab"] = "private/block/core/side-panel/enroll.php";
				require($APP_RootDir."private/block/core/material/main.php");
			?>
		</app>
	</body>
</html>