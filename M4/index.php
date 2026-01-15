<?php
	session_start();
	$dirPWroot = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/")-1);
	require_once($dirPWroot."e/enroll/resource/php/config.php");

	// Redirection only
	if (!isset($_SESSION["auth"])) header("Location: /account/sign-in#next=e%2Fenroll%2FM4%2F");

	/* Academic Year 2565
	if (time() <= strtotime("2022-03-02 23:59:59") || inDaterange("2022-03-03", "2022-03-04") || inDaterange("2022-03-16", "2022-03-18"))
		header("Location: present");
	else if (inDaterange("2022-03-05", "2022-03-10")) header("Location: change");
	else if (inDaterange("2022-03-11", "2022-03-15")) header("Location: confirm"); */

	/* Academic Year 2566
	if (inDaterange("2023-03-01", "2023-03-10")) header("Location: change");
	else if (inDaterange("2023-03-13", "2023-03-17")) header("Location: confirm");

	/* Academic Year 2567
	if (inDaterange("2024-03-20", "2024-03-21")) header("Location: present");
	else if (inDaterange("2024-03-04", "2024-03-11")) header("Location: change");
	else if (inDaterange("2024-03-06", "2024-03-20")) header("Location: confirm");

	/* Academic Year 2568
	if (inDaterange("2025-02-10", "2025-03-14")) header("Location: change");
	else if (inDaterange("2025-03-12", "2025-03-16")) header("Location: confirm");
	else { */

	/* Academic Year 2569 */
	if (inDaterange("2025-02-06", "2025-03-17")) header("Location: change");
	else if (inDaterange("2025-03-15", "2025-03-18")) header("Location: confirm");
	else {
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script type="text/javascript">
			alert("ขณะนี้อยู่นอกห้วงเวลาการใช้งานระบบ");
			if (
				(document.referrer.length && document.referrer.startsWith(location.origin) &&
				(new URL(document.referrer)).pathname != location.pathname) || (history.length > 1 && history.currentIndex)
			) history.back();
			else location.assign("/e/enroll/");
		</script>
	</head>
	<body></body>
</html>
<?php } ?>