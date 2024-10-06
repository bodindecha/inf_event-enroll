<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
	$default = "คำชี้แจงเอกสารการมอบตัว v2.pdf";
	$TCL -> sendFile("../".getcwd().$default);
?>