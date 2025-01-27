<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/start/API.php");
	API::initialize();
	require_once($APP_RootDir."public_html/e/enroll/api/_log-v1.php");
	// Execute
	if (!isset($_SESSION["auth"]) || !has_perm("admission")) API::errorMessage(1, "You are unauthorized."); else
	switch (API::$action) {
		case "direction": {
			$file_dir = $APP_RootDir."public_html/e/enroll/resource/upload/";
			switch (API::$command) {
				case "loadHTML": {
					$filename = API::$attr;
					if (!file_exists($file_dir."direction/$filename.html")) {
						API::errorMessage(3, "File not found");
						syslog_e(null, "admission", "mod", "getDirection", $filename, false, "", "NoResult");
					} $content = file_get_contents($file_dir."direction/$filename.html");
					API::successState(array("HTML" => $content));
				break; }
				case "update": {
					$filename = API::$attr["file"];
					$content = API::$attr["content"];
					if (!file_exists($file_dir."direction/$filename.html")) {
						API::errorMessage(3, "File not found");
						syslog_e(null, "admission", "mod", "editDirection", $filename, false, "", "NoResult");
					} $original = file_get_contents($file_dir."direction/$filename.html");
					if ($original == $content) {
						API::errorMessage(1, "No change detected. Action aborted");
						syslog_e(null, "admission", "mod", "editDirection", $filename, false, "", "Duplicate");
					} if (file_put_contents($file_dir."direction/$filename.html", $content)) {
						API::successState();
						syslog_e(null, "admission", "mod", "editDirection", $filename, true);
					} else {
						API::errorMessage(3, "There's an error saving this file");
						syslog_e(null, "admission", "mod", "editDirection", $filename, false, "", "FileSystemError");
					}
				break; }
				default: API::errorMessage(1, "Invalid command"); break;
			}
		break; }
		default: API::errorMessage(1, "Invalid type"); break;
	} $APP_DB[5] -> close();
	API::sendOutput();
?>