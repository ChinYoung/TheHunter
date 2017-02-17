<?php

/* (c) 2013 by Eric Bruggema, read license, do not alter, change or share anything below this line! */

function getPostOptionList($list, $in = '', $default = -1)
{
    $ret = '';

    if ($in != '' && isset($_POST[$in]))
    {
        $default = $_POST[$in];
    }

    foreach ($list AS $id => $opt)
    {
        $sel = '';
        if ($default == $id)
        {
            $sel = ' selected="selected"';
        }
        $ret .= '<option value="' . $id . '"' . $sel . '>' . $opt . '</option>';
    }

    return $ret;
}

function getPostList($in, $default = '')
{
    if (isset($_POST[$in]))
    {
        return stripslashes($_POST[$in]);
    }
    else
    {
        return stripslashes($default);
    }    
}

function getPostGet($in)
{
    if (isset($_POST[$in]))
    {
        return stripslashes($_POST[$in]);
    }
    elseif (isset($_GET[$in]))
    {
        return stripslashes($_GET[$in]);
    }
    else
    {
        return '';
    }
}

function getPostArr($arr, $in, $sel = '', $std = '')
{
    if (isset($_POST[$arr][$in]))
    {
        if ($sel != '')
        {   
            if ($sel == $_POST[$arr][$in])
            {
                return 'checked="checked"';
            }
        }
        else
        {
            return stripslashes($_POST[$arr][$in]);
        }
    }
    elseif ($std != '')
    {
        return stripslashes($std);
    }
    else
    {
        return '';
    }
}

function getPost($in, $std = '', $check = false)
{
    if (isset($_POST[$in]))
    {
        if ($check == true)
        {
            if ($_POST[$in] == $std)
            {
                return ' checked="checked"';
            }
        }
        
        return stripslashes($_POST[$in]);
    }
    elseif ($std != '')
    {
        return stripslashes($std);
    }
    else
    {
        return '';
    }
}

function getGet($in)
{
    if (isset($_GET[$in]))
    {
        return stripslashes($_GET[$in]);
    }
    else
    {
        return '';
    }
}
?>
