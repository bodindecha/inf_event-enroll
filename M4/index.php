<?php
    session_start();
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
    require_once($dirPWroot."e/enroll/resource/php/config.php");

    // Redirection only
    if (!isset($_SESSION['auth'])) header("Location: /?return_url=e%2Fenroll%2FM4%2F");
    if (time() <= strtotime("2022-03-02 23:59:59") || inDaterange("2022-03-03", "2022-03-04") || inDaterange("2022-03-16", "2022-03-18"))
        header("Location: present");
    else if (inDaterange("2022-03-05", "2022-03-10")) header("Location: change");
    else if (inDaterange("2022-03-11", "2022-03-15")) header("Location: confirm");
    else {
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <script type="text/javascript">
            alert("ขณะนี้อยู่นอกช่วงเวลาใช้งานระบบ");
            if (history.length > 1) history.back();
            else location = "/e/enroll/";
        </script>
	</head>
	<body>
	</body>
</html>
<?php } ?>