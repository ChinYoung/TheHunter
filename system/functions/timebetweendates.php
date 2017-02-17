<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

define("MINUTE", 60);
define("HOUR", 3600); // 60 * 60 
define("DAY", 86400); //  60 * 60 * 24 
define("WEEK", 604800); // 60 * 60 * 24 * 7
 
function timeBetweenDates($from, $date) 
{
    $since = abs(strtotime($from) - strtotime($date));
 
    if ($since > WEEK) 
    {
        $week = floor($since / WEEK);
        $day  = floor(($since - ($week * WEEK)) / DAY);
        return $week . " weken en " . $day . " dagen";
    }
 
    if ($since > DAY) 
    {
        $day  = floor($since / DAY);
        $hour = floor(($since - ($day * DAY)) / HOUR);

        if ($hour == 0)
        {
            return $day . " dagen";
        }
        else
        {
            return $day . " dagen en " . $hour . " uren";
        }
    }
 
    if ($since > HOUR) 
    {
        $hour   = floor($since / HOUR);
        $minute = floor(($since - ($hour * HOUR)) / MINUTE);

        if ($minute == 0)
        {
            return $hour . " uur";
        }
        else
        {
            return $hour . " uren en " . $minute . " minuten";
        }
    }
 
    if ($since > MINUTE) 
    {
        $minute = floor($since / MINUTE);
        return $minute . " minuten";
    }
 
    return "minder dan een miuut";	
}
?>
