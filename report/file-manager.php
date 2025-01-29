<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "File manager";
	$header["desc"] = "Private directory browsing and file management";

	$rootFolder = "public_html/e/enroll/resource/file/";

	// Safety first
	if (!isset($_SESSION["auth"])) {
		header("Location: $signinURL");
		exit(0);
	} else if (!$isAdministrator || !has_perm("admission")) {
		require_once($APP_RootDir."private/script/lib/TianTcl/various.php");
		TianTcl::http_response_code(901);
	}

	# error_reporting(0);
	set_time_limit(0);
	if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc())
		foreach ($_POST as $key => $value) $_POST[$key] = stripslashes($value);
	
	require($APP_RootDir."private/script/function/database.php");

	function perms($file) {
		$perms = fileperms($file);
		if (($perms & 49152) == 49152)		$info = "s";
		else if (($perms & 40960) == 40960)	$info = "l";
		else if (($perms & 32768) == 32768)	$info = "-";
		else if (($perms & 24576) == 24576)	$info = "b";
		else if (($perms & 16384) == 16384)	$info = "d";
		else if (($perms & 8192) == 8192)	$info = "c";
		else if (($perms & 4096) == 4096)	$info = "p";
		else								$info = "u";
		$info .= $perms & 256	? "r" : "-";
		$info .= $perms & 128	? "w" : "-";
		$info .= $perms & 64	? ($perms & 2048 ? "s" : "x") : ($perms & 2048	? "S" : "-");
		$info .= $perms & 32	? "r" : "-";
		$info .= $perms & 16	? "w" : "-";
		$info .= $perms & 8		? ($perms & 1024 ? "s" : "x") : ($perms & 1024	? "S" : "-");
		$info .= $perms & 4		? "r" : "-";
		$info .= $perms & 2		? "w" : "-";
		$info .= $perms & 1		? ($perms & 512  ? "t" : "x") : ($perms & 512	? "T" : "-");
		return $info;
	}

	$media_files = array("ico", "jpg", "jpeg", "png", "apng", "webp", "webm", "mp4", "mp3", "pdf");
	$APP_PAGE -> print -> head();
?>
<style type="text/css">
	app[name=main] .message > *:not(:last-child) { margin-bottom: 10px; }
	app[name=main] .form input[type="file"] {
		height: 38px !important;
		line-height: 3;
		display: flex;
	}
	app[name=main] .form input[type="file"]::file-selector-button { display: none; }
	app[name=main] pre code {
		font-size: .8em !important;
		white-space: pre-wrap !important;
		display: block !important;
	}
	app[name=main] .file-preview {
		padding: 0 !important;
		width: 100% !important; /* aspect-ratio: 4 / 3; */
		border: 1px solid var(--clr-main-black-absolute); border-radius: 1rem;
		background-color: var(--clr-gg-grey-300);
	}
</style>
<script type="text/javascript">
	
</script>
<?php $APP_PAGE -> print -> nav("enroll"); ?>
<main>
	<section class="container">
		<h2><?=$header["title"]?></h2>
		<div class="table list center"><table>
			<tr>
				<td align="left">Path :
