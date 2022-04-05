<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "การนำออกข้อมูล";
	$home_menu = "mod";
    
	$permitted = has_perm("admission");
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			main form > p { margin: 0px 0px 10px; }
			main form label.cb {
				height: 25px; line-height: 25px;
				display: inline-flex;
			}
			main form label.cb input[type="checkbox"]:after { transform: translate(calc(100% - 13.5px), calc(-100% - 4.625px)) !important; }
			main form label.cb input[type="checkbox"]:checked:after { transform: translate(calc(100% + 5px), calc(-100% - 4.625px)) !important; }
			main form > :last-child { margin-bottom: 0px !important; }
		</style>
		<script type="text/javascript">
			const verify = function() {
                (function() {
                    var send = {
                        sys: document.querySelector('main form [name="system"]'),
                        eft: document.querySelector('main form [name="filetype"]')
                    }; if (!["prs", "cng", "cnf", "new"].includes(send.sys.value.trim())) {
                        app.ui.notify(1, [1, "หมวดหมู่ไม่ถูกต้อง."]);
                        $(send.sys).focus();
                    } else if (!["csv", "tsv", "json"].includes(send.eft.value.trim())) {
                        app.ui.notify(1, [1, "ประเภทไฟล์ไม่ถูกต้อง."]);
                        $(send.eft).focus();
                    } else {
						let msg = "คุณต้องการนำออกข้อมูลการ", name = {
							sys: document.querySelector('main form [name="system"] option:checked')
						}; msg += name.sys.innerText + "ของ" + name.sys.parentNode.label + "ใช่หรือไม่ ?";
						if (confirm(msg)) document.querySelector("main form").submit();
					}
                }()); return false;
            }
		</script>
	</head>
	<body>
		<?php require($dirPWroot."e/enroll/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<?php if (!$permitted) echo '<iframe src="/error/901">901: No Permission</iframe>'; else { ?>
			<div class="container">
				<h2>การนำออกข้อมูล</h2>
                <form class="form message blue" method="post" action="response/export" target="dlframe">
					<p class="message yellow">ควรใช้งานหน้านี้ผ่านคอมพิวเตอร์หรือโน้ตบุ้ค เนื่องจากประเภทไฟล์นำออกไม่เหมาะแก่การเปิดบนโทรศัพท์มือถือหรือแท็บเล็ท</p>
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
					</div><div class="group">
						<span>ประเภทไฟล์</span>
						<select name="filetype">
							<option value disabled selected>---กรุณาเลือก---</option>
								<option value="csv">Comma Separated Value (.csv)</option>
								<option value="tsv">Tab Separated Value (.tsv)</option>
								<option value="json">JavaScript Object Notation (.json)</option>
							</optgroup>
						</select>
					</div>
					<!div class="group-inline"><label class="cb">
						<div><input type="checkbox" name="evdLink" class="switch"></div>
						<span>&nbsp; ใส่ลิงก์ไฟล์หลักฐาน (ถ้ามี)</span>
						<!--font style="color: var(--clr-bs-gray-dark)">&nbsp; เฉพาะประเภทนักเรียนเดิม</font-->
					</label><!/div>
					<input type="hidden" name="start">
                    <center><button class="yellow" onClick="return verify()">นำอออก (Export)</button></center>
                </form>
				<iframe name="dlframe" hidden></iframe>
			</div><?php } ?>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."e/enroll/resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>