<?php

/**
 * Cronjob settings file file for 2rip
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
 * @copyright 2013 NocRoom.com
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 */

defined('V5_SITE') or die ('Hack attemt');

$dashboard = array("cronjob/" => "Cronjob", "" => "Cronjob properties");

if (!$authCls->isLoggedin()) {
    header("Location: " . $configCls->get("application/site_url") . "login");
    exit;
} elseif ($_SESSION['auth']['status'] < 5) {
    header("Location: " . $configCls->get("application/site_url"));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    foreach (array('run_active','run_debug', 'run_time','run_failed_max','run_projects','curl_pauze','curl_timeout','run_pauze', 'run_pauze_timeout') AS $k) {
        if (!isset($_POST[$k])) {
            $dbCls->query("DELETE FROM `project_stats` 
                           WHERE `project_id` = 0 
                           AND   `status`     = ? 
                           LIMIT 1",
                          array($k));
        } else {
            $dbCls->query("INSERT INTO `project_stats` (`project_id`, `type`, `status`, `value`)
                           VALUES (0,'c',?,?) ON DUPLICATE KEY UPDATE `value` = ?",
                          array($k, $_POST[$k], $_POST[$k]));
        }
    }
    
    $_SESSION['notice'] = 'Cronjob settings changed';
    
    header("Location: " . $configCls->get("application/site_url") . "cronjob");
    exit;
}

$_POST['run_time']     = 1680;
$_POST['run_active']   = 1;
$_POST['run_projects'] = 5;
$_POST['curl_timeout'] = 2;
$_POST['run_pauze']    = 1000000;

// get options
$dbCls->query("SELECT `project_stats`.*
               FROM `project_stats`
               WHERE `project_id` = 0");
$options = array();
if ($dbCls->rows() > 0) {
    foreach ($dbCls->fetch() AS $null => $opt) {
        $_POST[$opt['status']] = $opt['value'];
    }
}