<?php
	if (isset($_GET["path"])) $path = $_GET["path"];
	else {
		# $path = getcwd();
		# $path = preg_replace("/(\/|\\\\)[^\/\\\\]+$/", "/", $_SERVER["PHP_SELF"]);
		$path = "/";
	} $path = str_replace("\\", "/", $path);
	$path = preg_replace("/(((^|\/)?\.\.?\/)+|\.$)/", "/", $path);
	$paths = explode("/", $path);
	foreach ($paths as $id => $pat) {
		if (!strlen($pat) && $id == 0) {
			$a = true;
			echo '<a href="javascript:" onClick="location.assign(location.pathname)">home</a> / ';
			continue;
		} if (!strlen($pat)) continue;
		echo '<a href="?path=';
		for ($i = 0; $i <= $id; $i++) {
			echo "{$paths[$i]}";
			if ($i <> $id) echo "/";
		}
		echo '">'.$pat."</a> / ";
	} $path = rtrim($path, "/");
	$lpath = $APP_RootDir.$rootFolder.ltrim($path, "/");
	echo '</td></tr><tr><td>';
	if (isset($_FILES["file"])) {
		if (move_uploaded_file($_FILES["file"]["tmp_name"], "$lpath/".$_FILES["file"]["name"])) {
			echo '<center class="message green">Upload Success</center>';
			syslog_a(null, "devtool", "new", "file", "$path/".$_FILES["file"]["name"], "pass", "", "", false, true);
		} else {
			echo '<center class="message red">Upload Failed</center>';
			syslog_a(null, "devtool", "new", "file", "$path/".$_FILES["file"]["name"], "fail", "", "", false, true);
		}
	}
	else if (isset($_POST["folder"]) && preg_match("/^[A-Z0-9a-zก-๛\-._ \(\)\[\]]{1,128}$/", trim($_POST["folder"]))) {
		if (!is_dir("$lpath/".$_POST["folder"]) && mkdir("$lpath/".$_POST["folder"])) {
			echo '<center class="message green">Folder Create Success</center>';
			syslog_a(null, "devtool", "new", "folder", "$spath/".$_POST["folder"], "pass", "", "", false, true);
		} else {
			echo '<center class="message red">Folder Create Failed</center>';
			syslog_a(null, "devtool", "new", "folder", "$spath/".$_POST["folder"], "fail", "", "", false, true);
		}
	}
	if (!isset($_GET["filesrc"]) && !isset($_GET["option"])) {
?>
	<form class="form form-bs inline fit" method="post">
		<div class="group">
			<label>New Folder</label>
			<input type="text" name="folder" required pattern="[A-Z0-9a-zก-๛\-._ \(\)\[\]]{1,128}" />
		</div>
		<button class="gray ripple-click">Create</button>
	</form>&emsp;
	<form class="form form-bs inline fit" method="post" enctype="multipart/form-data">
		<div class="group pill">
			<label>File Upload</label>
			<input type="file" class="default small" style="line-height: 2;" role="button" data-text="Drag & Drop or Browse..." name="file" required />
		</div>
		<button class="cyan pill ripple-click">upload</button>
	</form>
<?php
	}
	echo '</td></tr>';
	if (isset($_GET["filesrc"])) {
		$lang = explode(".", $_GET["filesrc"]); $lang = strtolower(end($lang));
		if ($lang == "php") $lang = "html";
		echo "<tr><td>Current File : ".
			$_GET["filesrc"].
			"</tr></td></table></div>";
		if (in_array($lang, $media_files)) {
			$html_src = $rootFolder.ltrim($_GET["filesrc"], "/");
			$html_src = substr($html_src, strlen(explode("/", $html_src)[0]));
			if (explode("/", ltrim($_GET["filesrc"], "/"))[0] == "private") echo '<div class="message orange">Sorry, file content preview is currently unavailable.</div>';
			else if (in_array($lang, array_slice($media_files, 0, 6))) echo '<div class="message blue"><img class="file-preview" src="'.$html_src.'" /></div>';
			else echo '<iframe class="file-preview ratio ratio-4-3 land" allow="*" src="'.$html_src.'">Loading...</iframe>';
		} else echo '<pre><code contenteditable style="font-size: 15px !important;" class="language-'.$lang.'" lang="'.$lang.'">'.htmlspecialchars(file_get_contents($APP_RootDir.$rootFolder.ltrim($_GET["filesrc"], "/"))).'</code></pre>';
	} else if (isset($_GET["option"]) && $_POST["opt"]<>"delete") {
		echo "</table></div><center class=\"message gray\"><div>".$_POST["name"]."</div>";
		if ($_POST["opt"]=="chmod") {
			if (isset($_POST["perm"]) && false) {
				if (chmod($_POST["path"], intval($_POST["perm"]))) {
					echo '<center class="message green">Change Permission Success</center>';
					syslog_a(null, "devtool", "overwrite", "file", $_POST["path"]." -> ".$_POST["perm"], "pass", "", "", false, true);
				} else {
					echo '<center class="message red">Change Permission Failed</center>';
					syslog_a(null, "devtool", "overwrite", "file", $_POST["path"]." -> ".$_POST["perm"], "fail", "", "", false, true);
				}
			}
?>
	<center class="message yellow">Function unavailable. You cannot modify this part now.</center>
	<form class="form form-bs inline" method="post">
		<div class="group">
			<label>Permission</label>
			<input name="perm" type="text" size="4" value="<?=substr(sprintf("%o", fileperms($_POST["path"])), -4)?>" pattern="0\d{3}" required />
		</div>
		<input type="hidden" name="path" value="<?=$_POST["path"]?>">
		<input type="hidden" name="opt" value="chmod">
		<button disabled class="yellow ripple-click">Go</button>
	</form>
<?php
		} else if ($_POST["opt"] == "rename") {
			if (isset($_POST["newname"])) {
				if (rename($_POST["path"], "$lpath/".$_POST["newname"])) {
					echo '<center class="message green">Rename File Success</center>';
					syslog_a(null, "devtool", "trans", "file", "$lpath/".$_POST["name"]." -> ".$_POST["newname"], "pass", "", "", false, true);
				} else {
					echo '<center class="message red">Rename File Fail</center>';
					syslog_a(null, "devtool", "trans", "file", "$lpath/".$_POST["name"]." -> ".$_POST["newname"], "fail", "", "", false, true);
				}
				$_POST["name"] = $_POST["newname"];
			}
?>
	<form class="form form-bs inline" method="post">
		<div class="group">
			<label>New Name</label>
			<input name="newname" type="text" size="20" value="<?=$_POST["name"]?>" required />
		</div>
		<input type="hidden" name="path" value="<?=$_POST["path"]?>">
		<input type="hidden" name="opt" value="rename">
		<button class="blue ripple-click">Go</button>
	</form>
<?php
		} else if ($_POST["opt"] == "edit") {
			$type = explode(".", $_POST["name"]); $type = strtolower(end($type));
			if (in_array($type, $media_files)) echo 'You cannot edit a non text editable file here.'; else {
			if (isset($_POST["src"])) {
				$fp = fopen($_POST["path"], "w");
				if (fwrite($fp, $_POST["src"])) {
					echo '<center class="message green">Success Edit File</center>';
					syslog_a(null, "devtool", "edit", "file", str_replace("//", "/", ltrim($_POST["path"], "/")), "pass", "", "", false, true);
				} else {
					echo '<center class="message red">Fail Edit File</center>';
					syslog_a(null, "devtool", "edit", "file", str_replace("//", "/", ltrim($_POST["path"], "/")), "fail", "", "", false, true);
				}
				fclose($fp);
			}
?>
	<form class="form form-bs" method="post">
		<textarea name="src" cols="80" rows="20"><?=htmlspecialchars(file_get_contents($_POST["path"]))?></textarea>
		<input type="hidden" name="path" value="<?=$_POST["path"]?>">
		<input type="hidden" name="opt" value="edit">
		<button class="green ripple-click">Save</button>
	</form>
<?php
		} echo "</center>";
	} } else {
		echo '</table></div>';
		if (isset($_GET["option"]) && $_POST["opt"]=="delete") {
			echo "<center class=\"message gray\">";
			if ($_POST["type"] == "dir") {
				if (rmdir($_POST["path"])) {
					echo '<center class="message green">Directory Deleted</center>';
					syslog_a(null, "devtool", "del", "file", "D: ".$_POST["path"], "pass", "", "", false, true);
				} else {
					echo '<center class="message red">Directory Delete Fail</center>';
					syslog_a(null, "devtool", "del", "file", "D: ".$_POST["path"], "fail", "", "", false, true);
				}
			} else if ($_POST["type"] == "file") {
				if (unlink($_POST["path"])) {
					echo '<center class="message green">File Deleted</center>';
					syslog_a(null, "devtool", "del", "file", "F: ".$_POST["path"], "pass", "", "", false, true);
				} else {
					echo '<center class="message red">File Delete Fail</center>';
					syslog_a(null, "devtool", "del", "file", "F: ".$_POST["path"], "fail", "", "", false, true);
				}
			}
			echo "</center>";
		}
?>
	<div class="table static responsive" id="content"><table><thead>
		<tr class="first">
			<th>Name</th>
			<th>Size</th>
			<th>Permission</th>
			<th>Modify</th>
		</tr>
	</thead><tbody>
		<tr><td colspan="4" center>— Folder —</td></tr>
<?php
		$scandir = scandir($lpath);
		foreach ($scandir as $dir) {
			if (!is_dir("$lpath/$dir") || $dir == "." || $dir == "..") continue;
?>
		<tr>
			<td><a href="?path=<?="$path/$dir"?>"><?=$dir?></a></td>
			<td center>—</td>
			<td center>
<?php
			if (is_writable("$lpath/$dir"))			echo '<font style="color: var(--clr-bs-green);">';
			else if (!is_readable("$lpath/$dir"))	echo '<font style="color: var(--clr-bs-red);">';
			echo perms("$lpath/$dir");
			if (is_writable("$lpath/$dir") || !is_readable("$lpath/$dir")) echo '</font>';
?>
			</td>
			<td>
				<form class="form form-bs inline center" method="post" action="?option&path=<?=$path?>">
					<select name="opt" required>
						<option value selected disabled>Select</option>
						<option value="delete">Delete</option>
						<option value="chmod">Chmod</option>
						<option value="rename">Rename</option>
					</select>
					<input type="hidden" name="type" value="dir">
					<input type="hidden" name="name" value="<?=$dir?>">
					<input type="hidden" name="path" value="<?="$lpath/$dir"?>">
					<button class="default small ripple-click">&gt;</button>
				</form>
			</td>
		</tr>
<?php
		}
		echo '</tbody><tbody><tr><td colspan="4" center>— File —</td></tr>';
		foreach ($scandir as $file) {
			if (!is_file("$lpath/$file")) continue;
			$size = filesize("$lpath/$file") / 1024;
			$size = round($size, 2);
			if ($size >= 1024) $size = round($size / 1024, 2)." MB";
			else $size = $size." KB";
			echo '<tr><td><a href="?filesrc='.$path.'/'.$file.'&path='.$path.'">'.$file.'</a></td><td align="right">'.$size.'</td><td center>';
			if (is_writable("$lpath/$file"))		echo '<font style="color: var(--clr-bs-green);">';
			else if (!is_readable("$lpath/$file"))	echo '<font style="color: var(--clr-bs-red);">';
			echo perms("$lpath/$file");
			if (is_writable("$lpath/$file") || !is_readable("$lpath/$file")) echo "</font>";
?>
			</td>
			<td>
				<form class="form form-bs inline center" method="post" action="?option&path=<?=$path?>">
					<select name="opt" required>
						<option value selected disabled>Select</option>
						<option value="delete">Delete</option>
						<option value="chmod">Chmod</option>
						<option value="rename">Rename</option>
						<option value="edit">Edit</option>
					</select>
					<input type="hidden" name="type" value="file">
					<input type="hidden" name="name" value="<?=$file?>">
					<input type="hidden" name="path" value="<?="$lpath/$file"?>">
					<button class="default small ripple-click">&gt;</button>
				</form>
			</td>
		</tr>
<?php
		} echo '</tbody></table></div>';
	} $APP_DB[0] -> close();
?>
	</section>
</main>
<?php
	$APP_PAGE -> print -> materials(side_panel: "enroll");
	$APP_PAGE -> print -> footer();
?>