<?php
	$dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "ลบรายการการตอบกลับ";
	$home_menu = "manage";
	
	$forceExternalBrowser = true;
	$permitted = has_perm("admission");
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			main div.container > * * { margin: 0; }
			main .form input[type="number"]::-webkit-inner-spin-button { display: none; }
			main .form .group label { display: flex; align-items: center; }
		</style>
		<script type="text/javascript">
			const drp = function() {
				const cv = {
					APIurl: "/e/enroll/resource/php/api",
					typeName: {
						"prs": "present",
						"cng": "change",
						"cnf": "confirm"
					}, resultClass: "result form message "
				};
				var sv = {};
				var search = function() {
					(function() {
						var data = {
							type: "mod", act: "find", param: {
								user: document.querySelector('main .form [name="sid"]').value.trim(),
								group: document.querySelector('main .form [name="system"] option:checked').value.trim()
						} }; if (!/^[1-9]\d{4,5}$/.test(data.param.user)) {
							app.ui.notify(1, [2, "รูปแบบเลขประจำตัวไม่ถูกต้อง"]);
							$('main .form [name="sid"]').focus();
						} else if (!/^(prs|cng|cnf|new)$/.test(data.param.group)) {
							app.ui.notify(1, [2, "ตัวเลือกหมวดหมู่ไม่ถูกต้อง"]);
							$('main .form [name="system"]').focus();
						} else {
							document.querySelector('main .form button[name="lookup"]').disabled = true;
							$.post(cv.APIurl, data, function(res, hsc) {
								var dat = JSON.parse(res);
								if (dat.success) {
									sv.success = undefined;
									$("main form.result")
										.attr("class", cv.resultClass+dat.info.msgType)
										.html('<p>'+dat.info.message+'</p>');
									if (dat.info.action) {
										sv.ID = data.param.user;
										sv.action = dat.info.impact;
										$("main form.result").append('<div class="group split">&nbsp;<button class="red icon" onClick="return drp.delete()" name="danger"><i class="material-icons">delete</i>ลบกรายการนี้</button></div>');
										if (dat.info.action > 1) $("main form.result .group").prepend('<a role="button" class="gray icon hollow" onClick="return drp.intercept(this,event)" href="/e/enroll/report/response/file?of='+sv.ID+'&type='+cv.typeName[data.param.group]+'" draggable="false"><i class="material-icons">visibility</i>ดูไฟล์หลักฐาน</button>');
									}
								} else dat.reason.forEach(em => app.ui.notify(1, em));
								document.querySelector('main .form button[name="lookup"]').disabled = false;
							});
						}
					}()); return false;
				};
				var danger = function() {
					(function() {
						if (typeof sv.action === 'undefined') {
							app.ui.notify(1, [3, "There's an error."]);
							$("main form.result").attr("class", cv.resultClass).html("");
						} else if (confirm("คุณต้องการลบรายการการตอบกลับนี้ใช่หรือไม่ ?")) {
							document.querySelector('main .form button[name="danger"]').disabled = true;
							var data = {
								type: "mod", act: "remove",
								param: sv.action
							}; $.post(cv.APIurl, data, function(res, hsc) {
								var dat = JSON.parse(res);
								if (dat.success) {
									$("main form.result").attr("class", cv.resultClass+"green").html('<div class="group split last"><label class="last">ลบรายการการตอบกลับของ '+sv.ID+' สำเร็จ</label><button class="cyan last" onClick="return drp.dismiss()">รับทราบ</button></div>');
									sv = { success: true };
								} else {
									dat.reason.forEach(em => app.ui.notify(1, em));
									document.querySelector('main .form button[name="danger"]').disabled = false;
									if (dat.reason.includes([2, "เกิดข้อผิดพลาด. กรุณาลองใหม่อีกครั้ง."]) || dat.reason.includes([2, "ไม่พบรายการที่จะทำการลบ."]) || dat.reason.includes([2, "ไม่มีข้อมูลให้ทำการลบ."]))
										$("main form.result").attr("class", cv.resultClass).html("");
								}
							});
						}
					}()); return false;
				};
				var close_success = function() {
					(function() {
						if (typeof sv.success !== 'undefined' && sv.success) {
							sv.success = undefined;
							$("main form.result").attr("class", cv.resultClass).html("");
						}
					}()); return false;
				};
				var show_example = function(m, e) {
					(function() {
						// e.preventDefault();
						if (e.ctrlKey) window.open(m.href);
						else app.ui.lightbox.open("mid", {title: "ไฟล์หลักฐานของ \""+sv.ID+"\"", allowclose: true, autoclose: 300000,
							html: '<iframe src="'+m.href+'" style="width:90vw;height:80vh;border:none">Loading...</iframe>'
						});
					}()); return false;
				};
				return {
					search: search,
					delete: danger,
					dismiss: close_success,
					intercept: show_example
				};
			}();
		</script>
	</head>
	<body>
		<?php require($dirPWroot."e/enroll/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<?php if (!$permitted) echo '<iframe src="/error/901">901: No Permission</iframe>'; else { ?>
			<div class="container">
				<h2>การลบรายการการตอบกลับ</h2>
				<form class="form message blue">
					<div class="group">
						<span>เลขประจำตัว</span>
						<input type="number" name="sid" maxlength="6">
					</div>
					<div class="group">
						<span>หมวดหมู่</span>
						<select name="system">
							<option value disabled selected>---กรุณาเลือก---</option>
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
					<div class="group spread">
						<button name="lookup" class="blue" onClick="return drp.search()">ค้นหาข้อมูล</button>
					</div>
				</form>
				<form class="result form message"></form>
			</div><?php } ?>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."e/enroll/resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>