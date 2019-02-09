<?php

$name = "Der Kammerjäger";

$key = "q03_der_kammer_jaeger";
$prePestId = 0;

$startCondition = ["level",">",0];


$questtext = [
    "new"=>["Hm überall Ratten.. könnt ihr nicht ein paar für mich töten?"],
    "active"=>["Habt ihr schon alle Ratten getötet?"],
    "solved"=>["Vielen dank. Es gibt hierfür auch eine kleinigkeit"]
];


$reward =  [
    ["xp",100],
    ["gold",110]
];

$goalCondition = [["flag",">",["q_rat_kill",20]]];

$objectiv = "Tötet 20 Ratten";

$goalaction = [[
    "actioin"=>"remvar","key"=>"q_rat_kill"
]];


Quest::createNew($name, $key,$prePestId, $startCondition, $goalaction, $reward, $goalCondition, $objectiv, $goalActions);






