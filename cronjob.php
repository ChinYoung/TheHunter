<?php

/**
 * cronjob file for 2rip
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
 
class crawlerDb
{
    public $dbCls;
    public $timeCls;
    
    public $settings;
    private $settingsTimer = -1;
    
    public $projects = array();
    public $project = array();
    
    function __construct($db, $time)
    {
        $this->dbCls         = $db;
        $this->timeCls       = $time;
        $this->settingsTimer = 0;
        
        $this->settings = array("run_debug"         => 1,
                                "run_active"        => 1,
                                "run_time"          => 1680, /* 1680 = 28 minutes */
                                "run_projects"      => 5,
                                "run_failed_max"    => 5,
                                "curl_timeout"      => 10,
                                "curl_pauze"        => 100000,
                                "run_pauze_timeout" => 3000000,
                                "run_pauze"         => 250000); 

        $this->getSettings(0);
    }
    
    function getSettings($debug = 1)
    {
        /* load cronjob main settings */
        $this->timeCls->__time('getsettings', 0);
        $this->dbCls->query("SELECT *, UNIX_TIMESTAMP(`project_stats`.`lastupdate`) AS tmStmp FROM `project_stats` WHERE project_id  = 0 AND   `type`      = 'c' HAVING tmStmp     > ?", array($this->settingsTimer));
        $this->timeCls->__time('getsettings', 1);
		
        if ($this->dbCls->rows() > 0) {
            foreach ($this->dbCls->fetch() AS $settings) {
                if (!isset($this->settings[$settings['status']]) OR $this->settings[$settings['status']] != $settings['value']) {
                    if ($debug == 1) {
                        $this->status('setting changed "' . $settings['status'] . '" with setting "' . $settings['value'] . '"', 1);
                    }
                    $this->settings[$settings['status']] = $settings['value'];
                }
                
                if ($this->settingsTimer < $settings['tmStmp']) {
                    // set last update time to timer..
                    $this->settingsTimer = $settings['tmStmp'];
                }
            }
            unset($settings);
        }    
    }
    
    function projectDailyStats($projectID, $key, $value = 1)
    {
        $this->timeCls->__time('projectdailystats', 0);
        $this->dbCls->query("INSERT INTO `project_daily_stats` (`project_id`, `date`, `key`, `value`) VALUES (?, CURDATE(), ?, ?)  ON DUPLICATE KEY UPDATE `value` = `value` + ?", array($projectID, $key, $value, $value));
        $this->timeCls->__time('projectdailystats', 1);
    }
    
    function projectStatus($projectID, $status = 'Status cannot be found?')
    {
        $this->dbCls->query("INSERT INTO `project_stats` (`project_id`, `type`, `status`, `value`) VALUES (?, 'p', ?, ?) ON DUPLICATE KEY UPDATE `value` = ?", array($projectID, 'status', $status, $status));
    }
    
    function projectUpdateStats($projectID, $type, $stats = 0, $field = 'number')
    {
        $this->timeCls->__time('projectupdatestats', 0);
        if ($stats > 0) {
            if ($field == 'number') {
                $this->projectDailyStats($projectID, $type, $stats);
            }
        
            $this->dbCls->query("INSERT INTO `project_stats` (`project_id`, `type`, `status`, `" . $field . "`) VALUES (?, 'p', ?, ?)  ON DUPLICATE KEY UPDATE `" . $field . "` = `" . $field . "` + ?", array($projectID, $type, $stats, $stats));
        }
        $this->timeCls->__time('projectupdatestats', 1);
    }
    
    function status($status)
    {
        $this->timeCls->__time('status', 0);
        if (strlen($status) > 0) {
            $str = date("Y-m-d H:i:s") . ' - ' . $status;
            if ($this->settings['run_debug'] == 1) {
                echo $str . PHP_EOL;
            }
            $this->dbCls->query("INSERT INTO `cronjob` (`status`) VALUES (?)", array($status));
        }
        $this->timeCls->__time('status', 1);
    }
    
    function statusClean()
    {
        $this->timeCls->__time('statusclean', 0);
        $this->dbCls->query("DELETE FROM cronjob WHERE lastupdate < (NOW() - INTERVAL 5 MINUTE)");
        $this->timeCls->__time('statusclean', 1);
    }
    
    function loadProjects()
    {
        $this->projects = array();
        $this->timeCls->__time('loadprojects', 0);
        $this->dbCls->query("SELECT `project`.`name` AS projectName, `project`.`url` AS projectURL, `project`.`id` AS projectID, `project_stats`.`lastupdate`  AS projectLastupdate FROM  `project`  LEFT JOIN  `project_stats` ON  `project_stats`.`project_id` =  `project`.`id`  AND  `project_stats`.`status` =  'queuetimer' WHERE  `project`.`status` = 1 ORDER BY  `project_stats`.`lastupdate`  LIMIT " . $this->settings['run_projects']);
                             
        if ($this->dbCls->rows() > 0) {
            foreach ($this->dbCls->fetch() AS $project) {
                $this->projects[$project['projectID']] = $project;
            }
        }
        $this->timeCls->__time('loadprojects', 1);
    }
    
    function loadProjectSettings($projectID)
    {
        // get settings
        $this->timeCls->__time('loadprojectsettings', 0);
        $this->dbCls->query("SELECT `project_stats`.* FROM `project_stats` WHERE `project_id` = ? AND   `type` = 'c'", array($projectID));
        $this->timeCls->__time('loadprojectsettings', 1);
        if ($this->dbCls->rows() > 0) {
            foreach ($this->dbCls->fetch() AS $null => $opt) {
                $this->projects[$projectID][($opt['type'] == "c") ? 'config': 'stats'][$opt['status']] = array('number' => $opt['number'], 'value'  => $opt['value']);
            }
        }
    }
    
    function loadProjectUrl($projectID)
    {
        $this->timeCls->__time('loadprojecturl', 0);
        $this->dbCls->query("SELECT `spider`.`id`     AS spiderID, `spider`.`url`    AS spiderURL, `spider`.`failed` AS spiderFailed FROM `spider`  WHERE `spider`.`project_id` = ? AND   `spider`.`processed` = 0 LIMIT 1", array($projectID));
        $this->timeCls->__time('loadprojecturl', 1);
        
        if ($this->dbCls->rows() > 0) {
            // add found URL to it!
            $this->projects[$projectID] = array_merge($this->projects[$projectID], $this->dbCls->fetch(true));
            $this->projects[$projectID]['spiderURL'] = trim(str_replace('amp;', '', $this->projects[$projectID]['spiderURL']));
			
            foreach (array("ftp", "http", "https") AS $scheme) {
                // make it bad if bad found, no changes
                $this->projects[$projectID]['spiderURL'] = str_replace($scheme . "://", $scheme . ":/", $this->projects[$projectID]['spiderURL']);
                // change bad to good!
                $this->projects[$projectID]['spiderURL'] = str_replace($scheme . ":/", $scheme . "://", $this->projects[$projectID]['spiderURL']);
            }
                                                                  
            return true;
        }
        
        return false;
    }
    
    function endProject($projectID, $endStatus = 'No more URLS to process')
    {
        $this->timeCls->__time('endproject', 0);
        $this->dbCls->query("UPDATE `project` SET    `status` = 2 WHERE  `id`     = ? LIMIT 1", array($projectID));
        $this->timeCls->__time('endproject', 1);
                      
        $this->timeCls->__time('endproject-stats', 0);
        $status = 'Project "' . $this->projects[$projectID]['projectName'] . '" finished: ' . $endStatus;
        
        $this->dbCls->query("INSERT INTO `project_stats` (`project_id`, `type`, `status`, `value`) VALUES (?, 'p', ?, ?) ON DUPLICATE KEY UPDATE `value` = ?", array($projectID, 'status', $status, $status));
        $this->dbCls->query("INSERT INTO `project_stats` (`project_id`, `type`, `status`, `value`) VALUES (?, 'p', ?, UNIX_TIMESTAMP(NOW()))  ON DUPLICATE KEY UPDATE `value` = UNIX_TIMESTAMP(NOW())", array($projectID, 'status_end'));
        $this->timeCls->__time('endproject-stats', 1);
    }
 
    function urlFailed($projectID, $urlID, $curl_errorID, $curl_error, $retry = false)
    {
        $this->timeCls->__time('urlfailed', 0);
        $this->dbCls->query("UPDATE `spider` SET `failed`     = `failed` + 1, `failed_id`  = ?, `failed_msg` = ?, `processed`  = ? WHERE `id`       = ? LIMIT 1", array($curl_errorID, $curl_error, (($retry == false) ? 1 : 0), $urlID));
        $this->timeCls->__time('urlfailed', 1);

        if ($retry == 0) {
            // don't retry so failed!
            $this->timeCls->__time('urlfailed-stats', 0);
            $this->projectUpdateStats($projectID, 'links_failed', 1);
            $this->timeCls->__time('urlfailed-stats', 1);            
        }
        
        $this->projectDailyStats($projectID, 'url_failed', 1);
    }
    
    function urlSucces($projectID, $urlID, $links = 0, $emails = 0)
    {
        $this->timeCls->__time('urlsucces', 0);
        $this->dbCls->query("UPDATE `spider` SET `processed` = 1, `links`     = ?, `emails`    = ? WHERE `id` = ? LIMIT 1", array($links, $emails, $urlID));
        $this->timeCls->__time('urlsucces', 1);
                      
        $this->timeCls->__time('urlsucces-stats', 0);
        $this->projectUpdateStats($projectID, 'links_processed', 1);
        $this->timeCls->__time('urlsucces-stats', 1);
        
        $this->projectDailyStats($projectID, 'url_succes', 1);
    }
}

