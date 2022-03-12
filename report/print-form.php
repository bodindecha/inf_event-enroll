<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "พิมพ์เอกสารใบมอบตัว";
	$home_menu = "mod";
	
	$permitted = has_perm("admission");
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			main .form input[type="number"]::-webkit-inner-spin-button { display: none; }
			main div.result > *:not(:last-child) { margin: 0px 0px 10px; }
			main button > i.material-icons { transform: translateY(5px); }
			main .last { margin-bottom: 0px !important; }
		</style>
		<script type="text/javascript">
			const psf = function() {
				const cv = { APIurl: "/e/enroll/resource/php/api" };
				var lookup = function() {
					(function() {
						var data = {
							type: "mod", act: "check",
							param: document.querySelector('main .form [name="sid"]').value.trim()
						}; if (!/^([13-7]\d{4}|8\d{5}|9{5})$/.test(data.param)) {
							app.ui.notify(1, [2, "รูปแบบเลขประจำตัวผู้สมัครไม่ถูกต้อง"]);
							$('main .form [name="sid"]').focus();
						} else {
							document.querySelector('main .form button[name="lookup"]').disabled = true;
							$.post(cv.APIurl, data, function(res, hsc) {
								var dat = JSON.parse(res);
								if (dat.success) {
									$("main div.result").html('<p>'+dat.info.message+'</p><div class="group split last">&nbsp;<button class="red hollow last" onClick="psf.close()" name="danger">ปิด</button></div>');
									if (dat.info.action) {
										let printData = dat.info.impact.split("+"); 
										$("main div.result .group")
											// .prepend('<a role="button" class="gray" href="print-docu?ment='+printData[1]+'&ID='+printData[0]+'" target="dlframe" draggable="false"><i class="material-icons">print</i>&nbsp;พิมพ์ใบมอบตัว</a>');
											.prepend('<button class="gray" onClick="psf.print(this)" data-href="print-docu?ment='+printData[1]+'&ID='+printData[0]+'"><i class="material-icons">print</i>&nbsp;พิมพ์ใบมอบตัว</button>');
									} $("main div.result").toggle("slide");
								} else {
									dat.reason.forEach(em => app.ui.notify(1, em));
									document.querySelector('main .form button[name="lookup"]').disabled = false;
								}
							});
						}
					}()); return false; 
				};
				var closeInfo = function() {
					$("main div.result").toggle("fade", function() {
						$(this).html("");
					}); document.querySelector('main .form button[name="lookup"]').disabled = false;
				};
				var printPDF = function(me) {
					var link = $(me).attr("data-href"), frame = document.querySelector('main iframe[name="dlframe"]');
					printJS("/e/enroll/report/"+link);
					// frame.src = link;
					// frame.focus(); frame.contentWindow.print();
				};
				return {
					search: lookup,
					close: closeInfo,
					print: printPDF
				};
			}();
		</script>
		<script type="text/javascript" src="/resource/js/lib/print.min.js"></script>
	</head>
	<body>
		<?php require($dirPWroot."e/enroll/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<?php if (!$permitted) echo '<iframe src="/error/901">901: No Permission</iframe>'; else { ?>
			<div class="container">
				<h2>การจัดพิมพ์เอกสาร (ใบมอบตัวนักเรียน)</h2>
				<form class="form inline">
					<div class="group">
						<span>เลขประจำตัวผู้สมัคร</span>
						<input type="number" name="sid" maxlength="6">
					</div>
					<button name="lookup" class="blue" onClick="return psf.search()">ค้นหาข้อมูล</button>
				</form>
				<div class="result form message cyan" style="display: none;"></div>
				<iframe name="dlframe" hidden></iframe>
			</div><?php } ?>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>