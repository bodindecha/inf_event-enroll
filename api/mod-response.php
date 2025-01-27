<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/start/API.php");
	API::initialize();
	$year = $_SESSION["stif"]["t_year"];
	require_once($APP_RootDir."public_html/e/enroll/api/_log-v1.php");
	require_once($APP_RootDir."public_html/resource/php/core/config.php");
	// Execute
	if (!isset($_SESSION["auth"]) || !has_perm("admission")) API::errorMessage(1, "You are unauthorized."); else
	switch (API::$action) {
		case "list": {
			switch (API::$command) {
				case "switch": {
					$order = escapeSQL(API::$attr);
					if ($order == "time") $order .= " DESC";
					$get = $APP_DB[5] -> query("SELECT a.stdid,b.namep,CONCAT(b.namefth, '  ', b.namelth) AS nameath, CONCAT(b.namefen, ' ', b.namelen) AS nameaen FROM admission_switch a INNER JOIN $mainDBname.user_s b on a.stdid=b.stdid GROUP BY stdid ORDER BY $order");
					if (!$get) {
						API::errorMessage(3, "There's an error getting students that match the criteria.");
						syslog_e(null, "admission", "mod", "getData", "listSwitch", false, "", "InvalidQuery");
					} else {
						$list = array();
						if ($get -> num_rows) while ($read = $get -> fetch_assoc()) array_push($list, array(
							"ID" => $read["stdid"],
							"fullname" => array(
								"TH" => prefixcode2text($read["namep"])["th"].$read["nameath"],
								"EN" => prefixcode2text($read["namep"])["en"]." ".$read["nameaen"]
							)
						)); API::successState($list);
					}
				break; }
				default: API::errorMessage(1, "Invalid command"); break;
			}
		break; }
		case "get": {
			switch (API::$command) {
				case "switch": {
					switch (API::$attr["data"]) {
						case "history": {
							$stdid = escapeSQL(API::$attr["ID"]);
							$get = $APP_DB[5] -> query("SELECT refID,prev,reason,time FROM admission_switch WHERE stdid=$stdid ORDER BY time DESC");
							if (!$get) {
								API::errorMessage(3, "Unable to load history.");
								syslog_e(null, "admission", "mod", "getData", "getSwitchHistory", false, "", "InvalidQuery");
							} else if (!$get -> num_rows) {
								API::errorMessage(1, "Sorry, but changes history for this student is currently not available.");
								syslog_e(null, "admission", "mod", "getData", "getSwitchHistory", false, "", "Empty");
							} else {
								$hist = array();
								while ($read = $get -> fetch_assoc()) array_push($hist, array(
									"reference" => switch_ref_encrypt($read["refID"]),
									"newChoice" => $read["prev"] == ADMISSION_ANSWER_NO,
									"hasMemorandum" => strlen($read["reason"]) > 0,
									"timestamp" => date("วันที่ d/m/Y เวลา H:i น.", strtotime($read["time"]))
								)); API::successState($hist);
							}
						break; }
						case "memorandum": {
							$refID = escapeSQL(switch_ref_decrypt(API::$attr["ID"]));
							$get = $APP_DB[5] -> query("SELECT reason FROM admission_switch WHERE refID=$refID");
							if (!$get) {
								API::errorMessage(3, "Unable to get notes.");
								syslog_e(null, "admission", "mod", "getData", "getSwitchNote", false, "", "InvalidQuery");
							} else if (!$get -> num_rows) {
								API::errorMessage(1, "Memorandum not found.");
								syslog_e(null, "admission", "mod", "getData", "getSwitchNote", false, "", "Empty");
							} else API::successState(($get -> fetch_array(MYSQLI_ASSOC))["reason"]);
						break; }
						default: API::errorMessage(1, "Invalid data requested"); break;
					}
				break; }
				default: API::errorMessage(1, "Invalid command"); break;
			}
		break; }
		default: API::errorMessage(1, "Invalid type"); break;
	} $APP_DB[5] -> close();
	API::sendOutput();
?>