<?php

/**
 * Installation file for 2rip
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Scripts
 * @package   TinyMVC
 * @author    NocRoom https://github.com/NocRoom/TheHunter
 * @copyright 2016 NocRoom.com
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 */

defined('V5_SITE') or die ('Hack attemt');

if ($authCls->isLoggedIn()) {
    header("Location: " . $configCls->get("application/site_url"));
    exit;
}

if (!defined('PHP_MAJOR_VERSION') OR PHP_MAJOR_VERSION < 5) {  
    $_SESSION['error'] .= 'You need atleast PHP 5 to run this script<br />';
}
if (!defined('PDO::ATTR_DRIVER_NAME')) {
    $_SESSION['error'] .= 'You need to have PDO compiled with PHP to run this script<br />';
}
if (!function_exists('curl_version')) {
    $_SESSION['error'] .= 'You need to install the CURL extension for PHP<br />';
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $validateCls = new validator($_POST,
                                 array("email"    => array("type"  => "email",
                                                           "error" => "Email address is incorrect"),
                                       "password" => array("type"  => "text",
                                                           "min"   => 5,
                                                           "max"   => 20,
                                                           "error" => "Forgotten your password?"),
                                       "password1"=> array("type"  => "text",
                                                           "min"   => 5,
                                                           "max"   => 20,
                                                           "error" => "Forgotten your password repeat?"),
                                       )
                                 );
                           
    if ($validateCls->errors() == false) {
        if ($_POST['password'] == $_POST['password1']) {
            // insert email and such in database, and goto login
            $dbCls->query("INSERT INTO `users` (`id`, `status`, `banned`, `validation`, `username`, `firstname`, `lastname`, `password`, `email`, `createdate`, `date_lastlogin`, `ip_lastlogin`, `ip_registration`) 
                           VALUES (NULL, '5', '0', '', 
                                   ?, 
                                   '2rip', '2rip', 
                                   ?, 
                                   ?, 
                                   NOW(), '', '', '');",
                          array($_POST['email'],
                                crypt($_POST['password'], $configCls->get("application/salt")),
                                $_POST['email']));
            header("Location: " . $configCls->get("application/site_url") . "login");
            exit;
        }
        else
        {
            $_SESSION['error'] = 'Your passwords are not the same';
        }
    }

    if ($validateCls->errors() > 0) {
        $_SESSION['error'] = $validateCls->parseErrors();
    }
}

$dbCls->query("SELECT * FROM users");
if ($dbCls->rows() == 1) {
    // user exists, this script cannot be used!
    header("Location: " . $configCls->get("application/site_url"));
    exit;
}