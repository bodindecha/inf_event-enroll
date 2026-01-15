<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/start/API.php");
	API::initialize(false);
	require_once($APP_RootDir."public_html/e/enroll/api/_log-v1.php");
	if (empty($APP_USER)) { header("Location: $signinURL"); exit(0); }
	if (!has_perm("PBL")) TianTcl::http_response_code(901);
	require_once($APP_RootDir."private/script/lib/TianTcl/virtual-token.php");
	// Get information
	if (!isset($_REQUEST["ment"]) || empty($_REQUEST["ment"]) || !isset($_REQUEST["ID"]) || empty($_REQUEST["ID"])) TianTcl::http_response_code(919);
	$docID = TianTcl::decrypt(urldecode($_REQUEST["ment"]), nest: 2, URLsafe: true);
	$user_override = $vToken -> read($_REQUEST["ID"]);
	if (empty($docID) || !strlen($user_override)) TianTcl::http_response_code(919);
	// Parsed
	require_once("../resource/hpe/doc-gen.php");
?>