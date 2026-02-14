<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "ระบบเปลี่ยนแปลงการใช้สิทธิ์เข้าศึกษาต่อ";
	$header["desc"] = "นักเรียนเดิม";

	$backPage = "confirm";
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	app[name=main] > main .text-middle { align-items: center; }
	app[name=main] > main .css-flex :where(.latest, h3) { margin-bottom: 0; }
	app[name=main] > main .ref-00001 { font-size: 1.1em; font-weight: bold; }
	app[name=main] > main .ahead i { font-size: .9em; line-height: 1.5; }
	app[name=main] > main .preview-file { margin-left: 2.5px; }
	app[name=main] > main .preview-file.unavailable { display: none !important; }
	app[name=main] > main .form .school > a { cursor: help; }
	app[name=main] > main .form .school > a + span { display: none; }
	app[name=main] > main .form .school > a:where(:hover, :focus) + span, app[name=main] > main .form .school > a + span:where(:hover, :focus-within) { display: inline; }
	app[name=main] > main .form .evi-file { border-radius: .3rem; }
	app[name=main] > main .form .reason { border-collapse: collapse; }
	app[name=main] > main .form .reason label { border-radius: var(--form-bdr-rad) var(--form-bdr-rad) 0 0 !important; }
	app[name=main] > main .form .reason textarea {
		margin-top: -1px;
		border-top-left-radius: 0;
	}
	app[name=main] > main .history .table { border-radius: .3rem; }
	app[name=main] > main .history hr {
		margin: 2.5px 0;
		width: 75%;
	}
