<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/start/API.php");
	API::initialize();
	require_once("_log-v1.php");
	// Execute
	function ThaiTime(string|null $ts=null): string {
		$ts ??= date(DATE_MYSQL);
		return "วันที่ ".date2TH(substr($ts, 0, 10), true)." เวลา ".substr($ts, 11, 5)." น.";
	}
	if ($APP_USER == $APP_CONST["USER_TYPE"][3]) API::errorMessage(3, "You are not signed-in. Please reload and try again."); else
	if ($_SESSION["auth"]["type"] <> "s") API::errorMessage(1, "Your account type is not student"); else
	switch (API::$action) {
		case "get": {
			switch (API::$command) {
				case "change": {
					if ($APP_USER == $APP_CONST["USER_TYPE"][3] || $_SESSION["auth"]["type"] <> "s")
						API::successState(array("hasChance" => false));
					else {
						$get = $APP_DB[5] -> query("SELECT a.type,a.choose,a.time,a.ip,b.start,b.stop FROM admission_change a INNER JOIN admission_timerange b ON a.timerange=b.trid WHERE a.stdid=$APP_USER");
						if (!$get || !$get -> num_rows) API::successState(array("hasChance" => false));
						$read = $get -> fetch_array(MYSQLI_ASSOC);
						$resp = array(
							"hasChance" => true,
							"placement" => $read["type"],
							"chose" => empty($read["choose"]) ? false : array(
								"group" => $read["choose"],
								"time" => ThaiTime($read["time"]),
								"ip" => $read["ip"]
							),
							"available" => TianTcl::inTimeRange($read["start"], $read["stop"])
						); if ($resp["available"]) {
							/* if (!$resp["chose"]) */ $resp["slotEnds"] = ThaiTime($read["stop"]);
							// Get sgroup list
							$get = $APP_DB[5] -> query("SELECT code,name FROM admission_sgroup ORDER BY code");
							if ($get && $get -> num_rows) $resp["groups"] = array_column($get -> fetch_all(MYSQLI_ASSOC), null, "code");
						} API::successState($resp);
					}
				break; }
				case "status": {
					$getInfo = $APP_DB[5] -> query("SELECT choose,filetype,lastupdate AS time,ip,(SELECT name FROM admission_sgroup WHERE type=code) AS name,(SELECT name FROM admission_sgroup WHERE code=(SELECT c.choose FROM admission_change c WHERE c.stdid=a.stdid)) AS new FROM admission_confirm a WHERE stdid=$APP_USER");
					$getHist = $APP_DB[5] -> query("SELECT 1 FROM admission_switch WHERE stdid=$APP_USER");
					if (!$getInfo || !$getHist) {
						API::errorMessage(3, "Unable to load data.");
						syslog_e($APP_USER, "admission", "swt", "getStatus", "", false, "", "InvalidQuery");
					} else if (!$getInfo -> num_rows) {
						API::errorMessage(1, "You are not allowed to perform this action for you are not on the list of those eligible to study.");
						syslog_e($APP_USER, "admission", "swt", "getStatus", "", false, "", "Empty");
					} else {
						$read = $getInfo -> fetch_array(MYSQLI_ASSOC);
						if ($read["choose"] == null) API::successState(array("chosen" => false));
						else API::successState(array(
							"chosen" => true,
							"choose" => $read["choose"] == ADMISSION_ANSWER_YES || $read["choose"] == ADMISSION_ANSWER_CHANGE,
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
						API::errorMessage(3, "Unable to load history.");
						syslog_e($APP_USER, "admission", "swt", "getHistory", "", false, "", "InvalidQuery");
					} else if (!$get -> num_rows) {
						API::errorMessage(1, "Sorry, but your changes history is currently not available.");
						syslog_e($APP_USER, "admission", "swt", "getHistory", "", false, "", "Empty");
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
					$refID = escapeSQL(switch_ref_decrypt(API::$attr["ref"]));
					$source = escapeSQL(API::$attr["source"]);
					$translation = array("confirm" => "cng", "switch" => "swt");
					if (!in_array($source, array_keys($translation))) {
						API::errorMessage(3, "Invalid source requested.");
						# syslog_e($APP_USER, "admission", "", "getNote", "", false, "", "Wrong");
					} else {
						$get = $APP_DB[5] -> query("SELECT stdid,reason FROM admission_$source WHERE refID=$refID");
						$source = $translation[$source];
						if (!$get) {
							API::errorMessage(3, "Unable to get notes.");
							syslog_e($APP_USER, "admission", $source, "getNote", "", false, "", "InvalidQuery");
						} else if (!$get -> num_rows) {
							API::errorMessage(1, "Memorandum not found.");
							syslog_e($APP_USER, "admission", $source, "getNote", "", false, "", "Empty");
						} else {
							$read = $get -> fetch_array(MYSQLI_ASSOC);
							if ($read["stdid"] <> $APP_USER) {
								API::errorMessage(3, "You don't have permission to view this memorandum.");
								syslog_e($APP_USER, "admission", $source, "getNote", "", false, "", "Unauthorized");
							} else API::successState($read["reason"]);
						}
					}
				break; }
				case "confirm": {
					if ($APP_USER == $APP_CONST["USER_TYPE"][3] || $_SESSION["auth"]["type"] <> "s")
						API::successState(array("hasChance" => false));
					else {
						$get = $APP_DB[5] -> query("SELECT a.choose,a.type,a.lastupdate AS time,a.ip,b.start,b.stop,(SELECT name FROM admission_sgroup WHERE code=a.type) AS type,(SELECT name FROM admission_sgroup WHERE code=(SELECT choose FROM admission_change c WHERE c.stdid=a.stdid)) AS request FROM admission_confirm a INNER JOIN admission_timerange b ON a.timerange=b.trid WHERE a.stdid=$APP_USER");
						if (!$get || !$get -> num_rows) API::successState(array("hasChance" => false));
						$read = $get -> fetch_array(MYSQLI_ASSOC);
						$resp = array(
							"hasChance" => true,
							"placement" => $read["type"],
							"chose" => empty($read["choose"]) ? false : array(
								"option" => $read["choose"],
								"time" => ThaiTime($read["time"]),
								"ip" => $read["ip"]
							),
							"available" => TianTcl::inTimeRange($read["start"], $read["stop"])
						); if ($resp["available"]) $resp["slotEnds"] = ThaiTime($read["stop"]);
						if (!empty($read["request"])) $resp["changeReq"] = $read["request"];
						API::successState($resp);
					}
				break; }
				default: API::errorMessage(1, "Invalid command"); break;
			}
		break; }
		case "request": {
			switch (API::$command) {
				case "commit": {
					$name = escapeSQL(API::$attr["to"]);
					$query = " INNER JOIN admission_timerange b ON a.timerange=b.trid WHERE a.stdid=$APP_USER";
					$types = array(
						"present" => [
							"prs", "/^[YN]$/", true,
							"SELECT a.choose,b.start,b.stop FROM admission_$name a $query"
						],
						"change" => [
							"cng", "/^[A-H]$/", true,
							"SELECT a.type,a.choose,b.start,b.stop FROM admission_$name a $query"
						],
						"confirm" => [
							"cnf", "/^[YNC]$/", true,
							"SELECT a.choose,b.start,b.stop,c.shortname,a.lastupdate AS time,a.ip,(SELECT name FROM admission_sgroup WHERE code=(SELECT choose FROM admission_change d WHERE d.stdid=a.stdid)) AS request FROM admission_$name a INNER JOIN admission_sgroup c ON a.type=c.code $query"
						],
						"switch" => [
							"swt", "/^[N]$/", false,
							"SELECT a.choose,a.filetype,b.shortname FROM admission_confirm a INNER JOIN admission_sgroup b ON a.type=b.code WHERE a.stdid=$APP_USER"
						]
					); if (!in_array($name, array_keys($types))) {
						API::errorMessage(1); // Invalid destination sent
						# syslog_e($APP_USER, "admission", "", "save", "", false, "", "Wrong");
					} else {
						[$to, $options, $checkTime, $query] = $types[$name];
						$answer = escapeSQL(API::$attr["answer"]);
						if (!RegExTest($options, $answer)) {
							API::errorMessage(2); // Invalid option
							syslog_e($APP_USER, "admission", $to, "save", $answer, false, "", "InvalidOption");
						} $get = $APP_DB[5] -> query($query);
						if (!$get) {
							API::errorMessage(3); // Error get
							syslog_e($APP_USER, "admission", $to, "save", $answer, false, "", "InvalidGetQuery");
						} else if (!$get -> num_rows) {
							API::errorMessage(4); // No user
							syslog_e($APP_USER, "admission", $to, "save", $answer, false, "", "Empty");
						} else {
							$read = $get -> fetch_array(MYSQLI_ASSOC);
							$hasFile = isset(API::$file["usf"]);
							$query = ["UPDATE admission_$name SET", "choose='$answer',ip='$USER_IP' WHERE stdid=$APP_USER"];
							if ($checkTime) $inTime = TianTcl::inTimeRange($read["start"], $read["stop"]);
							if ($checkTime && !$inTime) {
								API::errorMessage(5); // Timeout
								syslog_e($APP_USER, "admission", $to, "save", $answer, false, "", "Timeout");
							} else {
								require_once("_upload.php");
								switch ($to) {
									case "prs": {
										$fileReq = $answer == ADMISSION_ANSWER_YES;
										if (!empty($read["choose"])) {
											API::errorMessage(7); // Responded
											syslog_e($APP_USER, "admission", $to, "save", $answer, false, "", "Responded");
										} else if ($fileReq && !$hasFile) {
											API::errorMessage(8); // No file
											syslog_e($APP_USER, "admission", $to, "save", $answer, false, "", "NoFile");
										} else if (!$fileReq || $upload = try_upload_file($name, $to, $answer)) {
											$fileType = $fileReq ? get_file_type("usf", true) : "NULL";
											$success = "$query[0] filetype=$fileType,$query[1]";
										}
									break; }
									case "cng": {
										if ($answer == $read["type"] /* || $answer == $read["choose"] */) { // Allow same as before for reupload
											API::errorMessage(9); // Same group
											syslog_e($APP_USER, "admission", $to, "save", $answer, false, "", "Duplicate");
										} else if (!$hasFile) {
											API::errorMessage(8); // No file
											syslog_e($APP_USER, "admission", $to, "save", $answer, false, "", "NoFile");
										} else if ($upload = try_upload_file($name, $to, $answer)) {
											$fileType = get_file_type("usf", true);
											$success = "$query[0] times=times+1,filetype=$fileType,$query[1]";
										}
									break; }
									case "cnf": {
										$fileReq = $answer == ADMISSION_ANSWER_NO;
										if ($fileReq) {
											$reason = escapeSQL(nl2br(htmlspecialchars(API::$attr["reason"])));
											if (isset(API::$attr["school"])) $reason = escapeSQL(
												'เข้าศึกษาต่อ ณ <span data-title="'.
												API::$attr["school"]["ID"].'">โรงเรียน'.
												API::$attr["school"]["name"]."</span>"
											).(strlen($reason) ? "<hr>" : "").$reason;
										} if ($fileReq && strlen(API::$attr["reason"]) < 5) {
											API::errorMessage(11); // Condition: reason too short
											syslog_e($APP_USER, "admission", $to, "save", $log_data, false, "", "InvalidReason");
										} else if (!empty($read["choose"])) {
											API::errorMessage(7); // Responded
											syslog_e($APP_USER, "admission", $to, "save", $answer, false, "", "Responded");
										} else if ($fileReq && !$hasFile) {
											API::errorMessage(8); // No file
											syslog_e($APP_USER, "admission", $to, "save", $answer, false, "", "NoFile");
										} else if (!$fileReq || $upload = try_upload_file($name, $to, $answer)) {
											$fileType = $fileReq ? get_file_type("usf", true) : "NULL";
											$extra = $fileReq ? "reason='$reason'," : "";
											$success = "$query[0] $extra filetype=$fileType,$query[1]";
										}
									break; }
									case "swt": {
										$fileReq = $answer == ADMISSION_ANSWER_NO;
										$chose = $read["choose"]; // Old
										$choose = $chose == ADMISSION_ANSWER_YES ? ADMISSION_ANSWER_NO : ADMISSION_ANSWER_YES; // New = !Old
										$reason = escapeSQL(nl2br(htmlspecialchars(API::$attr["reason"])));
										if (isset(API::$attr["school"])) $reason = escapeSQL(
												'เข้าศึกษาต่อ ณ <span data-title="'.
												API::$attr["school"]["ID"].'">โรงเรียน'.
												API::$attr["school"]["name"]."</span>"
											).(strlen($reason) ? "<hr>" : "").$reason;
										$log_data = "$chose → $choose";
										if (empty($read["choose"])) {
											API::errorMessage(10); // Unauthorized: no prev record
											syslog_e($APP_USER, "admission", $to, "save", $log_data, false, "", "Unauthorized");
										} else if (strlen(API::$attr["reason"]) < 5) {
											API::errorMessage(11); // Condition: reason too short
											syslog_e($APP_USER, "admission", $to, "save", $log_data, false, "", "InvalidReason");
										} else if ($fileReq && !$hasFile) {
											API::errorMessage(8); // No file
											syslog_e($APP_USER, "admission", $to, "save", $log_data, false, "", "NoFile");
										} else if (!$fileReq || $upload = try_upload_file("confirm", $to, $answer)) {
											$fileType = $fileReq ? get_file_type("usf", true) : "NULL";
											$success = "UPDATE admission_confirm SET reason='$reason',filetype=$fileType,$query[1]";
											$APP_DB[5] -> query("INSERT INTO admission_switch (stdid,prev,reason,ip) VALUE ($APP_USER,'$chose','$reason','$USER_IP')");
											$reference = switch_ref_encrypt($APP_DB[5] -> insert_id);
											$answer = $log_data;
										}
									break; }
								} if (isset($success)) {
									$success = $APP_DB[5] -> query($success);
									if ($success) {
										syslog_e($APP_USER, "admission", $to, "save", $answer);
										switch ($to) {
											case "cng": {
												API::successState(array(
													"available" => $inTime,
													"chose" => array(
														"group" => $answer,
														"time" => ThaiTime(),
														"ip" => $USER_IP
													)
												));
											break; }
											case "cnf": {
												/* if ($answer == ADMISSION_ANSWER_NO) {
													require($APP_RootDir."private/script/lib/TianTcl/LINE.php");
													$LINE -> setToken(ADMISSION_LINE_TOKEN);
													$LINE -> notify("มีนักเรียนสละสิทธิ์\r\nประเภท: นักเรียนเดิม\r\nกลุ่ม: $read[shortname]\r\nเลขประจำตัว: $APP_USER\r\nชื่อ: ".$_SESSION["auth"]["name"]["th"]["a"]);
												} API::successState(); */
												$resp = array(
													"chose" => array(
														"option" => $answer,
														"time" => ThaiTime(),
														"ip" => $USER_IP
													),
												); if (!empty($read["request"])) $resp["changeReq"] = $read["request"];
												API::successState($resp);
											break; }
											case "swt": {
												API::successState(array(
													"token" => $reference,
													"signature" => ThaiTime()
												)); if ($choose == ADMISSION_ANSWER_NO) {
													# require($APP_RootDir."private/script/lib/TianTcl/LINE.php");
													# $LINE -> setToken(ADMISSION_LINE_TOKEN);
													# $LINE -> notify("มีนักเรียนสละสิทธิ์\r\nประเภท: นักเรียนเดิม\r\nกลุ่ม: $read[shortname]\r\nเลขประจำตัว: $APP_USER\r\nชื่อ: ".$_SESSION["auth"]["name"]["th"]["a"]);
												}
											break; }
											default: API::successState();
										} if (gettype($upload) == "string") API::infoMessage(1, $upload);
									} else {
										API::errorMessage(6); // Save error
										syslog_e($APP_USER, "admission", $to, "save", $answer, false, "", "InvalidQuery");
									}
								}
							}
						}
					}
				break; }
				default: API::errorMessage(1, "Invalid command"); break;
			}
		break; }
		default: API::errorMessage(1, "Invalid type"); break;
	} $APP_DB[5] -> close();
	API::sendOutput();
?>