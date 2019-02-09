<?php

class BuildMob {

    public $name;
    public $type;

    /**
     *
     * @var FightStats
     */
    public $stats;
    public $level;
    public $rear = 0;
    public $quest = 0;
    public $key;
    public $loot = [];
    public $action = [];
    public $ki = "rat";

    public function __construct() {
        $this->stats = new FightStats();
        $this->stats->armoreRed = 1;
        $this->stats->avoid = 1;
        $this->stats->crit = 1;
        $this->stats->maxDmg = 1;
        $this->stats->minDmg = 1;
        $this->stats->maxHp = 10;
        $this->stats->maxMana = 0;
        $this->stats->speed = 1;
        $this->level = 1;
        $this->key = "m" . rand(1, 99) . "_generated_" . rand(1, 99);
    }

    public function SetName($name) {

        $this->name = $name;
        return $this;
    }

    public function SetType($type) {
        $this->type = $type;
        return $this;
    }

    public function SetLevel($level) {
        $this->level = $level;
        return $this;
    }

    public function SetRear($rare) {
        $this->rear = $rare;
        return $this;
    }

    public function SetQuest($quest) {
        $this->quest = $quest;
        return $this;
    }

    public function SetMobKey($mobKey) {
        $this->key = $mobKey;
        return $this;
    }

    /**
     * Armor, Avoid, Crit, min Dm., max Dm, Hp, mana, speed
     * @param int $armor
     * @param int $avoid
     * @param int $crit
     * @param int $maxDmg
     * @param int $minDmg
     * @param int $maxHp
     * @param int $maxMana
     * @param int $speed
     * @return $this
     */
    public function SetStats($armor, $avoid, $crit, $minDmg, $maxDmg, $maxHp, $maxMana, $speed) {
        $this->stats->armoreRed = $armor;
        $this->stats->avoid = $avoid;
        $this->stats->crit = $crit;
        $this->stats->maxDmg = $maxDmg;
        $this->stats->minDmg = $minDmg;
        $this->stats->maxHp = $maxHp;
        $this->stats->maxMana = $maxMana;
        $this->stats->speed = $speed;
        return $this;
    }

    public function SetStatsString($stats){
        $this->stats = json_decode($stats,true);
    }
    
    public function AddLoot($name, $min, $max, $chance) {
        $this->loot[] = [$name, [$min, $max], $chance];
        return $this;
    }

    public function AddAction($action) {
        $this->action[] = $action;
        return $this;
    }

    public function SetKi($name) {
        $this->ki = $name;
        return $this;
    }

    public function create() {


        Mob::createNew(false, $this->name, $this->type, $this->stats, $this->level, $this->rear, $this->quest, $this->key, $this->loot, $this->action, $this->ki);
    }

}

class EquipCsvImporter {

    public function __construct($fileName) {
        $row = 1;
        if (($handle = fopen("import/$fileName", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                if ($row == 1) {
                    $row++;
                    continue;
                }
           $exists = R::findOne(DBTables::ITEMS, " name = ? ", [$data[0]]);
                if ($exists != null) {
                    $row++;
                    echo "Existing Item: ".$data[0]." <br/>";
                    continue;
                }
                
                $row++;
                echo "$num felder in zele $row<br/>";

     



                Equip::createNewItem($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9]);
            }
        }
    }

}

class BuildQuest{
    
    
    
    
    
}