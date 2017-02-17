<?php

/**
 * list email addresses file for 2rip
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
$dashboard = array("projects/" => "Projects", "" => "List project email addresses");

$startPosition = 0;
$emails_per_page = 50;

if (!$authCls->isLoggedin()) {
    header("Location: " . $configCls->get("application/site_url") . "login");
    exit;
}

if (isset($argument[0]) && is_numeric($argument[0])) {
    $dbCls->query("SELECT `project`.*
                   FROM `project`
                   WHERE `id` = ?
                   AND   `user_id` = ?
                   LIMIT 1",
                  array($argument[0], $_SESSION['userID']));

    if ($dbCls->rows() > 0) {
        $project = $dbCls->fetch(true);
        
        // check for upload
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (isset($_FILES['updatefile'], $_POST['action'])) {
                if ($_FILES['updatefile']['size'] > 0) {
                    $list = explode("\r\n", file_get_content($_FILES['updatefile']['tmp_name']));
                    
                    if (count($list) > 0) {
                        $action = isset($_POST['action']['bounced']) ? "b" : "s";
                        foreach ($list AS $null=>$k) {
                            $dbCls->query("UPDATE `email` 
                                           SET status = ?
                                           WHERE `email` = ?
                                           LIMIT 1",
                                          array($action,
                                                $k));
                        }
                        
                        $_SESSION['status'] .= "Updated " . count($list) . " addresses in the database<br />";
                        
                        header("Location: " . $configCls->get("application/site_url") . "projects/emaillist/" . $argument[0]);
                        exit;
                    } else {
                        $_SESSION['error'] .= 'Your uploaded file does not contain and information<br />';
                    }
                } else {
                    $_SESSION['error'] .= 'Your upload was not completed succesfully, try again<br />';
                }
            }
        }
        
        $searchSql = ' AND `status` = \'u\'';
        if (isset($argument[1], $argument[2]) && $argument[1] == "search") {
            switch ($argument[2]) {
                case "bounced":
                    $searchSql = ' AND `status` = \'b\'';
                break;
                
                default:
                // send
                    $searchSql = ' AND `status` = \'s\'';
                break;
            }
        }
        
        $totalRows = $dbCls->counter('email', 'project_id', ' project_id = ' . $argument[0] . $searchSql);
        $totalPages = ceil($totalRows / $emails_per_page);
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $startPosition = ((($_GET['page'] -1) * $emails_per_page) < $totalRows) ? (($_GET['page'] -1) * $emails_per_page) : 0;
        }

        if (isset($argument[1]) && !isset($argument[2])) {
            $dbCls->query("SELECT `email`.*
                           FROM `email`
                           WHERE `project_id` = ? " . 
                           $searchSql,
                          array($project['id']));
                   
            switch ($argument[1]) {
                case "zip":

                    $f_temp = CACHE_PATH . date("Y-m-d").MD5($_SERVER['REMOTE_ADDR'] . date("H:i:s") . $project['id']);
                    $f_good = date("Y-m-d-H-i-s") . "-emaillist-" . $project['id'];
                    
                    $fd = fopen($f_temp . ".txt", "w+");
                    foreach ($dbCls->fetch() AS $list) {
                        fputs($fd, $list['email'] . "\r\n");
                    }
                    fclose($fd);
                    
                    $zip = new ZipArchive();
                    $zip->open($f_temp . '.zip', ZIPARCHIVE::CREATE);
                    $zip->addFile($f_temp . ".txt", $f_good . ".txt");
                    $zip->close();
                    unlink($f_temp . ".txt");

                    header('Pragma: public');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate; post-check=0; pre-check=0');
                    header('Cache-Control: public');
                    header('Content-Description: File Transfer');
                    header('Content-type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . ($f_good . ".zip") . '"');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . filesize($f_temp . '.zip'));
                    
                    readfile($f_temp . '.zip');
                    unlink($f_temp . '.zip');
                    exit();
                        
                break;

                case "csv":
                
                    header('Content-type: text/html');
                    header('Content-disposition: attachment;filename=' . $project['id'] . '.csv');
                    foreach ($dbCls->fetch() AS $list) {
                        echo $list['email'] . "\r\n";
                    }
                    
                break;
                
                case "txt":
                
                    header('Content-type: text/html');
                    header('Content-disposition: attachment;filename=' . $project['id'] . '.txt');
                    foreach ($dbCls->fetch() AS $list) {
                        echo $list['email'] . "\r\n";
                    }
                    
                break;
                
                case "print":
                
                    header("Content-type: text/html");
                    foreach ($dbCls->fetch() AS $list) {
                        echo $list['email'] . '<br />';
                    }
                    echo '<script type="text/javascript">window.print();</script>';
                    
                break;
            }
            exit;
        }
    } else {    
        header("Location: " . $configCls->get("application/site_url") . "projects");
        exit;
    }
} else {    
    header("Location: " . $configCls->get("application/site_url") . "projects");
    exit;
}