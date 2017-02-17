<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Index file for MVC
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

session_start();
session_regenerate_id();

/* extra for current project */

define("MAX_ACTIVE_PROJECTS", 15);

// set defines
define('ROOT_PATH',   str_replace(CHR(92), "/", dirname(__FILE__)) . "/");
define('BASE_PATH',   str_replace(CHR(92), "/", dirname(__FILE__)) . "/base/");
define('CACHE_PATH',  str_replace(CHR(92), "/", dirname(__FILE__)) . "/cache/");
define('SYSTEM_PATH', str_replace(CHR(92), "/", dirname(__FILE__)) . "/system/");
define('V5_SITE',     true);
define('DEBUG',       false);

// add for arrays
require SYSTEM_PATH . 'arrays.php';

// session stuff
$_SESSION['error']  = isset($_SESSION['error'])  ? $_SESSION['error'] : '';
$_SESSION['status'] = isset($_SESSION['status']) ? $_SESSION['status'] : '';
$_SESSION['notice'] = isset($_SESSION['notice']) ? $_SESSION['notice'] : '';

/**
   * Get current directory name
   *
   * @param    string in in
   * @return   void
   *
   */

function getCurrentDirName($in) 
{
    $p = pathinfo($in);
    $s = strlen(BASE_PATH);
    
    return substr($p['dirname'], $s, strlen($in) - $s);
}

/**
   * Automatic load class 
   *
   * @param    string class_name $class_name
   * @return   void
   *
   */

function __autoload($class_name) 
{
    include_once SYSTEM_PATH . "classes/class." . str_replace("-", "/", $class_name) . ".php";
}

// load configuration
$configCls = new config(SYSTEM_PATH . "config.ini");

error_reporting($configCls->get("application/error_reporting"));
set_time_limit($configCls->get("application/time_limit"));

// set timezone
if ($configCls->get("application/timezone") !== false) {  
    date_default_timezone_set($configCls->get("application/timezone"));
}

// load function scripts
foreach (glob("./system/functions/*.php") AS $inc) {
    include_once $inc;
}


// setup database connection
$dbCls = new pdowrapper($configCls->get("database/hostname"),
                        $configCls->get("database/username"),
                        $configCls->get("database/password"),
                        $configCls->get("database/name"),
                        $configCls->get("database/port"));
                
$argument = array();
if ($dbCls == true) {
    // set utf8
    $dbCls->query("SET NAMES UTF8");
    
    $load = BASE_PATH;

    if (isset($_GET['arg'])) {
        /**
         * kijken of argumenten te omleiden zjn naar
         * een pagina of directory (met index.php) in
         * de base van de site
         **/
         
        if (file_exists(BASE_PATH . $_GET['arg'] . ".php")) {
            /**
             *  file on server, if found load
             **/

            $load .= $_GET['arg'] . ".php";
        } elseif (is_dir(BASE_PATH . $_GET['arg']) &&
                file_exists(BASE_PATH . $_GET['arg'] . "/index.php")) {
            /**
             * directory on server, if found load
             **/
            $load .= $_GET['arg'] . "/index.php";
        } else {
            /**
             * if none of above found,
             * upper directory and index.php found? load!
             **/

            $dirs = explode("/",
                            $_GET['arg']);
            $dirs_count = count($dirs);
            
            // loop all directories
            for ($x = $dirs_count; $x > 0; $x--) {
                $curDir = '';

                // set current 'virtual directory'
                for ($y = 0; $y < count($dirs); $y++) {
                    if (strlen($dirs[$y]) > 0) {
                        $curDir .= "/" . $dirs[$y];
                    }
                }

                // remove first slash /
                $curDir = substr($curDir,
                                 1,
                                 strlen($curDir) -1);

                // find out if file exist, if so; use and break
                if (file_exists(BASE_PATH . $curDir . ".php")) {
                    // set to load file
                    $load .= $curDir . ".php";
                    
                    // break loop, file found!
                    break;
                }
                /** if upper directory exists and index.php; use and break **/
                if (is_dir(BASE_PATH . $curDir) &&
                        file_exists(BASE_PATH . $curDir . "/index.php")) {
                    /** set to load file **/
                    $load .= $curDir . "/index.php";

                    /** break loop, file found! **/
                    break;
                }

                // set new argument (before being removed) **/
                $argument[] = $dirs[count($dirs)-1];
                // remove last argument **/
                unset($dirs[count($dirs)-1]);
            }
        }
    }

    /** reverse order from arguments **/
    $cnt = count($argument);
    if ($cnt > 0) {
        $nArray = array();
        for ($x = 0; $x < ($cnt); $x++) {
            $nArray[$x] = $argument[($cnt-1) - $x];
        }

        /** set nArray to $argument array; **/
        $argument = $nArray;
    }

    /** if only basepath is set, load index.php  **/
    if (strlen($load) == strlen(BASE_PATH)) {
        $load .= "index.php";
    }

    /** debug information?  **/
    if (DEBUG == true) { 
		echo "\r\n<!-- component file: " . $load . " -->\r\n"; 
	}

    // load auth function
    $authCls = new auth($dbCls, $configCls->get("application/salt"));
    
    
    // read pathinfo from current file.
    $pathinfoArray = pathinfo($load);

    // set content variable
    $content = '';

    // set template.
    $tpl = str_replace(".php", "", $pathinfoArray['basename']);
    
    // hide output (start buffering)
    ob_start();
    // load module
    include_once $load;
    // save content to buffer;
    $content = ob_get_contents();
    // clear buffer.
    ob_end_clean();

    // if $tpl is set (can be unset in modules)
    if (isset($tpl) && strlen($tpl) > 0) {
        /** $tpl file exists? */
        if (file_exists($pathinfoArray['dirname'] . "/" . $tpl . ".tpl")) {
            /** start buffering **/
            ob_start();
            /** debug **/
            if (DEBUG == true) { 
				echo "\r\n<!-- template file: " . $pathinfoArray['dirname'] . 
                     "/" . $tpl . ".tpl" . " -->\r\n"; 
			}

            /** load template (parse php) **/
            include_once $pathinfoArray['dirname'] . "/" . $tpl . ".tpl";

            /** read content and replace content string **/
            $content = ob_get_contents();

            /** clean content buffer **/
            ob_end_clean();
        }
    }
    /** load global design **/
    header ('Content-type: text/html; charset=utf-8');
		include_once ROOT_PATH . "layout/layout.tpl";
} else {
    /** display error messages for mysql status **/
    $message = "MySQL Server is down, probeer het op een later moment nogmaals";

    include_once SYSTEM_PATH . "base/error.php";
}

/** report debug information **/
if (DEBUG == true) { 
    echo "\r\n<!-- argument: " . print_r($argument, true) . " -->\r\n";
    echo "\r\n<!-- " . print_r($db->stats(), true) . " -->\r\n";
}

?>