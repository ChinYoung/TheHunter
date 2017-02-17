<?php

/* (c) 2013 by NocRoom.com, read license, do not alter, change or share anything below this line! */

class mailer
{
    public $fromName  = 'noreply@e.nocroom.com';
    public $fromEmail = 'noreply@e.nocroom.com';
    public $returnTo  = 'noreply@e.nocroom.com';
    public $parseArr  = array();
    
    public $emailTEXT = '';
    public $emailHTML = '';
    
    public $emailType = false; // 0 = text, 1 = html, 2 = both
    
    public $debug = false;
    
    public function mailerArray($array)
    {
        $this->parseArr = $array;
    }
    
    public function mailTemplateFile($file, $type = 0)
    {
        if (file_exists($file))
        {
            $buf = file_get_contents($file);
            
            if (strlen($buf) > 0)
            {
                switch ($type)
                {
                    case 1:  $this->emailHTML = $buf; break;
                    default: $this->emailTEXT = $buf; break;
                }
            }
            else
            {
                return false;
            }   
        }
        else
        {
            return false;
        }
    }
    
    public function mailTemplateString($string, $type = 0)
    {
        if (strlen($string) > 0)
        {
            switch ($type)
            {
                case 1: $this->emailHTML = $string; break;
                default: $this->emailTEXT = $string; break;
            }
        }
        else
        {
            return false;
        }
    }
    
    public function send($subject, $email)
    {
        $random_hash = md5(date('r', time()));

        $headers = "From: " . $this->fromName . " <" . $this->fromEmail . ">\r\n";
        $headers .= "X-Sender: <" . $this->fromEmail . ">\r\n";
        $headers .= "Return-Path: <" . $this->fromEmail . ">\r\n";
        $headers .= "Error-To: <" . $this->fromEmail . ">\r\n";
        $headers .= "X-Mailer: " . $_SERVER['SERVER_NAME'] . "\r\n";

        if ($this->emailHTML != '')
        {
            // html email?
            $headers .= "Content-Type: multipart/alternative; boundary=\"PHP-alt-" . $random_hash . "\"";
        }

        $text = $this->emailTEXT;
        $html = $this->emailHTML;
        
        if (is_array($this->parseArr) && count($this->parseArr) > 0)
        {
            foreach ($this->parseArr AS $van => $naar)
            {
                if ($text != '')
                {
                    $text = str_replace("%" . $van . "%", $naar, $text);
                }
                if ($html != '')
                {
                    $html = str_replace("%" . $van . "%", $naar, $html);
                }
                $subject = str_replace("%" . $van . "%", $naar, $subject);
            }
        }
        
        if ($this->emailType == false)
        {
            if (strlen($text) > 0 && strlen($html) > 0)
            {
                $this->emailType = 2;
            }
            elseif (strlen($text) > 0)
            {
                $this->emailType = 0;
            }
            elseif (strlen($html) > 0)
            {
                $this->emailType = 1;
            }
            else
            {
                throw new exception("Mail class haven't found any text to send (text or html), please check your code");
            }
        }

        switch ($this->emailType)
        {
            case "1":

                $message = 'Content-Type: text/html; charset="iso-8859-1"' . "\r\n";
                $message = 'Content-Transfer-Encoding: 7bit' . "\r\n";
                $message .= $html;

            break;

            case "2":
            
                ob_start(); //Turn on output buffering
?>
--PHP-alt-<?php echo $random_hash; ?>

Content-Type: text/plain; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

<?=$text; ?>

--PHP-alt-<?php echo $random_hash; ?>

Content-Type: text/html; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

<?=$html;?>

--PHP-alt-<?php echo $random_hash; ?>--
<?php
                $message = ob_get_clean();
                
            break;

            default:
            
                $message = $text;
                
            break;
        }

        if ($this->debug == true)
        {
            $fd = fopen(CACHE_PATH . "test-" . time() . ".eml", "w+");
            fputs($fd, $message);
            fclose($fd);
        }

        // @ ivm mailserver niet ingesteld op locale test versie.
        if (@mail($email,
                  $subject,
                  $message,
                  $headers))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>