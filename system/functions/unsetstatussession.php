<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

function unsetStatusSession()
{
    foreach (array('error','notice','status') AS $k)
    {
        if (isset($_SESSION[$k]))
        {
            unset($_SESSION[$k]);
        }
    }
}
?>