<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");
	$header_title = "การตั้งค่าเวลา";
	$home_menu = "mod";
	
	# $forceExternalBrowser = true;
	$permitted = has_perm("admission"); if ($permitted) {
		require($dirPWroot."e/resource/db_connect.php"); require_once($dirPWroot."e/enroll/resource/php/config.php");
		function escapeSQL($input) {
			global $db;
			return $db -> real_escape_string($input);
		} $authuser = $_SESSION['auth']['user'];
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
		$getset = $db -> query("SELECT trid,name,start,stop FROM admission_timerange WHERE NOT trid=0 AND year=2565");
		$has_result = ($getset && $getset -> num_rows);
	}
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
			html body main div.container div.filter {
				width: 100%; height: 100%;
				border-radius: 20px; box-shadow: 0px 2.5px 2.5px 2.5px rgb(0, 0, 0, 0.25);
				overflow: hidden;
			}
			html body main div.container div.filter *:not(i) { font-size: 15px; line-height: 20px; font-family: "THKodchasal", serif; }
			html body main div.container div.filter input {
				padding: 10px 55px 10px 15px;
				width: 100%;
			}
			html body main div.container div.filter input::-webkit-search-cancel-button {
				transform: scale(2.5) translateY(-1.25px);
				cursor: pointer;
			}
			html body main div.container div.filter i {
				position: absolute; right: 17.5px;
				width: 40px; height: 40px;
				font-size: 36px; line-height: 40px;
				color: var(--clr-gg-grey-500); text-align: center;
				pointer-events: none;
			}
			main .table td:nth-child(1) { text-align: right; }
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
			function fd() {
				var txt = $("html body main div.container div.filter input").val().trim();
				w3.filterHTML(".table table tbody", "tr", txt);
			}
			function ro(col) {
				w3.sortHTML(".table table tbody", "tr", "td:nth-child("+col.toString()+")");
			}
		</script>
		<script type="text/javascript" src="/resource/js/lib/w3.min.js"></script>
	</head>
	<body>
		<?php require($dirPWroot."e/enroll/resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<div class="container">
				<h2>การตั้งค่าเวลา</h2>
				<?php if (!$has_result) echo '<center class="message red">ไม่พบรายการกำหนดการเวลา. กรุณาแจ้งผู้ดูแลระบบให้เพิ่มค่าเริ่มต้นให้.</center>'; else { ?>
					<div class="filter"><input type="search" placeholder="Filter ... (ตัวกรอง)" onInput="fd()"/><i class="material-icons">filter_list</i></div>
					<form class="form table extend" method="post"><table><thead><tr>
						<th onClick="ro(1)">REF</th>
						<th onClick="ro(2)">กิจกรรม</th>
						<th>เริ่มต้น (เปิด)</th>
						<th>สิ้นสุด (ปิด)</th>
					</tr></thead><tbody>
						<?php while ($es = $getset -> fetch_assoc()) { ?>
						<tr>
							<td><?=$es["trid"]?>&nbsp;</td>
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