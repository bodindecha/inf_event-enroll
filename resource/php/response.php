<?php
    session_start();
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	// Inputs
    if (isset($_REQUEST['filter']) && isset($_REQUEST['page']) && isset($_REQUEST['show']) && isset($_REQUEST['sortBy']) && isset($_REQUEST['sortOrder'])) {
        require($dirPWroot."e/resource/db_connect.php"); require_once($dirPWroot."e/enroll/resource/php/config.php");
        // Clean up
        $filter = trim($_REQUEST['filter']);
        $page = trim($_REQUEST['page']);
        $show = trim($_REQUEST['show']);
        $sortBy = trim($_REQUEST['sortBy']);
        $sortOrder = trim($_REQUEST['sortOrder']);
        // Calculate
        if (isset($_REQUEST['change'])) {
            $change = trim($_REQUEST['change']); switch ($change) {
                case "filter": $page = "1"; $sortBy = "W"; $sortOrder = "DESC"; break;
                case "show": $page = strval(floor($_SESSION['var']['mUser_set']['page']*$_SESSION['var']['mUser_set']['show']/intval($show))+1); break;
                case "sortBy": $page = "1"; $sortOrder = "ASC"; break;
                case "sortOrder": $page = "1"; break;
                case "search": $page = "1"; break;
            }
        } if (!isset($_SESSION['var'])) $_SESSION['var'] = array();
        $_SESSION['var']['mUser_set'] = array( // Save for compare
            "filter" => $filter,
            "page" => intval($page)-1,
            "show" => intval($show),
            "sortBy" => $sortBy,
            "sortOrder" => $sortOrder
        );
        // Configuration
        if (isset($_REQUEST['list'])) { $list = trim($_REQUEST['list']); switch ($list) {
            case "new": {
                $dirname = "newstd";
                $colcode = array( // (colName, sortable, Display, Link)
                    "A" => array("amsid", true, "เลขประจำตัว", false, "a.amsid"),
                    "B" => array("namef", true, "ชื่อ", false, "CONCAT(a.namepth,a.namefth)"),
                    "C" => array("namelth", true, "สกุล", false, "a.namelth"),
                    "Z" => array("type", true, "ประเภทห้องเรียน", false, "a.type"),
                    "D" => array("choose2", true, "เลือก", false, "(CASE a.choose WHEN 'Y' THEN 'ยืนยันสิทธิ์' WHEN 'N' THEN 'สละสิทธิ์' ELSE 'ยังไม่ใช้สิทธิ์' END)"),
                    "E" => array("namefen", true, "ชื่ออังกฤษ", false, "a.namefen"),
                    "F" => array("namelen", true, "สกุลอังกฤษ", false, "a.namelen"),
                    "X" => array("filetype", true, "หมายเหตุ", false, "a.filetype"),
                    "W" => array("time", true, "แก้ไขล่าสุด", false, "a.time")
                ); $queryBegin = "SELECT a.amsid,CONCAT(a.namepth,a.namefth) AS namef,a.namelth,a.type,(CASE a.choose WHEN 'Y' THEN 'ยืนยันสิทธิ์' WHEN 'N' THEN 'สละสิทธิ์' ELSE 'ยังไม่ใช้สิทธิ์' END) AS choose2,a.filetype,a.namefen,a.namelen FROM admission_newstd a";
                $col = array("A", "B", "C", "Z", "D", "E", "F", "X");
                $searchable = array("A", "B", "C", "D", "E", "F");
                break;
            } case "prs": {
                $dirname = "present";
                $colcode = array( // (colName, sortable, Display, Link)
                    "A" => array("stdid", true, "ID", false, "a.stdid"),
                    "B" => array("namef", true, "ชื่อ", false, "CONCAT(c.namepth,c.namefth)"),
                    "C" => array("namelth", true, "สกุล", false, "c.namelth"),
                    "Y" => array("timerange", true, "รอบที่", false, "(CASE a.timerange WHEN 5 THEN 3 ELSE a.timerange END)"),
                    "D" => array("choose2", true, "เลือก", false, "(CASE a.choose WHEN 'Y' THEN 'ยืนยันสิทธิ์' WHEN 'N' THEN 'สละสิทธิ์' ELSE 'ยังไม่ใช้สิทธิ์' END)"),
                    "X" => array("filetype", true, "หมายเหตุ", true, "a.filetype"),
                    "W" => array("time", true, "แก้ไขล่าสุด", false, "a.time"),
                    "E" => array("room", true, "ห้อง", false, "c.room")
                ); $queryBegin = "SELECT a.stdid,(CASE a.timerange WHEN 5 THEN 3 ELSE a.timerange END) AS timerange,(CASE a.choose WHEN 'Y' THEN 'ยืนยันสิทธิ์' WHEN 'N' THEN 'สละสิทธิ์' ELSE 'ยังไม่ใช้สิทธิ์' END) AS choose2,a.filetype,CONCAT(c.namepth,c.namefth) AS namef,c.namelth,c.room FROM admission_present a INNER JOIN bd_student c ON a.stdid=c.stdid";
                $col = array("E", "A", "B", "C", "Y", "D", "X");
                $searchable = array("A", "B", "C", "D", "E");
                break;
            } case "cng": {
                $dirname = "change";
                $colcode = array( // (colName, sortable, Display, Link)
                    "A" => array("stdid", true, "ID", false, "a.stdid"),
                    "B" => array("namef", true, "ชื่อ", false, "CONCAT(c.namepth,c.namefth)"),
                    "C" => array("namelth", true, "สกุล", false, "c.namelth"),
                    "D" => array("name1", true, "สายการเรียน", false, "b.name"),
                    "E" => array("name2", true, "เปลี่ยนเป็น", false, "d.name"),
                    "X" => array("filetype", true, "หมายเหตุ", true, "a.filetype"),
                    "W" => array("time", true, "แก้ไขล่าสุด", false, "a.time"),
                    "F" => array("room", true, "ห้อง", false, "c.room")
                ); $queryBegin = "SELECT a.stdid,b.name AS name1,d.name AS name2,a.filetype,CONCAT(c.namepth,c.namefth) AS namef,c.namelth,c.room FROM admission_change a INNER JOIN admission_sgroup b ON a.type=b.code INNER JOIN bd_student c ON a.stdid=c.stdid LEFT JOIN admission_sgroup d ON a.choose=d.code";
                $col = array("F", "A", "B", "C", "D", "E", "X");
                $searchable = array("A", "B", "C", "D", "E", "F");
                break;
            } case "cnf": {
                $dirname = "confirm";
                $colcode = array( // (colName, sortable, Display, Link)
                    "A" => array("stdid", true, "ID", false, "a.stdid"),
                    "B" => array("namef", true, "ชื่อ", false, "CONCAT(c.namepth,c.namefth)"),
                    "C" => array("namelth", true, "สกุล", false, "c.namelth"),
                    "D" => array("name", true, "สายการเรียน", false, "b.name"),
                    "E" => array("choose2", true, "เลือก", false, "(CASE a.choose WHEN 'Y' THEN 'ยืนยันสิทธิ์' WHEN 'N' THEN 'สละสิทธิ์' ELSE 'ยังไม่ใช้สิทธิ์' END)"),
                    "X" => array("filetype", true, "หมายเหตุ", true, "a.filetype"),
                    "W" => array("time", true, "แก้ไขล่าสุด", false, "a.time"),
                    "F" => array("room", true, "ห้อง", false, "c.room")
                ); $queryBegin = "SELECT a.stdid,b.name,(CASE a.choose WHEN 'Y' THEN 'ยืนยันสิทธิ์' WHEN 'N' THEN 'สละสิทธิ์' ELSE 'ยังไม่ใช้สิทธิ์' END) AS choose2,a.filetype,CONCAT(c.namepth,c.namefth) AS namef,c.namelth,c.room FROM admission_confirm a INNER JOIN admission_sgroup b ON a.type=b.code INNER JOIN bd_student c ON a.stdid=c.stdid";
                $col = array("F", "A", "B", "C", "D", "E", "X");
                $searchable = array("A", "B", "C", "D", "E", "F");
                break;
            } default: die('{ "success": false }'); break;
        } } else die('{ "success": false }');
        // Pre generate SQL
        $sort = "ORDER BY ".$colcode[$sortBy][0]." $sortOrder";
        $disp = "LIMIT ".strval((intval($page)-1)*intval($show)).", $show";
        $queryEnd = "$sort $disp"; $sql = ""; $queryPreset = "$queryBegin WHERE 1";
        if (isset($_REQUEST['q']) && !empty(trim($_REQUEST['q']))) {
            $searchQuery = " AND ("; $q = $db -> real_escape_string(trim($_REQUEST['q']));
            foreach ($searchable as $cols) $searchQuery .= $colcode[$cols][4]." LIKE '$q%' OR ";
            $searchQuery = rtrim($searchQuery, " OR ").")";
        } else $searchQuery = "";
        // Translate
        $regex_sgroup = "/^[A-H]$/";
        $class = $_REQUEST["filter"]["class"];
        $group = $db -> real_escape_string(trim($_REQUEST["filter"]["group"]));
        $sql = $queryPreset;
        if ($list == "new") {
            if ($class<>"*") switch ($class) {
                case "ans": $sql .= " AND a.choose IS NOT NULL"; break;
                case "Y": $sql .= " AND a.choose='Y'"; $col = array("A", "B", "C", "Z", "D", "E", "F"); break;
                case "N": $sql .= " AND a.choose='N'"; $col = array("A", "B", "C", "Z", "D", "X"); break;
                case "una": $sql .= " AND a.choose IS NULL"; $col = array("A", "B", "C", "Z", "D"); break;
            } if ($group<>"*") $sql .= " AND a.timerange=$group";
        } else if ($list == "prs") {
            if ($class<>"*") switch ($class) {
                case "ans": $sql .= " AND a.choose IS NOT NULL"; break;
                case "Y": $sql .= " AND a.choose='Y'"; break;
                case "N": $sql .= " AND a.choose='N'"; $col = array("E", "A", "B", "C", "Y", "D"); break;
                case "una": $sql .= " AND a.choose IS NULL"; $col = array("E", "A", "B", "C", "Y", "D"); break;
            } if ($group<>"*") $sql .= " AND a.timerange=$group";
        } else if ($list == "cng") {
            if ($class<>"*") switch ($class) {
                case "ans": $sql .= " AND a.times>0"; break;
                case "una": $sql .= " AND a.times=0"; $col = array("F", "A", "B", "C", "D"); break;
            } if ($group<>"*") $sql .= " AND a.type='$group'";
        } else if ($list == "cnf") {
            if ($class<>"*") switch ($class) {
                case "ans": $sql .= " AND a.choose IS NOT NULL"; break;
                case "Y": $sql .= " AND a.choose='Y'"; $col = array("F", "A", "B", "C", "D", "E"); break;
                case "N": $sql .= " AND a.choose='N'"; break;
                case "una": $sql .= " AND a.choose IS NULL"; $col = array("F", "A", "B", "C", "D", "E"); break;
            } if ($group<>"*") $sql .= " AND a.type=$group";
        }
        if (!empty($sql)) {
            // Process
            # require($dirPWroot."e/resource/db_connect.php");
            $result = $db -> query("$sql $searchQuery $queryEnd");
            $all = $db -> query($sql); 
            # $db -> close();
            // Export
            $intlOut = '"intl": {'.
                '"page": { "current": '.$page.', "max": '.strval(max(ceil(($all -> num_rows)/intval($show)), 1)).', "disp": '.$show.' },'.
                '"sort": { "col": "'.$sortBy.'", "order": '.( $sortOrder=="DESC" ? "0" : "1" ).' }'.
            '}';
            if ($result && $result -> num_rows > 0) {
                $send = '{ "success": true, "info": { ';
                // send thead
                $send .= '"column": [';
                foreach ($col as $ec) $send .= '{ "name": "'.$colcode[$ec][2].'", "ref": "'.$ec.'", "sortable": '.($colcode[$ec][1] ? "true" : "false" ).' },';
                $send = rtrim($send, ","); $send .= '],';
                // send tbody
                $send .= '"users": [';
                while ($eu = $result -> fetch_assoc()) {
                    $send .= '{ ';
                    foreach ($col as $ec) {
                        if ($list == "new" && $ec == "Z") $send .= '"'.$ec.'": { "val": "'.$CV_groupAdmShort[intval($eu[$colcode[$ec][0]])-1].'" },';
                        else if ($ec == "X") {
                            if (!empty($eu[$colcode[$ec][0]])) $send .= '"'.$ec.'": { "val": "ดูไฟล์หลักฐาน", "link": "/e/enroll/report/response/file?of='.$eu[$colcode['A'][0]].'&type='.$dirname.'" },';
                            else $send .= '"'.$ec.'": { "val": "" },';
                        } else $send .= '"'.$ec.'": { "val": "'.$eu[$colcode[$ec][0]].'" },';
                    } $send = rtrim($send, ","); $send .= ' },';
                } $send = rtrim($send, ","); $send .= ']';
                // Send out
                echo "$send }, $intlOut }";
            } else echo '{ "success": true, "info": { "users": [] }, '.$intlOut.' }';
        } else echo '{ "success": false }';
        $db -> close();
    } else echo '{ "success": false }';


	// Fetch results
	/* echo '{
		"success": true,
		"info": {
			"column": [
				{"name": "colA", "ref": "A", "sortable": true },
				{"name": "colB", "ref": "B", "sortable": true },
				{"name": "colC", "ref": "C", "sortable": true }
			], "users": [
				{
					"A": {"val": "1a", "link": ""},
					"B": {"val": "1b", "link": ""},
					"C": {"val": "1c", "link": ""}
				}, {
					"A": {"val": "2a", "link": ""},
					"B": {"val": "2b", "link": ""},
					"C": {"val": "2c", "link": ""}
				}, {
					"A": {"val": "3a", "link": ""},
					"B": {"val": "3b", "link": ""},
					"C": {"val": "3c", "link": ""}
				}
			]
		}, "intl": {
			"page": { "current": 1, "max": 1, "disp": 20 },
			"sort": { "col": "A", "order": 0 }
		}
	}'; */
?>