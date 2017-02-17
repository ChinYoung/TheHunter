<?php

/**
 * Display project for 2rip
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

$dashboard = array("projects/" => "Projects", "" => "Listing project urls");

$urls_per_page = 50;
$startPosition = 0;

if (!$authCls->isLoggedin()) {
    header("Location: " . $configCls->get("application/site_url") . "login");
    exit;
}

if (isset($argument[0]) && is_numeric($argument[0])) {
    $dbCls->query("SELECT `project`.*
                   FROM `project`
                   WHERE `id`      = ?
                   AND   `user_id` = ?",
                  array($argument[0], $_SESSION['userID']));
    
    if ($dbCls->rows() > 0) {
        $project = $dbCls->fetch(true);
    
        $where = ' ';
        
        if (isset($argument[1], $argument[2]) && $argument[1] == "search") {
            switch ($argument[2]) {
                case "failed":
                    $where = ' AND `spider`.`failed` = 1';
                break;
                
                case "queue":
                    $where = ' AND `spider`.`processed` = 0';
                break;
                
                default:
                    $where = ' AND `spider`.`processed` = 1';
                break;
            }
        }
        
        $totalRows = $dbCls->counter('spider', 'id', ' project_id = ' . $argument[0] . $where);
        $totalPages = ceil($totalRows / $urls_per_page);
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $startPosition = ((($_GET['page'] -1) * $urls_per_page) < $totalRows) ? (($_GET['page'] -1) * $urls_per_page) : 0;
        }
    } else {
        header("Location: " . $configCls->get("application/site_url"));
        exit;
    }
}