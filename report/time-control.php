<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "การตั้งค่าเวลา";
	$home_menu = "mod";
	
	# $forceExternalBrowser = true;
	$permitted = has_perm("admission"); if ($permitted) {
		require($dirPWroot."e/resource/db_connect.php"); require_once($dirPWroot."e/enroll/resource/php/config.php");
		$authuser = $_SESSION['auth']['user'];
		$tsRegex = '20\d{2}-(0[1-9]|1[0-2])-(0[1-9]|[1-2]\d|3[0-1]) ([0-1]\d|2[0-3])(:([0-5]\d)){2}';
		// Update
		if (isset($_POST['save'])) {
			unset($_POST['save']);
			if (count($_POST)) {
				$data = array();
				foreach ($_POST as $act => $ts) {
					if (!preg_match("/^$tsRegex$/", $ts)) {
						$error = "902";
						slog($authuser, "admission", "mod", "setTime", "", "fail", "", "Ineligible");
						break;
					} $act = explode("_", $act);
					$rid = decryptNID($act[0]);
					if (is_nan($rid)) {
						$error = "902";
						slog($authuser, "admission", "mod", "setTime", "", "fail", "", "InvalidOption");
						break;
					} if (!isset($data[$rid])) $data[$rid] = array();
					$data[$rid][$act[1]] = $ts;
				} $sqlprefix = "UPDATE admission_timerange SET"; $sqlupd = "";
				foreach ($data as $act => $tss) {
					if (count(array_keys($tss)) <> 2) {
						$error = "902";
						slog($authuser, "admission", "mod", "setTime", "", "fail", "", "ParamEmpty");
						break;
					} $sqlupd .= "$sqlprefix start='".escapeSQL($tss["start"])."',stop='".escapeSQL($tss["stop"])."' WHERE trid=$act; ";
				} if (!isset($_SESSION['var'])) $_SESSION['var'] = array();
				if (isset($error)) $_SESSION['var']["tmp-message"] = '3, "There\'s an error. Please try again."';
				else {
					$success = $db -> multi_query(trim($sqlupd));
					if ($success) {
						$_SESSION['var']["tmp-message"] = '0, "New time-range saved (Updated) successfully."';
						slog($authuser, "admission", "mod", "setTime", "", "pass");
					} else {
						$_SESSION['var']["tmp-message"] = '3, "Unable to update time-range. Please try again."';
						slog($authuser, "admission", "mod", "setTime", "", "fail", "", "InvalidQuery");
					}
				}
			} header("Refresh: 0");
		}
		// Load
		$getset = $db -> query("SELECT trid,name,start,stop FROM admission_timerange WHERE year=2565");
		$has_result = ($getset && $getset -> num_rows);
	}
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				<?php
					if (isset($_SESSION['var']["tmp-message"])) {
						echo 'app.ui.notify(1, ['.$_SESSION['var']["tmp-message"].']);';
						unset($_SESSION['var']["tmp-message"]);
					}
				?>
			});
		</script>
	</head>
	<body>
		<?php require($dirPWroot."e/enroll/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<div class="container">
				<h2>การตั้งค่าเวลา</h2>
				<?php if (!$has_result) echo '<center class="message red">ไม่พบรายการกำหนดการเวลา. กรุณาแจ้งผู้ดูแลระบบให้เพิ่มค่าเริ่มต้นให้.</center>'; else { ?>
					<form class="form table extend" method="post"><table><thead><tr>
						<th>กิจกรรม</th><th>เริ่มต้น (เปิด)</th><th>สิ้นสุด (ปิด)</th>
					</tr></thead><tbody>
						<?php while ($es = $getset -> fetch_assoc()) { ?>
						<tr>
							<td><?=$es["name"]?></td>
							<td><input type="text" maxlength="19" pattern="<?=$tsRegex?>" required value="<?=$es["start"]?>" name="<?=encryptNID($es["trid"])?>_start"></td>
							<td><input type="text" maxlength="19" pattern="<?=$tsRegex?>" required value="<?=$es["stop"]?>" name="<?=encryptNID($es["trid"])?>_stop"></td>
						</tr>
						<?php } ?>
					</tbody></table><div class="group spread">
						<a role="button" class="red hollow" onClick="location.reload()" href="javascript:void(0)">&emsp;รีเซ็ต&emsp;</a>
						<button class="blue" name="save">&emsp;บันทึก&emsp;</button>
					</div></form>
				<?php } ?>
			</div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."e/enroll/resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>