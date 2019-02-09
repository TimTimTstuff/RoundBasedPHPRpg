<?php

function using($librarys){
    if(!is_array($librarys)) return;
    
    if(in_array("db", $librarys)){
        include_once 'database/include.php';
    }
    
    if(in_array("user", $librarys)){
        include_once 'user/include.php';
    }
    
    if(in_array("repository", $librarys)){
        include_once 'repository/include.php';
    }
    
    if(in_array("http", $librarys)){
        include_once 'http/include.php';
    }
    
    if(in_array("service", $librarys)){
        include_once 'service/include.php';
    }
    
    if(in_array("game", $librarys)){
        include_once 'game/include.php';
    }
}