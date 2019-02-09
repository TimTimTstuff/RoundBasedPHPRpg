<?php

class Mob {

    private $id;
    private $data;
    private $randNameGend;
    private $kiName;
    /**
     *
     * @var ManipulationActionParser
     */
    private $actionParser;

    /**
     *
     * @var FightStats
     */
    private $fightStats;
    private $loot;
    private $actions;
    private $attacklist;
    private $key;
    
    public function __construct($mobId, $randomNameGenerator, $actionParser) {
        $this->id = $mobId;
        $this->randNameGend = $randomNameGenerator;
        $this->actionParser = $actionParser;
        $this->load();
    }

    
    public function getKey(){
     return $this->key;   
    }
    
    public function load() {

        $mob = R::load(DBTables::MOB, $this->id);

        $this->fightStats = json_decode($mob->fightstats);
        $this->loot = json_decode($mob->loot, true);
        $this->actions = json_decode($mob->actions);
        $this->data = $mob;
        $this->attacklist = json_decode($mob->attacklist);
        $this->key = $mob->mobkey;
        $this->kiName = $mob->ki;
    }
    
    public function getKiName(){
        return $this->kiName;
    }

    public static function createNew($hasRandName, $name, $type, $fightStats, $level, $rare, $questmob, $mobkey, $loot, $actionCondition,$ki,$attacklist) {

        $mob = R::dispense(DBTables::MOB);
        $mob->name = $name;
        $mob->randomname = $hasRandName;
        $mob->type = $type;
        $mob->fightstats = json_encode($fightStats);
        $mob->level = $level;
        $mob->rare = $rare;
        $mob->questmob = $questmob;
        $mob->mobkey = $mobkey;
        $mob->loot = json_encode($loot);
        $mob->actions = json_encode($actionCondition);
        $mob->ki = $ki;
        $mob->attacklist = json_encode($attacklist);
        R::store($mob);
    }

    public function getRandomLoot() {

        $loot = [];
        $x = [];
      
      
        foreach ($this->loot as $loot) {
            $type = $loot[0];
            $wahr = $loot[2];
            
            $rand = rand(0, 100);
            if ($rand < $wahr) {
                $x[] = [$type, $loot[1]];
            }
        }
        return $x;
    }

    /**
     * 
     * @param ManipulationActionParser[] $listActionParser
     */
    public function onDefeat($listActionParser = null) {
       
        if ($listActionParser == null) {
            foreach ($this->actions as $ac) {
                if ($this->actionParser->validateAction($ac)) {
                    $this->actionParser->doAction($ac);
                }
            }
        } else {
            foreach ($listActionParser as $ap) {
                foreach ($this->actions as $ac) {
                    if ($ap->validateAction($ac)) {
                        $ap->doAction($ac);
                    }
                }
            }
        }
    }

    /**
     * 
     * @return FightStats
     */
    public function getFightStats() {
        return $this->fightStats;
    }

    public function getName() {
        return $this->data->name;
    }

    public function getId() {
        return $this->data->id;
    }

    public function getAttackList(){
        return $this->attacklist;
    }
}

class FightStats {

    public $maxHp;
    public $maxMana;
    public $crit;
    public $avoid;
    public $speed;
    public $maxDmg;
    public $minDmg;
    public $armoreRed;
    public $manaReg;
    public $critMulti;
    public $aggro;
    public $spell;

}
