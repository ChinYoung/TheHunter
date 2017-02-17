<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

function getHost()
{
    return gethostbyaddr(getIp());
}
?>
