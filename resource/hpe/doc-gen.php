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
			case "csgrf":	$user_action = "download";	$doc["source"] = "2568/ใบยื่นคำร้องขอเปลี่ยนกลุ่มการเรียน"; break;
			case "waiver":	$user_action = "download";	$doc["source"] = "2568/คำร้องขอสละสิทธิ์"; break;
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
		$versioning = count($versioning) ? strlen($versioning[1]) : 0;
		$doc["title"] = "[BD Admission] ".substr($name, 0, strlen($name) - $versioning); // งานรับนักเรียน รร.บ.ด.
		$doc["source"] .= ".pdf";
		$mFile = new setasign\Fpdi\Tcpdf\Fpdi("P", PDF_UNIT, "A4", true, "UTF-8", false);
		// Custom params
		if (!in_array($user_action, ["print", "download"])) TianTcl::http_response_code(915);
		$user_action ??= $_REQUEST["action"] ?? null;
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
?>