</style>
<script type="text/javascript">
	const TRANSLATION = location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+");
	$(document).ready(function() {
		page.init();
	});
	const page = (function(d) {
		const cv = {
			API_URL: AppConfig.APIbase + "enroll/v1/returning-std",
			option: chose => chose ? "ยืนยันสิทธิ์" : "สละสิทธิ์",
			MaxLength: 200
		};
		var sv = {
			inited: false,
			historyLoaded: false,
			reasons: {},
			school: null
		}, loading;
		var initialize = function() {
			if (sv.inited) return;
			loading = $("app[name=main] > main .status");
			setTimeout(checkState, 750);
			$("app[name=main] > main .history").on("click", loadHistory);
			$("app[name=main] > main .school input[name=school]").on("focus", selectSchool);
			sv.file = new FileUploadHandler.Watch("app[name=main] > main .form [name=usf]", {
				preview: $("app[name=main] > main .form div.file-box"),
				property: {
					name: d.querySelector("app[name=main] > main .form [data-name=name]"),
					size: d.querySelector("app[name=main] > main .form [data-name=size]")
				}, options: {
					sizeLimit: FileUploadHandler.convertSize.MB2B(10),
					types: ["png", "jpg", "jpeg", "gif", "heic", "pdf"]
				}, message: {
					noFile: `กรุณาเลือกไฟล์หลักฐาน`
				}
			});
			sv.inited = true;
		};
		var checkState = function(update = false) {
			if (typeof update === "object") update = false;
			app.Util.ajax(cv.API_URL, {act: "get", cmd: "status"}).then(function(dat) {
				if (!update) loading.hide();
				if (!dat) return;
				if (!dat.chosen) {
					$("app[name=main] > main .ahead").toggle("clip");
					$("app[name=main] > main a.back[role=button]").toggleClass("hollow black orange suggest-wave");
					setTimeout(function() {
						location.assign("<?=$backPage?>");
					}, 1e4);
				} else {
					if (!update) {
						$("app[name=main] > main .latest").toggle("blind");
						$("app[name=main] > main ." + (dat.choose ? "form" : "cannot-switch")).toggle("blind");
					} else $("app[name=main] > main :where(.form, .cannot-switch)").toggle("blind");
					if (dat.hasRecords) $("app[name=main] > main .history").fadeIn();
					$("app[name=main] > main .preview-file")[dat.hasUploadedDocument ? "removeClass" : "addClass"]("unavailable");
					// Fill data
					fillInfo("group", dat.group);
					fillInfo("choose", cv.option(dat.choose));
					fillInfo("at-date", dat.at.date);
					fillInfo("at-time", dat.at.time);
					fillInfo("ip", dat.ip);
					// Fill form
					$("app[name=main] > main .form .from").text(cv.option(dat.choose)).attr("class", "from pastel chip-tag " + (dat.choose ? "green" : "red"));
					$("app[name=main] > main .form .to").text(cv.option(!dat.choose)).attr("class", "to pastel chip-tag " + (!dat.choose ? "green" : "red"));
					$("app[name=main] > main .form .switch .text").text(cv.option(!dat.choose)).parent().attr("class", "switch xwide ripple-click " + (!dat.choose ? "green" : "red"));
					// Alter elements
					$("app[name=main] > main .form .school")[dat.choose ? "fadeIn" : "fadeOut"]();
				} sv.chose = dat.choose;
			});
		},
		fillInfo = function(field, info) {
			$('app[name=main] > main output[name="' + field + '"]').val(info);
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
			if (!sv.chose) return app.UI.notify(1, "คุณไม่สามารถยืนยันสิทธิ์ได้");
			var reason = $("app[name=main] > main .form .reason textarea").val();
			if (reason.length > cv.MaxLength) return app.UI.notify(2, "Your note is too long (limit 200 characters)");
			// if (sv.chose && dat.school == null) return app.UI.notify(1, "You must provide an attending school name");
			if (sv.chose && !sv.file.validate(true)) return $("app[name=main] > main .form [name=usf]").focus();
			if (!confirm("Are you sure you want to change your rights?")) return;
			$("app[name=main] > main .form").attr("disabled", "");
			var details = { reason, school: sv.school };
			if (sv.chose) {
				sv.file.uploadTo(cv.API_URL,
					{act: "request", cmd: "add_evi_file"}, {
						form: $("app[name=main] > main .form"),
						buttons: $("app[name=main] > main .form button"),
						uploadIcon: $("app[name=main] > main .loading")
					},
					function(dat) {
						if (dat) return processRequest(details);
						$("app[name=main] > main .form").removeAttr("disabled");
					}
				);
			} else processRequest(details);
		},
		processRequest = function(details) {
			app.Util.ajax(cv.API_URL, {act: "request", cmd: "switch", param: details}).then(function(dat) {
				$("app[name=main] > main .form").removeAttr("disabled");
				if (!dat) return;
				checkState(true);
				if (sv.historyLoaded) loadHistory(true);
				$("app[name=main] > main .form .reason textarea").val("");
				sv.school = null; $("app[name=main] > main .school input[name=school]").val("");
				app.UI.notify(0, "เปลี่ยนแปลงสิทธิ์เข้าศึกษาต่อสำเร็จ");
			});
		},
		getNotes = function(me) {
			var box = $(me.parentNode.parentNode);
			me = $(me);
			var reference = box.attr("data-reference");
			if (reference in sv.reasons) box.html(sv.reasons[reference]);
			else {
				me.attr("disabled", "");
				app.Util.ajax(cv.API_URL, {act: "get", cmd: "memorandum", param: {source: "switch", ref: reference}}).then(function(dat) {
					if (!dat) return me.removeAttr("disabled");
					box.html(dat);
					sv.reasons[reference] = dat;
				});
			}
		},
		selectSchool = function() {
			fs.school("เลือกสถานศึกษา", function() {
				var display = $("app[name=main] .school input[name=school]");
				if (!arguments.length) {
					sv.school = null;
					display.val("");
				} else {
					sv.school = arguments[0];
					sv.school.ID = parseInt(sv.school.ID);
					display.val(sv.school.name);
				}
			});
		},
		action = function(act, m, e) {
			if (app.IO.kbd.ctrl() || top.app.UI.lightbox.isOpen()) return;
			if (e.preventDefault) e.preventDefault();
			var frameHTML = `<div class="page-frame" data-action="${act.toLowerCase()}"><iframe src="${m.href}">Loading...</iframe></div>`;
			if (act == "User") return app.UI.lightbox("top", {title: "View " + act, exitTap: true, allowScroll: true}, frameHTML);
			top.app.UI.lightbox("center", {title: m.innerText.substring(10, m.innerText.length), exitTap: false}, frameHTML);
		};
		return {
			init: initialize,
			action,
			request,
			getNotes
		};
	}(document));
