<?php

/**
 * Edit project file for 2rip
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
$dashboard = array("projects/" => "Projects", "" => "Edit project");

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
                  array($argument[0],
                        $_SESSION['userID']));
    if ($dbCls->rows() > 0) {
        $project = $dbCls->fetch(true);
        
        if ($project['status'] != 1) {
            if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['name'], $_POST['url'])) {
                // check for duplicate name/url
                if (strlen($_POST['name']) < 2) {
                    $_SESSION['error'] .= 'Your project name must be atleast 2 chars<br />';
                } elseif ($dbCls->counter('project', 'id', '`name` = ? AND `id` != ? AND `user_id` = ?', array($_POST['name'], $project['id'], $_SESSION['userID']))) {
                    $_SESSION['error'] .= 'Your project name already exists, please try another<br />';
                }
                
                if (strlen(trim($_POST['url'])) < 7 OR
                    !filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
                    $_SESSION['error'] .= 'Please enter a correct website URL<br />';
                } elseif ($dbCls->counter('project', 'id', '`url` = ? AND `id` != ? AND `user_id` = ?', array($_POST['url'], $project['id'], $_SESSION['userID']))) {
                    $_SESSION['error'] .= 'Your project URL already exists, please try another<br />';
                }
                
                if ($_SESSION['error'] == '') {
                    // update project only when not running
                    $dbCls->query("UPDATE project
                                   SET `name` = ?,
                                       `url`  = ?
                                   WHERE `id` = ?
                                   LIMIT 1",
                                  array($_POST['name'],
                                        $_POST['url'],
                                        $project['id']));
                    $affected = $dbCls->affected();

                    // check for options
                    if (isset($_POST['options']) && is_array($_POST['options'])) {
                        foreach ($_POST['options'] AS $field => $value) {
                            if (strlen(trim($value)) > 0) {
                                $dbCls->query("INSERT INTO `project_stats` (`project_id`, `type`, `status`, `value`)
                                               VALUES (?,'c',?,?) ON DUPLICATE KEY UPDATE `value` = ?",
                                              array($project['id'], $field, $value, $value));
                            }
                        }
                    }
                        
                                        
                    if ($affected > 0) {
                        // update first URL in the spider index!
                        $dbCls->query("UPDATE `spider`
                                       SET url            = ?,
                                           failed         = 0,
                                           processed      = 0,
                                           failed_msg     = ''
                                       WHERE `project_id` = ?
                                       AND   `ref_id`     = 0
                                       LIMIT 1",
                                      array($_POST['url'], $project['id']));
                        
                        $_SESSION['notice'] = 'Your project has been updated!';
                    
                        header("Location: " . $configCls->get("application/site_url") . "projects/open/" . $project['id']);
                        exit;
                    }
                }
            }
            
            $_POST['name'] = $project['name'];
            $_POST['url']  = $project['url'];
            
            // get options
            $dbCls->query("SELECT `project_stats`.*
                           FROM `project_stats`
                           WHERE `project_id` = ?",
                          array($project['id']));
            $options = array();
            if ($dbCls->rows() > 0) {
                foreach ($dbCls->fetch() AS $null => $opt) {
                    $options[$opt['status']] = $opt['value'];
                    $_POST['options'][$opt['status']] = $opt['value'];
                }
            }
        } else {
            $_SESSION['error'] = 'Can\'t edit projects that are running';
            header("Location:  " . $configCls->get("application/site_url") . "projects");
            exit;
        }
    } else {
        $_SESSION['error'] = 'Project can\'t be found';
        header("Location:  " . $configCls->get("application/site_url") . "projects");
        exit;
    }
} else {
    header("Location:  " . $configCls->get("application/site_url") . "projects");
    exit;
}