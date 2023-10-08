<?php
	session_start(); ob_start();
	$my_url = ($_SERVER["REQUEST_URI"]=="/")?"":"account/sign-in?return_url=".urlencode(ltrim($_SERVER["REQUEST_URI"], "/")); // str_replace("#", "%23", "");
	if (preg_match("/^(((s|t)\/)?|\?return_url=(s|t)(%2F)?)$/", $my_url)) $my_url = "";
	if (!isset($dirPWroot)) $dirPWroot = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/")-1);

	// Permission checks
	function has_perm($what, $mods = true) {
		if (!(isset($_SESSION["auth"]) && $_SESSION["auth"]["type"]=="t")) return false;
		$mods = ($mods && $_SESSION["auth"]["level"]>=75); $perm = (in_array("*", $_SESSION["auth"]["perm"]) || in_array($what, $_SESSION["auth"]["perm"]));
		return ($perm || $mods);
	}

	// Redirection for authorized persons
	if (!isset($normalized_control)) $normalized_control = true;
	if ($normalized_control) {
		$url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
		// Not robot
		if (!preg_match('/(FBA(N|V)|facebookexternalhit|Line|line(-poker)?)/', $_SERVER["HTTP_USER_AGENT"])) {
			$require_sso = false; if (!isset($_SESSION["auth"]) && isset($_COOKIE["bdSSOv1a"]) && $_COOKIE["bdSSOv1a"]<>"") $require_sso = true;
			// Require basic authen
			else if (!isset($_SESSION["auth"]) && preg_match("/^\/e\/enroll\/((M4|report)\/.*)$/", $url)) {
				/* if (!preg_match("/^\/d\/sandbox\/.*$/", $url)) */ header("Location: /$my_url");
			} else if (isset($_SESSION["auth"]["type"])) {
				if ($_SESSION["auth"]["req_CP"] && !preg_match("/^\/(account\/complete(\?return_url=.+)?)$/", $url)) {
					if (!preg_match("/^\/e\/enroll\/.*$/", $url)) header("Location: /account/complete$my_url");
				} else {
					// Not all authened zone
					if ($_SESSION["auth"]["type"]=="s" && !preg_match("/^\/e\/enroll\/(M4\/.*|\d{4}|resource\/upload\/view)?$/", $url)) header("Location: /e/enroll/M4/"); // isStd
					else if ($_SESSION["auth"]["type"]=="t" && !preg_match("/^\/e\/enroll\/(report\/.*|\d{4})?$/", $url)) { // isTch
						/* if (has_perm("admission")) */ header("Location: /e/enroll/report/");
					}
				}
			}
		}
	} if (!isset($require_sso)) $require_sso = false;

	// App cookie settings
	$exptimeout = strval(time()+31536000);
	if (!isset($_COOKIE["set_theme"])) setcookie("set_theme", "light", $exptimeout, "/");
	if (!isset($_COOKIE["set_lang"])) setcookie("set_lang", "th", $exptimeout, "/");
	
	// Private pages
	function is_private($set = true) {
		if (!$set || ($set && isset($_SESSION["auth"]))) return false;
		else return true;
	}

	// Event custom
	$navtabpath = $dirPWroot."e/enroll/resource/hpe/aside-navigator.php";
?>