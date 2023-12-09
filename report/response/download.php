<?php
	# $normal_params = false;
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/start/API.php");
	$year = $_SESSION["stif"]["t_year"];
	require_once($APP_RootDir."public_html/e/enroll/api/_log-v1.php");
	$type2name = array("prs" => "present", "cng" => "change", "cnf" => "confirm", "new" => "newstd");
	$fileDir = $APP_RootDir."public_html/e/enroll/resource/upload/";
	// Execute
	switch ($action) {
		case "merge": {
			switch ($command) {
				case "squash": {
					$type = escapeSQL($attr);
					// Check latest update
					$get = $APP_DB[5] -> query("SELECT refID,time FROM admission_evidence WHERE type='$type' ORDER BY time DESC LIMIT 1");
					if (!$get) {
						errorMessage(3, "Unable to check manifests");
						syslog_e(null, "admission", "mod", "download", "UUEF: $type", "fail", "", "InvalidQuery");
					} else if ($get -> num_rows <> 1) {
						// No previous records: continue to zipping process
						goto ZIPprocess;
					} else {
						$read = $get -> fetch_array(MYSQLI_ASSOC);
						if (time() - strtotime($read["time"]) > 3*60*60 /* 3 hr */) // Previous record too old: continue to zipping process
							goto ZIPprocess;
						else { // Previous record ready: Exit to export process
							$refID = $read["refID"];
							goto ZIPcomplete;
						}
					} ZIPprocess:
					// Check if there is any file
					$amount = count(glob($fileDir.$type2name[$type]."/*"));
					if (!$amount) {
						errorMessage(1, "There are currently no file to download");
						syslog_e(null, "admission", "mod", "download", "UUEF: $type", "fail", "", "Empty");
					} else { // Zip file
						// First create record to prevent other overwrite
						$APP_DB[5] -> query("INSERT INTO admission_evidence (requester,type,gathered,ip) VALUE ('$APP_USER','$type',$amount,'$USER_IP')");
						$refID = $APP_DB[5] -> insert_id;
						$file = new ZipArchive();
						if (!$file -> open($fileDir."archive/$year/".$type2name[$type].".zip", ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
							errorMessage(3, "Unable to create ZIP file");
							syslog_e(null, "admission", "mod", "download", "UUEF: $type", "fail", "", "FileIneligible");
						} else {
							$file -> addGlob($fileDir.$type2name[$type]."/*");
							if ($file -> status == ZipArchive::ER_OK) {
								ZIPcomplete:
								require($APP_RootDir."private/script/lib/TianTcl/various.php");
								successState(array("token" => $TCL -> encrypt("$type-$refID")));
								syslog_e(null, "admission", "mod", "download", "UUEF: $type", "pass");
							} else {
								errorMessage(3, "There's an error zipping files");
								syslog_e(null, "admission", "mod", "download", "UUEF: $type", "fail", "", "NoResult");
							} $file -> close();
						}
					}
				break; }
				default: errorMessage(1, "Invalid command"); break;
			}
		break; }
		case "get": {
			switch ($command) {
				case "info": {

				break; }
				case "part": {
					/*
					$targetDir = 'uploads/';
					$chunkFile = $targetDir . $_POST['currentChunk'] . '_' . $_POST['totalChunks'] . '_' . md5_file($_FILES['chunk']['tmp_name']) . '.part';
					
					if (move_uploaded_file($_FILES['chunk']['tmp_name'], $chunkFile)) {
					  $chunkNum = 0;
					  for ($i = 0; $i < $_POST['totalChunks']; $i++) {
						$partFile = $targetDir . $i . '_' . $_POST['totalChunks'] . '_' . md5_file($targetDir . $i . '_' . $_POST['totalChunks'] . '_' . md5_file($chunkFile)) . '.part';
						if (file_exists($partFile)) {
						  $chunkNum++;
						}
					  }
					  if ($chunkNum == $_POST['totalChunks']) {
						$outputFile = $targetDir . md5_file($chunkFile);
						$outputHandle = fopen($outputFile, 'wb');
						for ($i = 0; $i < $_POST['totalChunks']; $i++) {
						  $partFile = $targetDir . $i . '_' . $_POST['totalChunks'] . '_' . md5_file($targetDir . $i . '_' . $_POST['totalChunks'] . '_' . md5_file($chunkFile)) . '.part';
						  $partHandle = fopen($partFile, 'rb');
						  stream_copy_to_stream($partHandle, $outputHandle);
						  fclose($partHandle);
						  unlink($partFile);
						}
						fclose($outputHandle);
					  }
					}
					*/
				break; }
				case "file": {

				break; }
				default: errorMessage(1, "Invalid command"); break;
			}
		break; }
		default: errorMessage(1, "Invalid type"); break;
	} $APP_DB[0] -> close();
	$APP_DB[5] -> close();
	sendOutput();
?>