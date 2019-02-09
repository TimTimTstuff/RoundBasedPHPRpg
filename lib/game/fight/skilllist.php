<?php

class AttackConfiguration{
    public $key;
    public $targettype;
    public $name;
    public $description;
    public $level;
    public $class;
    public $type;
    public $dmgtype;
    public $attackvalue;
    public $colorcode;
    public $manacost;
    public $rounds;
    
    /**
     * 
     * @param type $id
     * @return AttackConfiguration
     */
    public static function load($id){
        return R::load(DBTables::ATTACK_CONFIG, $id);
    }
    
    public static function createNew($key,$targettype,$name,$description,$level,$class,$type,$dmgtype,$attackvalue,$colorcode,$manacost,$rounds = 0){
        $attack = R::dispense(DBTables::ATTACK_CONFIG);
        $attack->key = $key;
        $attack->targettype = $targettype;
        $attack->name = $name;
        $attack->description = $description;
        $attack->level = $level;
        $attack->class = $class;
        $attack->type = $type;
        $attack->dmgtype = $dmgtype;
        $attack->attackvalue = $attackvalue;
        $attack->colorcode = $colorcode;
        $attack->manacost = $manacost;
        $attack->rounds = $rounds;
        
        R::store($attack);
    }
}

        
class SkillAttackProcessor{
    
    /**
     *
     * @var AttackConfiguration
     */
    private $attackConf;
    
    public function __construct($attackId) {
        $this->attackConf = AttackConfiguration::load($attackId);
    }
    
    public function getConfig(){
        return $this->attackConf;
    }
    
    /**
     * 
     * @return int 0 single 1 mulit
     */
    public function getTargetType(){
        return $this->attackConf->targettype;
    }
}


class HitAttack{
    
    /**
     *
     * @var FightMember
     */
    private $actor;
    /**
     *
     * @var FightMember[]
     */
    private $targets;
    
    /**
     * @param FightMember $actor
     * @param FightMember[] $targets
     */
    public function __construct($actor,$targets) {
        
    }
    
    public function process(){
        
        
        
    }
    
    
    
}

class Effects{
    public $value;
    public $leftrounds;
    public $status;
    public $name;
    public $id;
}