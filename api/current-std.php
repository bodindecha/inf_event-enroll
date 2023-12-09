<?php
	# $normal_params = false;
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/start/API.php");
	$year = $_SESSION["stif"]["t_year"];
	require_once($APP_RootDir."public_html/e/enroll/api/_log-v1.php");
	// Execute
	define("ADMISSION_ANSWER_YES", "Y");
	define("ADMISSION_ANSWER_NO", "N");
	define("ADMISSION_SECRET_KEY", "B0d1^/-4dm1$5|o/v");
	define("ADMISSION_SECRET_SALT", 2565.2024);
	function switch_ref_encrypt($refID) {
		global $TCL;
		if (!isset($TCL)) {
			if (!class_exists("TianTcl")) require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
			else $TCL = new TianTcl();
		} return $TCL -> encrypt(
			"swt".str_pad(strval($refID), 4, "0", STR_PAD_LEFT)."ADM",
			key: ADMISSION_SECRET_KEY,
			salt: ADMISSION_SECRET_SALT
		);
	}
	function switch_ref_decrypt($reference) {
		global $TCL;
		if (!isset($TCL)) {
			if (!class_exists("TianTcl")) require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
			else $TCL = new TianTcl();
		} return rtrim(substr($TCL -> decrypt($reference, key: ADMISSION_SECRET_KEY, salt: ADMISSION_SECRET_SALT), 3), "ADM");
	}
	if (!isset($_SESSION["auth"]) || $_SESSION["auth"]["type"] <> "s") errorMessage(1, "You are unauthorized.");
	else switch ($action) {
		case "get": {
			switch ($command) {
				case "status": {
					$getInfo = $APP_DB[5] -> query("SELECT a.choose,a.filetype,a.lastupdate AS time,a.ip,b.name,d.name AS new FROM admission_confirm a INNER JOIN admission_sgroup b ON a.type=b.code LEFT JOIN admission_change c ON a.stdid=c.stdid LEFT JOIN admission_sgroup d ON c.choose=d.code WHERE a.stdid=$APP_USER");
					$getHist = $APP_DB[5] -> query("SELECT 1 FROM admission_switch WHERE stdid=$APP_USER");
					if (!$getInfo || !$getHist) {
						errorMessage(3, "Unable to load data.");
						syslog_e($APP_USER, "admission", "swt", "getStatus", "", "fail", "", "InvalidQuery");
					} else if (!$getInfo -> num_rows) {
						errorMessage(1, "You are not allowed to perform this action for you are not on the list of those eligible to study.");
						syslog_e($APP_USER, "admission", "swt", "getStatus", "", "fail", "", "Empty");
					} else {
						$read = $getInfo -> fetch_array(MYSQLI_ASSOC);
						if ($read["choose"] == null) successState(array("chosen" => false));
						else successState(array(
							"chosen" => true,
							"choose" => $read["choose"] == ADMISSION_ANSWER_YES,
							"at" => array(
								"date" => date("d/m/Y", strtotime($read["time"])),
								"time" => date("H:i", strtotime($read["time"]))
							), "ip" => $read["ip"],
							"group" => $read["name"],
							"hasRecords" => $getHist -> num_rows > 0,
							"hasUploadedDocument" => ($read["choose"] == ADMISSION_ANSWER_NO && !empty($read["filetype"]))
						));
					}
				break; }
				case "history": {
					$get = $APP_DB[5] -> query("SELECT refID,prev,reason,time FROM admission_switch WHERE stdid=$APP_USER ORDER BY time DESC");
					if (!$get) {
						errorMessage(3, "Unable to load history.");
						syslog_e($APP_USER, "admission", "swt", "getHistory", "", "fail", "", "InvalidQuery");
					} else if (!$get -> num_rows) {
						errorMessage(1, "Sorry, but your changes history is currently not available.");
						syslog_e($APP_USER, "admission", "swt", "getHistory", "", "fail", "", "Empty");
					} else {
						$hist = array();
						while ($read = $get -> fetch_assoc()) array_push($hist, array(
							"reference" => switch_ref_encrypt($read["refID"]),
							"newChoice" => $read["prev"] == ADMISSION_ANSWER_NO,
							"hasMemorandum" => strlen($read["reason"]) > 0,
							"timestamp" => date("วันที่ d/m/Y เวลา H:i น.", strtotime($read["time"]))
						)); successState($hist);
					}
				break; }
				case "memorandum": {
					$refID = escapeSQL(switch_ref_decrypt($attr));
					$get = $APP_DB[5] -> query("SELECT stdid,reason FROM admission_switch WHERE refID=$refID");
					if (!$get) {
						errorMessage(3, "Unable to get notes.");
						syslog_e($APP_USER, "admission", "swt", "getNote", "", "fail", "", "InvalidQuery");
					} else if (!$get -> num_rows) {
						errorMessage(1, "Memorandum not found.");
						syslog_e($APP_USER, "admission", "swt", "getNote", "", "fail", "", "Empty");
					} else {
						$read = $get -> fetch_array(MYSQLI_ASSOC);
						if ($read["stdid"] <> $APP_USER) {
							errorMessage(3, "You don't have permission to view this memorandum.");
							syslog_e($APP_USER, "admission", "swt", "getNote", "", "fail", "", "Unauthorized");
						} else successState($read["reason"]);
					}
				break; }
				default: errorMessage(1, "Invalid command"); break;
			}
		break; }
		case "answer": {
			switch ($command) {
				case "switch": {
					$get = $APP_DB[5] -> query("SELECT choose,filetype FROM admission_confirm WHERE stdid=$APP_USER");
					if (!$get) {
						errorMessage(3, "Unable to load data.");
						syslog_e($APP_USER, "admission", "swt", "updateRights", "", "fail", "", "InvalidGetQuery");
					} else if (!$get -> num_rows) {
						errorMessage(1, "You are not allowed to perform this action for you are not on the list of those eligible to study.");
						syslog_e($APP_USER, "admission", "swt", "updateRights", "", "fail", "", "Empty");
					} else {
						$read = $get -> fetch_array(MYSQLI_ASSOC);
						if ($read["choose"] == null) {
							errorMessage(2, "You haven't choose any rights previously. You cannot perform this action.");
							syslog_e($APP_USER, "admission", "swt", "updateRights", "", "fail", "", "Unauthorized");
						} else {
							// Update and record
							$chose = $read["choose"];
							$choose = $chose == ADMISSION_ANSWER_YES ? ADMISSION_ANSWER_NO : ADMISSION_ANSWER_YES;
							$reason = escapeSQL(nl2br(htmlspecialchars($attr)));
							$update = $APP_DB[5] -> query("UPDATE admission_confirm SET choose='$choose',ip='$USER_IP' WHERE stdid=$APP_USER");
							$success = $APP_DB[5] -> query("INSERT INTO admission_switch (stdid,prev,reason,ip) VALUE ($APP_USER,'$chose','$reason','$USER_IP')");
							if (!$update || !$success) {
								errorMessage(3, "Unable to update information. Please try again.");
								syslog_e($APP_USER, "admission", "swt", "updateRights", "", "fail", "", "InvalidQuery");
							} else {
								$reference = switch_ref_encrypt($APP_DB[5] -> insert_id);
								successState(array("token" => $reference));
								syslog_e($APP_USER, "admission", "swt", "updateRights", "", "pass");
							}
						}
					}
				break; }
				default: errorMessage(1, "Invalid command"); break;
			}
		break; }
		default: errorMessage(1, "Invalid type"); break;
	} $APP_DB[0] -> close();
	$APP_DB[5] -> close();
	sendOutput();
?>