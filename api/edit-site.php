<?php
	# $normal_params = false;
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/start/API.php");
	require_once($APP_RootDir."public_html/e/enroll/api/_log-v1.php");
	// Execute
	if (!isset($_SESSION["auth"]) || !has_perm("admission")) errorMessage(1, "You are unauthorized."); else
	switch ($action) {
		case "direction": {
			$file_dir = $APP_RootDir."public_html/e/enroll/resource/upload/";
			switch ($command) {
				case "loadHTML": {
					$filename = $attr;
					if (!file_exists($file_dir."direction/$filename.html")) {
						errorMessage(3, "File not found");
						syslog_e(null, "admission", "mod", "getDirection", $filename, false, "", "NoResult");
					} $content = file_get_contents($file_dir."direction/$filename.html");
					successState(array("HTML" => $content));
				break; }
				case "update": {
					$filename = $attr["file"];
					$content = $attr["content"];
					if (!file_exists($file_dir."direction/$filename.html")) {
						errorMessage(3, "File not found");
						syslog_e(null, "admission", "mod", "editDirection", $filename, false, "", "NoResult");
					} $original = file_get_contents($file_dir."direction/$filename.html");
					if ($original == $content) {
						errorMessage(1, "No change detected. Action aborted");
						syslog_e(null, "admission", "mod", "editDirection", $filename, false, "", "Duplicate");
					} if (file_put_contents($file_dir."direction/$filename.html", $content)) {
						successState();
						syslog_e(null, "admission", "mod", "editDirection", $filename, true);
					} else {
						errorMessage(3, "There's an error saving this file");
						syslog_e(null, "admission", "mod", "editDirection", $filename, false, "", "FileSystemError");
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