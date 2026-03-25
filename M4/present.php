<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "ระบบรายงานตัวเข้าศึกษาต่อ";
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
		prs.init();
	});
	const prs = (function(d) {
		const cv = {
			API_URL: AppConfig.APIbase + "enroll/v1/returning-std",
			API_MSG_INFO: {
				pack: 2, prefix: "API_MSG_", map: [
					[], [4, 11], [1, 2, 5, 7, 8, 9, 10], [3, 50, 51, 6]
				]
			},
			option: chose => chose == "Y" ?
				app._var.translationDic()[3].messages["presented"][app.settings["lang"]] :
				app._var.translationDic()[3].messages["waived"][app.settings["lang"]],
			OPTION_YES: "Y",
			OPTION_NO: "N"
		};
		const field = name => $(`app[name=main] > main [name=${name}]`),
			cbox = name => $(`app[name=main] > main .${name}`);
		var sv = {inited: false};
		var initialize = function() {
			if (sv.inited) return;
			setTimeout(getStatus, 750); // Wait for msgPack to load first
			sv.inited = true;
		};
		var getStatus = function() {
			app.Util.ajax(cv.API_URL, {act: "get", cmd: "present"}).then(function(dat) {
				cbox("loading").hide();
				if (!dat) return;
				if (!dat.hasChance) return cbox("no-right").show();
				if (dat.chose) {
					if (dat.chose.option == cv.OPTION_YES) cbox("instruction").show();
					manSubmission(dat);
					return cbox("sent").show();
				} if (!dat.available) {
					if (!dat.chosen) return cbox("timeout").show();
				} field("date-limit").text(dat.slotEnds);
				cbox("date-range").show();
				// Initialize Forms
				cbox("main-f").toggle("fold");
			});
		},
		formAction = async function(dir) {
			switch (dir) {
				case "U": {
					cbox("main-f").toggle("fold");
					cbox("confirmation").toggle("fold");
					sv.file = new FileUploadHandler.Watch("app[name=main] > main .form [name=usf]", {
						preview: $("app[name=main] > main .form div.file-box"),
						property: {
							name: d.querySelector("app[name=main] > main .form [data-name=name]"),
							size: d.querySelector("app[name=main] > main .form [data-name=size]")
						}, options: {
							sizeLimit: FileUploadHandler.convertSize.MB2B(10),
							types: ["png", "jpg", "jpeg", "gif", "heic", "pdf"]
						}, message: {
							noFile: `กรุณาเลือกไฟล์ใบรับรองผลการสมัครเข้าศึกษาต่อ`
						}
					});
					field("usf").on("change", () => validateEvi(true));
				break; }
				case "S": {
					const ans = arguments[1];
					if (ans == cv.OPTION_YES) return requestEvi(ans);
					const btn = cbox("main-f .actions").attr("disabled", "");
					let isConfirmed = await new Promise(function(resolve, reject) {
						app.UI.modal(`<span class="ref-00014">${app._var.translationDic()[3].translations[2][app.settings["lang"]]}</span> <u><?=$_SESSION["auth"]["name"]["th"]["a"]?></u><span class="ref-00003">${app._var.translationDic()[3].translations[3][app.settings["lang"]]}</span> <u><?=$APP_USER?></u><span class="ref-00015">${app._var.translationDic()[3].translations[4][app.settings["lang"]]}</span>`,
							"alert", {
								text: app._var.translationDic()[3].messages["go-on"][app.settings["lang"]],
								jsaction: function() { resolve(true); app.UI.modal.close(); },
							}, {
								position: "top", importance: 1,
								onDismiss: function() { resolve(false); },
								dismissText: app._var.translationDic()[3].messages["retreat"][app.settings["lang"]]
							}
						);
					}); if (isConfirmed) requestSend(ans);
					else btn.removeAttr("disabled");
				break; }
			}
		},
		validateEvi = function(suspressNoti) {
			var btn = cbox("confirmation .submit"),
				pass = false;
			if (!sv.file.validate(!suspressNoti)) {
				if (!suspressNoti) field("usf").focus();
			} else pass = true;
			btn.prop("disabled", !pass);
			return pass;
		},
		requestEvi = function(answer) {
			if (!validateEvi(false)) return;
			var answer = {answer};
			sv.file.uploadTo(cv.API_URL,
				{act: "request", cmd: "commit", param: {to: "present", ...answer}}, {
					form: cbox("confirmation form"),
					buttons: cbox("confirmation .submit"),
					uploadIcon: cbox("loading")
				}, handleResponse, cv.API_MSG_INFO
			);
		},
		requestSend = function(answer) {
			const btn = cbox("main-f .actions");
			cbox("loading").show();
			app.Util.ajax(cv.API_URL,
				{act: "request", cmd: "commit", param: {to: "present", answer}},
				"POST", "json", {}, cv.API_MSG_INFO
			).then(handleResponse).finally(() => {
				cbox("loading").hide();
				btn.removeAttr("disabled");
			});
		},
		handleResponse = function(dat) {
			if (!dat) return;
			app.UI.notify(0, app._var.translationDic()[3].messages["submitted"][app.settings["lang"]]
				.replace("{option}", cv.option(dat.chose.option))
			);
			cbox("main-f").fadeOut();
			cbox("confirmation").fadeOut();
			cbox("date-range").fadeOut();
			// New info
			manSubmission(dat);
			if (!cbox("sent").is(":visible")) cbox("sent").toggle("blind");
		},
		manSubmission = function(dat) {
			field("option").text(cv.option(dat.chose.option));
			field("date-sent").text(dat.chose.time);
			field("IP-sent").text(dat.chose.ip);
			if (dat.chose.option == cv.OPTION_NO) return cbox("sent .gray").remove();
			if (!cbox("instruction").is(":visible")) cbox("instruction").toggle("fold");
		}
		action = function(act, m, e) {
			if (app.IO.kbd.ctrl() || top.app.UI.lightbox.isOpen()) return;
			if (e.preventDefault) e.preventDefault();
			var frameHTML = `<div class="page-frame" data-action="${act.toLowerCase()}"><iframe src="${m.href}">Loading...</iframe></div>`;
			if (act == "User") return app.UI.lightbox("top", {title: "View " + act, exitTap: true, allowScroll: true}, frameHTML);
			top.app.UI.lightbox("center", {title: act, exitTap: false}, frameHTML);
		};
		return {
			init: initialize,
			interact: formAction,
			action
		};
	}(document));
