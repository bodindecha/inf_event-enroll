<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "View uploaded data";

	$has_perm = has_perm("dev") && has_perm("admission");
	if (!$has_perm) {
		require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
		$TCL -> http_response_code(901);
	}

	$file_filter_blacklist = array(".", "..");
	function filter_for_uploads(string $filename): bool {
		global $file_filter_blacklist;
		if (in_array($filename, $file_filter_blacklist)) return false;
		$file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		if ($file_ext == "csv") return true;
		return false;
	}
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($APP_RootDir."private/block/core/heading.php"); require($APP_RootDir."private/script/start/CSS-JS.php"); ?>
		<style type="text/css">
			
		</style>
		<script type="text/javascript">
			// const TRANSLATION = location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+");
			$(document).ready(function() {
				page.init();
			});
			const page = (function(d) {
				const cv = {};
				var sv = {inited: false};
				var initialize = function() {
					if (sv.inited) return;

					sv.inited = true;
				};
				var myFunction = function() {
					
				};
				return {
					init: initialize,
					myFunction
				};
			}(document));
		</script>
	</head>
	<body>
		<app name="main">
			<?php require($APP_RootDir."private/block/core/top-panel/enroll.php"); ?>
			<main>
				<section class="container">
					<h2><?=$header["title"]?></h2>
					<div class="table static responsive striped">
						<table>
							<thead>
								<tr>
									<th>No.</th>
									<th>File name</th>
									<th>File size</th>
									<th>Actual time</th>
								</tr>
								<tbody><?php
									$uploads = array_filter(scandir("."), "filter_for_uploads");
									$f_inc = 1;
									foreach ($uploads as $file) { ?>
										<tr>
											<td class="center"><?=$f_inc++?></td>
											<td><a href="<?=$file?>" class="semi-blend" target="_blank"><?=$file?></a></td>
											<td class="right"><?=number_format(filesize($file));?></td>
											<td class="center"><?=date("Y-m-d H:i:s", (int)preg_replace("/\D/", "", $file))?></td>
										</tr>
									<?php }
								?></tbody>
							</thead>
						</table>
					</div>
				</section>
			</main>
			<?php
				$resourcePath["navtab"] = "private/block/core/side-panel/enroll.php";
				require($APP_RootDir."private/block/core/material/main.php");
			?>
		</app>
	</body>
</html>