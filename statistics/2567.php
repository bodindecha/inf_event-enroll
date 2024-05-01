<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "สถิติการสมัครเข้าศึกษาต่อ";

	if (false && !has_perm("dev")) {
		require($APP_RootDir."private/script/lib/TianTcl/various.php");
		$TCL -> http_response_code(909);
	}

	$admission = array(
		"year" => "2567"
	);
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($APP_RootDir."private/block/core/heading.php"); require($APP_RootDir."private/script/start/CSS-JS.php"); ?>
		<style type="text/css">
			app[name=main] main .pictures .holder {
				width: 100%; max-width: 800px;
				box-sizing: border-box;
				border: 1px solid var(--clr-pp-blue-grey-800);
				border-radius: .3rem;
				display: block; overflow: hidden;
			}
			app[name=main] main .pictures .holder img {
				margin-bottom: -11px;
				width: 100%;
			}
			app[name=main] main .pictures .holder:hover, app[name=main] main .pictures .holder:focus-within, app[name=main] main .pictures .holder:active {
				/* border-color: transparent; */
				box-shadow: 0 1px 2px 0 #4242424D /* rgba(var(--clr-pp-grey-800), 30%) */,
					0 2px 6px 2px #42424226 /* rgba(var(--clr-pp-grey-800), 15%) */;
			}
		</style>
		<script type="text/javascript">
			// const TRANSLATION = location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+");
			$(document).ready(function() {
				page.init();
			});
			const page = (function(d) {
				const cv = {
					API_URL: AppConfig.APIbase + "",
					posts: [
						{
							img: "regis-DPSTE.jpg",
							// link: "https://facebook.com/bodin.ac.th/posts/769791958516499"
							link: "https://bodin.ac.th/home/2024/01/31116"
						}, {
							img: "regis-GIFTED.jpg",
							link: "https://facebook.com/bodin.ac.th/posts/789836046512090"
							// link: "https://bodin.ac.th/home/2024/0_/_____"
						}, {
							img: "regis-SpecialAbility.jpg",
							link: "https://facebook.com/bodin.ac.th/posts/803992935096401"
							// link: "https://bodin.ac.th/home/2024/0_/_____"
						}, {
							img: "regis-Regular.jpg",
							// link: "https://facebook.com/bodin.ac.th/posts/806207111541650"
							link: "https://bodin.ac.th/home/2024/03/31810"
						} /* , {
							img: "",
							link: ""
						} */
					]
				};
				var sv = {inited: false};
				var initialize = function() {
					if (sv.inited) return;
					addPosts();
					sv.inited = true;
				};
				var addPosts = function() {
					var space = $("app[name=main] main .pictures"),
						added = 0;
					cv.posts.reverse().forEach(ep => {
						space.append(`<li class="css-flex center"><a class="holder" href="${AppConfig.baseURL}go?url=${encodeURIComponent(ep.link)}" target="_blank">
							<img src="${AppConfig.baseURL}_resx/upload/img/event/enroll/stats/2567/${ep.img}" ${++added > 2 ? 'loading="lazy"' : ""} />
						</a></li>`);
					}); app.UI.refineElements();
				};
				return {
					init: initialize
				};
			}(document));
		</script>
	</head>
	<body>
		<app name="main">
			<?php require($APP_RootDir."private/block/core/top-panel/enroll.php"); ?>
			<main>
				<section class="container">
					<h2><span class="ref-00001"><?=$header["title"]?>โรงเรียนบดินทรเดชา (สิงห์ สิงหเสนี) ปีการศึกษา</span> <?=$admission["year"]?></h2>
					<ul class="pictures blocks container"></ul>
				</section>
			</main>
			<?php
				$resourcePath["navtab"] = "private/block/core/side-panel/enroll.php";
				require($APP_RootDir."private/block/core/material/main.php");
			?>
		</app>
	</body>
</html>