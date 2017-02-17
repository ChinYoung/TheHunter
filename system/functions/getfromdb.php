<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

function getFromDb($dbCls, $query, $name)
{
    $dbCls->query($query);
    
    if ($dbCls->rows() > 0)
    {
        $l = $dbCls->fetch(true);
        if (isset($l[$name]))
        {
            return $l[$name];
        }
    }
    
    return 0;
}
?>