<?php

/*
 * Name des NPCs
 */
$name = "NPC NAME";

/*
 * Typ des NPCS. Den Wert hinter dem ::: ändern. 
 * Möglich: MONSTER, Person, Quest, Vendor, Trainer
 * 
 * WICHTIG: je nach typ müssen zusätzliche informationen angegeben werden. 
 * 
 */

$type = NpcType::MONSTER;

/*
 * 1. Random Text der aufgerufen wird, wenn kein Spezieller text angezeigt werden soll. 
 * Schlüssel: r<<zahl>>   bsp. r1, r2, r3, ... ,r999
 * Diese Text werden random beim laden der seite / des npc angezeigt. 
 * 
 * 2. Texte für bedingungen. 
 * z.B.  Wenn der char etwas bestimmtes gemacht hat, oder wenn bestimmte Quests erledigt werden sollen. 
 * hierfür kein irgend ein index außer die r<<zahl>> genutzt werden. z.B. 
 * text1, hallo, aa, b, 5
 */

$textList = [
    "r1"
];

/*
 *  Hier kann die Fraktino der der NPC angehören soll angegeben werden. Die IDs (zahlen) müssen
 *  aus der Fraktionsliste gelesen werden (wenn es sie denn schon gibt.
 * 
 * 0 = keine
 *  
 */

$faction = 0;

/*
 * Hier kann die Gilde die der NPC angehört angegeben werden. Die schlüssel müssen aus einer eigenen 
 * Liste gelesen werden. (wenn es sie denn schon gibt)
 * 
 */

$guild = 0;

/*
 *  Geschlecht des NPC  0=Weiblich 1 = Männlich
 * 
 */

$gender = 0;


/*
 *  Hier kann eine Beschreibung des NPC angegeben werden. 
 *  
 */

$description = "";

/*
 *  Conditions / Bedingungen 
 *  Operatoren  =  : wert ist gleich
 *              >  : wert ist größer
 *              <  : wert ist kleiner
 *              != : wert ist ungleich
 * 
 * variablen 
 *          xp  : die xp des users. Alle Operatoren. Erwartet: zahl
 *          level : das level des users. Alle Op. Erwartet: zahl
 *          name : name des users. =, !=. Erwartet: string
 *          gold : gold des users. Alle Op. Erwartet zahl
 *          iteminbag : ein item in der tasche des users. =,!= . Erwartet [itemid,anzahl]
 *          itemequip : ein item das der user Equiped hat. =, !=. Erwartet itemid
 *          var       : eine Sessionvariable die beim user gesetzt ist. Alle op. Erwartet any
 *          flag      : eine flag die beim user gesetzt ist. Alle op. Erwartet: any
 *          opennpc   : prüft auf den aktuell göffneten npc. =, !=. Erwartet: npcid
 *          queststatus : prüft auf den status einer quest. Alle op. Erwartet: [questId,status](status -1:unbekannt, 0: aktiv, 1: gelöst, 2:abgegeben
 *          (mehr folgen)
 * 
 * Gruppieren
 *          mehrere bedingungen können mit "and" und "or" verbunden werden. 
 * 
 * Aufbau
 *        Bedingungn werdne in Mehrdimensonalen Listen aufgebaut. Bsp: 
 * 
 *  Einfach: ["level",">",1] (level ist größer als 1)
 *  
 *  Gruppe : [
 *              "and"=>[
 *                      ["level","=",1],
 *                      ["gold",">",100]
 *                      ]
 *           ]//Wenn level gleich 1 ist und gold größer als 100, 
 * 
 *  Gruppen in Gruppen
 * 
 *          ["or"=>[
 *              "and"=>[
 *                      ["level","<",5],
 *                      ["xp",">",100]
 *                      ],
 *              "or"=>[
 *                      ["level","=",5],
 *                      ["iteminbag","=",[1,5]]
 *                      ]
 *          ]]
 * 
 *  Entweder user level ist kleiner als 5 undhat mehr als 100 xp 
 *  ODER 
 *  das level ist 5 und er hat 5* das item mit der id 1 in der tasche
 * 
 * 
 */




/*
 *  Hier können bedingungen für bestimmte aktionen des NPCs angegeben werden. 
 * 
 *  Aufbau   Schlüssel c ("c"=>[]) zeigt auf die bedingung die Wahr sein muss. 
 *           Schlüsse. t ("t"=>"xx") zeigt auf den Text aus der Textliste der angezeigt werden soll, 
 *                                   wenn die bedingung wahr ist. 
 * 
 */

$beispielConditions = [
    [//Anfang der ersten Bedingung
        "c"=>["level",">",5],//wenn level ">" größer ist als 5
        "t"=>"text1"//zeige den text mit dem Schlüssen text1 an
    ],//Ende der ersten bedingung
    [//zweite bedingung
        "c"=>[
            "and"=>[//wenn beide folgenden bedingung erfüllt sind
                ["gold","<",100],//das gold des spielers kleiner ist als 100
                ["level","=",5]//das level des spieler genau 5 ist
            ]
            ],
        "t"=>"text2" //zeige text mit dem Schlüssel text2 an. 
    ]
];


$conditions = [];


//********************   NUR FÜR VENDOR *****************
/*
 * INFO: npcs werden nach bestimmten zeitintervallen wieder aufgefrischt (gold und items)
 */

//mindestgold beim auffrischen
$mingold = 0;
//maximlaes gold beim auffrischen
$maxgold = 0;
//zeit zwischen den auffrischungen in Sekunden
$restockTime = 0;

/*
 *  Items die der Händler verkauf
 *  Item aufbau: [zahl,zahl,zahl]
 *  1. Itemid, 2. % wahrscheinlichkeit 3. Anzahl
 * 
 * bsp [1,50,5]  er hat zu 50% wahrscheinlichkeit 5 mal das item mit id 1.
 *  
 */

$beispielItemListe = [
    [1,50,5], //itemid 1 zu 50% 5mal
    [10,75,1], //itemid 10 zu 75% 1mal
    [4,101,2] //itemid 4 zu 100% 2mal
];

$itemconfig = [];


//************************ NUR QuestNPC *************************
/*
 * liste der quests die der npc hat. 
 * Quest ids. 
 * bsp. [1,2,3,4]  quest 1, 2 3 und 4 können bei dem npc abgeholt werden. 
 */
$questlist = [];


//******************** NUR LEHRER ******************************
//noch nicht entwickelt


//******************* NUR MONSTER ******************************
//noch nicht entwickelt