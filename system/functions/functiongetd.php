<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

function getD($dbCls, $projects = array(), $keys = array())
{
    $dbCls->query("SELECT `key`,  `date`, SUM(value) AS value 
                   FROM project_daily_stats 
                   WHERE `key` IN ('" . implode("','", $keys) . "')" . 
                  ((count($projects) > 0) ? " AND  `project_id` IN ('" . implode("','", $projects) . "')" : '') . "
                   GROUP BY `date`, `key`
                   ORDER BY `date` ASC");
    if ($dbCls->rows() > 0) 
    {
        $data = array();
        
        foreach ($dbCls->fetch() AS $list) 
        {
            $data[$list['date']][$list['key']] = $list['value'];
        }
        
        foreach ($data AS $date => $list)
        {
            foreach ($keys AS $null=>$k)
            {
                if (!isset($data[$date][$k]))
                {
                    $data[$date][$k] = 0;
                }
            }
        }
    }
    else
    {
        foreach ($keys AS $null=>$k)
        {
            if (!isset($data['0000-00-00'][$k]))
            {
                $data['0000-00-00'][$k] = 0;
            }
        }
    }
    
    return $data;
}

?>