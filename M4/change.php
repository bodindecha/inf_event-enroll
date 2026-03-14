<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "ระบบยื่นคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน";
	$header["desc"] = "นักเรียนเดิม";

	$year = $_SESSION["stif"]["t_year"] ?? null;
	$year = $year ? $year + 1 : (int)date("Y") + 543;

	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	
</style>
<script type="text/javascript">
	const TRANSLATION = ["e+enroll+M4+API", location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+")];
	$(document).ready(function() {
		cng.init();
	});
	const cng = (function(d) {
		const cv = {
			API_URL: AppConfig.APIbase + "enroll/v1/returning-std",
			API_MSG_INFO: {
				pack: 2, prefix: "API_MSG_", map: [
					[], [4], [1, 2, 5, 7, 8, 9, 10], [3, 50, 51, 6]
				]
			},
			groupNotFound: "<i>ไม่พบข้อมูลกลุ่มการเรียน</i>",
			groups: null
		};
		var sv = {inited: false};
		const field = name => $(`app[name=main] > main [name=${name}]`),
			secbox = name => $(`app[name=main] > main .${name}`),
			groupName = code => cv.groups[code]?.name ?? cv.groupNotFound;
		var initialize = function() {
			if (sv.inited) return;
			sv.file = new FileUploadHandler.Watch("app[name=main] > main .form [name=usf]", {
				preview: $("app[name=main] > main .form div.file-box"),
				property: {
					name: d.querySelector("app[name=main] > main .form [data-name=name]"),
					size: d.querySelector("app[name=main] > main .form [data-name=size]")
				}, options: {
					sizeLimit: FileUploadHandler.convertSize.MB2B(10),
					types: ["png", "jpg", "jpeg", "gif", "heic", "pdf"]
				}, message: {
					noFile: `กรุณาเลือกไฟล์ใบคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน`
				}
			}); getAll();
			$("app[name=main] > main .form").on("change", validate);
			sv.inited = true;
		};
		var getAll = function() {
			app.Util.ajax(cv.API_URL, {act: "get", cmd: "change"}).then(function(dat) {
				secbox("loading").hide();
				if (!dat) return;
				if (!dat.hasChance) return secbox("no-right").show();
				if (!dat.groups) secbox("form").prop("disabled", true);
				else cv.groups = dat.groups;
				field("placement").text(groupName(dat.placement));
				secbox("profile").show();
				if (dat.chose) {
					field("sent-group").text(groupName(dat.chose.group));
					field("date-sent").text(dat.chose.time);
					field("IP-sent").text(dat.chose.ip);
					secbox("sent").show();
				} if (!dat.available) {
					if (!dat.chosen) secbox("timeout").show();
					return;
				} field("date-limit").text(dat.slotEnds);
				secbox("date-range").show();
				if (typeof cv.groups == "object") Object.values(cv.groups).forEach(function(eg) {
					if (eg.code == dat.placement) return;
					field("group").append(`<option value="${eg.code}">${eg.name}</option>`);
				}); secbox("form").show();
				secbox("instruction").show();
			});
		},
		validate = function(suspressNoti) {
			var btn = $("app[name=main] > main .form button");
			if (!field("group").val()) return btn.prop("disabled", true);
			btn.prop("disabled", !sv.file.validate(!suspressNoti));
		},
		request = function() {
			var group = field("group").val();
			if (!group) {
				field("group").focus();
				return app.UI.notify(1, app._var.translationDic()[3].messages["please-select-group"][app.settings["lang"]]);
			} if (!sv.file.validate(false)) return field("usf").focus();
			sv.file.uploadTo(cv.API_URL,
				{act: "request", cmd: "commit", param: {to: "change", answer: group}}, {
					form: $("app[name=main] > main .form"),
					buttons: $("app[name=main] > main .form button"),
					uploadIcon: $("app[name=main] > main .loading")
				},
				function(dat) {
					if (!dat) return;
					app.UI.notify(0, app._var.translationDic()[3].messages["change-success"][app.settings["lang"]]);
					if (dat.available) d.querySelector("app[name=main] > main .form").reset();
					else {
						$("app[name=main] > main .form").fadeOut();
						secbox("instruction").toggle("clip");
						secbox("date-range").fadeOut();
					} // New info
					field("sent-group").text(groupName(dat.chose.group));
					field("date-sent").text(dat.chose.time);
					field("IP-sent").text(dat.chose.ip);
					if (!secbox("sent").is(":visible")) secbox("sent").toggle("blind");
				},
				cv.API_MSG_INFO
			);
		},
		action = function(act, m, e) {
			if (app.IO.kbd.ctrl() || top.app.UI.lightbox.isOpen()) return;
			if (e.preventDefault) e.preventDefault();
			var frameHTML = `<div class="page-frame" data-action="${act.toLowerCase()}"><iframe src="${m.href}">Loading...</iframe></div>`;
			if (act == "User") return app.UI.lightbox("top", {title: "View " + act, exitTap: true, allowScroll: true}, frameHTML);
			top.app.UI.lightbox("center", {title: act, exitTap: false}, frameHTML);
		};
		return {
			init: initialize,
			request,
			action
		};
	}(document));
</script>
<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/core/fileUploadHandler.js"></script>
<?php $APP_PAGE -> print -> nav("enroll"); ?>
<main>
	<section class="container">
		<h2><?=$header["title"]?> ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h2>
		<p><span class="ref-00001">การยื่นคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน ประเภทห้องเรียนปกติ จากนักเรียนที่จบชั้นมัธยมศึกษาปีที่ 3 ของโรงเรียนเดิม ปีการศึกษา</span> <?=$year?></p>
		<div class="no-right message red css-text-center" style="display: none;">นักเรียนไม่มีสิทธิ์ยื่นคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน ประเภทห้องเรียนปกติ</div>
		<p class="profile" style="display: none;"><span class="ref-00002">ผลการจัดกลุ่มการเรียนของ</span><u><?=$_SESSION["auth"]["name"]["th"]["a"]?></u> <span class="ref-00003">คือกลุ่มการเรียน</span><u><output name="placement"></output></u><span class="ref-00015"></span></p>
		<div class="timeout message gray css-text-center" style="display: none;">ขณะนี้อยู่นอกช่วงเวลาในการยื่นคำร้องขอเปลี่ยนแปลงกลุ่มการเรียนของนักเรียน</div>
		<blockquote class="date-range tab-line cyan unblock translucent" style="display: none;"><span class="ref-00004">นักเรียนสามารถยื่นคำร้องได้ภายใน</span><output name="date-limit"></output></blockquote>
		<div class="sent message green" style="display: none;">
			<span class="ref-00005">นักเรียนได้ยื่นคำร้องขอเปลี่ยนแปลงกลุ่มการเรียนเป็น</span><u><output name="sent-group"></output></u><span class="ref-00006">เรียบร้อยแล้ว</span><br><span class="ref-00007">ครั้งล่าสุดเมื่อ</span><output name="date-sent"></output> <span class="ref-00008">ผ่านที่อยู่ IP</span> <output name="IP-sent"></output><br>
			<a
				role="button" class="gray icon long pill ripple-click"
				href="<?=$APP_CONST["baseURL"]?>e/enroll/resource/upload/view?type=change"
				onClick="return cng.action('File', this, event)"
			><i class="material-icons">visibility</i> <span class="ref-00009">ไฟล์หลักฐาน</span></a>
		</div>
		<!-- instruction -->
		<?php include("$APP_RootDir$APP_CONST[publicDir]$APP_CONST[baseURL]e/enroll/resource/upload/direction/change.html"); ?>
		<!-- Form -->
		<form class="form form-bs message blue" style="display: none;">
			<p class="ref-00010 css-margin-bottom-0">เลือกกลุ่มการเรียนที่ต้องการ อัปโหลดไฟล์ใบคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน แล้วกดปุ่ม "ยืนยันการขอเปลี่ยนแปลงกลุ่มการเรียน"</p>
			<div class="group">
				<label class="nowrap ref-00011">กลุ่มการเรียน</label>
				<select name="group">
					<option value disabled selected>—กรุณาเลือกกลุ่มการเรียน—</option>
				</select>
			</div>
			<fieldset class="evi-file">
				<legend>กรุณาอัปโหลดไฟล์ใบคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน</legend>
				<div class="css-flex css-flex-gap-10 css-flex-autodir">
					<div class="file-box land r-widescr">
						<input type="file" name="usf" accept=".png, .jpg, .jpeg, .gif, .heic, .pdf" required />
					</div>
					<div class="css-flex css-flex-col css-flex-split css-flex-gap-10 css-full-x">
						<div class="container">
							<div class="group">
								<label class="ref-00012">ชื่อไฟล์</label>
								<input type="text" data-name="name" readonly />
							</div>
							<div class="group">
								<label class="ref-00013">ขนาดไฟล์</label>
								<input type="text" data-name="size" readonly />
							</div>
						</div>
						<div class="left">
							<a
								role="button" class="default icon long pill ripple-click"
								href="<?=$APP_CONST["baseURL"]?>e/enroll/form/csgrf"
								target="_blank" download="ฟอร์มคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน.pdf"
							><i class="material-icons">download</i><span class="text ref-00014">ฟอร์มคำร้องขอเปลี่ยนแปลงกลุ่มการเรียน</span></a>
						</div>
					</div>
				</div>
			</fieldset>
			<div class="group spread">
				<button class="yellow wide ripple-click" onClick="cng.request(); return false;" disabled>ยืนยันการขอเปลี่ยนแปลงกลุ่มการเรียน</button>
			</div>
		</form>
		<div class="loading css-full-x"></div>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials(side_panel: "enroll");
	$APP_PAGE -> print -> footer();
?>