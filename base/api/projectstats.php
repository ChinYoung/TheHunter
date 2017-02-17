<?php

/**
 * Api file for 2rip
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

header ("Content-Type:text/xml"); 
echo '<' . '?xml version="1.0" ' . '?>' . PHP_EOL;

$a = array('projects'          => 0, 
           'projects_canceled' => 0, 
           'projects_running'  => 0, 
           'links_processed'   => 0, 
           'links_parsed'      => 0, 
           'links_failed'      => 0, 
           'links_unique'      => 0, 
           'emails_unique'     => 0,
           'links_processing'  => 0);

if (!$authCls->isLoggedin()) {
    header("Location: " . $configCls->get("application/site_url") . "index");
    exit();
}

// first get projects from current user
$dbCls->query("SELECT id
               FROM `project`
               WHERE `user_id` = ?",
              array($_SESSION['userID']));
if ($dbCls->rows() > 0) {
    $projects = array();
    foreach ($dbCls->fetch() AS $project) {
        $projects[$project['id']] = true;
    }
    $projectsExplode = implode("','", array_keys($projects));
    
    $a['projects']          = $dbCls->counter('project', 'id', "`user_id` = '" . $_SESSION['userID'] . "'");
    $a['projects_canceled'] = $dbCls->counter('project', 'id',"`status` > 2 AND `user_id` = '" . $_SESSION['userID'] . "'");
    $a['projects_running']  = $dbCls->counter('project', 'id',"`status` = 1 AND `user_id` = '" . $_SESSION['userID'] . "'");
    $a['links_processed']   = getFromDb($dbCls, "SELECT SUM(number) AS res FROM project_stats WHERE status = 'links_processed' AND `project_id` IN ('" . $projectsExplode . "')", 'res');
    $a['links_parsed']      = getFromDb($dbCls, "SELECT SUM(number) AS res FROM project_stats WHERE status = 'links_parsed' AND `project_id` IN ('" . $projectsExplode . "')", 'res');
    $a['links_failed']      = getFromDb($dbCls, "SELECT SUM(number) AS res FROM project_stats WHERE status = 'links_failed' AND `project_id` IN ('" . $projectsExplode . "')", 'res');
    $a['links_unique']      = getFromDb($dbCls, "SELECT SUM(number) AS res FROM project_stats WHERE status = 'links_unique' AND `project_id` IN ('" . $projectsExplode . "')", 'res');
    $a['emails_unique']      = getFromDb($dbCls, "SELECT SUM(number) AS res FROM project_stats WHERE status = 'emails_unique' AND `project_id` IN ('" . $projectsExplode . "')", 'res');

    if ($a['links_unique'] > 0) {
        $a['links_processing']   = $a['links_unique'] - ($a['links_processed'] + $a['links_failed']);
        $p = $a['links_unique'] / 100;
        $a['links_processed'] = number_format($a['links_processed'], 0, '.',',') . " (" . round($a['links_processed'] / $p, 1) . " %)";
        $a['links_failed']    = number_format($a['links_failed'], 0, '.',',') . " (" . round($a['links_failed'] / $p, 1) . " %)";
    } else {
        $a['links_processed'] = $a['links_processed'] . " (0 %)";
        $a['links_failed']    = $a['links_failed'] . " (0 %)";
    }

    foreach (array('projects','projects_canceled','projects_running','links_processing','links_parsed','links_unique', 'emails_unique') AS $k) {
        $a[$k] = number_format($a[$k], 0, '.',',');
    }
}

echo '<stats>' . PHP_EOL;
foreach ($a AS $k=>$v) {
    echo '    <stat id="' . $k . '">' . $v . '</stat>' . PHP_EOL;
}           
echo '</stats>' . PHP_EOL;

exit;