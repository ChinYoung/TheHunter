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
 * @copyright 2016 NocRoom.com
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 */

defined('V5_SITE') or die ('Hack attemt');

header ("Content-Type:text/xml"); 
echo '<' . '?xml version="1.0" ' . '?>' . PHP_EOL;

if (!$authCls->isLoggedin()) {
    exit;
}

$t = '';
if (isset($_GET['time']) && is_numeric($_GET['time'])) {
    $t = ' AND UNIX_TIMESTAMP(`lastupdate`) > ' . $_GET['time'];
}

if (isset($_GET['name']) && $_GET['name'] == "cronjob") {
    $dbCls->query("SELECT `cronjob`.*,
                          UNIX_TIMESTAMP(`cronjob`.`lastupdate`) AS tmStmp
                   FROM `cronjob`
                   WHERE (`cronjob`.`lastupdate` > (NOW() - INTERVAL 15 MINUTE))
                   " . $t . "
                   ORDER BY `cronjob`.`lastupdate` DESC
                   LIMIT 10"); 
    if ($dbCls->rows() > 0) {
        $str = '';
        foreach($dbCls->fetch() AS $status) {
            if (!isset($statusTime)) {
                $statusTime = 1;
                echo '<status time="' . $status['tmStmp'] . '">' . PHP_EOL;
            }
            $str = '  <line time="' . $status['tmStmp'] . '">' . htmlspecialchars($status['status']) . '</line>' . $str;
        }
        echo $str;
        echo '</status>' . PHP_EOL;
    } else {
/*
?>
<status time="<?php echo time();?>">
   <line time="<?php echo time();?>"><?php echo date("Y-m-d H:i:s");?> - No current cronjob data</line>
</status>
<?php
*/
    }
} else {

    $dbCls->query("SELECT `project_stats`.`value`,
                          UNIX_TIMESTAMP(`project_stats`.`lastupdate`) AS tmStmp
                   FROM `project_stats`
                   WHERE `project_stats`.`status` = 'status'
                   AND   `project_stats`.`type`   = 'p'
                   " . $t . "
                   AND (`project_stats`.`lastupdate` > (NOW() - INTERVAL 5 MINUTE))
                   ORDER BY `project_stats`.`lastupdate` DESC
                   LIMIT 10"); 
    if ($dbCls->rows() > 0) {
        $str = '';
        foreach($dbCls->fetch() AS $status) {
            if (!isset($statusTime)) {
                $statusTime = 1;
                echo '<status time="' . $status['tmStmp'] . '">' . PHP_EOL;
            }
            $str = '  <line time="' . $status['tmStmp'] . '">' . htmlspecialchars($status['value']) . '</line>' . $str;
        }
        echo $str;
        echo '</status>' . PHP_EOL;
    } else {
/*
?>
<status time="<?php echo time();?>">
   <line time="<?php echo time();?>"><?php echo date("Y-m-d H:i:s");?> - No information found / projects running</line>
</status>
<?php
*/
    }
}

exit;
?>