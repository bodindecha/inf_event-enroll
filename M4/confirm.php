<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "ระบบยืนยันสิทธิ์เข้าศึกษาต่อ";
	$header["desc"] = "นักเรียนเดิม";

	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	app[name=main] > main .step > li {
		border-top: 1px solid var(--clr-bs-blue); border-bottom: 1px solid var(--clr-bs-blue);
		border-collapse: collapse;
		overflow: hidden;
	}
	app[name=main] > main .step > li:not(:first-child) { margin-top: -1px; }
	app[name=main] > main .title, app[name=main] > main .subs > li { padding: 1rem; }
	app[name=main] > main .subs > li { border-top: 1px solid var(--clr-pp-grey-400); }
	app[name=main] > main .subs form { padding-left: 1rem; }
	app[name=main] > main .form .school > a { cursor: help; }
	app[name=main] > main .form .school > a + span { display: none; }
	app[name=main] > main .form .school > a:where(:hover, :focus) + span, app[name=main] > main .form .school > a + span:where(:hover, :focus-within) { display: inline; }
	app[name=main] > main .form .reason { border-collapse: collapse; }
	app[name=main] > main .form .reason label { border-radius: var(--form-bdr-rad) var(--form-bdr-rad) 0 0 !important; }
	app[name=main] > main .form .reason textarea {
		margin-top: -1px;
		border-top-left-radius: 0;
	}
