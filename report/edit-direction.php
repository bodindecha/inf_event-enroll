<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "แก้ไขคำชี้แจง";
	$home_menu = "settings";

	$has_perm = has_perm("admission");
	if (!$has_perm) {
		require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
		TianTcl::http_response_code(901);
	}
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	app[name=main] main .control {
		width: 100%;
		display: grid; grid-template-columns: 3fr 41px 3fr; grid-template-rows: 1fr;
	}
	app[name=main] main .control select[name=for] { width: fit-content; }
	app[name=main] main .control .group:nth-child(3) { justify-content: flex-end; }
	app[name=main] main .wrapper { width: 100%; height: calc(100vh - var(--top-height) - 161px); }
	app[name=main] main .wrapper.editor-left { flex-direction: row; }
	app[name=main] main .wrapper.editor-top { flex-direction: column; }
	app[name=main] main .wrapper.editor-right { flex-direction: row-reverse; }
	app[name=main] main .wrapper.editor-bottom { flex-direction: column-reverse; }
	app[name=main] main .wrapper .window {
		width: 100%; height: 100%;
		border-radius: .3rem; border: 1px solid var(--clr-main-black-absolute);
	}
	app[name=main] main .wrapper .code {
		width: 100%; height: 100%;
		font-size: 14px;
	}
	app[name=main] main .wrapper .preview {
		padding: 5px;
		width: calc(100% - 10px); height: calc(100% - 10px);
	}
	/* app[name=main] main .wrapper .preview a[href] { pointer-events: none; touch-action: none; } */
	app[name=main] .lightbox :where(.il-form, .guide) { margin: 5px; }
	app[name=main] .lightbox .il-form button.blue { width: 80px; }
	app[name=main] .lightbox .guide > ul { margin: 0; padding-left: 30px; }
	app[name=main] .lightbox .guide code { font-size: 1.25rem; }
	app[name=main] .lightbox .guide button {
		margin: 2.5px 0;
		display: inline-flex !important;
		pointer-events: none;
	}
