<?php
    if (!isset($_SESSION)) session_start();
    if (!isset($dirPWroot)) $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
    if (!isset($_SESSION['var']['hash_salt'])) {
        if (!isset($_SESSION['var'])) $_SESSION['var'] = array();
        $hashSalt = rand(1001,9999);
        $_SESSION['var']['hash_salt'] = $hashSalt;
    } else $hashSalt = $_SESSION['var']['hash_salt'];
    function strtorandom($string) {
        for ($_ = 0; $_ < strlen($string); $_++)
            $string[$_] = rand(0,1) ? strtoupper($string[$_]) : strtolower($string[$_]);
        return $string;
    }
    function encryptNID($ID) {
        global $hashSalt;
        return strtorandom(base_convert((intval($ID)+$hashSalt)*$hashSalt, 10, 36));
    }
    function decryptNID($ID) {
        global $hashSalt;
        return base_convert(strtolower($ID), 36, 10)/$hashSalt-$hashSalt;
    }
    function inTimerange($start, $stop) {
        $now = time();
        return (strtotime($start) <= $now && $now <= strtotime($stop));
    }
    function inDaterange($start, $stop) {
        $now = time();
        return (strtotime("$start 00:00:00") <= $now && $now <= strtotime("$stop 23:59:59"));
    }
?>