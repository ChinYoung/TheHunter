<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

function getStat($dbCls, $projectId, $field)
{
    $dbCls->query("SELECT `project_stats`.`number`
                   FROM `project_stats`
                   WHERE `project_id` = ?
                   AND   `status` = ?
                   AND   `type` = 'p'",
                  array($projectId, $field));
    if ($dbCls->rows() == 1)
    {
        $f = $dbCls->fetch(true);
        return $f['number'];
    }
    
    return 0;
}

?>