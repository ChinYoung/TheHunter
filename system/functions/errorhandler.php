<?php

/* (c) 2013 by NocRoom.com, read license, do not alter, change or share anything below this line! */

function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars)
{
    global $configCls;
    
    // timestamp for the error entry
    $dt = date("Y-m-d H:i:s (T)");

    // define an assoc array of error string
    // in reality the only entries we should
    // consider are E_WARNING, E_NOTICE, E_USER_ERROR,
    // E_USER_WARNING and E_USER_NOTICE

    $errortype = array (E_ERROR              => 'Error',
                        E_WARNING            => 'Warning',
                        E_PARSE              => 'Parsing Error',
                        E_NOTICE             => 'Notice',
                        E_CORE_ERROR         => 'Core Error',
                        E_CORE_WARNING       => 'Core Warning',
                        E_COMPILE_ERROR      => 'Compile Error',
                        E_COMPILE_WARNING    => 'Compile Warning',
                        E_USER_ERROR         => 'User Error',
                        E_USER_WARNING       => 'User Warning',
                        E_USER_NOTICE        => 'User Notice',
                        E_STRICT             => 'Runtime Notice',
                        E_RECOVERABLE_ERROR  => 'Catchable Fatal Error',
                        E_DEPRECATED         => 'Ignore');

    // set of errors for which a var trace will be saved
    $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);

    $err = "<errorentry>\n";
    $err .= "\t<strong>time</strong>: "    . $dt . "<br />\n";
    $err .= "\t<strong>num</strong>: "     . $errno . "<br />\n";
    $err .= "\t<strong>type</strong>: "    . $errortype[$errno] . "<br />\n";
    $err .= "\t<strong>msg</strong>: "     . $errmsg . "<br />\n";
    $err .= "\t<strong>script</strong>: "  . $filename . "<br />\n";
    $err .= "\t<strong>linenum</strong>: " . $linenum . "<br />\n";
    $err .= "\t<strong>parameters</strong>:" . print_r($_GET, true) . "<br />\n";

    if (in_array($errno, $user_errors))
    {
        $err .= "\t<strong>vartrace</strong>: " . wddx_serialize_value($vars, "Variables") . "<br />\n";
    }
    $err .= "\n";

    // save to the error log, and e-mail me if there is a critical user error
    error_log($err, 3, ROOT_PATH . "logs/error.log");

    if ($configCls->get("application/site_url") == E_USER_ERROR && $mail != '')
    {
        mail($mail, 
             "Critical User Error", 
             $err);
    }
}

// start error handler!
$old_error_handler = set_error_handler("userErrorHandler");