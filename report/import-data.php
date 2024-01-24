<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "น้ำเข้าข้อมูล";

	if (!has_perm("admission")) {
		require($APP_RootDir."private/script/lib/TianTcl/various.php");
		$TCL -> http_response_code(901);
	}
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($APP_RootDir."private/block/core/heading.php"); require($APP_RootDir."private/script/start/CSS-JS.php"); ?>
		<style type="text/css">

		</style>
		<script type="text/javascript">
			// const TRANSLATION = location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+");
			$(document).ready(function() {
				enroll.init();
			});
			const enroll = (function(d) {
				const cv = {};
				var sv = { inited: false, showed: false };
				const mb2b = MB => MB*1024000,
					kb2mb = KB => KB/1024,
					b2kb = B => B/1024,
					b2mb = B => B/1024000;
				var initialize = function() {
					if (!sv.inited) {
						seek_param();
						$('app[name=main] > main .form [name="usf"]').on("change", function() { enroll.validate_file(false); });
						sv.inited = true;
					}
				},
				seek_param = function() { if (location.hash.length > 1) {
					// Extract hashes
					var hash = {}; location.hash.substring(1, location.hash.length).split("&").forEach((ehs) => {
						let ths = ehs.split("=");
						hash[ths[0]] = ths[1];
					});
					// Let's see
					if (typeof hash.msgID !== "undefined") gainNoti(hash.msgID);
					history.replaceState(null, null, location.pathname+location.search);
				} },
				gainNoti = function(IDs) {
					const message = [
						[2, "ประเภทข้อมูลไม่ถูกต้อง."],
						[1, "ไฟล์ที่เลือกมีคุณสมบัติไม่ตรงกับที่กำหนดไว้. กรุณาเลือกไฟล์ใหม่."],
						[3, "เกิดข้อผิดพลาดในการอัปโหลดไฟล์ กรุณาลองใหม่อีกครั้ง."],
						[3, "ไม่สามารถเปิดไฟล์ที่อัปโหลดมาได้."],
						[1, "ไม่สามารถอ่านไฟล์ที่อัปโหลดมาได้."],
						[1, "ไม่มีข้อมูลภายในไฟล์."],
						[0, "นำข้อมูลเข้าสำเร็จ."],
						[3, "เกิดข้อผิดพลาดขณะนำเข้าข้อมูล."]
					]; IDs.split("")
						.map(eid => parseInt(eid, 36))
						.forEach(mid => app.UI.notify(...message[mid]));
				},
				loadInfo = function(me) {
					var type = me.value;
					$("app[name=main] > main .about").hide().filter("."+type).show();
					if (sv.showed) return
					$("app[name=main] > main .information, main .submit").show();
					$("app[name=main] > main .form button").removeAttr("disabled");
					sv.showed = true;
				},
				byte2text = function(bytes) {
					let nv;
					if (bytes < 1024000) nv = Math.round(b2kb(bytes)*100)/100;
					else nv = Math.round(b2mb(bytes)*100)/100;
					if (!nv*100%100) nv = parseInt(nv);
					return nv+(bytes < 1024000 ? " KB" : " MB");
				},
				upload = function() {
					(function() {
						if (!$('app[name=main] > main .form [name="system"]').val()) $('main .form [name="system"]').focus();
						else if (!validate_file(true)) $('app[name=main] > main .form [name="usf"]').focus();
						else if (confirm("Are you sure you want to upload this file.\nTo delete a wrong data, you will need to contact a moderator.")) {
							$("app[name=main] > main .form button").attr("disabled", "");
							$("app[name=main] > main .upload-icon").show();
							$("app[name=main] > main form").submit();
						}
					}()); return false;
				},
				validate_file = function(recheck) {
					var f = d.querySelector('app[name=main] > main .form [name="usf"]').files[0],
						preview = $("app[name=main] > main .form div.file-box"), fprop = {
							name: d.querySelector('app[name=main] > main .form input[data-name="name"]'),
							size: d.querySelector('app[name=main] > main .form input[data-name="size"]')
						};
					// if (!recheck && typeof sv.img_link === "string") URL.revokeObjectURL(sv.img_link);
					if (typeof f !== "undefined") {
						let filename = f.name.toLowerCase().split(".");
						if (["csv"].includes(filename[filename.length-1]) && (f.size > 0)) {
							if (!recheck) {
								fprop["name"].value = f.name;
								fprop["size"].value = byte2text(f.size);
								try { if (!isSafari) {
									sv.img_link = URL.createObjectURL(f);
									preview.css("background-image", 'url("'+sv.img_link+'")');
								} } catch(ex) {}
							} return true;
						} else app.UI.notify(2, "กรุณาตรวจสอบว่าไฟล์ของคุณเป็นประเภท csv หรือไม่");
					} else {
						fprop["name"].value = ""; fprop["size"].value = "";
						preview.removeAttr("style");
						if (recheck) app.UI.notify(1, "กรุณาเลือกไฟล์ข้อมูลที่จะนำเข้า");
					} return false;
				};
				return {
					init: initialize,
					loadInfo, validate_file,
					import: upload
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
					<form class="form form-bs" method="post" enctype="multipart/form-data" action="response/import">
						<div class="group">
							<label>ประเภทข้อมูล</label>
							<select name="system" required onChange="enroll.loadInfo(this)">
								<option value disabled selected>— กรุณาเลือก —</option>
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
						<div class="message cyan information container" style="display: none;">
							<p>ในการทำไฟล์ข้อมูลสำหรับการนำเข้า ให้ทำโดยการนำข้อมูลใส่บน <a href="<?=$APP_CONST["baseURL"]?>go?url=https%3A%2F%2Fdocs.google.com%2Fa%2Fbodin.ac.th%2Fspreadsheets%2F" target="_blank">Google Sheets</a> แล้วกดดาวน์โหลดเป็นรูปแบบ "Comma-separated values (.csv)" ตรวจสอบว่าข้อมูลมีจำนวนหลักพอดี (คือไม่มีเครื่องหมายจุลภาคเกินมาที่ท้ายของแต่ละบรรทัด) แต่ละหลักถูกขั้นด้วยเพียงเครื่องหมายจุลภาคเพียงเครื่องหมายเดียวเท่านั้น ไม่มีเครื่องหมายอื่น (เช่น อัญประกาศคู่) และไม่มีหัวตาราง กล่าวคือ แถวแรกของไฟล์เป็นข้อมูลที่จะนำเข้า</p>
							<div class="about prs">
								<b>ข้อมูลการเรียงหัวตารางตามลำดับ</b>	
								<p class="selectable">เลขประจำตัวนักเรียน | <u data-title="ใช้เลขอ้างอิง">ช่วงเวลา</u></p>
							</div>
							<div class="about cng">
								<b>ข้อมูลการเรียงหัวตารางตามลำดับ</b>	
								<p class="selectable">เลขประจำตัวนักเรียน | <u data-title="แทนด้วยอักษร A-H">กลุ่มการเรียน</u> | <u data-title="ใช้เลขอ้างอิง">ช่วงเวลา</u></p>
							</div>
							<div class="about cnf">
								<b>ข้อมูลการเรียงหัวตารางตามลำดับ</b>	
								<p class="selectable">เลขประจำตัวนักเรียน | <u data-title="แทนด้วยอักษร A-H">กลุ่มการเรียน</u> | <u data-title="ใช้เลขอ้างอิง">ช่วงเวลา</u></p>
							</div>
							<div class="about new">
								<b>ข้อมูลการเรียงหัวตารางตามลำดับ</b>	
								<p class="selectable">เลขประจำตัวผู้สอบ | หมายเลขประจำตัวประชาชน | คำนำหน้าชื่อ | ชื่อจริง | นามสกุล <!-- | <u data-title="ใช้เลขอ้างอิงจากตารางด้านล่าง">ประเภทการรับ</u> --> | <u data-title="ใช้เลขอ้างอิง">ช่วงเวลา</u></p>
								<!-- <div class="table"><table class="normdisp"><thead><tr>
									<th>เลขอ้างอิง</th><th>ประเภทการรับ</th>
								</tr></thead><tbody>
									<tr><td center>1</td><td>ม.1 ห้องเรียนพิเศษคณิตศาสตร์</td></tr>
									<tr><td center>2</td><td>ม.1 ห้องเรียนพิเศษวิทยาศาสตร์ คณิตศาสตร์ เทคโนโลยี และสิ่งแวดล้อม ตามแนวทาง สสวท. และ สอวน.</td></tr>
									<tr><td center>3</td><td>ม.1 ห้องเรียนพิเศษ English Program</td></tr>
									<tr><td center>4</td><td>ม.4 ห้องเรียนพิเศษวิทยาศาสตร์ คณิตศาสตร์ เทคโนโลยี และสิ่งแวดล้อม</td></tr>
									<tr><td center>5</td><td>ม.4 โครงการห้องเรียน พสวท. (สู่ความเป็นเลิศ)</td></tr>
									<tr><td center>6</td><td>ม.1 ห้องเรียนทั่วไป (ในเขตพื้นที่บริการ)</td></tr>
									<tr><td center>7</td><td>ม.1 ห้องเรียนทั่วไป (นอกเขตพื้นที่บริการ)</td></tr>
									<tr><td center>8</td><td>ม.1 ห้องเรียนทั่วไป (ความสามารถพิเศษ)</td></tr>
									<tr><td center>9</td><td>ม.4 ห้องเรียนทั่วไป</td></tr>
								</tbody></table></div> -->
							</div>
						</div>
						<div class="submit container" style="display: none;">
						<div class="css-flex css-flex-gap-10">
							<div class="file-box land r-widescr">
								<input type="file" name="usf" accept=".csv" required />
							</div>
							<div class="css-flex css-flex-split css-flex-col css-full-x">
								<div class="container">
									<div class="group">
										<label>ชื่อไฟล์</label>
										<input type="text" data-name="name" readonly />
									</div>
									<div class="group">
										<label>ขนาดไฟล์</label>
										<input type="text" data-name="size" readonly />
									</div>
								</div>
								<div class="container">
									<button class="lime full-x center ripple-click" onClick="return enroll.import()" disabled>อัปโหลด</button>
									<div class="upload-icon" style="display: none;">
										<span>กำลังอัปโหลด...</span>
										<div class="animation">
											<i class="material-icons">file_upload</i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</section>
			</main>
			<?php
				$resourcePath["navtab"] = "private/block/core/side-panel/enroll.php";
				require($APP_RootDir."private/block/core/material/main.php");
			?>
		</app>
	</body>
</html>