</style>
<script type="text/javascript">
	const TRANSLATION = ["e+enroll+M4+API", location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+")];
	$(document).ready(function() {
		cnf.init();
	});
	const cnf = (function(d) {
		const cv = {
			API_URL: AppConfig.APIbase + "enroll/v1/returning-std",
			API_MSG_INFO: {
				pack: 2, prefix: "API_MSG_", map: [
					[], [4, 11], [1, 2, 5, 7, 8, 9, 10], [3, 50, 51, 6]
				]
			},
			option: chose => chose == "Y" ?
				app._var.translationDic()[3].messages["confirmed"][app.settings["lang"]] :
				(chose == "C" ? app._var.translationDic()[3].messages["changed"][app.settings["lang"]] : 
				app._var.translationDic()[3].messages["waived"][app.settings["lang"]]),
			OPTION_YES: "Y",
			OPTION_NO: "N",
			OPTION_CHANGE: "C",
			REASON_MIN: 5,
			REASON_MAX: 200
		};
		const field = name => $(`app[name=main] > main [name=${name}]`),
			cbox = name => $(`app[name=main] > main .${name}`);
		var sv = {inited: false};
		var initialize = function() {
			if (sv.inited) return;
			setTimeout(getStatus, 750); // Wait for msgPack to load first
			cbox("remark-1").insertBefore(cbox("actions"));
			cbox("remark-2").insertAfter(cbox("actions"));
			sv.inited = true;
		};
		var getStatus = function() {
			app.Util.ajax(cv.API_URL, {act: "get", cmd: "confirm"}).then(function(dat) {
				cbox("loading").hide();
				if (!dat) return;
				if (!dat.hasChance) return cbox("no-right").show();
				field("placement").text(dat.placement);
				cbox("profile").show();
				if (typeof dat.changeReq !== "undefined") {
					field("request").text(dat.changeReq);
					cbox("hasReq").show();
				} if (dat.chose) {
					manSubmission(dat);
					return cbox("sent").show();
				} if (!dat.available) {
					if (!dat.chosen) cbox("timeout").show();
					return;
				} field("date-limit").text(dat.slotEnds);
				cbox("date-range").show();
				// Initialize Forms
				cbox("step-1 .title").removeAttr("disabled");
				cbox("step-1 .subs-1").toggle("fold");
			});
		},
		formAction = async function(dir) {
			if ("GU".includes(dir)) cbox("step-1 .subs-1").toggle("fold");
			switch (dir) {
				case "G": {
					cbox("step-1 .title").attr("disabled", "");
					cbox("step-2 .title").removeAttr("disabled");
					cbox("step-2 .subs-1").toggle("fold");
				break; }
				case "U": {
					cbox("step-1 .subs-2").toggle("fold");
					cbox("step-2").fadeOut();
					sv.file = new FileUploadHandler.Watch("app[name=main] > main .form [name=usf]", {
						preview: $("app[name=main] > main .form div.file-box"),
						property: {
							name: d.querySelector("app[name=main] > main .form [data-name=name]"),
							size: d.querySelector("app[name=main] > main .form [data-name=size]")
						}, options: {
							sizeLimit: FileUploadHandler.convertSize.MB2B(10),
							types: ["png", "jpg", "jpeg", "gif", "heic", "pdf"]
						}, message: {
							noFile: `กรุณาเลือกไฟล์หลักฐานการสละสิทธิ์`
						}
					}); // cbox("step-1 .subs-2 form").on("change", validateEvi);
					field("reason").on("change", validateEvi);
					field("usf").on("change", () => validateEvi(true));
					field("school").on("focus", selectSchool);
				break; }
				case "S": {
					const ans = arguments[1];
					if (ans == cv.OPTION_NO) return requestEvi();
					const btn = cbox("step-2 .subs-1 .actions").attr("disabled", "");
					let isConfirmed = await new Promise(function(resolve, reject) {
						app.UI.modal(cbox("profile").html() + "<hr><b><span class=\"ref-000" + 
							(ans == cv.OPTION_YES ?
								`20">${app._var.translationDic()[3].translations[1][app.settings["lang"]]}</span><u>${field("placement").text()}</u>` :
								`21">${app._var.translationDic()[3].translations[2][app.settings["lang"]]}</span>`
							) + "</b>",
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
		selectSchool = function() {
			fs.school(app._var.translationDic()[3].messages["select-school"][app.settings["lang"]], function() {
				var display = field("school");
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
		validateEvi = function(suspressNoti) {
			var btn = cbox("step-1 .subs-2 .submit"),
				pass = false,
				reason = field("reason").val().trim();
			if (reason.length < cv.REASON_MIN) {
				if (!suspressNoti) {
					app.UI.notify(2, app._var.translationDic()[3].messages["reason-short"][app.settings["lang"]].replace("{min}", cv.REASON_MIN));
					field("reason").focus();
				}
			} else if (reason.length > cv.REASON_MAX) {
				if (!suspressNoti) {
					app.UI.notify(2, app._var.translationDic()[3].messages["reason-exceed"][app.settings["lang"]].replace("{max}", cv.REASON_MAX));
					field("reason").focus();
				}
			} else if (!sv.file.validate(!suspressNoti)) {
				if (!suspressNoti) field("usf").focus();
			} else pass = true;
			btn.prop("disabled", !pass);
			return pass;
		},
		requestEvi = function() {
			if (!validateEvi(false)) return;
			var answer = {
				answer: cv.OPTION_NO,
				reason: field("reason").val().trim()
			}; if (sv.school) answer.school = sv.school;
			sv.file.uploadTo(cv.API_URL,
				{act: "request", cmd: "commit", param: {to: "confirm", ...answer}}, {
					form: cbox("step-1 .subs-2 form"),
					buttons: cbox("step-1 .subs-2 .submit"),
					uploadIcon: cbox("loading")
				}, handleResponse, cv.API_MSG_INFO
			);
		},
		requestSend = function(answer) {
			const btn = cbox("step-2 .subs-1 .actions");
			cbox("loading").show();
			app.Util.ajax(cv.API_URL,
				{act: "request", cmd: "commit", param: {to: "confirm", answer}},
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
			cbox("step-1 .subs-2").fadeOut();
			cbox("step-2 .subs-1").fadeOut();
			// Add form2
			cbox("date-range").fadeOut();
			// New info
			manSubmission(dat);
			if (!cbox("sent").is(":visible")) cbox("sent").toggle("blind");
		},
		manSubmission = function(dat) {
			field("option").text(cv.option(dat.chose.option));
			field("date-sent").text(dat.chose.time);
			field("IP-sent").text(dat.chose.ip);
			if (typeof dat.changeReq !== "undefined" && dat.chose.option == cv.OPTION_CHANGE)
				cbox("hasReq").insertAfter(cbox("sent"))
			if (dat.chose.option == cv.OPTION_NO)
				return cbox("step-2").fadeOut();
			cbox("sent .gray").replaceWith("&nbsp;");
			cbox("instruction").insertAfter(cbox("sent"));
			if (dat.chose.option == cv.OPTION_YES)
				cbox("sent .orange").remove();
			else cbox("sent .group a").toggleClass("yellow orange");
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
		<div class="no-right message red css-text-center" style="display: none;">นักเรียนไม่มีสิทธิ์ยื่นยืนยันสิทธิ์ประเภทห้องเรียนปกติ</div>
		<p class="profile" x-class="css-bdr-1 css-bdr-solid css-bdr-pink css-bdr-rad-5 css-pad-5" style="display: none;"><span class="ref-00001">ข้าพเจ้า</span> <u><?=$_SESSION["auth"]["name"]["th"]["a"]?></u><span class="ref-00002"> รหัสนักเรียน</span> <u><?=$APP_USER?></u><span class="ref-00003"> ได้รับจัดสรรเข้าศึกษาห้องเรียนปกติ กลุ่มการเรียน</span><u><output name="placement"></output></u> <span class="ref-00004">ระดับชั้นมัธยมศึกษาปีที่ 4</span></p>
		<div class="timeout message gray css-text-center" style="display: none;">ขณะนี้อยู่นอกช่วงเวลาในการยืนยันสิทธิ์ของนักเรียน</div>
		<blockquote class="date-range tab-line cyan unblock translucent" style="display: none;"><span class="ref-00005">นักเรียนสามารถยืนยันสิทธิ์ได้ภายใน</span><output name="date-limit"></output></blockquote>
		<ul class="step blocks">
			<li class="step-1">
				<div class="title" disabled>
					<h3>1. การยืนยันสิทธิ์เรียนต่อที่โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h3>
				</div>
				<ul class="subs blocks">
					<li class="subs-1 container" style="display: none;">
						<!-- Instruction -->
						<?php require_once("$APP_RootDir$APP_CONST[publicDir]$APP_CONST[baseURL]e/enroll/resource/upload/direction/confirm.html"); ?>
						<div class="css-flex css-flex-spread css-flex-wrap css-flex-gap-10">
							<button class="green wide ripple-click" onClick="cnf.interact('G')"><span>ประสงค์เรียนต่อที่<br>โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</span></button>
							<button class="red wide ripple-click" onClick="cnf.interact('U')"><span>สละสิทธิ์การเรียนต่อที่<br>โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</span></button>
						</div>
					</li>
					<li class="subs-2" style="display: none;">
						<h4>การสละสิทธิ์การเรียนต่อที่โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h4>
						<form class="form form-bs">
							<div class="reason">
								<div class="group"><label>เนื่องจาก</label></div>
								<textarea name="reason" class="resize-y"
									placeholder="ระบุเหตุผล…"
									rows="3" maxlength="200"
								></textarea>
							</div>
							<div class="school css-flex css-flex-gap-5 css-flex-wrap">
								<span class="ref-00006 css-text-middle">กรณีศึกษาต่อโรงเรียนอื่น โปรดระบุ</span>
								<div class="group">
									<label>ชื่อโรงเรียน</label>
									<input type="text" name="school" />
								</div>
								<a class="css-text-middle" href="javascript:"><i class="material-icons">help</i></a>
								<span class="css-text-middle">
									<span class="ref-00007">หากไม่พบชื่อโรงเรียน กรุณาติดต่อ</span>
									<a href="<?=$APP_CONST["baseURL"]?>v2/ticket/create#&subj=เพิ่มชื่อสถานศึกษาในตัวเลือก&app=3&cat=6" target="_blank">สร้างคำร้องผ่านระบบ</a>
								</span>
							</div>
							<fieldset class="evi-file">
								<legend>กรุณาอัปโหลดไฟล์หลักฐานการสละสิทธิ์</legend>
								<div class="css-flex css-flex-gap-10 css-flex-autodir">
									<div class="file-box land r-widescr">
										<input type="file" name="usf" accept=".png, .jpg, .jpeg, .gif, .heic, .pdf" required />
									</div>
									<div class="css-flex css-flex-col css-flex-split css-flex-gap-10 css-full-x">
										<div class="container">
											<div class="group">
												<label class="ref-00008">ชื่อไฟล์</label>
												<input type="text" data-name="name" readonly />
											</div>
											<div class="group">
												<label class="ref-00009">ขนาดไฟล์</label>
												<input type="text" data-name="size" readonly />
											</div>
										</div>
										<div class="left">
											<a
												role="button" class="default icon long pill ripple-click"
												href="<?=$APP_CONST["baseURL"]?>e/enroll/form/rwaive"
												target="_blank" download="ฟอร์มสละสิทธิ์.pdf"
											><i class="material-icons">download</i><span class="text ref-00010">ฟอร์มสละสิทธิ์</span></a>
										</div>
									</div>
								</div>
							</fieldset>
							<div class="center">
								<button class="submit blue wide ripple-click" onClick="cnf.interact('S', 'N'); return false;" disabled>บันทึกการสละสิทธิ์</button>
							</div>
						</form>
					</li>
				</ul>
			</li>
			<li class="step-2">
				<div class="title" disabled>
					<h3>2. การยืนยันสิทธิ์กลุ่มการเรียน</h3>
				</div>
				<ul class="subs blocks">
					<li class="subs-1 container" style="display: none;">
						<!-- Remark 1 -->
						<div class="actions css-flex css-flex-spread css-flex-wrap css-flex-gap-10">
							<button class="green swide ripple-click" onClick="cnf.interact('S', 'Y')"><span>ยืนยันสิทธิ์<br>กลุ่มการเรียน</span></button>
							<button class="purple xwide ripple-click" onClick="cnf.interact('S', 'C')"><span>ขอประมวลการ<br>จัดกลุ่มการเรียนใหม่</span></button>
						</div>
						<!-- Remark 2 -->
						<p class="hasReq css-text-italic" style="display: none;"><span class="ref-00017">คำร้องการขอเปลี่ยนเป็นกลุ่มการเรียน</span><u><output name="request"></output></u><span class="ref-00018">ของนักเรียนจะได้รับการพิจารณาภายหลัง</span></p>
					</li>
				</ul>
			</li>
		</ul>
		<div class="sent message green" style="display: none;">
			<span class="ref-00011">นักเรียนได้</span><u><output name="option"></output></u><span class="ref-00012">เรียบร้อยแล้ว</span><br><span class="ref-00013">ครั้งล่าสุดเมื่อ</span><output name="date-sent"></output> <span class="ref-00014">ผ่านที่อยู่ IP</span> <output name="IP-sent"></output>
			<div class="form form-bs inline css-flex-split">
				<a
					role="button" class="gray icon long pill ripple-click"
					href="<?=$APP_CONST["baseURL"]?>e/enroll/resource/upload/view?type=confirm"
					onClick="return cnf.action('File', this, event)"
				><i class="material-icons">visibility</i> <span class="ref-00015">ไฟล์หลักฐาน</span></a>
				<div class="group">
					<a role="button" class="yellow long ripple-click" href="switch"><span class="ref-00016">เปลี่ยนแปลงคำตอบ</span></a>
					<a role="button" class="orange long ripple-click" href="change"><span class="ref-00019">ยื่นคำร้องขอเปลี่ยนกลุ่มการเรียน</span></a>
				</div>
			</div>
		</div>
		<div class="loading css-full-x"></div>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials(side_panel: "enroll");
	$APP_PAGE -> print -> footer();
?>