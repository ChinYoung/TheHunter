<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

class user
{
    public $db;
    private $salt       = 'abc1234';
    private $cookieName = 'abc12345667890';
    private $domain     = 'http://www.domain.com';
    
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getUserIdBy($type, $value)
    {
        /*
            read userID from type/value.
        */

        $query = $this->db->query("SELECT `users`.`id`
                                   FROM `users`
                                   WHERE `users`.`" . $type . "` = ?
                                   LIMIT 1",
                                  array($value));

        $items = $this->db->rows();
        if ($items == 1)
        {
            $list = $this->db->fetch(true);

            return $list['id'];
        }

        return false;
    }
    
    public function getUserByUsername($username)
    {
        $query = $this->db->query("SELECT `users`.`id`
                                   FROM `users`
                                   WHERE `users`.`username` = ?
                                   LIMIT 1",
                                  array($username));

        $items = $this->db->rows();
        if ($items == 1)
        {
            $list = $this->db->fetch(true);

            return $list['id'];
        }

        return false;
    }
    
    public function checkExists($type, $value)
    {
        $query = $this->db->query("SELECT count(`users`.`id`)
                                   FROM `users`
                                   WHERE `" . $this->db->escape($type) . "` = '" . $this->db->escape($value) . "'
                                   LIMIT 1");

        if ($this->db->result() == false)
        {
            return false;
        }
        return true;
    }

    public function register($username, $email)
    {
        /*
         * Insert user into database.
         */

        $rnd = substr(time() . rand(0,99999), 0, 12);

        $this->db->query("INSERT INTO `users` (`createdate`, `username`, `email`, `validation`)
                          VALUES (NOW(),?,?,?",
                         array($username,
                               $email,
                               $rnd));

        if ($this->db->insert_id())
        {
            return array("id"         => $this->db->insert_id(),
                         "validation" => $rnd);
        }

        return false;
    }

    public function requestNewPasswordCode($email)
    {
        $id = $this->getUserIdBy('email', 
                                 $email);
        
        // return salt.
        return MD5("vis" . $id . $email . "freaks" . MD5($id . $email)  . "requestpassword" . date("Y-m-d"));
    }
    
    function validate($id, $validationCode)
    {
        $this->db->query("SELECT id,
                                 validation
                          FROM `users`
                          WHERE `id` = ?
                          LIMIT 1",
                         array($id));
        if ($this->db->rows() == 1)
        {
            $validateUser = $this->db->fetch(true);
            
            if ($validateUser['validation'] == $validationCode)
            {
                $this->db->query("UPDATE `users`
                                  SET `validation` = ''
                                  WHERE id = ?
                                  LIMIT 1",
                                 array($validateUser['id']));
                                  
                return 1;
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return -1;
        }
    }
}
?>