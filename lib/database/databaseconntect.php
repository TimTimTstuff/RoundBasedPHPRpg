<?php

abstract class DBTables{
     const USER = "user";
     const STATS = "charstats";
     const CHAR = "charakter";
     const CHAR_EQUIP = "charequip";
     const ITEMS = "items";
     const BAG = "bag";
     const BAG_ITEMS = "bagitems";
     const ITEM_EQUIP = "itemsequip";
     const NPC = "npc";
     const NPC_VENDOR = "npcvendor";
     const NPC_TRAINER = "npctrainer";
     const CHAT = "chat";
     const LOG = "log";
     const FLAGS = "flags";
     const QUEST = "quest";
     const QUEST_LOG = "questlog";
     const NPC_QUEST = "npcquest";
     const STATISTICS = "statistics";
     const USER_GROUP = "usergroup";
     const FIGHT = "fight";
     const FIGHT_MEMBER = "fightmember";
     const FIGHT_LOG = "fightlog"; 
     const WORLD_FMAP = "worldfmap";
     const WORLD_SPACE = "worldspace";
     const WORLD_MAP = "worldmap";
     const WORLD_DUNGEON = "worlddungeon";
     const MOB = "mob";
     const ATTACK_CONFIG = "fightattacks";
}

abstract class StatisticItems{
    const XPEARND = "xpearnd";
    const ITEMSBUYED = "itemsbuyed";
    const ITEMSSELLED = "itemsselled";
    const GOLDEARND = "goldearnd";
    const GOLDUSED = "goldused";
    const QUESTSFINISHED = "questsfinished";
    const EARNDHONOR = "earndhonor";
    const LOSTHONOR = "losthonor";//no
    const USEDHONOR = "usedhonor";//no
    const TIMESLOGIN = "timeslogin";
    const ITEMSDELETED = "itemsdeleted";
    const MOBSKILLED = "mobskilled";
    const PLAYERFAINTED = "playerfainted";
}

abstract class DICT{
    const STATS_NAME = [
    "strength" => "Stärke",
    "vitality" => "Ausdauer",
    "speed" => "Tempo",
    "armour" => "Rüstung",
    "avoid" => "Ausweichen", 
    "crit" => "Krit",
    "inteligent" => "Int", 
    "wisdom" => "Weisheit"
]; 
    
//       const MILIZ = 0;
//    const PROTECTWARRIOR = 1;
//    const BERSERKER = 2;
//    const SPELLCASTER = 3;
//    const PRIEST = 4;
//    const BOWSMAN = 5;
//    const ASSASIN = 6;
    
    
    const CLASSNAME = [
        0=>"Miliz",
        1=>"Schutz Krieger",
        2=>"Berserker",
        3=>"Zauberer",
        4=>"Priester",
        5=>"Bogenschütze",
        6=>"Assasine"
    ];
    
    const MOB_TYPES = [
        0=>"Tier",
        1=>"Mensch",
        2=>"Geist"
    ];
   
    const GENDER = [
        0=>"Weiblich",
        1=>"Männlich"
    ];
    
    const NPC_TYPE = [
        0=>"Monster",
        1=>"Person",
        2=>"Auftraggeber",
        3=>"Händler",
        4=>"Lehrer"
    ];
    
    const ITEM_TYPE = [
        0=>"Equip",
        1=>"Müll",
        2=>"Material",
        3=>"Quest",
        4=>"Benutzbar"
    ];
    
   
    
    const MATERIAL_NAME = [
        0=>"Stoff",
        1=>"Holz",
        2=>"Leder",
        3=>"Kette",
        4=>"Platte",
        5=>"Diamant"
    ];
    const WEAPON_NAME = [
        0=>"Schwert",
        1=>"Axt",
        2=>"Stab",
        3=>"Zauberstab",
        4=>"Kolben",
        5=>"Bogen",
        6=>"Speer",
        7=>"Dolch",
        8=>"Buch"
        
    ];
    
    const EQUIP_SLOTS = [
       0 => "head",
       1 => "shoulder",
       2 => "breast",
       3 => "hand",
       4 => "legs",
       5 => "feet",
       6 => "wapon"
     
    ];
     const ARMOR_NAME = [
        0=>"Kopf",
        1=>"Schulter",
        2=>"Brust",
        3=>"Hände",
        4=>"Beine",
        5=>"Füße",
        6=>"Waffe"];
    
    const HERO_CLASS = [
        0 => "Keine",
        1 => "Nahkämpfer",
        1 => "Magier",
        2 => "Sucher"
    ];
    
