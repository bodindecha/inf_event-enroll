<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	$normalized_control = false;
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
    
	require_once($dirPWroot."e/enroll/resource/php/config.php");
	if (isset($_REQUEST["ment"]) && !empty($_REQUEST["ment"]) && isset($_REQUEST["ID"]) && !empty($_REQUEST["ID"])) {
		$group = intval(decryptNID(str_rot13(strrev(trim($_REQUEST["ment"]))))); $authuser = decryptNID(trim($_REQUEST["ID"]));
	}

    if (isset($group)) {
		switch ($group - 1) {
			case 0: case 1: case 2: $type = "pdf"; $dl = true; $path = "ใบมอบตัว ห้องเรียนทั่วไป ม.1.$type"; $pages = 1; break;
			case 3: $type = "pdf"; $dl = true; $path = "ใบมอบตัว ห้องเรียนคณิต ม.1.$type"; $pages = 1; break;
			case 4: $type = "pdf"; $dl = true; $path = "ใบมอบตัว ห้องเรียนวิทย์ ม.1.$type"; $pages = 1; break;
			case 5: $type = "pdf"; $dl = true; $path = "ใบมอบตัว ห้องเรียนพิเศษ ม.4.$type"; $pages = 1; break;
			case 6: $type = "pdf"; $dl = true; $path = "ใบมอบตัว ห้องเรียนทั่วไป ม.4.$type"; $pages = 1; break;
			case 7: $type = "pdf"; $dl = true; $path = "ใบมอบตัว ห้องเรียนพสวท ม.4.$type"; $pages = 1; break;
            default: $error = "900"; $errorMsg = '2, "ประเภทเอกสารไม่ถูกต้อง"'; break;
		} if (!isset($error) && !file_exists($dirPWroot."e/enroll/resource/file/$path")) { $error = "404"; $errorMsg = '3, "ไม่มีไฟล์ต้นฉบับ"'; }
    } else { $error = "902"; $errorMsg = '1, "ไม่พบข้อมูลเอกสาร"'; }

    if (!isset($error)) {
		if (!isset($authuser) || empty($authuser)) { $error = "901"; $errorMsg = '2, "เลขประจำตัวผู้สมัครไใาถูกต้อง"'; }
		else {
			/* --- PDF generation --- (BEGIN) */
			require_once($dirPWroot."resource/php/lib/tcpdf/tcpdf.php"); require_once($dirPWroot."resource/php/lib/fpdi/fpdi.php");
			$exportfile = new FPDI("P", PDF_UNIT, "A4", true, 'UTF-8', false);
			// Configuration
			$fileTitle = "งานรับนักเรียน รร.บ.ด. - ".substr($path, 0, strlen($path)-strlen($type)-1);
			# $exportfile -> SetProtection(array("modify", "copy", "annot-forms", "fill-forms", "extract", "assemble"), "", null, 0, null);
			$exportfile -> SetCreator("Bodindecha (Sing Singhaseni) School: INF-Webapp");
			$exportfile -> SetAuthor("งานรับนักเรียน โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)");
			$exportfile -> SetTitle($fileTitle);
			$exportfile -> SetSubject($fileTitle);
			$exportfile -> setPrintHeader(false);
			$exportfile -> setPrintFooter(false);
			# $exportfile -> SetKeywords("");
			$exportfile -> SetAutoPageBreak(false, 0);
			// Edit
			$pages = $exportfile -> setSourceFile($dirPWroot."e/enroll/resource/file/$path");
			for ($pageno = 1; $pageno <= $pages; $pageno++) {
				// Get original page
				$temppage = $exportfile -> importPage($pageno);
				$tempinfo = $exportfile -> getTemplateSize($temppage);
				$exportfile -> addPage($tempinfo['h'] > $tempinfo['w'] ? "P" : "L");
				$exportfile -> useTemplate($temppage);
				if ($pageno == 1) { // Write PDF for confirm
					$exportfile -> SetTextColor(0, 0, 0);
					// Fetch biological information
					require($dirPWroot."e/resource/db_connect.php");
					$stdbio = $db -> query("SELECT amsid,CONCAT(namepth,namefth,' ',namelth) AS nameath,natid AS citizen_id,CONCAT(namefen,' ',namelen) AS nameaen FROM admission_newstd WHERE datid=$authuser") -> fetch_array(MYSQLI_ASSOC);
					$db -> close();
					// Change file name
					$authuser = $stdbio['amsid'];
					$dlname = substr($path, 0, strlen($path)-strlen($type)-1)." - $authuser.$type";
					// Add fullname 1
					$exportfile -> SetFont("thsarabun", "R", 14);
					$exportfile -> SetXY(53.5, 53.75);
					$exportfile -> Cell(81, 0, $stdbio['nameath'], 0, 1, "C", 0, "", 0);
					// Add Citizen ID
					if (isset($stdbio) && !empty($stdbio['citizen_id'])) $stdbio['citizen_id'] = vsprintf("%s-%s%s%s%s-%s%s%s%s%s-%s%s-%s", str_split($stdbio['citizen_id']));
					$exportfile -> SetXY(166.5, 53.75);
					$exportfile -> Cell(33, 0, $stdbio['citizen_id'] ?? "", 0, 1, "C", 0, "", 0);
					// Add English name
					$exportfile -> SetXY(77, 59.25);
					$exportfile -> Cell(123, 0, $stdbio['nameaen'], 0, 1, "L", 0, "", 0);
					// Add fullname 2
					$exportfile -> SetXY(66.6, 106.1);
					$exportfile -> Cell(55.5, 0, $stdbio['nameath'], 0, 1, "C", 0, "", 0);
					// Add Grade
					$stdbio['oldgrade'] = $group > 4 ? "ม.3" : "ป.6";
					$exportfile -> SetXY(190, 146.85);
					$exportfile -> Cell(10, 0, $stdbio['oldgrade'], 0, 1, "C", 0, "", 0);
				}
			} // Send out file
			$exportfile -> Output($dlname ?? $path, "I");
			/* --- PDF generation --- (END) */
		}
    } else {
        $header_title = (isset($error) ? "Error: $error" : "ใบมอบตัว");

		$require_sso = false;
	}
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<script type="text/javascript">
			<?php if (isset($error)) { ?>$
			$(document).ready(function() {
				app.ui.notify(1, [<?=$errorMsg?>]);
			}); if (self == top) document.querySelector("main").innerHTML = '<iframe src="/error/<?=$error?>">Error: <?=$error?></iframe>';
			<?php } else echo 'window.print();'; ?>
		</script>
	</head>
	<body>
		<?php require($dirPWroot."e/enroll/resource/hpe/header.php"); ?>
		<main></main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."e/enroll/resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>