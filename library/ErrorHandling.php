<?php

if (function_exists('apc_exists')) {
    $apc = true;
} else {
    $apc = false;
}

function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars) {
    global $apc;

    $min = 60;
    $hour = $min * 60;
    $day = $hour * 24;

    $dupliErrorCheckPath = realpath(dirname(__FILE__) . '/../data/tmp/error_check');

    if (!is_dir($dupliErrorCheckPath)) {
        mkdir($dupliErrorCheckPath, 0777, true);
        chmod($dupliErrorCheckPath, 0777);
    }

    if (is_dir($dupliErrorCheckPath)) {
        $mydir = opendir($dupliErrorCheckPath);
        while (false !== ($file = readdir($mydir))) {
            if ($file != "." && $file != "..") {
                $fileInfo = stat($dupliErrorCheckPath . '/' . $file);
                $diff = time() - $fileInfo['ctime'];
                if ($diff > $day) {
                    @unlink($dupliErrorCheckPath . '/' . $file);
                }
            }
        }
        closedir($mydir);
    }

    $doErroCheck = true;
    if ($apc) {
        // 86400
        if (apc_exists("last_mail_timestamp")) {
            if (apc_exists("last_mail_timestamp") + 3600 > time()) {
                $doErroCheck = false;
            }
        } else {
            apc_add("last_mail_timestamp", time(), 3600);
            $doErroCheck = true;
        }
    }

    // donot display errors from mpdf library
    if (stripos($filename, 'mpdf') > 0) {
        $doErroCheck = false;
    }
    if ($doErroCheck) {
        // timestamp for the error entry
        if (isset($_SERVER['HTTP_HOST'])) {
            $httphost = $_SERVER['HTTP_HOST'];
        } else {
            $httphost = 'cron';
        }

        $toEmail = array();
        $fromEmail = "error-$httphost@videobychoice.com";
        $toEmail[] = "error@najoomi.com";

        $dt = date("Y-m-d H:i:s (T)");
        $headers = "From: $fromEmail";
        $headers .= "\rReply-To: $fromEmail";
        $headers .= "\rX-Mailer: PHP/" . phpversion();
        // define an assoc array of error string
        // in reality the only entries we should
        // consider are E_WARNING, E_NOTICE, E_USER_ERROR,
        // E_USER_WARNING and E_USER_NOTICE
        $errortype = array(E_ERROR => 'Error', E_WARNING => 'Warning', E_PARSE => 'Parsing Error', E_NOTICE => 'Notice', E_CORE_ERROR => 'Core Error', E_CORE_WARNING => 'Core Warning', E_COMPILE_ERROR => 'Compile Error', E_COMPILE_WARNING => 'Compile Warning', E_USER_ERROR => 'User Error', E_USER_WARNING => 'User Warning', E_USER_NOTICE => 'User Notice', E_STRICT => 'Runtime Notice', E_RECOVERABLE_ERROR => 'Catchable Fatal Error', E_DEPRECATED => 'Deprecated', E_COMPILE_WARNING => 'Complie Warning');
        //$errortype = array(E_ERROR => 'Error', E_WARNING => 'Warning', E_PARSE => 'Parsing Error', E_NOTICE => 'Notice', E_CORE_ERROR => 'Core Error', E_CORE_WARNING => 'Core Warning', E_COMPILE_ERROR => 'Compile Error', E_COMPILE_WARNING => 'Compile Warning', E_USER_ERROR => 'User Error', E_USER_WARNING => 'User Warning', E_USER_NOTICE => 'User Notice', E_STRICT => 'Runtime Notice', E_RECOVERABLE_ERROR => 'Catchable Fatal Error', E_COMPILE_WARNING => 'Complie Warning');
        // set of errors for which a var trace will be saved
        $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_USER_DEPRECATED);
        //$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);

        if (APPLICATION_ENV != 'production' or @$_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
            echo $errortype[$errno] . " $errmsg in $filename at line # $linenum<br>";
        }

        $err = "<errorentry>\n";
        $err .= "\t<datetime>" . $dt . "</datetime>\n";
        $err .= "\t<errornum>" . $errno . "</errornum>\n";
        $err .= "\t<errortype>" . $errortype[$errno] . "</errortype>\n";
        $err .= "\t<errormsg>" . $errmsg . "</errormsg>\n";
        $err .= "\t<scriptname>" . $filename . "</scriptname>\n";
        $err .= "\t<scriptlinenum>" . $linenum . "</scriptlinenum>\n";
        $err .= "\t<url>" . curPageURL() . "</url>\n";
        $err .= "\t<ip>" . getRealIpAddr() . "</ip>\n";
        if (isset($_SERVER['HTTP_REFERER'])) {
            $err .= "\t<referer>" . $_SERVER['HTTP_REFERER'] . "</referer>\n";
        }

        if (in_array($errno, $user_errors)) {
            $err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";
        }
        $err .= "</errorentry>\n\n";


        $fileString = $errmsg . '|' . $filename . '|' . $linenum;
        $errorCheckFileMd5 = md5($fileString);
        $dupliErrorCheckFile = $dupliErrorCheckPath . '/' . $errorCheckFileMd5;
        if (!file_exists($dupliErrorCheckFile) or @$_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
            $asciifile = fopen($dupliErrorCheckFile, "w");
            fwrite($asciifile, $err);
            fclose($asciifile);
            chmod($dupliErrorCheckFile, 0666);
            foreach ($toEmail as $email) {
                @mail($email, strtoupper($errortype[$errno]) . " : " . $errmsg, $err, $headers);
            }
        }
    }
}

$old_error_handler = set_error_handler("userErrorHandler");

function curPageURL() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"])) {
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
    }
    $pageURL .= "://";
    if (isset($_SERVER["SERVER_NAME"])) {
        if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
    } else {
        $pageURL .= "from-cron-job";
    }
    return $pageURL;
}

function getRealIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = "127.0.0.1";
    }
    return $ip;
}

?>