    const QUEST_STATUS = [
        0 => "Aktiv",
        1 => "Bereit zur Abgabe",
        2 => "Abgeschlossen"
    ];
}

abstract class TypeOfItem {

    const Equip = 0;
    const Trash = 1;
    const WorkMaterial = 2;
    const Quest = 3;
    const USABLE = 4;
}

abstract class RarityItem{
    const TRASH = 0;
    const NORMAL = 1;
    const BESONDERS = 2;
    const RARE = 3;
    const LEGENDARY = 4;
    const UNIQUE = 5;
}

abstract class EquipType {

    const HEAD = 0;
    const BREAST = 1;
    const SHOULDER = 2;
    const LEGS = 3;
    const FEET = 4;
    const HAND = 5;
    const WAPON = 6;

}

abstract class ArmorMaterial {

    const CLOTH = 0;
    const WOOD = 1;
    const LETHER = 2;
    const CHAIN = 3;
    const PLATE = 4;
    const DIAMOND = 5;

}

abstract class WaponType {

    const NONE = -1;
    const SWORD = 0;
    const AXE = 1;
    const STAFF = 2;
    const WAND = 3;
    const MACE = 4;
    const BOW = 5;
    const SPEAR = 6;
    const KNIFE = 7;
    const BOOK = 8;

}

abstract class BagTypes{
    const Player = 1;
    const PlayerBank = 2;
    const Merchant = 3;
    const SellHouse = 3;
}

abstract class NpcType{
   
    const MONSTER = 0;
    const Person = 1;
    const Quest = 2;
    const Vendor = 3;
    const Trainer = 4;
    
}

abstract class QuestStatus{
    const NOTACCEPTED = -1;
    const ACTIVE = 0;
    const SOLVED = 1;
    const FINISHED = 2;
}

abstract class ManipulationActionTypes{
    const ADDITEM = 0;
    const REMITEM = 1;
    const ADDGOLD = 2;
    const REMGOLD = 3;
    const ADDXP = 4;
    const REMXP = 5;
    const ADDHONOR = 6;
    const REMHONOR = 7;
    const ADDREPUTATION = 8;
    const REMREPUTATION = 9;
    const CHANGEVAR = 10;
    const REMVAR = 11;
    const CHANGEFLAG = 12;
    const REMFLAG = 13;
    const CHANGEQUESTSTATUS = 14;
    const REMQUEST = 15;
    const CHANGENPC = 16;
    const REMNPC = 17;
    const STARTFIGHT = 18;
    const ADDTOFLAG = 19;
    const ADDTOVAR = 20;
    const STARTRANDFIGHT = 21;
    const CHANGEHP = 22;
    const CHANGEMANA = 23;
    
}

abstract class MobTypes{
    const ANIMAL = 0;
    const HUMAN = 1;
    const GOHST = 2;
}

abstract class FighterStatus{
    const NORMAL = 0;
    const SLEEP = 0;
    
}

abstract class FightType{
    const AUTO = 0;
    const HIT = 1;
    const SPELL = 2;
    const HEAL = 3;
    const HEALAOE = 4;
    const HITAOE = 5;
    const SPELLAOE = 6;
    const HOT = 7;
    const DOT = 8;
    const STATUS = 9;
}

abstract class EffectTypes{
       const HOT = 7;
       const DOT = 8;
       const SLEEP = 0;
       const CRIT = 1;
       const AVOID = 2;
       const SPEED = 3;
       const HP = 4;
       const MANA = 5;
       const ARMOR = 6;
       const DMG = 9;
       const Spell = 10;
       const HPR = 11;
}

abstract class DmgAttackAddType{
    const PERCENT = 0;
    const LIKEVALUE = 1;
}

abstract class QuestType{
    const FLAG = 0;
    const KILL = 1;
    const COLLECT = 2;
    
}

abstract class CHARKLASS{
    
    const MILIZ = 0;
    const PROTECTWARRIOR = 1;
    const BERSERKER = 2;
    const SPELLCASTER = 3;
    const PRIEST = 4;
    const BOWSMAN = 5;
    const ASSASIN = 6;
    
}

abstract class FMapAction{
    
    const Vendor = "openvendor";
    const Trainer = "opentrainer";
    const Space = "openspace";
    const Quest = "openquest";
    const MOVE_TOFMAP = "openfmap";
    
    
}

abstract class MapType{
    const FMAP = 0;
    const WORLDM = 1;
    const DUNGEON = 2;
}