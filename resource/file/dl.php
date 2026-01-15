<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/start/API.php");
	API::initialize(false);
	require_once($APP_RootDir."public_html/e/enroll/api/_log-v1.php");
	# if (empty($APP_USER)) { header("Location: $signinURL"); exit(0); }
	# hasPermission("dev", denyTo: 901);
	require_once($APP_RootDir."private/script/lib/TianTcl/virtual-token.php");
	// Get information
	$docID = escapeSQL($_REQUEST["name"]);
	if (empty($docID)) TianTcl::http_response_code(919);
	// Parsed
	require_once("../hpe/doc-gen.php");
?>