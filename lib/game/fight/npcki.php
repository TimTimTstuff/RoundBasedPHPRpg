<?php

abstract class NpcKiFactory {

    /**
     * 
     * @param string $name
     * @param FightMember[] $team
     * @param FightMember[] $targets
     */
    public static function getKi($name, $team, $targets,$actor) {
        switch ($name) {
            case "berserker":
                return new BerserkerKi($actor,$team,$targets);
            case "healer":
                return new HealerKi($actor, $team, $targets);
            default:
                return new RatKi($actor,$team, $targets);
        }
    }

}

abstract class NpcKi {

    /**
     *
     * @var FightMember[] 
     */
    protected $targets;

  
    /**
     *
     * @var FightMember
     */
    protected $actor;
    /**
     * @var FightMember[]
     * 
     */
    protected $team;
    
    public function __construct($actor,$team, $targets) {
        $this->team = $team;
        $this->targets = $targets;
        $this->actor = $actor;
    }

    /**
     * @return FightAction Description
     */
    public abstract function getAction();
}

class RatKi extends NpcKi {

    public function getAction() {

        $maxT = -1;
        $target = $this->targets[0];
        foreach ($this->targets as $t) {
            if (!$t->isFainted() && $t->getAggro() > $maxT) {
                $maxT = $t->getAggro();
                $target = $t;
            }
        }

        $action = new FightAction();
        $action->id = 0;
        $action->target = $target->getActorId();
        $action->type = "a";

        return $action;
    }

}

class BerserkerKi extends NpcKi{
    
    
    
    public function getAction() {
         $maxT = -1;
        $target = $this->targets[0];
        foreach ($this->targets as $t) {
            if (!$t->isFainted() && $t->getAggro() > $maxT) {
                $maxT = $t->getAggro();
                $target = $t;
            }
        }

        $useAttack = rand(0,100)<71;
        $id = 0;
        if($useAttack && $this->actor->getCurrMana() > 9){
            $id = 1;
        }
        
        $atConf = AttackConfiguration::load(1);
        
        $action = new FightAction();
        $action->id = $id;
        $action->target = $target->getActorId();
        $action->type = "a";

        return $action;
    }

}

class HealerKi extends NpcKi{
    
    private function getFriendWithLowerThan50pHp(){
        
        $lowest = 100/$this->team[0]->getMaxHp()*$this->team[0]->getCurrentHp();
        $target = $this->team[0];
        /**
         * @var FightMember $friend
         */
        foreach ($this->team as $friend) {
            if($lowest > 100/$friend->getMaxHp()*$friend->getCurrentHp() && $friend->getCurrentHp() > 0 ){
                $lowest = $friend->getCurrentHp();
                $target = $friend;
            }
        }
        
        if($target->getMaxHp()/2 > $target->getCurrentHp()){
            return $target;
        }
        
        return null;
        
    }
    
    private function getAggroTarget(){
          $target = $this->targets[0];
          $maxT = 0;
        foreach ($this->targets as $t) {
            if (!$t->isFainted() && $t->getAggro() > $maxT) {
                $maxT = $t->getAggro();
                $target = $t;
            }
        }
        return $target;
    }
    
    public function getAction() {
         $maxT = -1;
          
         $confSmallHeal = AttackConfiguration::load(3);
         $confHot = AttackConfiguration::load(8);
         $canUseSmallHeal = $confSmallHeal->manacost  <= $this->actor->getCurrMana();
         $canUseHot = $confHot->manacost <= $this->actor->getCurrMana();
      
         $healT = $this->getFriendWithLowerThan50pHp();
        // print_r($healT);
         if($healT != null && ($canUseHot || $canUseSmallHeal)){
             $action = new FightAction();
             $action->target = $healT->getActorId();
                    $action->type = "a";
             if($canUseSmallHeal){
                   
                    $action->id = 1;
             }else if($canUseHot){
                 $action->id = 2;
             }
             return $action;
         }
         
         

        $action = new FightAction();
        $action->id = 0;
        $action->target = $this->getAggroTarget()->getActorId();
        $action->type = "a";
        
        return $action;
    }

}