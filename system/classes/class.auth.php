<?php

/* (c) 2013 by NocRoom.com, read license, do not alter, change or share anything below this line! */

class auth
{
    private $db         = false;
    private $salt       = 'abc1234';
    private $cookieName = 'abc12345667890';
    private $domain     = 'http://www.domain.com';
    
    function __construct($db, $salt)
    {
        $this->db = $db;
        $this->salt = $salt;
    }
    
    function auth($db, $salt)
    {
        $this->__construct($db, $salt);
    }
    
    public function login($email = '', $password = '', $stayloggedin = false, $sha = false, $admin = false)
    {
        $this->db->query("SELECT `users`.`id`,
                                 `users`.`username`,
                                 `users`.`email`,
                                 `users`.`status`,
                                 `users`.`password`,
                                 `users`.`validation`,
                                 `users`.`createdate`,
                                 `users`.`date_lastlogin`,
                                 `users`.`banned`
                          FROM `users`
                          WHERE `users`.`email` = ?
                          LIMIT 1",
                        array($email));

        if ($this->db->rows() == 1)
        {
            // username is valid
            $uList = $this->db->fetch(true);

            if (crypt($password, $this->salt) == $uList['password'])
            {
                if (strlen(trim($uList['validation'])) == 0 OR $admin == true)
                {
                    if ($uList['banned'] == 0 OR $admin == true)
                    {
                        $_SESSION['userID'] = $uList['id'];

                        $_SESSION['auth'] = array("id"        => $uList['id'],
                                                  "username"  => $uList['username'],
                                                  "lastlogin" => $uList['date_lastlogin'],
                                                  "email"     => $uList['email'],
                                                  "status"    => $uList['status'],
                                                  "ip"        => $_SERVER['REMOTE_ADDR'],
                                                  "browser"   => $_SERVER['HTTP_USER_AGENT']);

                        if ($stayloggedin == true)
                        {
                            $cookie = base64_encode(serialize(array("uId"      => $uList['id'],
                                                                    "uName"    => $uList['username'],
                                                                    "uPass"    => $uList['username'],
                                                                    "uBrowser" => SHA1($_SERVER['HTTP_USER_AGENT'] . $this->salt . $_SERVER['SERVER_NAME'])
                                                                    )
                                                              )
                                                    );

                            setcookie($this->cookieName,
                                      $cookie,
                                      time()+(60*60*24*265),
                                      '/',
                                      $this->domain);
                        }
                        
                        return 1;
                    }   
                    else
                    {
                        return 0;
                    }
                }
                else
                {
                    return -2;
                }
            }
        }

        return -1;
    }
    
    public function isLoggedin()
    {
        if (isset($_SESSION['userID']) && 
            $_SESSION['userID'] > 0 && 
            isset($_SESSION['auth']))
        {
            if ($_SERVER['REMOTE_ADDR'] == $_SESSION['auth']['ip'] &&
                $_SERVER['HTTP_USER_AGENT'] == $_SESSION['auth']['browser'])
            {
                $query = $this->db->query("SELECT id
                                           FROM  `users`
                                           WHERE `users`.`id`     = ?
                                           AND   `users`.`banned` = '1'
                                           LIMIT 1",
                                          array($_SESSION['auth']['id']));
                if ($this->db->rows() == 0)
                {
                    return true;
                }
            }
            
            unset($_SESSION['auth']);
            
            if (isset($_COOKIE[$this->cookieName]))
            {
                setcookie($this->cookieName,
                          '',
                          time()- 100000,
                          '/',
                          $this->domain);
            }
        }
        elseif (isset($_COOKIE[$this->cookieName]))
        {
            // validate cookie
    die('werkt2');
            
            $cookie = unserialize(base64_decode($_COOKIE[$this->cookieName]));
            
            if (SHA1($_SERVER['HTTP_USER_AGENT'] . $salt . $_SERVER['SERVER_NAME']) == $cookie['uBrowser'])
            {
                if (isset($_COOKIE['uName']) && isset($_COOKIE['uPass']))
                {
                    // valid cookie then
                    return $this->login($_COOKIE['uName'],
                                        $_COOKIE['uPass'],
                                        true,
                                        true);
                }
            }
            
            setcookie($this->cookieName,
                      '',
                      time()- 100000,
                      '/',
                      $this->domain);
        }
    }
        
}
