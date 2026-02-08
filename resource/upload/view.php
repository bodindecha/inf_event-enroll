<?php
	$dirPWroot = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");

	if (!isset($_REQUEST["type"])) $error = 902;
	else {
		require($dirPWroot."e/resource/db_connect.php"); require_once($dirPWroot."e/enroll/resource/php/config.php");
		require_once($dirPWroot."resource/php/lib/TianTcl/virtual-token.php");
		if (!function_exists("escapeSQL")) { function escapeSQL(int|string|float $input): string {
			global $db;
			return $db -> real_escape_string(trim((string)$input));
		} }
		$authuser = $_SESSION["auth"]["user"] ?? $vToken -> read($_REQUEST["authuser"]);
		$dir = escapeSQL($_REQUEST["type"]);
		$field = ($dir == "newstd" ? "amsid" : "stdid");
		$getExtn = $db -> query("SELECT filetype FROM admission_$dir WHERE $field=$authuser");
		if (!$getExtn) $error = 905;
		else if (!$getExtn -> num_rows) $error = 900;
		else {
			$extension = ($getExtn -> fetch_array(MYSQLI_ASSOC))["filetype"];
			$path = "$dir/$authuser.$extension";
			if (!file_exists($path)) $error = 404;
		} $db -> close();
	}
	if (isset($error)) $redirect = "/error/$error#ref=".urlencode($_SERVER["REQUEST_URI"]);
	else $redirect = "/_resx/service/view/file?furl=".urlencode("e/enroll/resource/upload/$path");
	header("Location: $redirect"); exit(0);
?>