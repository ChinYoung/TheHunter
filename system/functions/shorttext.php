<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

function shortText($tekst, $num = '')
{
    if (!isset($num)) {
        $num = 25;
    }

    $words = explode(" ", $tekst);
    $total = count($words);

    if ($total >= $num) {
        for ($i = 0; $i <= $num; $i++) {
            if (!isset($short)) {
                $short = $words[$i];
            } else {
                $short = $short." ".$words[$i];
            }
        }
    } else {
        $short = $tekst;
    }

    return $short;
}

?>