</style>
<script type="text/javascript">
	const TRANSLATION = location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+");
	$(document).ready(function() {
		page.init();
	});
	const page = (function(d) {
		const cv = {
			API_URL: AppConfig.APIbase + "enroll/v1/edit-site",
			filename: {prs: "present", cng: "change", cnf: "confirm", new: "new"},
			editorSide: ["left", "top", "right", "bottom"],
			regex: {
				URL: /^((http(s)?:)?\/\/)?((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z0-9\-_]+\.)+[a-zA-Z]{2,13}))((\/|\?|#)\S*)?$/,
				tmt: /^((tel:(\+\d{1,3}(\ \d{1,3})?(\ )?)?\d{8,13})|(mailto:(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@([a-zA-Z0-9\-_]+\.)+[a-zA-Z]{2,13})(\?(&?(cc|bcc|subject|body)=.+)+)?)$/,
				internalURL: /^\/?([A-Z0-9a-z\-_.!$~+]+\/?)+$/
			}
		};
		var sv = {inited: false, unsavedChanges: false, editorSide: "left", previewRendered: false, warnLeave: false};
		var initialize = function() {
			if (sv.inited) return;
			setUpEditor();
			$("app[name=main] main .control select[name=for]").on("change", changeFile);
			$('app[name=main] header button[onClick^="app.UI.theme"]').on("click", updateTheme);
			sv.inited = true;
		};
		var setUpEditor = function() {
			sv.editor = ace.edit("editor", {
				mode: "ace/mode/html"
			}); updateTheme();
		},
		updateTheme = function() {
			sv.editor.setTheme("ace/theme/" + (app.settings["theme"] == "light" ? "xcode" : "monokai"));
		},
		changeFile = function() {
			if (typeof sv.currentFile !== "string") startEditor();
			var option = $("app[name=main] main .control select[name=for]");
			if (sv.unsavedChanges && !confirm("You have unsaved changes. Are you sure you want to change file?"))
				return option.val(sv.currentFile);
			var selectedFile = option.val();
			if (!selectedFile in cv.filename) {
				app.UI.notify(2, app.UI.language.getMessage("invalid-file"));
				return option.val(sv.currentFile);
			} sv.currentFile = selectedFile;
			option.attr("disabled", "");
			app.Util.ajax(cv.API_URL, {act: "direction", cmd: "loadHTML", param: cv.filename[sv.currentFile]}).then(function(dat) {
				option.removeAttr("disabled");
				if (!dat) return;
				sv.original = dat.HTML; // atob(dat.HTML);
				sv.editor.session.setValue(sv.original, -1);
				updatePreview();
				if (sv.warnLeave) {
					app.nav.confirmLeave(false);
					sv.warnLeave = false;
				}
			});
		},
		startEditor = function() {
			$("app[name=main] main .wrapper .editor, app[name=main] main .control div:nth-child(1) button, app[name=main] main .control .group:nth-child(3) button:not(.default)").removeAttr("disabled");
			sv.editor.session.on("change", function(delta) {
				sv.lastUpdate = Date.now();
				sv.previewRendered = false;
				sv.unsavedChanges = getSecureHTML(false) != sv.original;
				if (sv.unsavedChanges && !sv.warnLeave) {
					app.nav.confirmLeave();
					sv.warnLeave = true;
				}
			});
			sv.updateCheck = setInterval(function() {
				if (Date.now() - sv.lastUpdate >= 1e3) updatePreview();
			}, 1e3);
		},
		updatePreview = function() {
			if (sv.previewRendered) return;
			var sandbox = $("app[name=main] main .wrapper .preview").html(getSecureHTML(false));
			sandbox.find("a[href]")
				.attr("onClick", "return false")
				.attr("draggable", "false");
			sandbox.children().show();
			sv.previewRendered = true;
		},
		getSecureHTML = function(alertFault = true) {
			var context = sv.editor.session.getValue();
			if (/<(script|style|meta|\?(=|php)?)[^>]*>/.test(context)) {
				if (alertFault) app.UI.notify(2, app.UI.language.getMessage("tag-not-allowed"));
				context = context
					.replaceAll(/(<(script|style|meta) ?)/g, "<!-- $1").replaceAll(/(<\/(script|style|meta)>)/g, "$1 -->")
					.replaceAll(/<meta ([^>]+)>/g, "<!meta $1>")
					.replaceAll(/<(\?(=|php)?)/g, "<!-- <!$1").replaceAll(/\?>/g, "?> -->");
			} return context.trim();
		},
		turnSide = function() {
			sv.editorSide = cv.editorSide[(cv.editorSide.indexOf(sv.editorSide) + 1) % cv.editorSide.length];
			$("app[name=main] main .wrapper").attr("class", `wrapper css-flex css-flex-gap-10 editor-${sv.editorSide}`);
		},
		restartFile = function() {
			if (!confirm("Are you sure you want to start again?")) return;
			sv.editor.session.setValue(sv.original);
			sv.previewRendered = false;
			updatePreview();
			if (sv.warnLeave) {
				app.nav.confirmLeave(false);
				sv.warnLeave = false;
			}
		},
		saveFile = function() {
			if (!sv.unsavedChanges) return app.UI.notify(1, app.UI.language.getMessage("no-change-detected"));
			const context = getSecureHTML(),
				editable = $("app[name=main] main .control select[name=for], app[name=main] main .control div:nth-child(1) button, app[name=main] main .wrapper .editor, app[name=main] main .control .group:nth-child(3) button:not(.default)");
			editable.attr("disabled", "");
			app.Util.ajax(cv.API_URL, {act: "direction", cmd: "update", param: {
				file: cv.filename[sv.currentFile], content: context // btoa(context)
			}}).then(function(dat) {
				editable.removeAttr("disabled");
				if (!dat) return;
				app.UI.notify(0, "File successfully saved");
				sv.original = context;
				sv.unsavedChanges = false;
				if (sv.warnLeave) {
					app.nav.confirmLeave(false);
					sv.warnLeave = false;
				}
			});
		},
		showHelp = function() {
			app.UI.lightbox("top", {title: getElementText(11)}, $("app[name=main] .guideTemplate").html());
		},
		insertLink = function(formFilled = false) {
			(function() {
				if (!formFilled) {
					app.UI.lightbox("top", {
						title: getElementText(2), exitTap: false
					}, $("app[name=main] .formTemplate").html());
					return setTimeout(app.UI.refineElements, 250);
				} $("app[name=main] .lightbox .il-form").attr("disabled", "");
				var data = {
					text: $("app[name=main] .lightbox .il-form [name=link-text]").val().trim(),
					link: $("app[name=main] .lightbox .il-form [name=link-href]").val().trim(),
					oint: $("app[name=main] .lightbox .il-form [name=new_tab]").is(":checked")
				}; if (!data.text.length) {
					app.UI.notify(1, app.UI.language.getMessage("err-empty-il-text"));
					$("app[name=main] .lightbox .il-form [name=link-text]").focus();
				} else if (!data.link.length) {
					app.UI.notify(1, app.UI.language.getMessage("err-empty-il-link"));
					$("app[name=main] .lightbox .il-form [name=link-href]").focus();
				} else if (!cv.regex.URL.test(data.link) && !cv.regex.tmt.test(data.link) && !cv.regex.internalURL.test(data.link)) {
					app.UI.notify(2, app.UI.language.getMessage("err-empty-il-URL"));
					$("app[name=main] .lightbox .il-form [name=link-href]").focus();
				} else {
					if (/^(tel|mailto):.+/.test(data.link) || (/^(https?:)?\/\/.+/.test(data.link) && !/^https?:\/\/inf\.bodin\.ac\.th.*/.test(data.link))) {
						data.oint = true;
						data.link = AppConfig.baseURL + "go?url=" + encodeURIComponent(data.link);
					} sv.editor.insert(`<a href="${data.link}"${data.oint?' target="_blank"':""}>${data.text}</a>`);
					backToEditor();
				} $("app[name=main] .lightbox .il-form").removeAttr("disabled");
			}()); return false;
		},
		getElementText = function(index, dictionary = 2) {
			return app._var.translationDic()[dictionary].translations[index][app.settings["lang"]];
		},
		backToEditor = function() {
			if (app.UI.lightbox.isOpen()) app.UI.lightbox.close();
			d.querySelector("app[name=main] main .wrapper .code").click();
		};
		return {
			init: initialize,
			turnSide,
			restartFile, saveFile,
			showHelp,
			insertLink, backToEditor
		};
	}(document));
</script>
<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/ace-c9/ace.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/ace-c9/theme-xcode.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/ace-c9/theme-monokai.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/lib/ace-c9/mode-html.js" charset="utf-8"></script>
<?php $APP_PAGE -> print -> nav("enroll"); ?>
<main>
	<!?php ?>
	<section class="container">
		<h2><?=$header["title"]?></h2>
		<div class="control form form-bs">
			<div class="css-flex css-flex-gap-10">
				<div class="group">
					<label>สำหรับ</label>
					<select name="for">
						<option value selected disabled>— กรุณาเลือก —</option>
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
				<button disabled class="black hollow icon" onClick="page.insertLink()" data-title="แทรกลิงก์">
					<span class="material-symbols-rounded">add_link</span>
				</button>
			</div>
			<button class="gray icon pill hollow ref-00001" onClick="page.turnSide()" data-title="เปลี่ยนด้าน"><i class="material-icons">rotate_right</i></button>
			<div class="group">
				<button class="default icon" onClick="page.showHelp()" data-title="คำแนะนำ"><i class="material-icons">info</i></button>
				<button disabled class="red hollow icon" onClick="page.restartFile()" data-title="เริ่มใหม่"><i class="material-icons">close</i></button>
				<button disabled class="blue icon" onClick="page.saveFile()" data-title="บันทึก"><i class="material-icons">save</i></button>
			</div>
		</div>
		<div class="wrapper css-flex css-flex-gap-10 editor-left">
			<div class="editor window slider" disabled>
				<div class="code" id="editor"></div>
			</div>
			<div class="preview window container slider" data-title="จำลองผลลัพธ์"></div>
		</div>
		<div class="formTemplate" hidden>
			<form class="il-form form form-bs">
				<div class="group">
					<label class="ref-00002">ข้อความแสดง</label>
					<input type="text" name="link-text" required />
				</div>
				<div class="group">
					<label class="ref-00003">ลิงก์ที่หมาย</label>
					<input type="url" name="link-href" required />
				</div>
				<label class="css-flex css-flex-gap-5">
					<input type="checkbox" class="switch on-cyan" name="new_tab" />
					<span class="ref-00004">เปิดในแท็บใหม่</span>
				</label>
				<div class="group spread">
					<button class="gray hollow ripple-click" type="reset" onClick="page.backToEditor()">ยกเลิก</button>
					<button class="blue ripple-click" type="submit" onClick="return page.insertLink(true)">แทรก</button>
				</div>
			</form>
		</div>
		<div class="guideTemplate" hidden>
			<div class="guide container">
				<ul>
					<li><span class="ref-00005">ไม่ควรลบหรือแก้ไขบรรทักแรก</span> <code lang="html" class="language-html">&lt;div class="message gray" name="instruction"&gt;</code> <span class="ref-00006">และบรรทัดสุดท้าย</span> <code lang="html" class="language-html">&lt;/div&gt;</code> <span class="ref-00007">ของไฟล์</span></li>
					<li><span class="ref-00008">หากคุณต้องการแทรกลิงก์ สามารถกดปุ่ม</span> <button class="black hollow icon"><i class="material-icons">insert_link</i></button> <span class="ref-00009">ได้</span></li>
					<li><span class="ref-00010">กดปุ่ม</span> <button class="gray hollow pill icon"><i class="material-icons">rotate_right</i></button> <span class="ref-00011">เพื่อเปลี่ยนด้านของหน้าต่างแก้ไขโค้ดและหน้าต่างจำลองผลลัพธ์</span></li>
					<li><span class="ref-00010">กดปุ่ม</span> <button class="red hollow icon"><i class="material-icons">close</i></button> <span class="ref-00012">เพื่อคืนค่าเป็นค่าจากการบันทึกครั้งล่าสุด</span></li>
					<li><span class="ref-00010">กดปุ่ม</span> <button class="blue icon"><i class="material-icons">save</i></button> <span class="ref-00013">เพื่อบันทึกการเปลี่ยนแปลงไฟล์</span></li>
				</ul>
			</div>
		</div>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials();
	$APP_PAGE -> print -> footer("enroll");
?>