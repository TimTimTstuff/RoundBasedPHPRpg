<?php

$attacks = [];

$a = new HotDot();
$a->id = 0;
$a->name = "Heilende wirbel";
$a->desc = "Heilt das Ziel für 5Hp pro Runde für 4 Runden";
$a->ishot = 1;
$a->mana = 5;
$a->rounds = 4;
$a->value = 5;

$attacks[0] = $a;

$a = new HotDot();
$a->id = 1;
$a->name = "Gezielter Schnitt";
$a->desc = "Fügt dem Ziel eine Wunde zu die für 4 Runden 3 Schaden zufügt";
$a->ishot = 0;
$a->mana = 5;
$a->rounds = 4;
$a->value = 3;