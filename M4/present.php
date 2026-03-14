<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "";
	$header["desc"] = "นักเรียนเดิม";

	$APP_PAGE -> print -> head();
?>
<style type="text/css">

</style>
<script type="text/javascript">
	// const TRANSLATION = location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+");
	$(document).ready(function() {
		page.init();
	});
	const page = (function(d) {
		const cv = {
			API_URL: AppConfig.APIbase + "enroll/v1/"
		};
		var sv = {inited: false};
		var initialize = function() {
			if (sv.inited) return;

			sv.inited = true;
		};
		var myFunction = function() {

		},
		action = function(act, m, e) {
			if (app.IO.kbd.ctrl() || top.app.UI.lightbox.isOpen()) return;
			if (e.preventDefault) e.preventDefault();
			var frameHTML = `<div class="page-frame" data-action="${act.toLowerCase()}"><iframe src="${m.href}">Loading...</iframe></div>`;
			if (act == "User") return app.UI.lightbox("top", {title: "View " + act, exitTap: true, allowScroll: true}, frameHTML);
			top.app.UI.lightbox("center", {title: act, exitTap: false}, frameHTML);
		};
		return {
			init: initialize,
			action
		};
	}(document));
</script>
<?php $APP_PAGE -> print -> nav("enroll"); ?>
<main>
	<section class="container">
		<h2><?=$header["title"]?></h2>
		
		<iframe name="dlframe" hidden></iframe>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials(side_panel: "enroll");
	$APP_PAGE -> print -> footer();
?>