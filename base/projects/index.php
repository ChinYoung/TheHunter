<?php

/**
 * Listing projects file for 2rip
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

defined('V5_SITE') or die ('Hack attempt');
$dashboard = array("projects/" => "Projects", "" => "Listing projects");

if (!$authCls->isLoggedin()) {
    header("Location: " . $configCls->get("application/site_url") . "login");
    exit;
}

if (isset($argument[0], $argument[1], $argument[2]) && $argument[0] == "action" && is_numeric($argument[2])) {
    // first validate if $id is from user
    // second check if process is running.
    $dbCls->query("SELECT *
                   FROM `project`
                   WHERE `id`      = ?
                   AND   `user_id` = ?
                   LIMIT 1",
                  array($argument[2], $_SESSION['userID']));
    if ($dbCls->rows() == 1) {    
        $project = $dbCls->fetch(true);
        
        if ($project['status'] == 1) {
            // running
            switch ($argument[1]) {
                case "stop":
                
                    $dbCls->query("UPDATE project
                                   SET `status` = '3'
                                   WHERE `id` = ?
                                   LIMIT 1",
                                  array($argument[2]));
                           
                    $_SESSION['notice'] = 'Your project "' . $project['name'] . '" has been stopped.';
                           
                break;
            }
        } else {
            switch ($argument[1]) {
                case "start":

                    if ($dbCls->counter('project', 'id', 'status = 1 AND user_id = ' . $_SESSION['userID']) < MAX_ACTIVE_PROJECTS) {
                        $dbCls->query("UPDATE project
                                       SET `status` = '1'
                                       WHERE `id` = ?
                                       LIMIT 1",
                                      array($argument[2]));
                                      
                        $dbCls->query("INSERT INTO `project_stats` (`project_id`, `type`, `status`, `value`)
                               VALUES (?,'p',?,UNIX_TIMESTAMP(NOW())) ON DUPLICATE KEY UPDATE `value` = UNIX_TIMESTAMP(NOW())",
                              array($argument[2], 'status_start'));
                               
                        $_SESSION['notice'] = 'Your project "' . $project['name'] . '" is started';
                    } else {
                        $_SESSION['error'] = 'Your project isn\'t started. You may have maximum of ' . MAX_ACTIVE_PROJECTS . ' projects running. <br />You need to wait to finish your projects or stop one to start another';
                    }

                break;
                
                case "reset":
                
                    $dbCls->query("DELETE FROM `email` 
                                   WHERE `project_id` = ?", 
                                  array($project['id']));
                    $dbCls->query("DELETE FROM `spider` 
                                   WHERE `project_id` = ?
                                   AND   `ref_id` != 0", 
                                  array($project['id']));
                    $dbCls->query("UPDATE `spider`
                                   SET `processed`  = 0,
                                       `failed`     = 0,
                                       `failed_msg` = ''
                                   WHERE `project_id` = ?
                                   LIMIT 1",
                                  array($project['id']));
                    $dbCls->query("DELETE FROM `project_stats` 
                                   WHERE `project_id` = ?
                                   AND   `type` = 'p'", 
                                  array($project['id']));
                    $dbCls->query("UPDATE `project` 
                                   SET status         = 0,
                                       crawled_urls   = 0,
                                       crawled_emails = 0,
                                       crawled_failed = 0
                                   WHERE `id` = ?
                                   LIMIT 1", 
                                  array($project['id']));
                    
                    $_SESSION['notice'] = 'Your project "' . $project['name'] . '" is resetted!';
                    
                    header("Location: " . $configCls->get("application/site_url") . "projects/open/" . $project['id']);
                    exit;
                                        
                break;
                
                case "delete":
                
                    $dbCls->query("DELETE FROM `email` 
                                   WHERE `project_id` = ?", 
                                  array($project['id']));
                    $dbCls->query("DELETE FROM `spider` 
                                   WHERE `project_id` = ?", 
                                  array($project['id']));
                    $dbCls->query("DELETE FROM `project_stats` 
                                   WHERE `project_id` = ?", 
                                  array($project['id']));
                    $dbCls->query("DELETE FROM `project`
                                   WHERE `id` = ?
                                   LIMIT 1", 
                                  array($project['id']));
                           
                    $_SESSION['notice'] = 'Your project "' . $project['name'] . '" is removed completly';
                    
                break;
            }
        }
    }
    
    header("Location: " . $configCls->get("application/site_url") . "projects");
    exit;
}