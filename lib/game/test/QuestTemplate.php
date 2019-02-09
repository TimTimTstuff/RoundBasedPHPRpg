<?php

//name der quest
$name = "";

/*
 * Questschlüssel. Irgendwas "einzigartiges" bsp
 * q01_find_100_fische (max 100 zeichen)
 */
$key = "";

/*
 * Vorhergehende quest
 * QuestId 
 * 0 = keine
 */
$prePestId = 0;

/*
 *  Start bedingung (für bedingungen in NpcTemplate schauen
 *  Wenn diese bedingung erfüllt ist wird die quest dem user angezeigt 
 */
$startCondition = [];

/* 
 * Die text die in der Quest angezeigt werden sollen. Welche der Questgeber sagt. 
 * new = noch nicht angenommen
 * activ = angenommen aber noch nicht erfüllt
 * solvee = erfüllt (bereit zur abgabe)
 * 
 * text werden in einer liste angegeben
 * ["","",""]
 * 
 * 1. Text was der questgeber sagt. 2. text für Annehmen oder 3. Zurück button (optional) 
 * bsp "new"=>["Oh gott, denk doch einer an die kinder","ja ich ich!","ne lieber nicht"]
 */
$questtext = [
    "new"=>[""],
    "active"=>[""],
    "solved"=>[""]
];

/*
 * Questbelohnung
 * Liste mit belohnungen. 
 * 
 * Möglich: 
 *  xp :zahl
 *  gold: zahl
 *  item [id,zahl]
 *  honor zahl
 *  peputation [faction,zahl]
 * 
 *  aufbau
 *  [
 *      ["xp",100],
 *      ["gold",40],
 *      ["item",[1,10]] //10* das item mit id 1
 *  ]
 * 
 */

$reward =  [];

/*
 *  Bedingung die zum erfüllen der quest nötig sind. 

 */
$goalCondition = [];

/*
 * Beschreibungstext der quest der in dem QuestLog angezeigt werden soll. 
 */

$objectiv = "";

/*
 *  aktionen die nach der abgabe der quest ausgeführt werden sollen (nicht belohnung)
 * z.B. entfernen von items/gold oder bereinigen von variablen die für die quest benötigt werden. 
 * 
 * 
 * 
 * möglich 
 *  remvar : bereinige variable : erwartet var key
 *  remitem : entfernt items : erwartet itemid, anzahl
 *  remflag : bereinigt flag : erwartet key
 *  remgold : bereinigt gold : erwartet anzahl
 * 
 * aufbau: [
 *      ["action"=>"remvar","key"=>"test"],//entfernt die var test
 *      ["action"=>"remitem","key"=>1,"amount"=>5]//entfernt 5`* item 1 aus dem inventar
 * ]
 * 
 */
$goalaction = [];









