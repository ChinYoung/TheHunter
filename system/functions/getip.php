<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

function getIp()
{
    $ip = false;

    if(!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $ips = explode(",", 
                       $_SERVER['HTTP_X_FORWARDED_FOR']);
        for ($i = 0; $i < count($ips); $i++)
        {
            $ips = trim($ips[$i]);
            if (!eregi("^(10|172\.16|192\.168)\.", 
                       $ips[$i]))
            {
                    $ip = $ips[$i];
                    break;
            }
        }
    }
    elseif (!empty($_SERVER['HTTP_VIA']))
    {
        $ips = explode(",", 
                       $_SERVER['HTTP_VIA']);
        for ($i = 0; $i < count($ips); $i++)
        {
            $ips = trim($ips[$i]);
            if (!eregi("^(10|172\.16|192\.168)\.", 
                       $ips[$i]))
            {
                    $ip = $ips[$i];
                    break;
            }
        }
    }
    elseif (!empty($_SERVER['REMOTE_ADDR']))
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    if (($longip = ip2long($ip)) !== false)
    {
        if ($ip == long2ip($longip))
        {
            return $ip;
        }
    }
    
    return false;
}

?>
