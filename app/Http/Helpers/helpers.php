<?php

function makeShortCutName($name)
{
    $name_part = explode(" ", $name);

    /* 1ère lettre du Nom et Prénom */
    $letter_1 = substr($name_part[0], 0, 1);
    $letter_2 = substr($name_part[1], 0, 1);

    $name = $letter_1 . $letter_2;
    /* 
    dd($name, $letter_1, $letter_2); */

    return $name;
}

function created_at_format_date($date)
{
    $currentDateTime = $date;
    $newDateTime = date('h:i A', strtotime($currentDateTime));

    return $newDateTime;
}

function string_to_html_plus_clean_div($message)
{
    $message_converted = htmlspecialchars_decode($message);
    echo $message_converted;
}

function contains_html_tags($message)
{
    $match = preg_match('#(&lt;[a-z]+&gt;)+#', $message);
    return $match;
}