</script>
<script type="text/javascript" src="<?=$APP_CONST["baseURL"]?>_resx/plugin/TianTcl/find-search/data.js"></script>
<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/core/fileUploadHandler.js"></script>
<?php $APP_PAGE -> print -> nav("enroll"); ?>
<main>
	<section class="container">
		<h2><?=$header["title"]?> ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h2>
		<p><span class="ref-00001">การรายงานตัวเข้าศึกษาต่อ ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ประเภทห้องเรียนปกติ จากนักเรียนที่จบชั้นมัธยมศึกษาปีที่ 3 ของโรงเรียนเดิม ปีการศึกษา</span> <?=$year?> <span class="ref-00002">ของ</span><u><?=$_SESSION["auth"]["name"]["th"]["a"]?></u><span class="ref-00003"> รหัสนักเรียน</span> <u><?=$APP_USER?></u></p>
		<div class="no-right message red css-text-center" style="display: none;">นักเรียนไม่มีสิทธิ์รายงานตัวประเภทห้องเรียนปกติ</div>
		<div class="timeout message gray css-text-center" style="display: none;">ขณะนี้อยู่นอกช่วงเวลาในการรายงานตัวของนักเรียน</div>
		<blockquote class="date-range tab-line cyan unblock translucent" style="display: none;"><span class="ref-00004">นักเรียนสามารถเลือกได้เพียง 1 ครั้งเท่านั้น ภายใน</span><output name="date-limit"></output></blockquote>
		<div class="main-f" style="display: none;">
			<p><span class="ref-00005">กดปุ่มเพื่อยืนยัน หรือ สละสิทธิ์ เข้าศึกษาต่อ ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ประเภทห้องเรียนปกติ จากนักเรียนที่จบชั้นมัธยมศึกษาปีที่ 3 ของโรงเรียนเดิม ปีการศึกษา</span> <?=$year?></p>
			<div class="actions css-flex css-flex-spread css-flex-wrap css-flex-gap-10">
				<button class="green wide ripple-click" onClick="prs.interact('U')"><span>ยืนยันสิทธิ์การเข้าศึกษาต่อ</span></button>
				<button class="red wide ripple-click" onClick="prs.interact('S', 'N')"><span>สละสิทธิ์การเข้าศึกษาต่อ</span></button>
			</div>
		</div>
		<div class="confirmation" style="display: none;">
			<h4>การรายงานตัวเข้าศึกษาต่อ</h4>
			<form class="form form-bs">
				<fieldset class="evi-file">
					<legend>กรุณาอัปโหลดไฟล์ใบรับรองผลการสมัครเข้าศึกษาต่อระดับชั้นมัธยมศึกษาปีที่ 4</legend>
					<div class="css-flex css-flex-gap-10 css-flex-autodir">
						<div class="file-box land r-widescr">
							<input type="file" name="usf" accept=".png, .jpg, .jpeg, .gif, .heic, .pdf" required />
						</div>
						<div class="css-flex css-flex-col css-flex-split css-flex-gap-10 css-full-x">
							<div class="container">
								<div class="group">
									<label class="ref-00006">ชื่อไฟล์</label>
									<input type="text" data-name="name" readonly />
								</div>
								<div class="group">
									<label class="ref-00007">ขนาดไฟล์</label>
									<input type="text" data-name="size" readonly />
								</div>
							</div>
							<div class="left">
								<a
									role="button" class="default icon long pill ripple-click"
									href="<?=$APP_CONST["baseURL"]?>e/enroll/form/sggtmf"
									target="_blank" onClick="prs.action('View example', this, event)"
								><i class="material-icons">visibility</i><span class="text ref-00008">ตัวอย่างใบรับรองผลการสมัครเข้าศึกษาต่อ</span></a>
							</div>
						</div>
					</div>
				</fieldset>
				<div class="center">
					<button class="submit blue wide ripple-click" onClick="prs.interact('S', 'Y'); return false;" disabled>บันทึกการรายงานตัว</button>
				</div>
			</form>
		</div>
		<div class="sent message green" style="display: none;">
			<span class="ref-00009">นักเรียนได้</span><u><output name="option"></output></u><span class="ref-00010">เรียบร้อยแล้ว</span><br><span class="ref-00011">ครั้งล่าสุดเมื่อ</span><output name="date-sent"></output> <span class="ref-00012">ผ่านที่อยู่ IP</span> <output name="IP-sent"></output><br>
			<a
				role="button" class="gray icon long pill ripple-click"
				href="<?=$APP_CONST["baseURL"]?>e/enroll/resource/upload/view?type=present"
				onClick="prs.action('Uploaded document', this, event)"
			><i class="material-icons">visibility</i> <span class="ref-00013">ไฟล์ใบรับรองผลการสมัครเข้าศึกษาต่อ</span></a>
		</div>
		<!-- Instruction -->
		<?php require_once("$APP_RootDir$APP_CONST[publicDir]$APP_CONST[baseURL]e/enroll/resource/upload/direction/present.html"); ?>
		<div class="loading css-full-x"></div>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials(side_panel: "enroll");
	$APP_PAGE -> print -> footer();
?>