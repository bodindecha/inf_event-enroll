<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/start/API.php");
	API::initialize(false);
	require_once($APP_RootDir."public_html/e/enroll/api/_log-v1.php");
	# if (empty($APP_USER)) { header("Location: $signinURL"); exit(0); }
	# hasPermission("dev", denyTo: 901);
	require_once($APP_RootDir."private/script/lib/TianTcl/virtual-token.php");
	require_once("../hpe/doc-gen.php");
	// Get information
	$docID = escapeSQL($_REQUEST["name"]);
	if (empty($docID)) TianTcl::http_response_code(919);
	getDocTemplate($docID);
	// Get student data
	$authuser = $_SESSION["auth"]["user"] ?? "";
	if (!strlen($authuser) && isset($_REQUEST["authuser"])) $authuser = $vToken -> read(trim($_REQUEST["authuser"]));
	if (strlen($authuser) && (isset($_REQUEST["authuser"]) || $_SESSION["auth"]["type"]=="s")) {
		$is_oldstd = ($docID == "sef-4n" && RegExTest("/^[45]/", $authuser)) || $authuser == "99999";
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
					textAt(7.25, atDocRow(9), 15.75, ROW_HGT, $read["nameath"], $showBdr, 1, "L");
					textAt(11.25, atDocRow(10), 20, ROW_HGT, strtoupper($read["nameaen"]), $showBdr, 1, "L");
					if (isset($read["citizen_id"]) && !empty($read["citizen_id"])) {
						$natID = $read["citizen_id"]; # vsprintf("%s-%s%s%s%s-%s%s%s%s%s-%s%s-%s", str_split($read["citizen_id"]));
						$natSpc = [1, 5, 10, 12]; $natShift = 0;
						for ($idx = 0; $idx < 13; $idx++) {
							if (in_array($idx, $natSpc)) $natShift += 1;
							textAt(
								(7.2 + $idx) + ($idx * 0.06) + ($natShift * 0.3),
								atDocRow(11) + 2, 0.85, ROW_HGT,
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
					textAt(24, atDocRow(39)-4, 7.5, ROW_HGT, $is_oldstd ? "ม.3" : "ป.6", $showBdr, 1, "C");
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
			define("ROW_HGT", 36); // px
			$cfg["margin"] = array("left" => 9.6, "top" => 5.8, "right" => 9.6);
			textAt(8.75, atDocRow(11), 11, ROW_HGT, $read["nameath"], $showBdr, 1, "C");
			textAt(28, atDocRow(11), 1.5, ROW_HGT, num2locale($read["room"]), $showBdr, 1, "C");
			textAt(31, atDocRow(11), 2, ROW_HGT, num2locale($read["number"]), $showBdr, 1, "C");
			textAt(9, atDocRow(12), 7, ROW_HGT, num2locale($authuser), $showBdr, 1, "C");
		} else if ($docID == "waiver") {
			define("ROW_HGT", 34); // px
			$cfg["margin"] = array("left" => 9.6, "top" => 6.2, "right" => 9.6);
			$mFile -> SetFont($cfg["font"], "R", 16);
			textAt(30, atDocRow(10), 3, ROW_HGT, num2locale((int)date("Y")+543), $showBdr, 1, "C");
			textAt(3, atDocRow(17), 10.5, ROW_HGT, num2locale($authuser), $showBdr, 1, "C");
		}
	} addPrintJS();
	// Send out file
	$mFile -> Output($exportName ?? "$cfg[title].pdf", $output_mode);
	/* --- PDF generation --- (END) */
	$APP_DB[0] -> close();
	$APP_DB[5] -> close();
?>