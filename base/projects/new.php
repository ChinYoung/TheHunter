<?php

/**
 * Create new project for 2rip
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
$dashboard = array("projects/" => "Projects", "" => "Create new project");

if (!$authCls->isLoggedin()) {
    header("Location: " . $configCls->get("application/site_url") . "login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['name'], $_POST['url'])) {
    // check for duplicate name/url
    if (strlen($_POST['name']) < 2) {
        $_SESSION['error'] .= 'Your project name must be atleast 2 chars<br />';
    } elseif ($dbCls->counter('project', 'id', '`name` = ? AND `user_id` = ?', array($_POST['name'], $_SESSION['userID']))) {
        $_SESSION['error'] .= 'Your project name already exists, please try another<br />';
    }
    
    if (strlen(trim($_POST['url'])) < 7 OR
        !filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
        $_SESSION['error'] .= 'Please enter a correct website URL<br />';
    } elseif ($dbCls->counter('project', 'id', '`url` = ? AND `user_id` = ?', array($_POST['url'], $_SESSION['userID']))) {
        $_SESSION['error'] .= 'Your project URL already exists, please try another<br />';
    }
    
    if ($_SESSION['error'] == '') {
        $dbCls->query("INSERT INTO `project` (`name`, `url`, `user_id`)
                       VALUES (?,?,?)",
                      array($_POST['name'],
                            $_POST['url'],
                            $_SESSION['userID']));
        
        if ($dbCls->affected() > 0) {
            $projectId = $dbCls->insert_id();
            
            // insert URL as first URL
            $dbCls->query("INSERT INTO `spider` (`project_id`, `url`)
                           VALUES (?,?) ON DUPLICATE KEY UPDATE `id` = LAST_INSERT_ID(`id`)",
                          array($projectId, $_POST['url']));
                          
            // check for options
            if (isset($_POST['options']) && is_array($_POST['options'])) {
                foreach ($_POST['options'] AS $field => $value) {
                    if (strlen(trim($value)) > 0) {
                        $dbCls->query("INSERT INTO `project_stats` (`project_id`, `type`, `status`, `value`)
                                       VALUES (?,'c',?,?) ON DUPLICATE KEY UPDATE `value` = ?",
                                      array($projectId, $field, $value, $value));
                    }
                }
            }
            
            $_SESSION['notice'] = 'Your new project has been created!';
        
            header("Location: " . $configCls->get("application/site_url") . "projects/open/" . $projectId);
            exit;
        }
    }
}