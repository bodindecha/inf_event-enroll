<?php
	function get_file_type(string $key, bool $encapsulate=false): string {
		if (!isset(API::$file[$key])) return "";
		$type = strtolower(pathinfo(basename(API::$file[$key]["name"]), PATHINFO_EXTENSION));
		return $encapsulate ? "'$type'" : $type;
	}
	function try_upload_file(string $dir, string $log_type, string $log_desc): string|false {
		if (!isset(API::$file["usf"])) return false;
		global $APP_USER, $APP_RootDir, $APP_CONST;
		// Set target directory
		$targetDir = "$APP_RootDir$APP_CONST[publicDir]$APP_CONST[baseURL]e/enroll/resource/upload/$dir";
		if (!is_dir($targetDir)) mkdir($targetDir, 0755);
		// Process upload
		$fileType = get_file_type("usf");
		$filename = "$APP_USER.$fileType";
		$targetFile = "$targetDir/$filename";
		$fileSizeLimit = 10; // MB
		if (API::$file["usf"]["size"] <= 0 || API::$file["usf"]["size"] >= $fileSizeLimit * 1024e3) {
			API::errorMessage(3, "File too large (larger than $fileSizeLimit MB) or file is empty.<br>ไฟล์ที่นักเรียนเลือกมีมีขนาดใหญ่เกินที่กำหนดไว้. กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน $fileSizeLimit MB แล้วลองใหม่อีกครั้ง.");
			syslog_e($APP_USER, "admission", $log_type, "save", "$log_desc, $fileType", false, "", "FileIneligible");
		} else if (!in_array($fileType, ["png", "jpg", "jpeg", "gif", "heic", "pdf"])) {
			API::errorMessage(50);
			syslog_e($APP_USER, "admission", $log_type, "save", "$log_desc, $fileType", false, "", "FileIneligible");
		} else {
			// Remove previous
			if (file_exists($targetFile)) unlink($targetFile);
			// Complete upload
			if (move_uploaded_file(API::$file["usf"]["tmp_name"], $targetFile)) {
				$originalFileName = API::$file["usf"]["name"];
				if (strlen($originalFileName) > 25) $originalFileName = mb_substr($originalFileName, 0, 25)."…";
				return "File \"$originalFileName\" uploaded successfully";
			} else {
				API::errorMessage(51);
				syslog_e($APP_USER, "admission", $log_type, "save", "$log_desc, $fileType", false, "", "UploadError");
			}
		} return false;
	} $query_tail = "a INNER JOIN admission_timerange b ON a.timerange=b.trid WHERE a.stdid=$APP_USER";
?>