define('ROOT_PATH',   str_replace(CHR(92), "/", dirname(__FILE__)) . "/");
define('BASE_PATH',   str_replace(CHR(92), "/", dirname(__FILE__)) . "/base/");
define('CACHE_PATH',  str_replace(CHR(92), "/", dirname(__FILE__)) . "/cache/");
define('SYSTEM_PATH', str_replace(CHR(92), "/", dirname(__FILE__)) . "/system/");

// php function for automatic loading classes
function __autoload($class_name)
{
    include_once SYSTEM_PATH . "classes/class." . str_replace("-", "/", $class_name) . ".php";
}

// load configuration
$configCls = new config(SYSTEM_PATH . "config.ini");

error_reporting($configCls->get("cronjob/error_reporting"));
set_time_limit($configCls->get("cronjob/time_limit"));

// set timezone
date_default_timezone_set($configCls->get("application/timezone"));

// add for arrays
require_once SYSTEM_PATH . 'arrays.php';

// load function scripts
foreach (glob("./system/functions/*.php") AS $inc) {
    include_once $inc;
}


if ((isset($_GET['key']) && $_GET['key'] != $configCls->get("cronjob/key") && (!isset($argv, $argv[1]) OR $argv[1] != $configCls->get("cronjob/key")))) {
    die("No access to cronjob");
}

