<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

class parseemails
{
    private $emails = array();
    public $ignoreParts = array(); // only from the end, so all domains must end with extension!!!
    public $validateEmails = false;
    
    function parse($document)
    {
        $this->emails = array();
        
        // try to find email addresses
        $res = preg_match_all("/[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}/i",
                              $document,
                              $matches);
        if ($res)
        {
            // loop all 
            foreach (array_unique($matches[0]) AS $email) 
            {
                // validate by filter them
                if (filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    $ignore = false;
                    if (count($this->ignoreParts) > 0) 
                    {
                        foreach ($this->ignoreParts AS $k=>$v)
                        {
                            if (substr($email, -strlen($v))  == $v OR 
                                substr($email, 0, strlen($v)) == $v)
                            {
                                $ignore = true;
                            }
                        }
                    }
                    
                    if ($ignore == false)
                    {
                        // check for mail DNS
                        if ($this->validateEmails == true)
                        {
                            $domain = explode("@", $email, 2);
                            $domainCheck = checkdnsrr($domain[1]);
                            
                            if ($domainCheck == true)
                            {
                                $this->emails[$email] = true;
                            }
                        }
                        else
                        {
                            $this->emails[$email] = true;
                        }
                    }
                }
            }
        }
        
        return $this->emails;
    }
}