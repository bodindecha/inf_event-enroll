<?php
	// Get resources
	require_once($APP_RootDir."private/script/lib/tcpdf/tcpdf.php"); # require_once($APP_RootDir."private/script/lib/fpdi/fpdi.php");
	require_once($APP_RootDir."private/script/lib/fpdi/autoload.php"); require_once($APP_RootDir."private/script/lib/fpdi/Tcpdf/Fpdi.php");
	// Helper functions
	function getDocTemplate(string $template_name) {
		global $APP_RootDir, $APP_CONST, $doc, $user_action, $output_mode, $mFile;
		$doc = [];
		switch ($template_name) {
			case "sggtmf":	header("Location: $APP_CONST[baseURL]_resx/service/view/file?furl=e%2Fenroll%2Fresource%2Ffile%2Fตัวอย่างใบรับรองผลการสมัครเข้าศึกษาต่อระดับชั้นมัธยมศึกษาปีที่-4.jpg"); exit(0); break;
			# case "eioad":	$user_action = "print";		$doc["source"] = "2569/คำชี้แจงเอกสารการมอบตัว"; break;
			case "eioad":	$user_action = "print";		$doc["source"] = "2569/แนวทางการเข้ามอบตัว ม.4 ห้องเรียน พสวท. 2569"; break;
			case "csgrf":	$user_action = "download";	$doc["source"] = "2569/ใบยื่นคำร้องขอเปลี่ยนกลุ่มการเรียน"; break;
			case "nwaive":	$user_action = "download";	$doc["source"] = "2569/คำร้องขอสละสิทธิ์นักเรียนใหม่"; break;
			case "rwaive":	$user_action = "download";	$doc["source"] = "2569/คำร้องขอสละสิทธิ์นักเรียนเดิม"; break;
			case "sef-1n":	$user_action = "download";	$doc["source"] = "2569/ใบมอบตัว ห้องเรียนทั่วไป ม.1"; break;
			case "sef-1m":	$user_action = "download";	$doc["source"] = "2569/ใบมอบตัว ห้องเรียนคณิต ม.1"; break;
			# case "sef-1s":	$user_action = "download";	$doc["source"] = "2566/ใบมอบตัว ห้องเรียนวิทย์ ม.1"; break;
			case "sef-1p":	$user_action = "download";	$doc["source"] = "2569/ใบมอบตัว ห้องเรียนวิทย์ สอวน. ม.1"; break;
			case "sef-1i":	$user_action = "download";	$doc["source"] = "2569/ใบมอบตัว ห้องเรียนวิทย์ สสวท. ม.1"; break;
			case "sef-4n":	$user_action = "download";	$doc["source"] = "2569/ใบมอบตัว ห้องเรียนทั่วไป ม.4"; break;
			case "sef-4s":	$user_action = "download";	$doc["source"] = "2569/ใบมอบตัว ห้องเรียนพิเศษ ม.4"; break;
			case "sef-4d":	$user_action = "download";	$doc["source"] = "2569/ใบมอบตัว ห้องเรียนพสวท ม.4"; break;
			case "sef-1e":	$user_action = "download";	$doc["source"] = "2569/ใบมอบตัว ห้องเรียน EP ม.1"; break;
			case "sef-4e":	$user_action = "download";	$doc["source"] = "2569/ใบมอบตัว ห้องเรียน EP ม.4"; break;
			default: TianTcl::http_response_code(902);
		} $doc["source"] = "$APP_RootDir$APP_CONST[publicDir]$APP_CONST[baseURL]e/enroll/resource/file/$doc[source]";
		if (!file_exists("$doc[source].pdf")) TianTcl::http_response_code(900);
		$name = basename($doc["source"]);
		regExTest("/(\ v(\d|\-)+)$/", $name, $versioning);
		$versioning = $versioning && count($versioning) ? strlen($versioning[1]) : 0;
		$doc["title"] = "[BD Admission] ".substr($name, 0, strlen($name) - $versioning); // งานรับนักเรียน รร.บ.ด.
		$doc["source"] .= ".pdf";
		$mFile = new setasign\Fpdi\Tcpdf\Fpdi("P", PDF_UNIT, "A4", true, "UTF-8", false);
		// Custom params
		if (isset($_REQUEST["action"])) $user_action = $_REQUEST["action"];
		if (!in_array($user_action, ["print", "download"])) TianTcl::http_response_code(915);
		$output_mode ??= [
			"print" => "I",
			"download" => "D"
		][$user_action];
	}
	// Custom params
	$linkPrefix = "$APP_CONST[domain]$APP_CONST[baseURL]";
	if (isset($_REQUEST["export"]) && strlen(trim($_REQUEST["export"]))) $exportName = trim($_REQUEST["export"]);
	// Miscelleneous functions
	define("PX_DEV", 3.62);
	define("CELL_PD", 1);
	$row_dev = 0;
	function in2px(float|int|string $in): float {
		return (float)$in * 72;
	}
	function in2mm(float|int|string $in): float {
		return (float)$in * 25.4;
	}
	function px2mm(float|int|string $px): float {
		return (float)$px / DOC_DPI * 25.4;
	}
	function cs2mm(float|int|string $cs): float {
		return px2mm($cs * (CELL_SP + 2 * PX_DEV));
	}
	function cellTxtoe(mixed $text, int|float $cellWidth, string $ellipsis = "…"): string {
		global $mFile;
		if (gettype($text) != "string") $text = (string)$text;
		$textWidth = $mFile -> GetStringWidth($text);
		if ($textWidth <= $cellWidth - CELL_PD) return $text;
		$ellipsisWidth = $mFile -> GetStringWidth($ellipsis);
		$truncatedText = "";
		foreach (mb_str_split($text) as $char) {
			if ($mFile -> GetStringWidth($truncatedText . $char) > $cellWidth - $ellipsisWidth - CELL_PD)
				return $truncatedText . $ellipsis;
			$truncatedText .= $char;
		} return $text;
	}
	function textAt(
		float|int|string $x, float|int|string $y,
		float|int|string $w, float|int|string $h = 0,
		mixed $txt = "",
		int $border = 0,
		int $lines = 0,
		string $align = "",
		bool $fill = false,
		string $link = "",
		int $stretch = 0,
		bool $ignore_min_height = false,
		string $calign = "T",
		string $valign = "M",
		string $ellipsis = "…"
	): void {
		global $mFile, $cfg;
		$cellWidth = cs2mm((float)$w);
		$options = [$border, $lines, $align, $fill, $link, $stretch, $ignore_min_height, $calign, $valign];

		$mFile -> SetXY(
			($cfg["margin"]["left"] ?? 0) + cs2mm((float)$x),
			($cfg["margin"]["top"] ?? 0) + px2mm((float)$y)
		); $symbol = "⌀";
		if (str_contains($txt, $symbol)) { # RegExTest("/[⌀→]/gu", $txt)
			$parts = explode($symbol, $txt); $moved = $mFile -> GetStringWidth($parts[0]);
			$font_settings = [$mFile -> getFontFamily(), $mFile -> getFontStyle(), $mFile -> getFontSizePt()];
			$mFile -> Cell(
				$moved, px2mm((float)$h),
				$parts[0], ...$options
			); $parts = array_slice($parts, 1);
			foreach ($parts as $part) {
				$mFile -> SetFont("SegoeUISymbol", "R", $font_settings[2]);
				$symbol_size = $mFile -> GetStringWidth($symbol);
				$mFile -> SetXY(
					($cfg["margin"]["left"] ?? 0) + cs2mm((float)$x) + $moved,
					($cfg["margin"]["top"] ?? 0) + px2mm((float)$y)
				); $mFile -> Cell(
					$symbol_size, px2mm((float)$h),
					$symbol, ...$options
				); $moved += $symbol_size;
				$mFile -> SetFont(...$font_settings);
				$mFile -> SetXY(
					($cfg["margin"]["left"] ?? 0) + cs2mm((float)$x) + $moved,
					($cfg["margin"]["top"] ?? 0) + px2mm((float)$y)
				); $mFile -> Cell(
					$cellWidth - $moved, px2mm((float)$h),
					cellTxtoe($part, $cellWidth - $moved, $ellipsis),
					...$options
				);
			}
		} else $mFile -> Cell(
			$cellWidth, px2mm((float)$h),
			cellTxtoe($txt, $cellWidth, $ellipsis),
			...$options
		);
	}
	function atRow(int|string $row): int {
		global $row_dev;
		return ($row_dev ?? 0) + ((int)$row - 0.5) * 2 * PX_DEV;
	}
	function atDocRow(int|string $row): int {
		return ROW_HGT * ((int)$row - 1) + atRow($row);
	}
	function asFloatVal(string|int|float $val): float {
		return (float)str_replace(",", "", $val);
	}
	function asCeil(string|int|float $val): int|string {
		$val = asFloatVal($val);
		return $val ? ceil($val) : "";
	}
	function addPrintJS(): void {
		global $mFile;
		$script = "print();";
		$mFile -> IncludeJS($script);
	}
	// Parsed document
	getDocTemplate($docID);
	// Get student data
	$authuser = $user_override ?? ($_SESSION["auth"]["user"] ?? "");
	if (!strlen($authuser) && isset($_REQUEST["authuser"])) $authuser = $vToken -> read(trim($_REQUEST["authuser"]));
	if (strlen($authuser) && (isset($_REQUEST["authuser"]) || $_SESSION["auth"]["type"]=="s" || isset($user_override))) {
		$is_oldstd = (in_array($docID, ["csgrf", "sef-4n", "rwaive"]) && RegExTest("/^[45]/", $authuser)) || $authuser == "99999";
		// Fetch biological information
		$get = $is_oldstd
			? "SELECT citizen_id,birthy+543 AS birthy,birthm,birthd,room,number FROM user_s WHERE stdid=$authuser"
			: "SELECT amsid,CONCAT(namepth,namefth,'  ',namelth) AS nameath,natid AS citizen_id,CONCAT(namefen,' ',namelen) AS nameaen FROM admission_newstd WHERE datid=$authuser"
		; $read = $APP_DB[$is_oldstd ? 0 : 5] -> query($get) -> fetch_array(MYSQLI_ASSOC);
		$read["nameath"] = $_SESSION["auth"]["name"]["th"]["a"] ?? $read["nameath"] ?? "";
		$read["nameaen"] = $_SESSION["auth"]["name"]["en"]["a"] ?? $read["nameaen"] ?? "";
		// Change file name
		if (isset($read["amsid"])) $authuser = $read["amsid"];
		$doc["title"] .= " - $authuser";
	}
	// Create & fill document
	/* --- PDF generation --- (BEGIN) */
	// Configuration
	$cfg = array(
		"title" => "$doc[title]",
		"font" => "thsarabun"
	);
	$mFile -> SetTitle($cfg["title"]);
	$mFile -> SetSubject($cfg["title"]);
	$mFile -> SetAuthor("งานรับนักเรียน โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)");
	$mFile -> SetCreator("Bodindecha (Sing Singhaseni) School: INF-Webapp");
	# $mFile -> SetKeywords("");
	$mFile -> setPrintHeader(false);
	$mFile -> setPrintFooter(false);
	$mFile -> SetAutoPageBreak(false, 0);
	# $mFile -> setDocCreationTimestamp(" GMT+07:00");
	# $mFile -> setDocModificationTimestamp(" GMT+07:00");
	$mFile -> setProtection(
		["modify", "copy", "annot-forms", "extract", "assemble"],
		$user_action == "print" ? "" : DBConfig::get("PSWD-PDF_view"),
		DBConfig::get("PSWD-PDF_master"),
	1);
	$mFile -> setViewerPreferences(array(
		"FitWindow" => true,
		"CenterWindow" => true,
		"PrintScaling" => "AppDefault", // None, AppDefault
		# "Duplex" => "DuplexFlipLongEdge", // Simplex, DuplexFlipShortEdge, DuplexFlipLongEdge
		# "PrintPageRange" => [1, 2, 3],
		"NumCopies" => 1
	));
	/***
	 * Pixel size: 1240×1754
	 * Physical size: 8.27×11.69 in
	 * DPI: 150
	 **/
	define("DOC_DPI", 150);
	define("CELL_SP", 24); // px
	// Edit
	$pages = $mFile -> setSourceFile($doc["source"]);
	for ($pageno = 1; $pageno <= $pages; $pageno++) {
		// Get original page
		$tmp_page = $mFile -> importPage($pageno);
		$tmp_info = $mFile -> getTemplateSize($tmp_page);
		$mFile -> addPage($tmp_info["orientation"]);
		$showBdr = (int)($_REQUEST["bdr"] ?? 0);
		$mFile -> useTemplate($tmp_page);
		// Annotations
		$mFile -> SetTextColor(0, 0, 0);
		$mFile -> SetFont($cfg["font"], "R", 14);
		if (regExTest("/^sef\-(1[nmspie]|4[nsde])$/", $docID)) {
			define("ROW_HGT", 33); // px
			$cfg["margin"] = array("left" => 30.75, "top" => 6.4, "right" => 9.6);
			switch ($pageno) {
				case 1: {
					textAt(7.25, atDocRow(9) + 2, 15.75, ROW_HGT, $read["nameath"], $showBdr, 1, "L");
					textAt(11.25, atDocRow(10), 20, ROW_HGT, strtoupper($read["nameaen"]), $showBdr, 1, "L");
					if (isset($read["citizen_id"]) && !empty($read["citizen_id"])) {
						$natID = $read["citizen_id"]; # vsprintf("%s-%s%s%s%s-%s%s%s%s%s-%s%s-%s", str_split($read["citizen_id"]));
						$natSpc = [1, 5, 10, 12]; $natShift = 0;
						for ($idx = 0; $idx < 13; $idx++) {
							if (in_array($idx, $natSpc)) $natShift += 1;
							textAt(
								(7.2 + $idx) + ($idx * 0.06) + ($natShift * 0.3),
								atDocRow(11) + 2.5, 0.85, ROW_HGT,
								$natID[$idx], $showBdr, 1, "C"
                            );
                        }
					}
					textAt(12.5, atDocRow(27)+2, 19, ROW_HGT, $read["nameath"], $showBdr, 1, "L");
					if ($is_oldstd) {
						$mFile -> SetFont($cfg["font"], "B", 16);
						textAt(28.8, atDocRow(5)+25, 3, ROW_HGT, $authuser, $showBdr, 1, "C");
						$mFile -> SetFont($cfg["font"], "R", 14);
						if (!empty($read["birthd"])) textAt(2.2, atDocRow(28), 2.4, ROW_HGT, $read["birthd"], $showBdr, 1, "C");
						if (!empty($read["birthm"])) textAt(6.3, atDocRow(28), 5.5, ROW_HGT, $APP_CONST["TH"]["month"][(int)$read["birthm"] - 1], $showBdr, 1, "C");
						if (!empty($read["birthy"])) textAt(13.4, atDocRow(28), 2.6, ROW_HGT, $read["birthy"], $showBdr, 1, "C");
						textAt(4.8, atDocRow(39)-4, 15, ROW_HGT, "โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)", $showBdr, 1, "L");
					}
					textAt(24, atDocRow(39)-4, 7.5, ROW_HGT, (int)$docID[4]==4 ? "ม.3" : "ป.6", $showBdr, 1, "C");
				break; }
				case 2: {
					textAt(13.9, atDocRow(20)-2, 18.1, ROW_HGT, $read["nameath"], $showBdr, 1, "L");
					textAt(15.8, atDocRow(28)+8, 13, ROW_HGT, $read["nameath"], $showBdr, 1, "C");
					# $curDate = explode(" ", date2TH("", false));
					# $curDate = str_repeat(" ", strlen($curDate[0]) + strlen($curDate[1]) + 2).$curDate[2];
					# textAt(18.8, atDocRow(29)+8, 9.5, ROW_HGT, $curDate, $showBdr, 1, "C");
					textAt(25.5, atDocRow(29)+8, 2, ROW_HGT, (int)date("Y")+543, $showBdr, 1, "C");
				break; }
            }
		} else if ($docID == "csgrf") {
			// 2565,69
			define("ROW_HGT", 31.5); // px
			// @14pt
			# $cfg["margin"] = array("left" => 9.6, "top" => 8.2, "right" => 9.6);
			# textAt(28.1, atDocRow(12)-13, 2, ROW_HGT, num2locale((int)date("Y")+543), $showBdr, 1, "C");
			# textAt(8.75, atDocRow(13), 11, ROW_HGT, $read["nameath"], $showBdr, 1, "C");
			# textAt(28, atDocRow(13), 1.5, ROW_HGT, num2locale($read["room"]), $showBdr, 1, "C");
			# textAt(31.15, atDocRow(13), 2, ROW_HGT, num2locale($read["number"]), $showBdr, 1, "C");
			# textAt(9.25, atDocRow(14), 6.75, ROW_HGT, num2locale($authuser), $showBdr, 1, "C");
			// @16pt
			$cfg["margin"] = array("left" => 9.6, "top" => 7.6, "right" => 9.6);
			textAt(8.75, atDocRow(13)+3, 11, ROW_HGT, $read["nameath"], $showBdr, 1, "C");
			$mFile -> SetFont($cfg["font"], "R", 16);
			textAt(28, atDocRow(12)-13, 2.5, ROW_HGT, num2locale((int)date("Y")+543), $showBdr, 1, "C");
			textAt(28, atDocRow(13), 1.5, ROW_HGT, num2locale($read["room"]), $showBdr, 1, "C");
			textAt(31.15, atDocRow(13), 2, ROW_HGT, num2locale($read["number"]), $showBdr, 1, "C");
			textAt(9.25, atDocRow(14), 6.75, ROW_HGT, num2locale($authuser), $showBdr, 1, "C");
			// 2567-68
			# define("ROW_HGT", 36); // px
			# $cfg["margin"] = array("left" => 9.6, "top" => 5.8, "right" => 9.6);
			# textAt(28.5, atDocRow(10), 2, ROW_HGT, (int)date("Y")+543, $showBdr, 1, "C");
			# textAt(8.75, atDocRow(11), 11, ROW_HGT, $read["nameath"], $showBdr, 1, "C");
			# textAt(28, atDocRow(11), 1.5, ROW_HGT, num2locale($read["room"]), $showBdr, 1, "C");
			# textAt(31, atDocRow(11), 2, ROW_HGT, num2locale($read["number"]), $showBdr, 1, "C");
			# textAt(9, atDocRow(12), 7, ROW_HGT, num2locale($authuser), $showBdr, 1, "C");
		} else if ($docID == "nwaive") { // formerly waiver
			define("ROW_HGT", 34); // px
			$mFile -> SetFont($cfg["font"], "R", 16);
			// 2567-68
			# $cfg["margin"] = array("left" => 9.6, "top" => 6.2, "right" => 9.6);
			# textAt(30, atDocRow(10), 3, ROW_HGT, num2locale((int)date("Y")+543), $showBdr, 1, "C");
			# $read["nameath"] = preg_replace("/^(ด\.[ชญ]\.|นา(ย|งสาว)) ?/", "", $read["nameath"]);
			# textAt(13.25, atDocRow(16)-2, 13, ROW_HGT, $read["nameath"], $showBdr, 1, "C");
			# textAt(3, atDocRow(17), 10.5, ROW_HGT, num2locale($authuser), $showBdr, 1, "C");
			// 2569
			define("ROW_HGT", 34); // px
			$cfg["margin"] = array("left" => 9.6, "top" => 3.6, "right" => 9.6);
			textAt(29.75, atDocRow(10), 3.25, ROW_HGT, num2locale((int)date("Y")+543), $showBdr, 1, "C");
			$read["nameath"] = preg_replace("/^(ด\.[ชญ]\.|นา(ย|งสาว)) ?/", "", $read["nameath"]);
			textAt(13, atDocRow(17)-2, 12.5, ROW_HGT, $read["nameath"], $showBdr, 1, "C");
			textAt(3, atDocRow(18), 10, ROW_HGT, num2locale($authuser), $showBdr, 1, "C");
		} else if ($docID == "rwaive") {
			define("ROW_HGT", 34); // px
			$cfg["margin"] = array("left" => 9.6, "top" => 3.6, "right" => 9.6);
			$mFile -> SetFont($cfg["font"], "R", 16);
			textAt(29.75, atDocRow(10), 3.25, ROW_HGT, num2locale((int)date("Y")+543), $showBdr, 1, "C");
			$read["nameath"] = preg_replace("/^(ด\.[ชญ]\.|นา(ย|งสาว)) ?/", "", $read["nameath"]);
			textAt(12.25, atDocRow(17)-2, 16, ROW_HGT, $read["nameath"], $showBdr, 1, "C");
			textAt(30.5, atDocRow(17)-2, 2.5, ROW_HGT, num2locale($read["room"]), $showBdr, 1, "C");
			textAt(6.5, atDocRow(18), 5, ROW_HGT, num2locale($authuser), $showBdr, 1, "C");
		}
	} addPrintJS();
	// Send out file
	$mFile -> Output($exportName ?? "$cfg[title].pdf", $output_mode);
	/* --- PDF generation --- (END) */
	$APP_DB[0] -> close();
	$APP_DB[5] -> close();
?>