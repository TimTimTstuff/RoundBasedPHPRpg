<?php

function butyfyStats($statsString){
    $statsObj = json_decode($statsString,true);
    $output = "";
    $in = 0;
    foreach ($statsObj as $key => $value) {
        if($in%2==0)$output.=" <br/>";
        $output.= $key.": ".$value." ";
        $in++;
    }
    return $output;
}

