<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

function calculateTime($from, $to, $returnText = false)
{
    $start = strtotime($from);
    $end = strtotime($to);
    
    list($days, $hours, $minutes, $seconds, $text) = array('','','','','');
    
    $totaltime = ($end - $start);

    $days = intval($totaltime / (3600 * 24));
    $time_remain = ($totaltime - ($hours * 3600));

    $hours = intval($time_remain / 3600);
    $time_remain = ($totaltime - ($hours * 3600));

    $minutes = intval($time_remain / 60);
    $seconds = ($time_remain - ($minutes * 60));
    
    if ($returnText != false)
    {
        $text = '';
        $text .= ($days > 0) ? $days . ' dagen, ' : '';
        $text .= ($hours > 0) ? $hours . ' uren, ' : '';
        $text .= ($minutes > 0) ? $minutes . ' minuten, ' : '';
        if(strlen($text) > 0) { $text = substr($text, 0, strlen($text) -2); }
        
        return $text;
    }
    else
    {
        return array("days"    => $days,
                     "hours"   => $hours,
                     "minutes" => $minutes,
                     "text"    => $text);
    }
}