<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "ระบบเปลี่ยนแปลงการใช้สิทธิ์เข้าศึกษาต่อ";
	$header["desc"] = "นักเรียนเดิม";

	if ($APP_USER <> "99999" && !has_perm("dev")) {
		require($APP_RootDir."private/script/lib/TianTcl/various.php");
		$TCL -> http_response_code(909);
	}

	$backPage = "confirm";
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($APP_RootDir."private/block/core/heading.php"); require($APP_RootDir."private/script/start/CSS-JS.php"); ?>
		<style type="text/css">
			app[name=main] main .text-middle { align-items: center; }
			app[name=main] main .css-flex :where(.latest, h3) { margin-bottom: 0; }
			app[name=main] main .ref-00001 { font-size: 1.1em; font-weight: bold; }
			app[name=main] main .ahead i { font-size: .9em; line-height: 1.5; }
			app[name=main] main .preview-file { margin-left: 2.5px; }
			app[name=main] main .preview-file.unavailable { display: none !important; }
			app[name=main] main .form :where(.from, .to) { pointer-events: none; }
			app[name=main] main .form .reason { border-collapse: collapse; }
			app[name=main] main .form .reason label { border-radius: var(--form-bdr-rad) var(--form-bdr-rad) 0 0 !important; }
			app[name=main] main .form .reason textarea {
				margin-top: -1px;
				border-top-left-radius: 0;
			}
			app[name=main] main .form .switch { min-width: 125px; }
			app[name=main] main .history .table { border-radius: .3rem; }
		</style>
		<script type="text/javascript">
			const TRANSLATION = location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+");
			$(document).ready(function() {
				page.init();
			});
			const page = (function(d) {
				const cv = {
					API_URL: AppConfig.APIbase + "enroll/v1/current-std",
					option: chose => chose ? "ยืนยันสิทธิ์" : "สละสิทธิ์",
					MaxRetries: 3, MaxLength: 200
				};
				var sv = {
					inited: false,
					historyLoaded: false,
					reasons: {}
				}, loading;
				var initialize = function() {
					if (sv.inited) return;
					loading = $("app[name=main] main .status");
					setTimeout(checkState, 7.5e2);
					$("app[name=main] .history").on("click", loadHistory);
					sv.inited = true;
				};
				var checkState = function(update = false, tries = 0) {
					if (typeof update === "object") update = false;
					app.Util.ajax(cv.API_URL, {act: "get", cmd: "status"}).then(function(dat) {
						if (!update) loading.hide();
						if (!dat) {
							if (update && tries < cv.MaxRetries) checkState(update, tries + 1);
							else return;
						} if (!dat.chosen) {
							$("app[name=main] main .ahead").toggle("clip");
							$("app[name=main] main a.back[role=button]").toggleClass("hollow black orange suggest-wave");
							setTimeout(function() {
								location.assign("<?=$backPage?>");
							}, 1e4);
						} else {
							if (!update) $("app[name=main] main :where(.latest, .form)").toggle("blind");
							if (dat.hasRecords) $("app[name=main] main .history").fadeIn();
							$("app[name=main] main .preview-file")[dat.hasUploadedDocument ? "removeClass" : "addClass"]("unavailable");
							// Fill data
							fillInfo("group", dat.group);
							fillInfo("choose", cv.option(dat.choose));
							fillInfo("at-date", dat.at.date);
							fillInfo("at-time", dat.at.time);
							fillInfo("ip", dat.ip);
							// Fill form
							$("app[name=main] main .form .from").text(cv.option(!dat.choose)).attr("class", "from pill " + (dat.choose ? "green" : "red"));
							$("app[name=main] main .form .to").text(cv.option(dat.choose)).attr("class", "to pill " + (!dat.choose ? "green" : "red"));
							$("app[name=main] main .form .switch .text").text(cv.option(!dat.choose)).parent().attr("class", "switch ripple-click " + (!dat.choose ? "green" : "red"));
						}
					});
				},
				fillInfo = function(field, info) {
					$('app[name=main] main output[name="' + field + '"]').val(info);
				},
				intercept = function(m, e) {
					if (e.ctrlKey) return; // window.open(m.href);
					else if (typeof e.preventDefault == "function") e.preventDefault();
					app.UI.lightbox("center", {title: m.innerText.substring(10, m.innerText.length), allowClose: true, autoClose: 3e5},
						'<iframe src="'+m.href+'" style="width:90vw;height:80vh;border:none">Loading...</iframe>'
					);
				},
				loadHistory = function(update = false) {
					if (typeof update === "object") update = false;
					if (sv.historyLoaded && !update) return;
					app.Util.ajax(cv.API_URL, {act: "get", cmd: "history"}).then(function(dat) {
						if (!dat) return $("app[name=main] .history").fadeOut(1e3);
						var decrement = dat.length, table = [], options;
						dat.forEach(er => {
							options = "";
							if (er.hasMemorandum) {
								if (er.reference in sv.reasons) options = sv.reasons[er.reference];
								else options = '<div class="center"><a role="button" class="blue bare small" onClick="page.getNotes(this)" href="javascript:"><i class="material-icons">description</i> <span class="text">แสดงบันทึก</span></a></div>';
							}
							table.push('<td class="center">' + (decrement--).toString() + '</td>'+
								'<td>' + er.timestamp + '</td>'+
								'<td class="center">' + cv.option(!er.newChoice) + '</td>'+
								'<td class="center">' + cv.option(er.newChoice) + '</td>'+
								'<td data-reference="' + er.reference + '">' + options + '</td>'
							);
						}); $("app[name=main] .history tbody").html('<tr>' + table.join('</tr><tr>') + '</tr>');
						app.UI.language.load();
						app.UI.refineElements();
					});
					if (!update) {
						$("app[name=main] .history").off("click");
						sv.historyLoaded = true;
					}
				},
				request = function() {
					(function() {
						var reason = $("main .form .reason textarea").val();
						if (reason.length > cv.MaxLength) return app.UI.notify(2, "Your note is too long (limit 200 characters)");
						if (!confirm("Are you sure you want to change your rights?")) return;
						$("app[name=main] main .form").attr("disabled", "");
						app.Util.ajax(cv.API_URL, {act: "answer", cmd: "switch", param: reason}).then(function(dat) {
							$("app[name=main] main .form").removeAttr("disabled");
							if (!dat) return;
							checkState(true);
							if (sv.historyLoaded) loadHistory(true);
							$("main .form .reason textarea").val("");
							app.UI.notify(0, "เปลี่ยนแปลงสิทธิ์เข้าศึกษาต่อสำเร็จ");
						});
					}()); return false;
				},
				getNotes = function(me) {
					var box = $(me.parentNode.parentNode);
					me = $(me);
					var reference = box.attr("data-reference");
					if (reference in sv.reasons) box.html(sv.reasons[reference]);
					else {
						me.attr("disabled", "");
						app.Util.ajax(cv.API_URL, {act: "get", cmd: "memorandum", param: reference}).then(function(dat) {
							if (!dat) return me.removeAttr("disabled");
							box.html(dat);
							sv.reasons[reference] = dat;
						});
					}
				};
				return {
					init: initialize,
					intercept,
					request,
					getNotes
				};
			}(document));
		</script>
	</head>
	<body>
		<app name="main">
			<?php require($APP_RootDir."private/block/core/top-panel/enroll.php"); ?>
			<main>
				<section class="container">
					<h2>ระบบเปลี่ยนแปลงการใช้สิทธิ์เข้าศึกษาต่อ ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h2>
					<div class="css-flex css-flex-gap-5 text-middle">
						<a role="button" class="back black icon hollow ripple-click" href="<?=$backPage?>">
							<i class="material-icons">arrow_back</i>
							<span class="text">ย้อนกลับ</span>
						</a>
						<div class="status css-flex text-middle">
							<div class="loading"></div>
							<span>กำลังตรวจสอบข้อมูล</span>
						</div>
						<p class="latest" style="display: none;">
							<?=$_SESSION["auth"]["name"]["th"]["a"]?> ประเภทห้องเรียนปกติ กลุ่มการเรียน<u><output name="group"></output></u>
						</p>
					</div>
					<center class="ahead message orange" style="display: none;">
						<span class="ref-00001">คุณยังไม่ได้ใช้สิทธิ์ กรุณาเลือกใช้สิทธิ์</span><hr>
						<span class="ref-00002">หากคุณใช้สิทธิ์แล้วและต้องการเปลี่ยนแปลงให้กลับมาที่หน้านี้</span><br>
						<i>ระบบจะนำคุณกลับโดยอัตโนมัติใน 10 วินาที</i>
					</center>
					<p class="latest" style="display: none;">
						นักเรียนได้<b><output name="choose"></output></b>เรียบร้อยแล้วเมื่อวันที่ <output name="at-date"></output> เวลา <output name="at-time"></output> น. ผ่านที่อยู่ IP <output name="ip"></output>
						<a role="button" class="preview-file blue hollow -bare pill ripple-click"
							href="<?=$APP_CONST["baseURL"]?>e/enroll/resource/upload/view?type=confirm"
							onClick="page.intercept(this, event)"
						>
							<i class="material-icons">visibility</i>
							<span class="text">ไฟล์หลักฐาน</span>
						</a>
					</p>
					<form class="form form-bs message gray" style="display: none;">
						<div class="css-flex css-flex-gap-10 text-middle">
							<h3>เปลี่ยนแปลงสถานะ</h3>
							<a class="from pill" role="button"></a>
							<i class="material-icons">arrow_forward</i>
							<a class="to pill" role="button"></a>
						</div>
						<div class="reason">
							<div class="group"><label>บันทึกช่วยจำ (เหตุผลการเปลี่ยนแปลง)</label></div>
							<textarea name="reason" class="resize-y"
								placeholder="เพิ่มบันทึก..."
								rows="3" maxlength="200"
							></textarea>
						</div>
						<div class="group split">&nbsp;<button class="switch ripple-click" onClick="return page.request()"><span class="text"></span></button></div>
					</form>
					<details class="history card message cyan" style="display: none;">
						<summary>ประวัติการเปลี่ยนแปลงสิทธิ์</summary>
						<div class="table responsive"><table><thead>
							<tr>
								<th rowspan="2">ลำดับ</th>
								<th rowspan="2">ประทับเวลา</th>
								<th colspan="2">การเปลี่ยนแปลงสิทธิ์</th>
								<th rowspan="2">เหตุผล</th>
							</tr>
							<tr>
								<th>จาก</th><th>เป็น</th>
							</tr>
						</thead><tbody></tbody></table></div>
					</details>
				</section>
			</main>
			<?php
				$resourcePath["navtab"] = "private/block/core/side-panel/enroll.php";
				require($APP_RootDir."private/block/core/material/main.php");
			?>
		</app>
	</body>
</html>