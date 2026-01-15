<?php
	$dirPWroot = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "ระบบรายงานตัว/ยืนยันสิทธิ์เข้าศึกษาต่อ";
	$header_desc = "นักเรียนใหม่";

	$forceExternalBrowser = true;
	require_once($dirPWroot."e/enroll/resource/php/config.php");

	$admission = array(
		"year" => "2569"
	);
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			html body main div.container * { margin: 0px 0px 10px; }
			main .form input[type="number"]::-webkit-inner-spin-button { display: none; }
			main .form.modern { --mvlbt: -7.5px; }
			main .form.modern > * { margin: 0px 0px 10px; }
			main .form.modern input, main .form.modern select {
				margin: 7.5px 2.5px; padding: 2.5px 5px;
				width: calc(100% - 17px); height: 30px;
				font-size: 20px; line-height: 30px; font-family: "Sarabun", serif;
				border: 1px solid var(--clr-bs-gray-dark); border-radius: 3px;
				transition: var(--time-tst-fast);
			}
			main .form.modern input + label {
				padding: 2.5px 5px;
				position: absolute; left: 15px; transform: translate(calc(var(--mvlbt) + 12.5px), 10px /*-41px*/);
				height: var(--txt-ipt-h);
				font-size: 18.75px; font-family: 'Sarabun', sans-serif; color: gray; line-height: var(--txt-ipt-h);
				transition: calc(var(--time-tst-xfast)/1.5); pointer-events: none;
			}
			main .form.modern input[required] + label { color: var(--clr-bs-red); }
			main .form.modern input:focus + label, main .form.modern input[filled="true"] + label {
				transform: translate(var(--mvlbt), -7px /*-59px*/) scale(0.75);
				color: var(--clr-main-black-absolute);
				background-image: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,0) 42.4%, rgba(255,255,255,1) 42.5%, rgba(255,255,255,1) 55%, rgba(255,255,255,0) 55.1%);
			}
			main a > i.material-icons { transform: translateY(5px); }
			main .form .last { margin-bottom: 0px !important; }
			@media only screen and (max-width: 768px) {
				main .form.modern input + label { transform: translate(calc(var(--mvlbt) + 7.5px), 12.5px); }
				main .form.modern input:focus + label, main .form.modern input[filled="true"] + label { transform: translate(calc(var(--mvlbt) - 7.5px), -4px) scale(0.75); }
			}
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				$("form input").on("input change", validate_field)
			});
			const cnf = function() {
				const cv = {
					APIurl: "/e/enroll/resource/php/api",
					group: <?=arrDump($CV_groupAdm)?>,
					dlURL: "/e/enroll/resource/file/dl?name=sef-"
				};
				var sv = {};
				var authen = function() {
					(function(d) {
						var data = {
							type: "new", act: "authen", param: {
								user: d.querySelector('main .form [name="sid"]').value.trim(),
								pswd: d.querySelector('main .form [name="cid"]').value.trim()
						} }; if (!/^[1-9]\d{4,5}$/.test(data.param.user)) {
							app.ui.notify(1, [1, "รูปแบบเลขประจำตัวผู้สมัครไม่ถูกต้อง"]);
							$('main .form [name="sid"]').focus();
						} else if (!/^(\d{13}|G\d{12}|[A-Z]{2}\d{7})$/.test(data.param.pswd)) {
							app.ui.notify(1, [1, "รูปแบบเลขประจำตัวประชาชนไม่ถูกต้อง"]);
							$('main .form [name="cid"]').focus();
						} else {
							d.querySelector('main .form button[name="authen"]').disabled = true;
							$.post(cv.APIurl, data, function(res, hsc) {
								var dat = (typeof res !== "object" ? JSON.parse(res) : res);
								if (dat.success) {
									$('main form output[name="rtype"]').val(cv.group[dat.info.type]);
									$('main form output[name="fullname"]').val(dat.info.name);
									$('main form output[name="closetime"]').val(dat.info.expire);
									$('form[name="authenticate"]').hide();
									$('form[name="bio"]').show();
									if (dat.info.done) {
										$('main form output[name="choice"]').val(dat.info.choice=="Y" ? "ยืนยัน" : "สละ");
										$('main form output[name="dsctime"]').val(dat.info.decidetime);
										$('main form output[name="IPaddr"]').val(dat.info.IPaddr);
										$('form[name="complete"]').toggle("blind");
										if (dat.info.choice == "Y") {
											$('form[name="instruction"]').toggle("blind");
											$('form a[target="dlframe"][download]').attr("href", cv.dlURL+dat.info.signature+"&authuser="+dat.info.authuser);
										} else if (dat.info.choice == "N" && dat.info.evfile) {
											$('form[name="complete"] > center').removeClass("last");
											$('form[name="complete"]').append('<center class="last"><a href="/e/enroll/resource/upload/view?type=newstd&authuser='+dat.info.evfile+'" onClick="return cnf.intercept(this,event)">[<i class="material-icons">visibility</i> ไฟล์หลักฐาน ]</a></center>');
										}
									} else if (dat.info.inTime) {
										sv.ID = dat.info.returnTo;
										sv.type = dat.info.type;
										sv.signature = dat.info.signature;
										$('form[name="choose"]').toggle("blind");
									} else $('form[name="timeout"]').toggle("blind");
								} else {
									dat.reason.forEach(em => app.ui.notify(1, em));
									d.querySelector('main .form button[name="authen"]').disabled = false;
								}
							});
						}
					}(document)); return false;
				};
				var choose = function(select) {
					(function(select, d) {
						if (typeof sv.ID === "undefined") restartOnError();
						else {
							let msg = "คุณต้องการ" + (select ? "ยืนยัน" : "สละสิทธิ์") + "สิทธิ์การเข้าศึกษาต่อใช่หรือไม่ ?";
							var data = { type: "new", act: "decide", param: {
								user: sv.ID,
								choose: select ? "Y" : "N",
								"file-ext": null
							} }, collect = true;
							if (select) {
								data.param["namefen"] = d.querySelector('main .form [name="firstname"]').value.trim();
								data.param["namelen"] = d.querySelector('main .form [name="lastname"]').value.trim().toUpperCase();
								if (!/^[A-Z][A-Za-z\- ]{1,49}$/.test(data.param.namefen) || /^(\-| ){2,}$/.test(data.namefen)) {
									app.ui.notify(1, [2, "รูปแบบชื่อจริงภาษาอังกฤษไม่ถูกต้อง"]);
									$('main .form [name="firstname"]').focus(); collect = false;
								} else if (!/^[A-Z][A-Z\- ]{1,49}$/.test(data.param.namelen) || /^(\-| ){2,}$/.test(data.namelen)) {
									app.ui.notify(1, [2, "รูปแบบนามสกุลภาษาอังกฤษไม่ถูกต้อง"]);
									$('main .form [name="lastname"]').focus(); collect = false;
								} if (!collect) app.ui.notify(1, [1, "กรุณาพิมพ์ชื่อในรูป proper-case (Aaa)"]);
							} if (collect && confirm(msg)) select ? proceed(data) : getFile(data);
						}
					}(select, document)); return false;
				};
				var restartOnError = function() {
					app.ui.notify(1, [3, "There's an error."]);
					$('form[name="authenticate"]').show();
					$('form[name="bio"]').hide();
					$('form[name="choose"]').hide();
					$('form[name="timeout"]').hide();
					$('form[name="complete"]').hide();
					$('form[name="instruction"]').hide();
					document.querySelector('main .form button[name="authen"]').disabled = false;
				};
				var proceed = function(data) {
					$('main .form div[name="decide"]').attr("disabled", "");
					$.post(cv.APIurl, data, function(res, hsc) {
						var dat = (typeof res !== "object" ? JSON.parse(res) : res);
						if (dat.success) {
							$('form[name="choose"]').toggle("blind");
							if (dat.info.result) {
								$('main form output[name="choice"]').val(dat.info.choice=="Y" ? "ยืนยัน" : "สละ");
								$('main form output[name="dsctime"]').val(dat.info.decidetime);
								$('main form output[name="IPaddr"]').val(dat.info.IPaddr);
								$('form[name="complete"]').toggle("blind");
								if (dat.info.choice=="Y") {
									$('form[name="instruction"]').toggle("blind");
									$('form a[target="dlframe"][download]').attr("href", cv.dlURL+sv.signature+"&authuser="+sv.ID);
								} else if (dat.info.choice == "N" && dat.info.evfile) {
									$('form[name="complete"] > center').removeClass("last");
									$('form[name="complete"]').append('<center class="last"><a href="/e/enroll/resource/upload/view?type=newstd&authuser='+dat.info.evfile+'" onClick="return cnf.intercept(this,event)">[<i class="material-icons">visibility</i> ไฟล์หลักฐาน ]</a></center>');
								}
							} else $('form[name="timeout"]').toggle("blind");
							delete sv.ID, sv.type;
						} else {
							dat.reason.forEach(em => app.ui.notify(1, em));
							$('main .form div[name="decide"]').removeAttr("disabled");
						}
					});
				};
				var getFile = function(parseDat) {
					sv.parseDat = parseDat;
					app.ui.lightbox.open("mid", {title: "กรุณาอัปโหลดไฟล์หลักฐานการสละสิทธิ์", allowclose: false,
						html: '<iframe name="fr" src="new-swefur?a='+sv.ID+'" style="width:90vw;height:80vh;border:none">Loading...</iframe>'
					});
				};
				var has_file = function(ft) {
					if (typeof sv.parseDat === "undefined") restartOnError();
					else if (!["png", "jpg", "jpeg", "heic", "gif", "pdf"].includes(ft)) {
						app.ui.notify(1, [2, "กรุณาตรวจสอบว่าภาพของคุณเป็นประเภท PNG/JPG/GIF/HEIF/PDF และมีขนาดไม่เกิน 10 MB"]);
						document.querySelector('body .lightbox iframe[name="fr"]').src = "new-swefur?a="+sv.ID;
					} else {
						app.ui.notify(1, [0, "อัปโหลดไฟล์สำเร็จ"]);
						app.ui.lightbox.close();
						sv.parseDat.param["file-ext"] = ft;
						proceed(sv.parseDat);
						delete sv.parseDat
					}
				};
				var show_example = function(m, e) {
					(function() {
						// e.preventDefault();
						if (e.ctrlKey) window.open(m.href);
						else app.ui.lightbox.open("mid", {title: m.innerText.substring(12, m.innerText.length-2), allowclose: true, autoclose: 300000,
							html: '<iframe src="'+m.href+'" style="width:90vw;height:80vh;border:none">Loading...</iframe>'
						});
					}()); return false;
				};
				return {
					check: authen,
					choose: choose,
					recieved: has_file,
					intercept: show_example
				};
			}(); top.cnf = cnf;
			function validate_field() {
				document.querySelectorAll('form input').forEach((eio) => {
					var ei = $(eio);
					ei.attr("filled", (ei.val()==""?"false":"true"));
				});
			}
		</script>
	</head>
	<body>
		<?php require($dirPWroot."e/enroll/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<div class="container">
				<h2>ระบบรายงานตัว/ยืนยันสิทธิ์เข้าศึกษาต่อ ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</h2>
				<!--center class="message red">ขณะนี้ระบบอยู่ระหว่างการปรับปรุง กรุณาเข้ามาใหม่หลัง 10.15 น.</center-->
				<form class="form modern --message-black" name="authenticate">
					<input type="number" name="sid" maxlength="5" autofocus><label>เลขประจำตัวผู้สมัคร 5 หลัก</label>
					<input type="text" name="cid" maxlength="13"><label>เลขประจำตัวประชาชน 13 หลัก</label>
					<p>ใส่เลขประจำตัวผู้สมัครและเลขประจำตัวประชาชนโดยไม่ต้องมีขีดกลางหรือเว้นวรรค</p>
					<button class="blue full-x last" onClick="return cnf.check()" name="authen">ตรวจสอบสิทธิ์</button>
				</form>
				<form name="bio" style="display: none;">
					<center class="message cyan">การรายงานตัว/ยืนยันสิทธิ์เข้าศึกษาต่อ ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ประเภท<u><output name="rtype"></output></u> ปีการศึกษา <?=$admission["year"]?><br><output name="fullname"></output></center>
				</form>
				<form class="form" name="choose" style="display: none;">
					<center class="message yellow">นักเรียนสามารถเลือกได้เพียง 1 ครั้งเท่านั้น ภายใน<output name="closetime"></output></center>
					<div class="message blue last">
						<center>กรุณาศึกษารายละเอียดการเข้าศึกษาต่ออย่างถี่ถ้วน แล้วกรอกข้อมูลด้านล่าง และกดปุ่มเพื่อยืนยันหรือสละสิทธิ์ เข้าศึกษาต่อ ณ โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ประเภท<u><output name="rtype"></output></u> ปีการศึกษา <?=$admission["year"]?></center>
						<fieldset>
							<legend>กรณียืนยันสิทธิ์ กรุณากรอกข้อมูล</legend>
							<div class="group">
								<span>ชื่อจริงภาษาอังกฤษ</span>
								<input type="text" name="firstname" maxlength="50">
							</div>
							<div class="group last">
								<span>นามสกุลภาษาอังกฤษ</span>
								<input type="text" name="lastname" maxlength="50" style="text-transform: uppercase">
							</div>
						</fieldset>
						<div class="group spread last" name="decide">
							<button class="last green" onClick="return cnf.choose(true)">ยืนยันสิทธิ์การเข้าศึกษาต่อ</button>
							<button class="last red" onClick="return cnf.choose(false)">สละสิทธิ์การเข้าศึกษาต่อ</button>
						</div>
					</div>
				</form>
				<form class="form message red" name="timeout" style="display: none;">
					<center class="last">ขณะนี้อยู่นอกช่วงเวลาในการยืนยันสิทธิ์ของนักเรียน</center>
				</form>
				<form class="form message green" name="complete" style="display: none;">
					<center class="last">นักเรียนได้<b><output name="choice"></output>สิทธิ์</b>เรียบร้อยแล้วเมื่อ<output name="dsctime"></output> ผ่านที่อยู่ IP <output name="IPaddr"></output></center>
				</form>
				<?php include($dirPWroot."e/enroll/resource/upload/direction/new.html"); ?>
				<iframe name="dlframe" hidden></iframe>
			</div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."e/enroll/resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>