</script>
<script type="text/javascript" src="<?=$APP_CONST["baseURL"]?>_resx/plugin/TianTcl/find-search/data.js"></script>
<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/core/fileUploadHandler.js"></script>
<?php $APP_PAGE -> print -> nav("enroll"); ?>
<main>
	<section class="container">
		<h2>ระบบเปลี่ยนแปลงการใช้สิทธิ์เข้าศึกษาต่อ ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h2>
		<div class="css-flex css-flex-gap-5 text-middle">
			<a role="button" class="back black icon hollow ripple-click css-flex-noshrink" href="<?=$backPage?>">
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
				onClick="page.action('file', this, event)"
			>
				<i class="material-icons">visibility</i>
				<span class="text">ไฟล์หลักฐาน</span>
			</a>
		</p>
		<center class="cannot-switch message pink" style="display: none;">คุณไม่สามารถเปลี่ยนแปลงสิทธิ์เป็นยืนยันสิทธิ์ได้</center>
		<form class="form form-bs message gray" style="display: none;">
			<div class="css-flex css-flex-gap-10 text-middle">
				<h3>เปลี่ยนแปลงสถานะ</h3>
				<div class="from chip-tag pastel"></div>
				<i class="material-icons">arrow_forward</i>
				<div class="to chip-tag pastel"></div>
			</div>
			<div class="school css-flex css-flex-gap-5 css-flex-wrap">
				<span class="ref-00003 css-text-middle">กรณีศึกษาต่อโรงเรียนอื่น โปรดระบุ</span>
				<div class="group">
					<label>ชื่อโรงเรียน</label>
					<input type="text" name="school" />
				</div>
				<a class="css-text-middle" href="javascript:"><i class="material-icons">help</i></a>
				<span class="css-text-middle">
					<span class="ref-00004">หากไม่พบชื่อโรงเรียน กรุณาติดต่อ</span>
					<a href="<?=$APP_CONST["baseURL"]?>v2/ticket/create#&subj=เพิ่มชื่อสถานศึกษาในตัวเลือก&app=3&cat=6" target="_blank">สร้างคำร้องผ่านระบบ</a>
				</span>
			</div>
			<fieldset class="evi-file">
				<legend>กรุณาอัปโหลดไฟล์หลักฐานการสละสิทธิ์</legend>
				<div class="css-flex css-flex-gap-10 css-flex-autodir">
					<div class="file-box land r-widescr">
						<input type="file" name="usf" accept=".png, .jpg, .jpeg, .gif, .heic, .pdf" required />
					</div>
					<div class="css-flex css-flex-col css-flex-split css-full-x">
						<div class="container">
							<div class="group">
								<label class="ref-00005">ชื่อไฟล์</label>
								<input type="text" data-name="name" readonly />
							</div>
							<div class="group">
								<label class="ref-00006">ขนาดไฟล์</label>
								<input type="text" data-name="size" readonly />
							</div>
						</div>
						<div class="left">
							<a role="button" class="hollow icon long pill ripple-click" href="<?=$APP_CONST["baseURL"]?>e/enroll/form/rwaive" target="_blank" download="ฟอร์มสละสิทธิ์.pdf"><i class="material-icons">download</i><span class="text ref-00007">ฟอร์มสละสิทธิ์</span></a>
						</div>
					</div>
				</div>
			</fieldset>
			<div class="reason">
				<div class="group"><label>บันทึกช่วยจำ (เหตุผลการเปลี่ยนแปลง)</label></div>
				<textarea name="reason" class="resize-y"
					placeholder="เพิ่มบันทึก..."
					rows="3" maxlength="200"
				></textarea>
			</div>
			<div class="group split">&nbsp;<button class="switch xwide ripple-click" onClick="page.request(); return false;"><span class="text"></span></button></div>
		</form>
		<details class="history card message cyan" style="display: none;">
			<summary>ประวัติการเปลี่ยนแปลงสิทธิ์</summary>
			<div class="table static responsive"><table><thead>
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
	$APP_PAGE -> print -> materials(side_panel: "enroll");
	$APP_PAGE -> print -> footer();
?>