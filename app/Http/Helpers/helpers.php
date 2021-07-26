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
   /*  dd($date); */
    $currentDateTime = $date;
    $newDateTime = date('h:i A', strtotime($currentDateTime));

    return $newDateTime;
}
