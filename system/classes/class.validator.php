<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

class validator
{
    public $arr    = array();
    public $errors = array();
     
    // de functie
    public function __construct($arr = array(), $checks = array(), $err = false)
    {
        // is $array false?
        if (!is_array($arr) && 
            count($arr) == 0)
        {
            return;
        }
        // if $cecks false?
        elseif (!is_array($checks) && 
                count($checks) == 0)
        {
            return;
        }

        // loop checks
        foreach ($checks AS $naam => $todo)
        {
            // set fout as false
            $fout = false;

            // is the $naam found in the $arr
            if (!isset($arr[$naam]))
            {
                if (!isset($todo['min']) OR 
                    $todo['min'] > 0)
                {
                    // min not set at 0
                    $fout = true;
                }
            }
            else
            {
                // select type validate
                switch ($todo['type'])
                {
                    // validate with regx
                    case "regex":
                    
                        if (isset($todo['regex']))
                        {
                            if (!preg_match("/" . $todo['regex'] . "/is", 
                                $arr[$naam]))
                            {
                                $fout = true;
                            }
                        }
                        else
                        {
                            $fout = true;
                        }
                        
                    break;
                    
                    // validate text size
                    case "text":

                        if (isset($todo['min']) && 
                            strlen($arr[$naam]) < $todo['min'])
                        {
                            $fout = true;
                            $todo['error'] .= " min " . $todo['min'] . " and max " . $todo['max'] . " chars";
                        }
                        elseif (strlen($arr[$naam]) == 0)
                        {
                            $fout = true;
                        }
                        elseif (isset($todo['max']) && 
                                strlen($arr[$naam]) > $todo['max'])
                        {
                            $fout = true;
                            $todo['error'] .= " min " . $todo['min'] . " and max " . $todo['max'] . " chars";
                        }

                    break;

                    // validate number
                    case "numeric":

                        if (is_numeric($arr[$naam]))
                        {
                            if (isset($todo['min']) && 
                                strlen($arr[$naam]) < $todo['min'])
                            {
                                $fout = true;
                            }
                            if (isset($todo['max']) && 
                                strlen($arr[$naam]) > $todo['max'])
                            {
                                $fout = true;
                            }
                        }
                        else
                        {
                            $fout = true;
                        }

                    break;
                    
                    // validate IP address
                    case "ip":

                        if (!filter_var($arr[$naam], 
                                        FILTER_VALIDATE_IP))
                        {
                            $fout = true;
                        }

                    break;

                    // validate email address
                    case "email":

                        if (!stristr($arr[$naam], "@") OR
                            !filter_var($arr[$naam], 
                                        FILTER_VALIDATE_EMAIL))
                        {
                            if (isset($todo['max']) && 
                                $todo['max'] != 0)
                            {
                                // required
                                $fout = true;
                            }
                        }

                    break;

                    // validate link
                    case "link":

                        if (!preg_match('/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\//i', 
                            $arr[$naam], 
                            $m))
                        {
                            if (isset($todo['max']) && 
                                $todo['max'] != 0)
                            {
                                // required
                                $fout = true;
                            }
                        }

                    break;
                }
            }

            if ($fout != false OR strlen($fout) != 0)
            {
                $inh = "";
                if (isset($arr[$naam]))
                {
                    $inh = $arr[$naam];
                }

                $this->errors[$naam] = array("inhoud" => $inh ,
                                             "error"  => $todo['error']);
            }
        }
    }

    public function setError($name, $content, $error)
    {
        $this->errors[$name] = array("inhoud" => $content ,
                                     "error"  => $error);
    }

    public function errors($ret = false)
    {
        if ($ret == false)
        {
            if (count($this->errors) == 0)
            {
                return false;
            }
            else
            {
                return true;
            }
        }

        return $this->errors;
    }

    public function parseErrors()
    {
        $str = '';

        if (isset($this->errors) && 
            is_array($this->errors))
        {
            foreach ($this->errors AS $key => $values)
            {
                $str .= " - " . $values['error'] . "<br />";
            }
        }

        return $str;
    }
}

?>