// setup database connection
$dbCls = new pdowrapper($configCls->get("database/hostname"), $configCls->get("database/username"), $configCls->get("database/password"), $configCls->get("database/name"), $configCls->get("database/port"));

$timeCls = new time();

/* start cronjob class */
$cronjob = new crawlerDb($dbCls, $timeCls);
                         
// set time limit for the script to run
set_time_limit($cronjob->settings['run_time'] + ($cronjob->settings['curl_timeout'] * $cronjob->settings['run_projects']));  // = 20 seconds more then needed, but that's better

// start cronjob timer
$cronjobstart = time();

// debug cronjob info.
$strTmp = '';
foreach ($cronjob->settings AS $k=>$v) { 
    $strTmp .= $k . '=' . $v . ', '; 
}
$cronjob->status("Initial settings: " .  $strTmp);

$timeCls->__time('project', 0);
// loop cronjob as long as possible
$cnt = 0;
do {
    // start round.
    $timeCls->__time('rounds', 0);
    $cnt++;
    $cronjob->getSettings();
    
    // check new cronjob settings.
    if ($cronjob->settings['run_active'] == 1) {
        $dbCls->query("START TRANSACTION");

        $cronjob->status('Round #' . $cnt . ' :: load projects (' . $cronjob->settings['run_projects'] . ')');
        $cronjob->loadProjects();
        
        // loop all projects
        foreach ($cronjob->projects AS $projectID => $project) {
            // load project settings
            $cronjob->loadProjectSettings($projectID);
            
            // check for max urs processed 
            if (isset($cronjob->projects[$projectID]['config']['maxurls'], $cronjob->projects[$projectID]['stats']['links_processed']) && is_numeric($cronjob->projects[$projectID]['config']['maxurls']['value']) && $cronjob->projects[$projectID]['stats']['links_processed']['number'] >= $cronjob->projects[$projectID]['config']['maxurls']['value']) {
                // max processed, so end project
                $cronjob->endProject($projectID, 'Maximum of URLS are found, so exiting.');
                
                if (isset($cronjob->projects[$projectID]['config'], $cronjob->projects[$projectID]['config']['emailresult']['value']) && filter_var($cronjob->projects[$projectID]['config']['emailresult']['value'], FILTER_VALIDATE_EMAIL)) {
                    $mailerCls = new mailer();
                    $mailerCls->fromEmail = $configCls->get("application/site_email");
                    $mailerCls->returnTo  = $configCls->get("application/site_email");
                    $mailerCls->mailTemplateFile(ROOT_PATH . "email-cronjob-succes.txt");
                    $mailerCls->mailTemplateFile(ROOT_PATH . "email-cronjob-succes.tpl", 1);
                    $mailerCls->mailerArray(array("projecttitle" => $cronjob->projects[$projectID]['title']));
                    $mailerCls->send("Your " . $cronjob->projects[$projectID]['title'] . " project has ended", $cronjob->projects[$projectID]['config']['emailresult']['value']);
                }                
            } else {
                // try to load URL from list, if none found, end project
                if ($cronjob->loadProjectUrl($projectID) == false) {
                    // end project; remove project from list;
                    $cronjob->status('Round #' . $cnt . ' :: endProject (' . $projectID . ')');
                    $cronjob->endProject($projectID, 'All found URLS are processed.');
                    
                    if (isset($cronjob->projects[$projectID]['config'], $cronjob->projects[$projectID]['config']['emailresult']['value']) && filter_var($cronjob->projects[$projectID]['config']['emailresult']['value'],  FILTER_VALIDATE_EMAIL)) {
                        $mailerCls = new mailer();
                        $mailerCls->fromEmail = $configCls->get("application/site_email");
                        $mailerCls->returnTo  = $configCls->get("application/site_email");
                        $mailerCls->mailTemplateFile(ROOT_PATH . "email-cronjob-succes.txt");
                        $mailerCls->mailTemplateFile(ROOT_PATH . "email-cronjob-succes.tpl", 1);
                        $mailerCls->mailerArray(array("projecttitle" => $cronjob->projects[$projectID]['title']));
                        $mailerCls->send("Your " . $cronjob->projects[$projectID]['title'] . " project has ended", $cronjob->projects[$projectID]['config']['emailresult']['value']);
                    }
                }
            }
        }
        $dbCls->query("COMMIT");
        
        // loop URLS
        if (count($cronjob->projects) > 0) {
            $dbCls->query("START TRANSACTION");

            $timeCls->__time('projectCurl', 0);

            // init curl            
            $master = curl_multi_init();
            $curl_arr = array();

            // add urls to curl multi
            foreach ($cronjob->projects AS $projectID => $project) {
                $ch = curl_init();
                $curl_options = array(CURLOPT_URL               => $project['spiderURL'],
                                      CURLOPT_TIMEOUT           => $cronjob->settings['curl_timeout'],          // in seconds
                                      CURLOPT_TIMEOUT_MS        => $cronjob->settings['curl_timeout'] * 1000,   // in ms
                                      CURLOPT_CONNECTTIMEOUT    => $cronjob->settings['curl_timeout'],          // in seconds
                                      CURLOPT_CONNECTTIMEOUT_MS => $cronjob->settings['curl_timeout'] * 1000,   // in ms
                                      CURLOPT_NOBODY            => false,                                       // return body
                                      CURLOPT_VERBOSE           => false,                                       // Minimize logs
                                      CURLOPT_AUTOREFERER       => true,                                        // ?
                                      CURLOPT_FAILONERROR       => true,                                        // fail connection on error!
                                      CURLOPT_RETURNTRANSFER    => true,                                        // return (get) data
                                      CURLOPT_SSL_VERIFYHOST    => false,                                       // no certificate
                                      CURLOPT_SSL_VERIFYPEER    => false,                                       // no verify!
                                      CURLOPT_POST              => false,                                       // no posting
                                      CURLOPT_FOLLOWLOCATION    => true,                                        // follow redirects
                                      CURLOPT_MAXREDIRS         => 10,                                           // max number of redirects to follow
                                     );
                    
                /* add given referer if any! */
                if (isset($project['config']['curlreferer']) && filter_var($project['config']['curlreferer']['value'], FILTER_VALIDATE_URL)) {
                    $curl_options[CURLOPT_REFERER] = $project['config']['curlreferer']['value'];
                }
                /* add useragent string if any */
                if (isset($project['config']['curluseragentstring'])) {
                    $curl_options[CURLOPT_USERAGENT] = $project['config']['useragentstring']['value'];
                }
                /* check if there is a need to add a cookie */
                if (isset($project['config']['curlcookie'])) {
                    $curl_options[CURLOPT_COOKIE] = $project['config']['curlcookie']['value'];
                }
                /* check for proxy */
                if (isset($project['config']['proxyip']) && inet_pton($project['config']['proxyip']) !== false) {
                    $curl_options[CURLOPT_PROXY] = $project['config']['proxyip'];
                    $curl_options[CURLOPT_PROXYTYPE] = 'HTTP';
                    
                    /* do we need to specify a proxy port? */
                    if (isset($project['config']['proxyport']) && strlen(trim($project['config']['proxyport'])) > 0 && is_numeric($project['config']['proxyport'])) {
                        $curl_options[CURLOPT_PROXYPORT] = $project['config']['proxyport'];
                    }
                    /* username and password required? */
                    if (isset($project['config']['proxyusername']) && strlen(trim($project['config']['proxyusername'])) > 0) {
                        $curl_options[CURLOPT_PROXYUSERPWD] = $project['config']['proxyusername'] . ':' . (isset($project['config']['proxypassword']) ? $project['config']['proxypassword'] : '');
                    }
                }
                
                curl_setopt_array($ch, $curl_options);
                curl_multi_add_handle($master, $ch);
                
                // set handle so we can find back the releated data...
                $handles[(integer)$ch] = $projectID;
            }

            // execute CURL MULTI EXEC
            do { 
                while (($execrun = curl_multi_exec($master, $running)) == CURLM_CALL_MULTI_PERFORM);

                if ($execrun != CURLM_OK)
                    break;

                // a request was just completed -- find out which one
                while ($done = curl_multi_info_read($master)) {
                    // get current handler
                    $projectID = $handles[(int)$done['handle']];
                    
                    // get the output 
                    $document = curl_multi_getcontent($done['handle']);
                    // save settings for later use
                    $cronjob->projects[$projectID]['CURL_HTTP_CODE'] = '';
                    $cronjob->projects[$projectID]['CURL_SIZE']      = strlen($document);
                    $cronjob->projects[$projectID]['CURL_ERRNO']     = curl_errno($done['handle']);
                    $cronjob->projects[$projectID]['CURL_ERROR']     = curl_error($done['handle']);
                    $cronjob->projects[$projectID]['CURL_INFO']      = curl_getinfo($done['handle']);
                    $cronjob->projects[$projectID]['CURL_HTTP_CODE'] = $cronjob->projects[$projectID]['CURL_INFO']['http_code'];
                    
                    // remove the curl handle that just completed
                    curl_multi_remove_handle($master, $done['handle']);

                    // work with the data
                    $retry = false;
                    switch ($cronjob->projects[$projectID]['CURL_HTTP_CODE']) {
                        case 200:
                        
                            $parseLinksCls  = new parselinks();
                            $parseEmailsCls = new parseemails();
                            
                            $cronjob->projects[$projectID]['LNKunique']   = 0;
                            $cronjob->projects[$projectID]['LNKinserted'] = 0;
                            $cronjob->projects[$projectID]['EMunique']    = 0;
                            $cronjob->projects[$projectID]['EMinserted']  = 0;
                            
                            // ignore links starting with
                            if (isset($cronjob->projects[$projectID]['options']['blocklinks']) && strlen(trim(strlen($cronjob->projects[$projectID]['options']['blocklinks']))) > 2) {
                                $parseLinksCls->ignoreParts = array_merge($parseLinksCls->ignoreParts, explode(";", $cronjob->projects[$projectID]['options']['blocklinks']));
                            }
                            
                            $parseLinksCls->baseUrl    = $cronjob->projects[$projectID]['projectURL'];
                            $parseLinksCls->currentUrl = $cronjob->projects[$projectID]['spiderURL'];

                            // parse links and emails from document
                            if (isset($cronjob->projects[$projectID]['options']['blockemails']) && trim(strlen($cronjob->projects[$projectID]['options']['blockemails'])) != '') {
                                $parseEmailsCls->ignoreParts = explode(";", $cronjob->projects[$projectID]['options']['blockemails']);
                            }
                            
                            $parseEmailsCls->validateEmails = (isset($cronjob->projects[$projectID]['config']['validateemail']['value']) && $cronjob->projects[$projectID]['config']['validateemail']['value'] == 1) ? 1 : 0;
                            
                            $timeCls->__time('projectCurlGetLinks', 0);
                            $extractedLinks  = $parseLinksCls->parse($document);                            
                            $timeCls->__time('projectCurlGetLinks', 1);

                            $timeCls->__time('projectCurlGetEmails', 0);
                            $extractedEmails = array();
                            $size = 1000;
                            if (strlen($document) > $size) {
                                $loop = ceil(strlen($document) / $size);
                                $start = 0;
                                for ($x = 0; $x < $loop; $x++) {
                                    $start = substr($document, $x * $size, $size + 500);
                                    
                                    $extractedEmails = array_merge($extractedEmails, $parseEmailsCls->parse($start));
                                }
                            } else {
                                $extractedEmails = $parseEmailsCls->parse($document);
                            }
                            $extractedEmails = array_merge($extractedEmails, $parseEmailsCls->parse($project['spiderURL']));
                            $timeCls->__time('projectCurlGetEmails', 1);
             
                            // process links
                            if (count($extractedLinks) > 0) {
                                foreach ($extractedLinks AS $link => $null) {
                                    $timeCls->__time('projectCurlGetLinksInsert', 0);
                                    // add URL to queue
                                    $dbCls->query("INSERT INTO `spider` (`project_id`, `url`, `ref_id`) VALUES (?, ?, ?)  ON DUPLICATE KEY UPDATE `times` = `times` + 1", array($projectID, $link, $cronjob->projects[$projectID]['spiderID']));
                                    // unique url found? add unique counter
                                    $cronjob->projects[$projectID]['LNKunique']  += ($dbCls->affected() == 1) ? +1 : +0;
                                    $cronjob->projects[$projectID]['LNKinserted']++;                                    
                                    $timeCls->__time('projectCurlGetLinksInsert', 1);
                                }
                                
                                $timeCls->__time('projectCurlGetLinksInsertStats', 0);
                                $cronjob->projectUpdateStats($projectID, 'links_parsed', $cronjob->projects[$projectID]['LNKinserted']);
                                $cronjob->projectUpdateStats($projectID, 'links_unique', $cronjob->projects[$projectID]['LNKunique']);
                                $timeCls->__time('projectCurlGetLinksInsertStats', 1);
                            }

                            // process emails 
                            if (count($extractedEmails) > 0) {
                                foreach ($extractedEmails AS $email => $null) {
                                    $timeCls->__time('projectCurlGetEmailsInsert', 0);
                                    // add URL to queue
                                    $dbCls->query("INSERT INTO `email` (`project_id`, `email`, `processed`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `times` = `times` + 1", array($projectID, $email, $parseEmailsCls->validateEmails));
                                    // unique url found? update unique
                                    $cronjob->projects[$projectID]['EMunique']  += ($dbCls->affected() == 1) ? +1 : +0;
                                    $cronjob->projects[$projectID]['EMinserted']++;
                                    $timeCls->__time('projectCurlGetEmailsInsert', 1);
                                }
                                
                                $timeCls->__time('projectCurlGetEmailsInsertStats', 0);
                                $cronjob->projectUpdateStats($projectID, 'emails_parsed', $cronjob->projects[$projectID]['EMinserted']);
                                $cronjob->projectUpdateStats($projectID, 'emails_unique', $cronjob->projects[$projectID]['EMunique']);
                                $timeCls->__time('projectCurlGetEmailsInsertStats', 1);
                            }
                        
                            // update link in database
                            $cronjob->status('Round #' . $cnt . ' :: urlSucces ' . $cronjob->projects[$projectID]['spiderURL'] . ' [' . $cronjob->projects[$projectID]['LNKinserted'] . ':' . $cronjob->projects[$projectID]['LNKunique'] . '/' . $cronjob->projects[$projectID]['EMinserted'] . ':' . $cronjob->projects[$projectID]['EMunique'] . ']');
                            $cronjob->urlSucces($projectID,  $cronjob->projects[$projectID]['spiderID'],  $cronjob->projects[$projectID]['LNKunique'], $cronjob->projects[$projectID]['EMunique']);                                                
                            $cronjob->projectStatus($projectID, 'URL: ' . $cronjob->projects[$projectID]['spiderURL'] . ' [' . $cronjob->projects[$projectID]['LNKinserted'] . ':' .  $cronjob->projects[$projectID]['LNKunique'] . '/' . $cronjob->projects[$projectID]['EMinserted'] . ':' . $cronjob->projects[$projectID]['EMunique'] . ']');
                        break;

                        case 0:
                            $retry = true;
                        case 408: // Request Timeout
                            $retry = true;
                        case 301:
                            $retry = true;
                        case 302:
                            $retry = true;
                        default:
                            
                            $cronjob->status('Round #' . $cnt . ' :: urlFailed ' . $cronjob->projects[$projectID]['spiderURL'] . ' [' . $cronjob->projects[$projectID]['CURL_HTTP_CODE'] . '/' . $cronjob->projects[$projectID]['CURL_ERRNO'] . '/' . $cronjob->projects[$projectID]['CURL_ERROR'] . ']');
                            
                            // if number of failed is bigger, fail!!
                            if ($retry == true && $cronjob->projects[$projectID]['spiderFailed'] > $cronjob->settings['run_failed_max']) {
                                $retry = false;
                            }
                            
                            $cronjob->urlFailed($projectID, $cronjob->projects[$projectID]['spiderID'], $cronjob->projects[$projectID]['CURL_ERRNO'], $cronjob->projects[$projectID]['CURL_ERROR'], $retry);
                                                
                            $cronjob->projectStatus($projectID, 'URL: ' . $cronjob->projects[$projectID]['spiderURL'] . ' failed (' . (($retry == false) ? 'Not retrying' : 'Retrying on later moment') . ")");

                        break;
                    }
                    
                    $cronjob->status('#' . $projectID . '/' . (isset($cronjob->projects[$projectID]['spiderID']) ? $cronjob->projects[$projectID]['spiderID'] : -1) . ' - (' . $cronjob->projects[$projectID]['CURL_HTTP_CODE'] . '/' . $cronjob->projects[$projectID]['CURL_SIZE'] . ') ' . (isset($cronjob->projects[$projectID]['spiderURL']) ? $cronjob->projects[$projectID]['spiderURL'] : 'No URL') . ' - ' . $cronjob->projects[$projectID]['CURL_ERROR']);                    
                }
            } 
            while ($running);
            
            // update timestamp for projects
            $cronjob->status('Round #' . $cnt . ' :: update projects');
            foreach ($cronjob->projects AS $projectID => $null) {
                $dbCls->query("INSERT INTO `project_stats` (`project_id`, `type`, `status`, `number`) VALUES (?,'p',?,UNIX_TIMESTAMP(NOW())) ON DUPLICATE KEY UPDATE `number` = UNIX_TIMESTAMP(NOW())", array($projectID, 'queuetimer'));
            }
            // commit queries
            $dbCls->query("COMMIT");

            $timeCls->__time('projectCurl', 1);
        }
    } else {
        $cronjob->status('Round #' . $cnt . ' - Pauze');
        
        if (isset($cronjob->settings['run_pauze_timeout'])) {
            usleep($cronjob->settings['run_pauze_timeout']);
        }
    }

    // clean cronjob messages older then 5 minutes
    $cronjob->statusClean();
    
    // sleep for a moment, relax and start process again
    usleep($cronjob->settings['run_pauze']);

    $timeCls->__time('rounds', 1);
    // end round;

    // run max runtime - curl_timout * number of projects
    $pAvg     = (isset($timeCls->timers['rounds']['max']) ? ceil($timeCls->timers['rounds']['max']) : ($cronjob->settings['curl_timeout'] * $cronjob->settings['run_projects']) * 2);
    $timeLeft = $cronjob->settings['run_time'] - (time() - $cronjobstart);
}
while ($timeLeft > $pAvg);
$timeCls->__time('project', 1);

foreach ($timeCls->timers AS $group => $timer) {
    $strTmp = '';
    foreach ($timer AS $field => $value) {
        $strTmp .= $field . "=" . $value . ', ';
    }
    echo $group . " - " . $strTmp . "\r\n";
}
// $cronjob->status('End runned ' . (time() - $cronjobstart) . ' secs');
die("Round exit");