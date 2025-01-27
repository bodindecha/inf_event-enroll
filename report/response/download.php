<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/start/API.php");
	API::initialize();
	$year = $_SESSION["stif"]["t_year"];
	require_once($APP_RootDir."public_html/e/enroll/api/_log-v1.php");
	$type2name = array("prs" => "present", "cng" => "change", "cnf" => "confirm", "new" => "newstd");
	$fileDir = $APP_RootDir."public_html/e/enroll/resource/upload/";
	// Execute
	switch (API::$action) {
		case "merge": {
			switch (API::$command) {
				case "squash": {
					$type = escapeSQL(API::$attr);
					// Check latest update
					$get = $APP_DB[5] -> query("SELECT refID,time FROM admission_evidence WHERE type='$type' ORDER BY time DESC LIMIT 1");
					if (!$get) {
						API::errorMessage(3, "Unable to check manifests");
						syslog_e(null, "admission", "mod", "download", "UUEF: $type", false, "", "InvalidQuery");
					} else if ($get -> num_rows <> 1) {
						// No previous records: continue to zipping process
						goto ZIPprocess;
					} else {
						$read = $get -> fetch_array(MYSQLI_ASSOC);
						if (time() - strtotime($read["time"]) > 3*60*60 /* 3 hr */) // Previous record too old: continue to zipping process
							goto ZIPprocess;
						else { // Previous record ready: Exit to export process
							$refID = $read["refID"];
							$time = strtotime($read["time"]);
							goto ZIPcomplete;
						}
					} ZIPprocess:
					// Check if there is any file
					$amount = count(glob($fileDir.$type2name[$type]."/*"));
					if (!$amount) {
						API::errorMessage(1, "There are currently no file to download");
						syslog_e(null, "admission", "mod", "download", "UUEF: $type", false, "", "Empty");
					} else { // Zip file
						// First create record to prevent other overwrite
						$APP_DB[5] -> query("INSERT INTO admission_evidence (requester,type,gathered,ip) VALUE ('$APP_USER','$type',$amount,'$USER_IP')");
						$refID = $APP_DB[5] -> insert_id;
						$file = new ZipArchive();
						if (!$file -> open($fileDir."archive/$year/".$type2name[$type].".zip", ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
							API::errorMessage(3, "Unable to create ZIP file");
							syslog_e(null, "admission", "mod", "download", "UUEF: $type", false, "", "FileIneligible");
						} else {
							# $file -> addGlob($fileDir.$type2name[$type]."/*");
							$ev_files = glob($fileDir.$type2name[$type]."/*");
							foreach ($ev_files as $ev_file) $file -> addFile($ev_file, basename($ev_file));
							$file -> close();
							if ($file -> status == ZipArchive::ER_OK) {
								$time = time();
								ZIPcomplete:
								require($APP_RootDir."private/script/lib/TianTcl/various.php");
								if (gettype($time) != "string") $time = strval($time);
								API::successState(array("token" => $TCL -> encrypt("$refID-$type-$time")));
								syslog_e(null, "admission", "mod", "download", "UUEF: $type", true);
							} else {
								API::errorMessage(3, "There's an error zipping files");
								syslog_e(null, "admission", "mod", "download", "UUEF: $type", false, "", "NoResult");
							}
						}
					}
				break; }
				default: API::errorMessage(1, "Invalid command"); break;
			}
		break; }
		case "get": {
			switch ($command) {
				case "info": {
					require($APP_RootDir."private/script/lib/TianTcl/various.php");
					API::$attr = explode("-", escapeSQL(TianTcl::decrypt(API::$attr)));
					// Save download attemp
					$success = $APP_DB[5] -> query("UPDATE admission_evidence SET downloads=downloads+1 WHERE refID=".$attr[0]);
					if (!$success) {
						API::errorMessage(3, "There was an error fetching file information");
						syslog_e(null, "admission", "mod", "download", "UUEF: ".API::$attr[0], false, "", "InvalidQuery");
					} else {
						$filesrc = $fileDir."archive/$year/".$type2name[$attr[1]].".zip";
						API::successState(array(
							"filename" => "BD-Admission UUEF#".$type2name[API::$attr[1]]." ".date("Y-m-d\\TH_i_s", API::$attr[2]).".zip",
							"filesize" => filesize($filesrc),
							"mime" => TianTcl::mime_file_type($filesrc)
						));
					}
				break; }
				case "file": {
					require($APP_RootDir."private/script/lib/TianTcl/various.php");
					$token = explode("-", TianTcl::decrypt(API::$attr["token"]));
					$filesrc = $fileDir."archive/$year/".$type2name[$token[1]].".zip";
					if (!file_exists($filesrc)) {
						API::errorMessage(3, "Unable to find the requested file. Please start over.");
						syslog_e(null, "admission", "mod", "download", "UUEF: $token[0]", false, "", "NoResult");
					} else if (!isset(API::$attr["range"]) || !preg_match("/^\d+-\d+$/", API::$attr["range"])) {
						API::errorMessage(3, "Incorrect file signature.");
						syslog_e(null, "admission", "mod", "download", "UUEF: $token[0]", false, "", "Incorrect");
					} else {
						$range = explode("-", API::$attr["range"]);
						$start = intval($range[0]);
						$end = intval($range[1]);
						$chunk_size = $end - $start + 1;
						// — Start Force Download —
						if (ob_get_contents()) die("Some data has already been output, can't download file");
						header("Content-Description: File Transfer");
						if (headers_sent()) die("Some data has already been output to browser, can't download file");
						header("Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1");
						# header("Cache-Control: public, must-revalidate, max-age=0"); // HTTP/1.1
						header("Pragma: public");
						header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
						header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT+7");
						// force download dialog
						if (strpos(php_sapi_name(), "cgi") === false) {
							# header("Content-Type: $mime", true);
							header("Content-Type: ".mime_file_type($filesrc));
							header("Content-Type: application/octet-stream", false);
							header("Content-Type: application/download", false);
							header("Content-Type: application/force-download", false);
						} else header("Content-Type: ".mime_file_type($filesrc));
						// use the Content-Disposition header to supply a recommended filename
						header("Content-Disposition: attachment; filename=\"BD-Admission UUEF#".$type2name[$token[1]]." ".date("Y-m-d\\TH_i_s", $token[2]).".zip\"");
						header("Content-Transfer-Encoding: binary");
						header("HTTP/1.1 206 Partial Content");
						header("Content-Range: bytes $start-$end/".filesize($filesrc));
						header("Content-Length: $chunk_size");
						# echo file_get_contents($path);
						# fpassthru(fopen($path, "rb"));
						# readfile($filesrc);
						$fileBuffer = fopen($filesrc, "rb");
						fseek($fileBuffer, $start);
						echo fread($fileBuffer, $chunk_size);
						fclose($fileBuffer);
						// — End Force Download —
						$APP_DB[0] -> close();
						$APP_DB[5] -> close();
						exit(0);
					}
				break; }
				default: API::errorMessage(1, "Invalid command"); break;
			}
		break; }
		default: API::errorMessage(1, "Invalid type"); break;
	} $APP_DB[5] -> close();
	API::sendOutput();
?>