<?php

/**
 * Signout file for 2rip
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

foreach ($_SESSION AS $key => $value) {
    unset($_SESSION[$key]);
}

header("Location: " . $configCls->get("application/site_url") . "index");
exit();

?>
