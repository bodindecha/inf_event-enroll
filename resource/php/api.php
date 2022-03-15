<?php
    session_start();
    // Recieve
    $type = $_REQUEST["type"] ?? null; $command = $_REQUEST["act"] ?? null; $attr = $_REQUEST["param"] ?? null;
    // Review
    $return = array("success" => false, "reason" => array(array(3, "Attributes empty")));
    if (empty($type) || empty($command)) die(json_encode($return));
    else $return["reason"] = array();
    // Connect
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
    require($dirPWroot."e/resource/db_connect.php"); require_once($dirPWroot."e/enroll/resource/php/config.php");
    require($dirPWroot."resource/php/core/getip.php");
    function escapeSQL($input) {
        global $db;
        return $db -> real_escape_string($input);
    } function successState($output) {
        global $return;
        $return["success"] = true; unset($return["reason"]);
        if (!empty($output)) $return["info"] = $output;
    } function errorMessage($type, $text=null) {
        global $return;
        array_push($return["reason"], (empty($text) ? $type : array($type, $text)));
    } function has_perm($what, $mods = true) {
        if (!(isset($_SESSION['auth']) && $_SESSION['auth']['type']=="t")) return false;
        $mods = ($mods && $_SESSION['auth']['level']>=75); $perm = (in_array("*", $_SESSION['auth']['perm']) || in_array($what, $_SESSION['auth']['perm']));
        return ($perm || $mods);
    }
    // Execute
    switch ($type) {
        case "new": { switch ($command) {
            case "authen": {
                if (!preg_match("/^([13-8]\d{4}|8\d{5}|9{5})$/", $attr["user"]) || !preg_match("/^\d{13}$/", $attr["pswd"]))
                    errorMessage(2, "รูปแบบเลขประจำตัวผู้สมัครหรือเลขประจำตัวประชาชนไม่ถูกต้อง");
                else {
                    $amsid = escapeSQL($attr["user"]); $natid = escapeSQL($attr["pswd"]);
                    $get = $db -> query("SELECT a.datid, a.amsid, CONCAT(a.namepth, a.namefth, ' ', a.namelth) AS nameath, a.type, a.choose, a.filetype, a.time, a.ip, b.start, b.stop FROM admission_newstd a INNER JOIN admission_timerange b ON a.timerange=b.trid WHERE a.amsid=$amsid AND a.natid=$natid");
                    if ($get) {
                        if ($get -> num_rows == 1) {
                            $read = $get -> fetch_array(MYSQLI_ASSOC); $data = array(
                                "type" => intval($read["type"]),
                                "name" => $read["nameath"],
                                "expire" => date("วันที่ d/m/Y เวลา H:iน.", strtotime($read["stop"])),
                                "done" => !empty($read["choose"]),
                                "evfile" => (($read["choose"] == "N" && !empty($read["filetype"])) ? encryptNID($read["amsid"]) : null)
                            ); if ($data["done"]) {
                                $data["choice"] = $read["choose"];
                                $data["decidetime"] = date("วันที่ d/m/Y เวลา H:i:s", strtotime($read["time"]));
                                $data["IPaddr"] = $read["ip"];
                                $data["authuser"] = encryptNID($read["datid"]);
                            } else {
                                $data["inTime"] = inTimerange($read["start"], $read["stop"]);
                                if ($data["inTime"]) $data["returnTo"] = encryptNID($read["datid"]);
                            } successState($data);
                            slog($read["datid"], "admission", $type, $command, "", "pass");
                        } else if ($get -> num_rows > 1) {
                            errorMessage(1, "เกิดข้อผิดพลาด. มีข้อมูลของท่านมากกว่า 1 รายการ.<br>กรุณาติดต่อผู้ดูแลระบบ.");
                            slog("webForm", "admission", $type, $command, "$amsid,$natid", "fail", "", "MultipleResult");
                        } else {
                            errorMessage(1, "ไม่มีข้อมูลของท่านในระบบ");
                            slog("webForm", "admission", $type, $command, "$amsid,$natid", "fail", "", "NoResult");
                        }
                    } else {
                        errorMessage(3, "Unable to get data.");
                        slog("webForm", "admission", $type, $command, "$amsid,$natid", "fail", "", "InvalidQuery");
                    }
                } break;
            } case "decide": {
                if (!preg_match("/^[0-9A-Za-z]{4,7}$/", $attr["user"]))
                    errorMessage(3, "เกิดขอ้ผิดพลาด. กรุณาปิดแล้วเปิดหน้านี้และลองใหม่อีกครั้ง.");
                else if (!preg_match("/^(Y|N)$/", $attr["choose"]))
                    errorMessage(1, "รูปแบบคำตอบไม่ถูกต้อง. กรุณาลองใหม่อีกครั้ง.");
                else {
                    $datid = escapeSQL(decryptNID($attr["user"])); $choose = escapeSQL($attr["choose"]);
                    // Check timerange
                    $getTR = $db -> query("SELECT b.start, b.stop FROM admission_newstd a INNER JOIN admission_timerange b ON a.timerange=b.trid WHERE a.datid=$datid");
                    if (!($getTR && $getTR -> num_rows == 1)) {
                        errorMessage(3, "Unable to get time range.");
                        slog($datid, "admission", $type, $command, "getTR", "fail", "", "InvalidQuery");
                    } else {
                        $readTR = $getTR -> fetch_array(MYSQLI_ASSOC);
                        if (inTimerange($readTR["start"], $readTR["stop"])) {
                            // Clean input
                            $record = true; $dataSQL = ""; switch ($choose) {
                                case "Y": {
                                $namefen = escapeSQL($attr["namefen"]); $namelen = escapeSQL($attr["namelen"]);
                                if (!preg_match("/^[A-Z][a-z\- ]{1,49}$/", $namefen) || preg_match("/^(\-| ){2,}$/", $namefen)) {
                                    $record = false; errorMessage(1, "รูปแบบชื่อจริงภาษาอังกฤษไม่ถูกต้อง");
                                } else if (!preg_match("/^[A-Z][A-Z\- ]{1,49}$/", $namelen) || preg_match("/^(\-| ){2,}$/", $namelen)) {
                                    $record = false; errorMessage(1, "รูปแบบนามสกุลภาษาอังกฤษไม่ถูกต้อง");
                                } $dataSQL = ",namefen='$namefen',namelen='$namelen'";
                                break;
                            } case "N": {
                                $fileType = escapeSQL($attr["file-ext"]);
                                if (!in_array($fileType, array("png", "jpg", "jpeg", "gif", "heic", "pdf"))) {
                                    $record = false; errorMessage(2, "ประเภทไฟล์ไม่ถูกต้อง กรุณาติดต่อเจ้าหน้าที่ให้ทำการลบไฟล์ แล้วเริ่มใหม่");
                                } else $dataSQL = ",filetype='$fileType'";
                               break; 
                            } } if ($record) {
                                $success = $db -> query("UPDATE admission_newstd SET choose='$choose',ip='$ip'$dataSQL WHERE datid=$datid");
                                if ($success) {
                                    $getbio = $db -> query("SELECT amsid,choose,filetype,time,ip FROM admission_newstd WHERE datid=$datid");
                                    if (!($getbio && $getbio -> num_rows == 1)) {
                                        errorMessage(3, "Unable to get data.");
                                        slog($datid, "admission", $type, $command, "getUR", "fail", "", "InvalidQuery");
                                    } else {
                                        $readbio = $getbio -> fetch_array(MYSQLI_ASSOC);
                                        successState(array(
                                            "result" => true,
                                            "choice" => $readbio["choose"],
                                            "decidetime" => date("วันที่ d/m/Y เวลา H:i:s", strtotime($readbio["time"])),
                                            "IPaddr" => $readbio["ip"],
                                            "evfile" => (($readbio["choose"] == "N" && !empty($readbio["filetype"])) ? encryptNID($readbio["amsid"]) : null)
                                        )); slog($datid, "admission", $type, $command, "getUR", "pass");
                                    }
                                } else {
                                    errorMessage(3, "Unable to record data.");
                                    slog($datid, "admission", $type, $command, $choose, "fail", "", "InvalidQuery");
                                }
                            }
                        } else {
                            successState(array("result" => false));
                            slog($datid, "admission", $type, $command, $choose, "fail", "", "Timeout");
                        }
                    }
                } break;
            } case "record": {
                if (!preg_match("/^[0-9A-Za-z]{4,7}$/", $attr))
                    errorMessage("0");
                else {
                    $datid = escapeSQL(decryptNID($attr));
                    // Check timerange
                    $getdata = $db -> query("SELECT a.amsid, a.choose, a.filetype, b.start, b.stop FROM admission_newstd a INNER JOIN admission_timerange b ON a.timerange=b.trid WHERE a.datid=$datid");
                    if (!($getdata && $getdata -> num_rows == 1)) {
                        errorMessage("1"); // No user
                        slog($datid, "admission", $type, $command, "getDt", "fail", "", "InvalidQuery");
                    } else {
                        $readdata = $getdata -> fetch_array(MYSQLI_ASSOC);
                        $inTime = inTimerange($readdata["start"], $readdata["stop"]);
                        $hasFile = (!empty($readdata["filetype"]));
                        $allowFile = ($readdata["choose"] <> "Y" && !$hasFile);
                        if ($hasFile) {
                            errorMessage("2");
                            slog($datid, "admission", $type, $command, "getDt", "fail", "", "Duplicate");
                        } else if (!$inTime) {
                            errorMessage("3");
                            slog($datid, "admission", $type, $command, "getDt", "fail", "", "Timeout");
                        } else if (!$allowFile) {
                            errorMessage("4");
                            slog($datid, "admission", $type, $command, "getDt", "fail", "", "NotAccept");
                        } else if (!isset($_FILES['usf'])) {
                            errorMessage("5"); // No file
                            slog($datid, "admission", $type, $command, "getDt", "fail", "", "NoFile");
                        } else {
                            $target_dir = "../upload/newstd/"; $fileType = strtolower(pathinfo(basename($_FILES['usf']["name"]), PATHINFO_EXTENSION));
                            $newFileName = $readdata["amsid"].".$fileType"; $target_file = $target_dir.$newFileName;
                            $uploadOk = ($_FILES['usf']["size"] > 0 && $_FILES['usf']["size"] <= 10240000); // 10 MB
                            if (!in_array($fileType, array("png", "jpg", "jpeg", "gif", "heic", "pdf"))) $uploadOk = false;
                            if ($uploadOk) {
                                if (file_exists($target_file)) unlink($target_file);
                                if (move_uploaded_file($_FILES['usf']["tmp_name"], $target_file)) {
                                    slog($datid, "admission", $type, $command, "uf", "pass");
                                    die('<script type="text/javascript">top.cnf.recieved("'.$fileType.'");</script>');
                                } else {
                                    errorMessage("7"); // Upload error
                                    slog($datid, "admission", $type, $command, "uf", "fail", "", "UploadError");
                                }
                            } else {
                                errorMessage("6"); // Ineligible file
                                slog($datid, "admission", $type, $command, "uf", "fail", "", "FileIneligible");
                            }
                        }
                    }
                } header("Location: /e/enroll/new-swefur".(!empty($return["reason"] ?? null) ? "#msgID=".implode("", $return["reason"]) : ""));
            } default: array_push($return["reason"], array(1, "Invalid command")); break; } break;
        } case "save": {
            $authuser = $_SESSION['auth']['user'] ?? null;
            $options = ($command == "cng" ? str_split("ABCDEFG") : array("Y", "N"));
            if (empty($authuser)) {
                errorMessage("0"); // Unauthorized
                slog("webForm", "admission", $command, $type, $attr, "fail", "", "Unauthorized");
            } else if ($_SESSION['auth']['type'] <> "s") {
                errorMessage("1"); // Not student
                slog($authuser, "admission", $command, $type, $attr, "fail", "", "NotStudentUserType");
            } else if (!in_array($attr, $options)) {
                errorMessage("2"); // Invalid option
                slog($authuser, "admission", $command, $type, $attr, "fail", "", "InvalidOption");
            } else {
                function try_upload_file($dir) {
                    if (!isset($_FILES['usf'])) return false;
                    global $authuser, $command, $type, $attr, $fileType;
                    $target_dir = "../upload/$dir/"; $fileType = strtolower(pathinfo(basename($_FILES['usf']["name"]), PATHINFO_EXTENSION));
                    $newFileName = "$authuser.$fileType"; $target_file = $target_dir.$newFileName;
                    $uploadOk = ($_FILES['usf']["size"] > 0 && $_FILES['usf']["size"] <= 10240000); // 10 MB
                    if (!in_array($fileType, array("png", "jpg", "jpeg", "gif", "heic", "pdf"))) $uploadOk = false;
                    if ($uploadOk) {
                        if (file_exists($target_file)) unlink($target_file);
                        if (move_uploaded_file($_FILES['usf']["tmp_name"], $target_file)) return true;
                        else {
                            errorMessage("9"); // Upload error
                            slog($authuser, "admission", $command, $type, $attr, "fail", "", "UploadError");
                        }
                    } else {
                        errorMessage("8"); // Ineligible file
                        slog($authuser, "admission", $command, $type, $attr, "fail", "", "FileIneligible");
                    } return false;
                } $name = ""; $sqlTail = "a INNER JOIN admission_timerange b ON a.timerange=b.trid WHERE a.stdid=$authuser";
                switch ($command) {
                    case "prs": {
                        $name = "present";
                        $getchk = $db -> query("SELECT a.choose, b.start, b.stop FROM admission_$name $sqlTail");
                        if (!$getchk) {
                            errorMessage("3"); // Error get
                            slog($authuser, "admission", $command, $type, $attr, "fail", "", "InvalidQueryG");
                        } else if ($getchk -> num_rows == 1) {
                            $readchk = $getchk -> fetch_array(MYSQLI_ASSOC);
                            if (!empty($readchk["choose"])) {
                                errorMessage("5"); // Responded
                                slog($authuser, "admission", $command, $type, $attr, "fail", "", "Responded");
                            } else if (!inTimerange($readchk["start"], $readchk["stop"])) {
                                errorMessage("6"); // Timeout
                                slog($authuser, "admission", $command, $type, $attr, "fail", "", "Timeout");
                            } else if ($attr=="Y" && !isset($_FILES['usf'])) {
                                errorMessage("7"); // No file
                                slog($authuser, "admission", $command, $type, $attr, "fail", "", "NoFile");
                            } else if ($attr=="N" || try_upload_file($name)) {
                                $choose = escapeSQL($attr); $fileType = (isset($fileType) ? "'".$fileType."'" : "NULL");
                                $success = $db -> query("UPDATE admission_$name SET choose='$choose',filetype=$fileType,ip='$ip' WHERE stdid=$authuser");
                                if ($success) {
                                    successState(null);
                                    slog($authuser, "admission", $command, $type, $attr, "pass");
                                } else {
                                    errorMessage("A"); // Error record
                                    slog($authuser, "admission", $command, $type, $attr, "fail", "", "InvalidQueryR");
                                }
                            }
                        } else {
                            errorMessage("4"); // Invalid response
                            slog($authuser, "admission", $command, $type, $attr, "fail", "", "InvalidResponse");
                        }
                        break;
                    } case "cng": {
                        $name = "change";
                        $getchk = $db -> query("SELECT a.type, b.start, b.stop FROM admission_$name $sqlTail");
                        if (!$getchk) {
                            errorMessage("3"); // Error get
                            slog($authuser, "admission", $command, $type, $attr, "fail", "", "InvalidQueryG");
                        } else if ($getchk -> num_rows == 1) {
                            $readchk = $getchk -> fetch_array(MYSQLI_ASSOC);
                            if (!inTimerange($readchk["start"], $readchk["stop"])) {
                                errorMessage("6"); // Timeout
                                slog($authuser, "admission", $command, $type, $attr, "fail", "", "Timeout");
                            } else if ($choose == $readchk["type"]) {
                                errorMessage("B"); // Same group
                                slog($authuser, "admission", $command, $type, $attr, "fail", "", "Duplicate");
                            } else if (!isset($_FILES['usf'])) {
                                errorMessage("7"); // No file
                                slog($authuser, "admission", $command, $type, $attr, "fail", "", "NoFile");
                            } else if (try_upload_file($name)) {
                                $choose = escapeSQL($attr);
                                $success = $db -> query("UPDATE admission_$name SET times=times+1,choose='$choose',filetype='$fileType',ip='$ip' WHERE stdid=$authuser");
                                if ($success) {
                                    successState(null);
                                    slog($authuser, "admission", $command, $type, $attr, "pass");
                                } else {
                                    errorMessage("A"); // Error record
                                    slog($authuser, "admission", $command, $type, $attr, "fail", "", "InvalidQueryR");
                                }
                            }
                        } else {
                            errorMessage("4"); // Invalid response
                            slog($authuser, "admission", $command, $type, $attr, "fail", "", "InvalidResponse");
                        }
                        break;
                    } case "cnf": {
                        $name = "confirm";
                        $getchk = $db -> query("SELECT a.choose, b.start, b.stop FROM admission_$name $sqlTail");
                        if (!$getchk) {
                            errorMessage("3"); // Error get
                            slog($authuser, "admission", $command, $type, $attr, "fail", "", "InvalidQueryG");
                        } else if ($getchk -> num_rows == 1) {
                            $readchk = $getchk -> fetch_array(MYSQLI_ASSOC);
                            if (!empty($readchk["choose"])) {
                                errorMessage("5"); // Responded
                                slog($authuser, "admission", $command, $type, $attr, "fail", "", "Responded");
                            } else if (!inTimerange($readchk["start"], $readchk["stop"])) {
                                errorMessage("6"); // Timeout
                                slog($authuser, "admission", $command, $type, $attr, "fail", "", "Timeout");
                            } else if ($attr=="N" && !isset($_FILES['usf'])) {
                                errorMessage("7"); // No file
                                slog($authuser, "admission", $command, $type, $attr, "fail", "", "NoFile");
                            } else if ($attr=="Y" || try_upload_file($name)) {
                                $choose = escapeSQL($attr); $fileType = (isset($fileType) ? "'".$fileType."'" : "NULL");
                                $success = $db -> query("UPDATE admission_$name SET choose='$choose',filetype=$fileType,ip='$ip' WHERE stdid=$authuser");
                                if ($success) {
                                    successState(null);
                                    slog($authuser, "admission", $command, $type, $attr, "pass");
                                } else {
                                    errorMessage("A"); // Error record
                                    slog($authuser, "admission", $command, $type, $attr, "fail", "", "InvalidQueryR");
                                }
                            }
                        } else {
                            errorMessage("4"); // Invalid response
                            slog($authuser, "admission", $command, $type, $attr, "fail", "", "InvalidResponse");
                        }
                        break;
                    } default: array_push($return["reason"], array(1, "Invalid command")); break;
                }
            } header("Location: /e/enroll/M4/$name".(!empty($return["reason"] ?? null) ? "#msgID=".implode("", $return["reason"]) : ""));
            break;
        } case "mod": {
            if (!has_perm("admission")) {
                errorMessage(2, "You are unauthorized.");
                slog("webForm", "admission", $type, $command, "", "fail", "", "Unauthorized");
            } else {
                $authuser = $_SESSION['auth']['user'] ?? "";
                function optionResult($choice) { return (empty($choice) ? "ยังไม่ใช้" : ($choice ? "ยืนยัน" : "สละ")); }
                switch ($command) {
                    case "find": {
                        $user = escapeSQL($attr['user']); $group = $attr['group'];
                        if (!preg_match("/^([13-7]\d{4}|8\d{5}|9{5})$/", $user))
                            errorMessage(2, "รูปแบบเลขประจำตัวไม่ถูกต้อง");
                        else {
                            switch ($group) {
                                case "new": $rtype = 1; $sqlinfo = "SELECT datid,CONCAT(namepth,namefth,' ',namelth) AS nameath,type,choose,time,ip FROM admission_newstd WHERE amsid=$user"; break;
                                case "prs": $rtype = 2; $sqlinfo = "SELECT a.stdid,CONCAT(b.namepth,b.namefth,' ',b.namelth) AS nameath,(CASE a.timerange WHEN 5 THEN 3 ELSE a.timerange END) AS timerange,a.choose,a.filetype,a.time,a.ip FROM admission_present a INNER JOIN bd_student b ON a.stdid=b.stdid WHERE a.stdid=$user"; break;
                                case "cng": $rtype = 3; $sqlinfo = "SELECT a.stdid,CONCAT(b.namepth,b.namefth,' ',b.namelth) AS nameath,c.name AS name1,a.choose,d.name AS name2,a.filetype,a.time,a.ip FROM admission_change a INNER JOIN bd_student b ON a.stdid=b.stdid INNER JOIN admission_sgroup c ON a.type=c.code LEFT JOIN admission_sgroup d ON a.choose=d.code WHERE a.stdid=$user"; break;
                                case "cnf": $rtype = 4; $sqlinfo = "SELECT a.stdid,CONCAT(b.namepth,b.namefth,' ',b.namelth) AS nameath,c.name,a.choose,a.filetype,a.time,a.ip FROM admission_confirm a INNER JOIN bd_student b ON a.stdid=b.stdid INNER JOIN admission_sgroup c ON a.type=c.code WHERE a.stdid=$user"; break;
                            } if (isset($sqlinfo)) {
                                $getinfo = $db -> query($sqlinfo);
                                if (!$getinfo) {
                                    errorMessage(3, "Unable to get data.");
                                    slog($authuser, "admission", $type, $command, "$user,$group", "fail", "", "InvalidQuery");
                                } else if ($getinfo -> num_rows <> 1) {
                                    successState(array(
                                        "msgType" => "red",
                                        "message" => '<center class="last">ไม่พบข้อมูลของเลขประจำตัว '.$user.' ในหวมดหมู่ที่ท่านเลือก</center>',
                                        "action" => null
                                    )); slog($authuser, "admission", $type, $command, "$user,$group", "fail", "", "NotExisted");
                                } else {
                                    $readinfo = $getinfo -> fetch_array(MYSQLI_ASSOC); $data = array(
                                        "msgType" => "cyan",
                                        "action" => intval(!empty($readinfo["choose"])) + intval(!empty($readinfo["filetype"] ?? null)),
                                        "impact" => encryptNID($readinfo[($group=="new" ? "datid" : "stdid")])."+".strrev(str_rot13(encryptNID($rtype)))
                                    ); $intype = array(
                                        "ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // ในเขตพื้นที่บริการ
                                        "ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // ในเขตพื้นที่บริการ (คุณสมบัติไม่ครบ) [deprecated]
                                        "ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // นอกเขตพื้นที่บริการ
                                        "ห้องเรียนพิเศษคณิตศาสตร์", // ชั้นมัธยมศึกษาปีที่ 1
                                        "ห้องเรียนพิเศษวิทยาศาสตร์ คณิตศาสตร์ เทคโนโลยี และสิ่งแวดล้อม ตามแนวทาง สสวท. และ สอวน.", // ชั้นมัธยมศึกษาปีที่ 1
                                        "ห้องเรียนพิเศษวิทยาศาสตร์ คณิตศาสตร์ เทคโนโลยี และสิ่งแวดล้อม", // ชั้นมัธยมศึกษาปีที่ 4
                                        "ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 4
                                        "โครงการห้องเรียน พสวท. (สู่ความเป็นเลิศ)" // ชั้นมัธยมศึกษาปีที่ 4
                                    ); function ts() {
                                        global $readinfo;
                                        return (!empty($readinfo["choose"]) ? " เมื่อ".date("วันที่ d/m/Y เวลา H:i:s", strtotime($readinfo["time"]))." ผ่านที่อยู่ IP ".$readinfo["ip"] : "");
                                    } switch ($group) {
                                        case "new": $data["message"] = $readinfo["nameath"]." <u>".optionResult($readinfo["choose"])."สิทธิ์</u>การรายงานตัวประเภท<u>".$intype[intval($readinfo["type"])-1]."</u>".ts(); break;
                                        case "prs": $data["message"] = $readinfo["nameath"]." <u>".optionResult($readinfo["choose"])."สิทธิ์</u>การรายงานคัวเข้ารับการศึกษาชั้นมัธยมศึกษาปีที่ 4 โรงเรียนเดิม".ts(); break;
                                        case "cng": $data["message"] = $readinfo["nameath"].(empty($readinfo["choose"]) ? "ไม่ได้ยื่นคำชอเปลี่ยนแปลงกลุ่มการเรียน" : "ยื่นคำขอเปลี่ยนแปลงกลุ่มการเรียนจากเดิมกลุ่มการเรียน<u>".$readinfo["name1"]."</u> เป็นกลุ่มการเรียน<u>".$readinfo["name2"]."</u>"); break;
                                        case "cnf": $data["message"] = $readinfo["nameath"]." <u>".optionResult($readinfo["choose"])."สิทธิ์</u>การเข้าเรียนกลุ่มการเรียน<u>".$readinfo["name"]."</u>".ts(); break;
                                    } successState($data);
                                    slog($authuser, "admission", $type, $command, "$user,$group", "pass");
                                }
                            } else {
                                errorMessage(2, "ตัวเลือกหมวดหมู่ไม่ถูกต้อง");
                                slog($authuser, "admission", $type, $command, "$user,$group", "fail", "", "InvalidOption");
                        } } break;
                    } case "remove": {
                        if (!preg_match("/^[0-9A-Za-z]{4,7}\+[0-9A-Za-z]{4,7}$/", $attr))
                            errorMessage(2, "เกิดข้อผิดพลาด. กรุณาลองใหม่อีกครั้ง.");
                        else {
                            $attr = explode("+", $attr);
                            $user = decryptNID($attr[0]); $group = decryptNID(str_rot13(strrev($attr[1])));
                            switch ($group) {
                                case 1: $group = "new"; $sqlchk = "SELECT choose FROM admission_newstd WHERE datid=$user"; break;
                                case 2: $group = "prs"; $sqlchk = "SELECT choose FROM admission_present WHERE stdid=$user"; break;
                                case 3: $group = "cng"; $sqlchk = "SELECT choose FROM admission_change WHERE stdid=$user"; break;
                                case 4: $group = "cnf"; $sqlchk = "SELECT choose FROM admission_confirm WHERE stdid=$user"; break;
                            } if (isset($sqlchk)) {
                                $getchk = $db -> query($sqlchk);
                                if (!$getchk) {
                                    errorMessage(3, "Unable to get data.");
                                    slog($authuser, "admission", $type, $command, "$user,$group", "fail", "", "InvalidQueryG");
                                } else if ($getchk -> num_rows <> 1) {
                                    errorMessage(2, "ไม่พบรายการที่จะทำการลบ.");
                                    slog($authuser, "admission", $type, $command, "$user,$group", "fail", "", "NotExisted");
                                } else {
                                    $readchk = ($getchk -> fetch_array(MYSQLI_ASSOC))["choose"];
                                    if (empty($readchk)) {
                                        errorMessage(2, "ไม่มีข้อมูลให้ทำการลบ.");
                                        slog($authuser, "admission", $type, $command, "$user,$group", "fail", "", "Empty");
                                    } else {
                                        switch ($group) {
                                            case "new": $sqldone = "UPDATE admission_newstd SET choose=NULL,time=NULL,ip='',namefen='',namelen='' WHERE datid=$user"; break;
                                            case "prs": $sqldone = "UPDATE admission_present SET choose=NULL,filetype=NULL,time=NULL,ip='' WHERE stdid=$user"; break;
                                            case "cng": $sqldone = "UPDATE admission_change SET choose=NULL,filetype=NULL,time=NULL,ip='' WHERE stdid=$user"; break;
                                            case "cnf": $sqldone = "UPDATE admission_confirm SET choose=NULL,filetype=NULL,time=NULL,ip='' WHERE stdid=$user"; break;
                                        } $success = $db -> query($sqldone);
                                        if ($success) {
                                            successState(null);
                                            slog($authuser, "admission", $type, $command, "$user,$group", "pass");
                                        } else {
                                            errorMessage(3, "Unable to record data.");
                                            slog($authuser, "admission", $type, $command, "$user,$group", "fail", "", "InvalidQueryR");
                                        }
                                    }
                                }
                            } else {
                                errorMessage(2, "ตัวเลือกหมวดหมู่ไม่ถูกต้อง");
                                slog($authuser, "admission", $type, $command, "$user,$group", "fail", "", "InvalidOption");
                        } } break;
                    } case "check": {
                        $user = escapeSQL($attr['user']); $group = $attr['group'];
                        if (!preg_match("/^([13-7]\d{4}|8\d{5}|9{5})$/", $user))
                            errorMessage(2, "รูปแบบเลขประจำตัวไม่ถูกต้อง");
                        else {
                            switch ($group) {
                                case "new": $sqlinfo = "SELECT datid,CONCAT(namepth,namefth,' ',namelth) AS nameath,type,choose FROM admission_newstd WHERE amsid=$user"; break;
                                case "old": $sqlinfo = "SELECT a.stdid AS datid,CONCAT(b.namepth,b.namefth,' ',b.namelth) AS nameath,c.name AS type,a.choose FROM admission_confirm a INNER JOIN bd_student b ON a.stdid=b.stdid INNER JOIN admission_sgroup c ON a.type=c.code WHERE a.stdid=$user"; break;
                            } if (isset($sqlinfo)) {
                                $getinfo = $db -> query($sqlinfo);
                                if (!$getinfo) {
                                    errorMessage(3, "Unable to get data.");
                                    slog($authuser, "admission", $type, $command, "$user,$group", "fail", "", "InvalidQuery");
                                } else if ($getinfo -> num_rows <> 1) {
                                    errorMessage(1, "ไม่พบข้อมูลของเลขประจำตัว $user");
                                    slog($authuser, "admission", $type, $command, "$user,$group", "fail", "", "NotExisted");
                                } else {
                                    $readinfo = $getinfo -> fetch_array(MYSQLI_ASSOC); $data = array(
                                        "action" => $readinfo["choose"] == "Y",
                                        "impact" => encryptNID($readinfo["datid"])."+".strrev(str_rot13(encryptNID($group == "new" ? intval($readinfo["type"]) : 7)))
                                    ); if ($group == "new") {
                                        $intype = array(
                                            "ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // ในเขตพื้นที่บริการ
                                            "ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // ในเขตพื้นที่บริการ (คุณสมบัติไม่ครบ) [deprecated]
                                            "ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 1 // นอกเขตพื้นที่บริการ
                                            "ห้องเรียนพิเศษคณิตศาสตร์", // ชั้นมัธยมศึกษาปีที่ 1
                                            "ห้องเรียนพิเศษวิทยาศาสตร์ คณิตศาสตร์ เทคโนโลยี และสิ่งแวดล้อม ตามแนวทาง สสวท. และ สอวน.", // ชั้นมัธยมศึกษาปีที่ 1
                                            "ห้องเรียนพิเศษวิทยาศาสตร์ คณิตศาสตร์ เทคโนโลยี และสิ่งแวดล้อม", // ชั้นมัธยมศึกษาปีที่ 4
                                            "ห้องเรียนทั่วไป", // ชั้นมัธยมศึกษาปีที่ 4
                                            "โครงการห้องเรียน พสวท. (สู่ความเป็นเลิศ)" // ชั้นมัธยมศึกษาปีที่ 4
                                        ); $readinfo["type"] = $intype[intval($readinfo["type"]) - 1];
                                    } $data["message"] = $readinfo["nameath"]." <u>".optionResult($readinfo["choose"])."สิทธิ์</u>การรายงานตัว".($group == "new" ? "ประเภท" : "กลุ่มการเรียน")."<u>".$readinfo["type"]."</u>";
                                    successState($data);
                                    slog($authuser, "admission", $type, $command, "$user,$group", "pass");
                                }
                            } else {
                                errorMessage(2, "ตัวเลือกหมวดหมู่ไม่ถูกต้อง");
                                slog($authuser, "admission", $type, $command, "$user,$group", "fail", "", "InvalidOption");
                        } } break;
                    } default: array_push($return["reason"], array(1, "Invalid command")); break;
                } break;
            }
        } default: array_push($return["reason"], array(1, "Invalid type")); break;
    } $db -> close();
    echo json_encode($return);
?>