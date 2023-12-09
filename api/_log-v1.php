<?php
	$APP_DB[5] = connect_to_database(5);
	if (!function_exists("syslog_e")) { function syslog_e($iExor="", $iApp="", $iCmd="", $iAct="", $iData="", $iVal="", $iAttr="", $iRef="", $close=false, $forced=false) {
		global $APP_DB, $USER_IP;
		// Clean data
		if ($iExor == "" || $iExor == null) $dExor = strval($_SESSION["evt"]["user"] ?? ($_SESSION["evt2"]["user"] ?? ""));
		else { $dExor = trim(strval($iExor)); try { $dExor = $APP_DB[5] -> real_escape_string($dExor); } catch(Exception$e){} }
		$dApp = trim(strval($iApp)); try { $dApp = $APP_DB[5] -> real_escape_string($dApp); } catch(Exception$e){}
		$dCmd = trim(strval($iCmd)); try { $dCmd = $APP_DB[5] -> real_escape_string($dCmd); } catch(Exception$e){}
		$dAct = trim(strval($iAct)); try { $dAct = $APP_DB[5] -> real_escape_string($dAct); } catch(Exception$e){}
		$dData = trim(strval($iData)); try { $dData = $APP_DB[5] -> real_escape_string($dData); } catch(Exception$e){}
		$dVal = trim(strval($iVal)); try { $dVal = $APP_DB[5] -> real_escape_string($dVal); } catch(Exception$e){}
		$dAttr = trim(strval($iAttr)); try { $dAttr = $APP_DB[5] -> real_escape_string($dAttr); } catch(Exception$e){}
		$dRef = trim(strval($iRef)); try { $dRef = $APP_DB[5] -> real_escape_string($dRef); } catch(Exception$e){}
		// Filter user
		if (!$forced) {
			if ($dApp == "PathwaySCon" && in_array($dExor, array("0", "10000"))) return false;
			if ($dApp == "admission" && in_array($dExor, array("99999", "test02"))) return false;
		} // Record em
		$success = $APP_DB[5] -> query("INSERT INTO all_log_action (exor,app,cmd,act,data,val,attr,ref,ip) VALUES ('$dExor','$dApp','$dCmd','$dAct','$dData','$dVal','$dAttr','$dRef','$USER_IP')");
		// Close connection
		if ($close && isset($APP_DB[5])) $APP_DB[5] -> close();
		// Returns status (bool)
		return $success;
	} }
?>