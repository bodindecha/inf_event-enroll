<?php
	$APP_DB[5] = connect_to_database(5);
	function syslog_e($doer, string $flow, string $action, string $impact, string $detail="", bool $state=true, string $attr="", string $remark="", bool $force=false, bool $close_db_connection=false) {
		// Check connection
		global $APP_CONST, $APP_DB, $APP_USER, $USER_IP, $APP_RootDir;
		if (!isset($USER_IP) || !isset($APP_USER)) require($APP_RootDir."private/script/function/utility.php");

		// Clean data
		# if ($doer == null) $doer = strval($_SESSION["auth"]["user"] ?? $APP_CONST["USER_TYPE"][0]); else
		if ($doer == null) $doer = strval($_SESSION["evt"]["user"] ?? ($_SESSION["evt2"]["user"] ?? ($APP_USER ?? ($_SESSION["auth"]["user"] ?? $APP_CONST["USER_TYPE"][0])))); else
		$doer	= escapeSQL(trim(strval($doer)));
		$flow	= escapeSQL(trim(strval($flow)));
		$action	= escapeSQL(trim(strval($action)));
		$impact	= escapeSQL(trim(strval($impact)));
		$detail	= escapeSQL(trim(strval($detail)));
		$state	= ($state ? "PASS" : "FAIL");
		$attr	= escapeSQL(trim(strval($attr)));
		$remark	= escapeSQL(trim(strval($remark)));

		// Filter user
		if (!$force) {
			if ($flow == "PathwaySCon" && in_array($doer, array("0", "10000"))) return false;
			if ($flow == "admission" && in_array($doer, $APP_CONST["USER_NO_SHADOW"])) return false;
		}

		// Record
		$success = $APP_DB[5] -> query("INSERT INTO all_log_action (exor,app,cmd,act,data,val,attr,ref,ip) VALUE ('$doer','$flow','$action','$impact','$detail','$state','$attr','$remark','$USER_IP')");

		// Close connection
		if ($close_db_connection) $APP_DB[5] -> close();

		return $success;
	}
	$mainDBname = "`tiantcl_inf`";

	define("ADMISSION_ANSWER_YES", "Y");
	define("ADMISSION_ANSWER_NO", "N");
	define("ADMISSION_SECRET_KEY", "B0d1^/-4dm1$5|o/v");
	define("ADMISSION_SECRET_SALT", 2565.2024);
	function switch_ref_encrypt($refID) {
		if (!class_exists("TianTcl")) require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
		return TianTcl::encrypt(
			"swt".str_pad(strval($refID), 4, "0", STR_PAD_LEFT)."ADM",
			key: ADMISSION_SECRET_KEY,
			salt: ADMISSION_SECRET_SALT
		);
	}
	function switch_ref_decrypt($reference) {
		if (!class_exists("TianTcl")) require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
		return rtrim(substr(TianTcl::decrypt($reference, key: ADMISSION_SECRET_KEY, salt: ADMISSION_SECRET_SALT), 3), "ADM");
	}
?>