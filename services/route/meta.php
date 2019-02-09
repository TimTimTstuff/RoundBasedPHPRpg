<?php
using(["game"]);
/** @var $content ServiceObject  */
$getPara = ["mob","item","equip","attack","quest"];


if(!isset($_GET["e"]) || !in_array($_GET["e"], $getPara)){
    echo  '{"none":null}';
    die();
}
$request = $_GET["e"];
$search = null;
if(isset($_GET["s"])){
    $search = $_GET["s"];
}

if($request == "mob"){
    
    $d = R::find(DBTables::MOB,"name like ?",["%".$search."%"]);
    echo json_encode($d);
}
if($request == "attack"){
    
    $d = R::find(DBTables::ATTACK_CONFIG,"name like ?",["%".$search."%"]);
    echo json_encode($d);
}
if($request == "item"){
    
    $d = R::find(DBTables::ITEMS," type != ? and  name like ?",[TypeOfItem::Equip,"%".$search."%"]);
    echo json_encode($d);
}

if($request == "quest"){
    
    $d = R::find(DBTables::QUEST,"name like ?",["%".$search."%"]);
    echo json_encode($d);
}

if($request == "equip"){
    
    $d = R::find(DBTables::ITEMS,"name like ? and type = ?",["%".$search."%", TypeOfItem::Equip]);
    echo json_encode($d);
}
die();