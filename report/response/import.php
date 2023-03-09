<?php
	$dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	$normalized_control = false;
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");

    require($dirPWroot."e/resource/db_connect.php");
    function escapeSQL($input) {
        global $db;
        return $db -> real_escape_string(trim($input));
    } $authuser = $_SESSION['auth']['user'] ?? "";
    if (empty($authuser)) {
        $error = array("901", '3, "You are unauthorized."');
        slog("webForm", "admission", "mod", "export", trim($_POST['system'] ?? "").",".trim($_POST['filetype'] ?? "").",".strval(isset($_POST['evdLink'])), "fail", "", "Unauthorized");
    } else if (!has_perm("admission")) {
        $error = array("901", '2, "You don\'t have permission to perform this action."');
        slog($authuser, "admission", "mod", "export", trim($_POST['system'] ?? "").",".trim($_POST['filetype'] ?? "").",".strval(isset($_POST['evdLink'])), "fail", "", "NoPerm");
    } else if (isset($_POST['system'])) {
        /* Import code starts here */
        $return = array(); function errorMessage($msgID) {
            global $return;
            array_push($return, $msgID);
        } function importReader($file) {
            global $db, $authuser, $type, $preQuery, $fileName;
            function stopEFA() {
                global $authuser, $fileName;
                slog($authuser, "admission", "mod", "import", $fileName, "fail", "", "FileNoOpen");
                errorMessage("3");
            } $fileReader = fopen($file, "r") or stopEFA();
            if (empty($fileReader)) {
                slog($authuser, "admission", "mod", "import", $fileName, "fail", "", "FileNoRead");
                return errorMessage("4");
            } $data = array(); while (!feof($fileReader)) {
                $content = fgets($fileReader);
                if (empty(trim($content))) continue;
                $read = explode(",", trim($content)); $read = array_map("escapeSQL", $read);
                if ($type == "prs") $query = "$read[0],$read[1]";
                else if ($type == "cng" || $type == "cnf") $query = "$read[0],'$read[1]',$read[2]";
                else if ($type == "new") $query = "$read[0],'$read[1]','$read[2]','$read[3]','$read[4]',$read[5],$read[6]";
                array_push($data, "($query)");
            } fclose($fileReader);
            if (!count($data)) {
                slog($authuser, "admission", "mod", "import", $fileName, "fail", "", "NoData");
                errorMessage("5");
            } else {
                $success = $db -> query("INSERT INTO admission_$preQuery VALUES ".implode(",", $data));
                if ($success) {
                    slog($authuser, "admission", "mod", "import", $fileName, "pass");
                    errorMessage("6");
                } else {
                    slog($authuser, "admission", "mod", "import", $fileName, "fail", "", "InvalidQuery");
                    errorMessage("7");
                    errorMessage("&_error=".urlencode($db -> error));
                }
            }
        } $type = trim($_POST["system"]); switch ($type) {
            case "prs": $preQuery = "present (stdid,timerange)"; break;
            case "cng": $preQuery = "change (stdid,type,timerange)"; break;
            case "cnf": $preQuery = "confirm (stdid,type,timerange)"; break;
            case "new": $preQuery = "newstd (amsid,natid,namepth,namefth,namelth,type,timerange)"; break;
        } if (!isset($preQuery)) {
            slog($authuser, "admission", "mod", "import", $fileName, "fail", "", "InvalidType");
            errorMessage("0");
        } $target_dir = "../../resource/upload/DB-import/"; $fileName = explode(" ", $preQuery)[0]."-".time().".csv"; $target_file = $target_dir.$fileName;
        if ($_FILES['usf']["size"] > 0) {
            if (move_uploaded_file($_FILES['usf']["tmp_name"], $target_file)) importReader($target_file);
            else {
                slog($authuser, "admission", "mod", "import", $fileName, "fail", "", "UploadError");
                errorMessage("2"); // Upload error
            }
        } else {
            slog($authuser, "admission", "mod", "import", $fileName, "fail", "", "FileIneligible");
            errorMessage("1"); // Ineligible file
        }
        header("Location: /e/enroll/report/import-data".(!empty($return ?? null) ? "#msgID=".implode("", $return) : ""));
        exit(0);
    } else {
        $error = array("902", '1, "ไม่พบคำสั่งเริ่มต้น"');
    } $db -> close();

    if (isset($error)) {
        $header_title = "การนำเข้าข้อมูล";
        $home_menu = "settings";
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
                app.ui.notify(1, [<?=$error[1]?>]);
            });
		</script>
	</head>
	<body>
		<?php require($dirPWroot."e/enroll/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<iframe src="/error/<?=$error[0]?>">Loading...</iframe>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>
<?php } ?>