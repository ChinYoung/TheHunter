<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

function stripAllSlashes (&$ArrayGET, $Value) 
{ 
   if (is_array ($ArrayGET)) array_walk ($ArrayGET, "stripAllSlashes"); 
   else $ArrayGET = stripslashes ($ArrayGET); 
} 

?>
