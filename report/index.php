<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "แผงควบคุม - งานรับนักเรียน";

	$has_perm = has_perm("admission");
	if (!$has_perm) {
		require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
		TianTcl::http_response_code(901);
	}

	$permission = array(
		"isModerator" => $isAdministrator,
		"isDeveloper" => $isDeveloper,
		"modEnroll" => $has_perm,
		"modUAC" => has_perm("user")
	);
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	
</style>
<link rel="stylesheet" href="<?=$APP_CONST["cdnURL"]?>static/style/ext/menu.css" />
<script type="text/javascript">
	const TRANSLATION = ["@component-menu", location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+")];
	$(document).ready(function() {
		page.init();
	});
	const page = (function(d) {
		const cv = {
			PERMISSIONS: <?=json_encode($permission, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)?>
		};
		var sv = {inited: false};
		var initialize = function() {
			if (sv.inited) return;
			menu.dashboard("enroll", cv.PERMISSIONS, "<?=$_SESSION["auth"]["user"] ?? ""?>");
			sv.inited = true;
		};
		return {
			init: initialize
		};
	}(document));
</script>
<script type="text/javascript" src="<?=$APP_CONST["cdnURL"]?>static/script/ext/menu.js"></script>
<?php $APP_PAGE -> print -> nav("enroll"); ?>
<main>
	<section class="container">
		<p><span class="ref-00001">ยินดีต้อนรับ</span><a class="blend" href="<?=$APP_CONST["baseURL"]?>user/<?=$_SESSION["auth"]["user"]?>"><?=$_SESSION["auth"]["name"][$_COOKIE["set_lang"] ?? "th"]["a"]; ?></a></p>
		<p class="ref-00002">เข้าสู่ระบบจัดการงานรับนักเรียนโรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี)</p>
		<p class="ref-00003">คุณสามารถเลือกดูรายงานการตอบกลับได้จากเมนูด้านบนหรือตัวเลือกด้านล่าง</p>
		<div class="menu-dash"></div>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials();
	$APP_PAGE -> print -> footer("enroll");
?>