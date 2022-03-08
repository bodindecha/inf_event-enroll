<?php
    session_start();
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
    $normalized_control = false;
	require($dirPWroot."e/enroll/resource/hpe/init_ps.php");

    require($dirPWroot."e/resource/db_connect.php");
    $authuser = $_SESSION['auth']['user'] ?? "";
    if (empty($authuser)) {
        $error = array("901", '3, "You are unauthorized."');
        slog("webForm", "admission", "mod", "export", trim($_POST['system'] ?? "").",".trim($_POST['filetype'] ?? "").",".strval(isset($_POST['evdLink'])), "fail", "", "Unauthorized");
    } else if (!has_perm("admission")) {
        $error = array("901", '2, "You don\'t have permission to perform this action."');
        slog($authuser, "admission", "mod", "export", trim($_POST['system'] ?? "").",".trim($_POST['filetype'] ?? "").",".strval(isset($_POST['evdLink'])), "fail", "", "NoPerm");
    } else if (isset($_POST['start'])) {
        $system = trim($_POST['system'] ?? "");
        $reqType = trim($_POST['filetype'] ?? "");
        if (empty($system) || empty($reqType)) {
            $error = array("902", '1, "Parameter empty."');
            slog($authuser, "admission", "mod", "export", "$system,$reqType,".strval(isset($_POST['evdLink'])), "fail", "", "ParamEmpty");
        } else if (!in_array($system, array("prs", "cng", "cnf", "new")) || !in_array($reqType, array("csv", "tsv", "json"))) {
            $error = array("902", '2, "ตัวเลือกไม่ถูกต้อง"');
            slog($authuser, "admission", "mod", "export", "$system,$reqType,".strval(isset($_POST['evdLink'])), "fail", "", "InvalidOption");
        } else {
            // Gain data
            $evdLink = isset($_POST['evdLink']); $dltime = date("Y-m-d H_i_s", time());
            $linkPrefix = "ลิงก์: https://inf.bodin.ac.th/e/enroll/report/response/file?of=";
            switch ($system) {
                case "prs": {
                    $name = "รายงานตัวม3 ขึ้นม4";
                    $result = $db -> query("SELECT stdid,timerange,choose,filetype,COALESCE(time,lastupdate) AS time FROM admission_present ORDER BY time ASC");
                    $has_result = ($result && $result -> num_rows); switch ($reqType) {
                        case "csv": case "tsv": {
                            $delimeter = ($reqType == "tsv" ? "\t" : ",");
                            $outputData = "\"ประทับเวลา\"$delimeter\"รอบที่\"$delimeter\"เลขประจำตัวนักเรียน\"$delimeter\"การเลือกใช้สิทธิ์\"";
                            if ($evdLink) $outputData .= "$delimeter\"ไฟล์หลักฐาน\"";
                            if ($has_result) { while ($er = $result -> fetch_assoc()) {
                                // Modify
                                if ($er["timerange"]=="5") $er["timerange"] = "3";
                                $er["choose"] = (empty($er["choose"]) ? "ยังไม่ใช้สิทธิ์" : ($er["choose"]=="Y" ? "ยืนยันสิทธิ์" : "สละสิทธิ์"));
                                if (!empty($er["filetype"])) $er["filetype"] = $linkPrefix.$er["stdid"]."&type=present";
                                // Concat
                                $outputData .= "\n\"".$er["time"]."\"$delimeter\"".$er["timerange"]."\"$delimeter\"".$er["stdid"]."\"$delimeter\"".$er["choose"]."\"";
                                if ($evdLink) $outputData .= "$delimeter\"".$er["filetype"]."\"";
                            } }
                            break;
                        } case "json": {
                            $outputData = array();
                            if ($has_result) { while ($er = $result -> fetch_assoc()) {
                                // Modify
                                if ($er["timerange"]=="5") $er["timerange"] = "3";
                                $er["choose"] = (empty($er["choose"]) ? "ยังไม่ใช้สิทธิ์" : ($er["choose"]=="Y" ? "ยืนยันสิทธิ์" : "สละสิทธิ์"));
                                $er["filetype"] = (empty($er["filetype"]) ? "" : $linkPrefix.$er["stdid"]."&type=present");
                                // Concat
                                $rowdata = array(
                                    "ประทับเวลา" => $er["time"],
                                    "รอบที่" => $er["timerange"],
                                    "เลขประจำตัวนักเรียน" => $er["stdid"],
                                    "การเลือกใช้สิทธิ์" => $er["choose"]
                                ); if ($evdLink) $rowdata["ไฟล์หลักฐาน"] = $er["filetype"];
                                array_push($outputData, $rowdata);
                            } }
                            break;
                    } } break;
                } case "cng": {
                    $name = "เปลี่ยนกลุ่มการเรียน";
                    $result = $db -> query("SELECT a.stdid,b.name AS name1,COALESCE(c.name,'') AS name2,a.times,a.filetype,COALESCE(a.time,a.lastupdate) AS time FROM admission_change a INNER JOIN admission_sgroup b ON a.type=b.code LEFT JOIN admission_sgroup c ON a.choose=c.code ORDER BY time ASC");
                    $has_result = ($result && $result -> num_rows); switch ($reqType) {
                        case "csv": case "tsv": {
                            $delimeter = ($reqType == "tsv" ? "\t" : ",");
                            $outputData = "\"ประทับเวลา\"$delimeter\"เลขประจำตัวนักเรียน\"$delimeter\"กลุ่มการเรียน\"$delimeter\"แก้ไขครั้งที่\"$delimeter\"เป็นกลุ่มการเรียน\"";
                            if ($evdLink) $outputData .= "$delimeter\"ไฟล์หลักฐาน\"";
                            if ($has_result) { while ($er = $result -> fetch_assoc()) {
                                // Modify
                                if (!empty($er["filetype"])) $er["filetype"] = $linkPrefix.$er["stdid"]."&type=change";
                                // Concat
                                $outputData .= "\n\"".$er["time"]."\"$delimeter\"".$er["stdid"]."\"$delimeter\"".$er["name1"]."\"$delimeter\"".$er["times"]."\"".$er["name2"]."\"";
                                if ($evdLink) $outputData .= "$delimeter\"".$er["filetype"]."\"";
                            } }
                            break;
                        } case "json": {
                            $outputData = array();
                            if ($has_result) { while ($er = $result -> fetch_assoc()) {
                                // Modify
                                $er["filetype"] = (empty($er["filetype"]) ? "" : $linkPrefix.$er["stdid"]."&type=change");
                                // Concat
                                $rowdata = array(
                                    "ประทับเวลา" => $er["time"],
                                    "เลขประจำตัวนักเรียน" => $er["stdid"],
                                    "กลุ่มการเรียน" => $er["name1"],
                                    "แก้ไขครั้งที่" => $er["times"],
                                    "เป็นกลุ่มการเรียน" => $er["name2"]
                                ); if ($evdLink) $rowdata["ไฟล์หลักฐาน"] = $er["filetype"];
                                array_push($outputData, $rowdata);
                            } }
                            break;
                    } } break;
                } case "cnf": {
                    $name = "ยืนยันสิทธิ์ม3 ขึ้นม4";
                    $result = $db -> query("SELECT a.stdid,b.name,a.choose,a.filetype,COALESCE(a.time,a.lastupdate) AS time FROM admission_confirm a INNER JOIN admission_sgroup b ON a.type=b.code ORDER BY time ASC");
                    $has_result = ($result && $result -> num_rows); switch ($reqType) {
                        case "csv": case "tsv": {
                            $delimeter = ($reqType == "tsv" ? "\t" : ",");
                            $outputData = "\"ประทับเวลา\"$delimeter\"เลขประจำตัวนักเรียน\"$delimeter\"กลุ่มการเรียน\"$delimeter\"การเลือกใช้สิทธิ์\"";
                            if ($evdLink) $outputData .= "$delimeter\"ไฟล์หลักฐาน\"";
                            if ($has_result) { while ($er = $result -> fetch_assoc()) {
                                // Modify
                                $er["choose"] = (empty($er["choose"]) ? "ยังไม่ใช้สิทธิ์" : ($er["choose"]=="Y" ? "ยืนยันสิทธิ์" : "สละสิทธิ์"));
                                if (!empty($er["filetype"])) $er["filetype"] = $linkPrefix.$er["stdid"]."&type=confirm";
                                // Concat
                                $outputData .= "\n\"".$er["time"]."\"$delimeter\"".$er["stdid"]."\"$delimeter\"".$er["name"]."\"$delimeter\"".$er["choose"]."\"";
                                if ($evdLink) $outputData .= "$delimeter\"".$er["filetype"]."\"";
                            } }
                            break;
                        } case "json": {
                            $outputData = array();
                            if ($has_result) { while ($er = $result -> fetch_assoc()) {
                                // Modify
                                $er["choose"] = (empty($er["choose"]) ? "ยังไม่ใช้สิทธิ์" : ($er["choose"]=="Y" ? "ยืนยันสิทธิ์" : "สละสิทธิ์"));
                                $er["filetype"] = (empty($er["filetype"]) ? "" : $linkPrefix.$er["stdid"]."&type=confirm");
                                // Concat
                                $rowdata = array(
                                    "ประทับเวลา" => $er["time"],
                                    "เลขประจำตัวนักเรียน" => $er["stdid"],
                                    "กลุ่มการเรียน" => $er["name"],
                                    "การเลือกใช้สิทธิ์" => $er["choose"]
                                ); if ($evdLink) $rowdata["ไฟล์หลักฐาน"] = $er["filetype"];
                                array_push($outputData, $rowdata);
                            } }
                            break;
                    } } break;
                } case "new": {
                    $name = "รายงานตัวนักเรียนใหม่"; $intype = array(
                        "ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // ในเขตพื้นที่บริการ
                        "ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // ในเขตพื้นที่บริการ (คุณสมบัติไม่ครบ) [deprecated]
                        "ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // นอกเขตพื้นที่บริการ
                        "ห้องเรียนพิเศษคณิตศาสตร์", // ชั้นมัธยมศึกษาปีที่ 1
                        "ห้องเรียนพิเศษวิทยาศาสตร์ คณิตศาสตร์ เทคโนโลยี และสิ่งแวดล้อม ตามแนวทาง สสวท. และ สอวน.", // ชั้นมัธยมศึกษาปีที่ 1
                        "ห้องเรียนพิเศษวิทยาศาสตร์ คณิตศาสตร์ เทคโนโลยี และสิ่งแวดล้อม", // ชั้นมัธยมศึกษาปีที่ 4
                        "ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 4
                        "โครงการห้องเรียน พสวท. (สู่ความเป็นเลิศ)" // ชั้นมัธยมศึกษาปีที่ 4
                    );
                    $result = $db -> query("SELECT amsid,natid,namepth,namefth,namelth,type,COALESCE(time,lastupdate) AS time,choose,namefen,namelen FROM admission_newstd ORDER BY time ASC");
                    $has_result = ($result && $result -> num_rows); switch ($reqType) {
                        case "csv": case "tsv": {
                            $delimeter = ($reqType == "tsv" ? "\t" : ",");
                            $outputData = "\"ประทับเวลา\"$delimeter\"เลขประจำตัวผู้สมัคร\"$delimeter\"เลขประจำตัวประชาชน\"$delimeter\"คำนำหน้า\"$delimeter\"ชื่อจริง\"$delimeter\"นามสกุล\"$delimeter\"ประเภทห้องเรียน\"$delimeter\"การเลือกใช้สิทธิ์\"$delimeter\"ชื่อจริงภาษาอังกฤษ\"$delimeter\"นามสกุลภาษาอังกฤษ\"";
                            if ($has_result) { while ($er = $result -> fetch_assoc()) {
                                // Modify
                                $er["type"] = $intype[intval($er["type"])-1];
                                $er["choose"] = (empty($er["choose"]) ? "ยังไม่ใช้สิทธิ์" : ($er["choose"]=="Y" ? "ยืนยันสิทธิ์" : "สละสิทธิ์"));
                                // Concat
                                $outputData .= "\n\"".$er["time"]."\"$delimeter\"".$er["amsid"]."\"$delimeter\"".$er["natid"]."\"$delimeter\"".$er["namepth"]."\"$delimeter\"".$er["namefth"]."\"$delimeter\"".$er["namelth"]."\"$delimeter\"".$er["type"]."\"$delimeter\"".$er["choose"]."\"$delimeter\"".$er["namefen"]."\"$delimeter\"".$er["namelen"]."\"";
                            } }
                            break;
                        } case "json": {
                            $outputData = array();
                            if ($has_result) { while ($er = $result -> fetch_assoc()) {
                                // Modify
                                $er["type"] = $intype[intval($er["type"])-1];
                                $er["choose"] = (empty($er["choose"]) ? "ยังไม่ใช้สิทธิ์" : ($er["choose"]=="Y" ? "ยืนยันสิทธิ์" : "สละสิทธิ์"));
                                // Concat
                                $rowdata = array(
                                    "ประทับเวลา" => $er["time"],
                                    "เลขประจำตัวผู้สมัคร" => $er["amsid"],
                                    "เลขประจำตัวประชาชน" => $er["natid"],
                                    "คำนำหน้า" => $er["namepth"],
                                    "ชื่อจริง" => $er["namefth"],
                                    "นามสกุล" => $er["namelth"],
                                    "ประเภทห้องเรียน" => $er["type"],
                                    "การเลือกใช้สิทธิ์" => $er["choose"],
                                    "ชื่อจริงภาษาอังกฤษ" => $er["namefen"],
                                    "นามสกุลภาษาอังกฤษ" => $er["namelen"]
                                ); array_push($outputData, $rowdata);
                            } }
                            break;
                    } } break;
                }
            } $name .= " $dltime.$reqType";
            switch ($reqType) {
                case "csv": $mime = "text/csv"; break;
                case "tsv": $mime = "text/tsv"; break;
                case "json": $mime = "application/json"; break;
            } if ($reqType == "json") $outputData = json_encode($outputData, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            if (isset($error)) {
                $error = array("905", '3, "Unknown error occurred."');
                slog($authuser, "admission", "mod", "export", "$system,$reqType,$evdLink", "fail");
            } else {
                // --- Start Force Download ---
                if (ob_get_contents()) {
                    die("Some data has already been output, can't export data file");
                }
                header("Content-Description: File Transfer");
                if (headers_sent()) {
                    die("Some data has already been output to browser, can't export data file");
                }
                header("Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1");
                # header("Cache-Control: public, must-revalidate, max-age=0"); // HTTP/1.1
                header("Pragma: public");
                header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
                header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
                // force download dialog
                if (strpos(php_sapi_name(), "cgi") === false) {
                    header("Content-Type: $mime", true);
                    # header("Content-Type: application/force-download");
                    header("Content-Type: application/octet-stream", false);
                    header("Content-Type: application/download", false);
                    # header("Content-Type: application/pdf", false);
                    # header("Content-Type: $mime", false);
                    header("Content-Length: ".strlen(strval($outputData)));
                } else header("Content-Type: $mime");
                // use the Content-Disposition header to supply a recommended filename
                header("Content-Disposition: attachment; filename=\"".basename($name)."\"");
                header("Content-Transfer-Encoding: binary");
                # TCPDF_STATIC::sendOutputData($this->getBuffer(), $this->bufferlen);
                echo strval($outputData);
                // --- End Force Download ---
                slog($authuser, "admission", "mod", "export", "$system,$reqType,$evdLink", "pass");
            }
        }
    } else {
        $error = array("902", '1, "ไม่พบคำสั่งเริ่มต้น"');
    } $db -> close();

    if (isset($error)) {
        $header_title = "การนำออกข้อมูล";
        $header_menu = "mod";
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