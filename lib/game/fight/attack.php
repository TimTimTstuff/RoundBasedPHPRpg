<?php

class AttackProcessor {

    /**
     *
     * @var FightMember
     */
    private $actor;

    /**
     *
     * @var FightMember
     */
    private $target;
    private $template = "";

    public function __construct($actor, $target) {
        $this->actor = $actor;
        $this->target = $target;
        $this->template = RenderMain::loadGameTemplate("fight_log");
    }

    public function getFightMessage() {
        
    }

    public function process() {

        $actorStats = $this->actor->getStats();
        $fighterStats = $this->target->getStats();
        $dmgActor = rand($actorStats->minDmg, $actorStats->maxDmg);
        $dmgReduce = floor(($dmgActor / 100) * $fighterStats->armoreRed);
        $dmgReal = $dmgActor - $dmgReduce;
        //check crit
        $isCrit = rand(0, 100) < $actorStats->crit;

        //check avoid
        $isAvoid = rand(0, 100) < $fighterStats->avoid;

        if ($isCrit) {
            $dmgReal = floor($dmgReal * 2.5);
        }

        if ($isAvoid) {
            $dmgReal = 0;
        }

        $aggro = $dmgReal / 2;

        if ($isCrit) {
            $aggro = $aggro * 2.5;
        }

        $this->target->changeHp($dmgReal);
        $this->actor->addAggro($aggro);

        return $this->createFightLog($isCrit, $isAvoid, "Standard", $dmgReal);
    }

    private function createFightLog($isCrit, $isAvoid, $attackName, $dmg) {


        $data = ["actor" => $this->actor->getActorName(),
            "target" => $this->target->getActorName(),
            "attack" => $attackName,
            "dmg" => $dmg,
            "crit" => $isCrit,
            "avoid" => $isAvoid,
            "ai" => $this->actor->getIndex(),
            "ti" => $this->target->getIndex()
        ];
        return json_encode($data);
        $textLine = "";
        if ($isAvoid) {
            $textLine .= "<span class='avoid'>" . $this->target->getActorName() . " weicht aus!</span><br/>";
        } else if ($isCrit) {
            $textLine .= "<span class='crit'>" . $this->actor->getActorName() . " führt einen Kritischen Treffer aus.</span><br/>";
        }



        $textLine .= $this->actor->getActorName() . " verursacht " . $dmg . " Schaden an " . $this->target->getActorName();
        $head = "<span class='actor_p'>" . $this->actor->getActorName() . "</span> greift <span class='target_p'>" . $this->target->getActorName() . "</span> mit <span class='att_name'>" . $attackName . "</span> an";
        $temp = str_replace("{{head}}", $head, $this->template);

        return str_replace("{{extra}}", $textLine, $temp);
    }

}

class AttackProcessorAdvanced {

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
     *
     * @var AttackConfiguration
     */
    private $configuration;
    private $isCrit = null;
    private $isAvoid = [];
    private $fightId;

    public function __construct($actor, $targets, $configuration, $fightId) {
        $this->actor = $actor;
        $this->targets = $targets;
        $this->configuration = $configuration;
        $this->fightId = $fightId;
    }

    public function isCrit($set = null) {

        if ($this->isCrit == null) {
            $rand = rand(1, 100);
            $speed = $this->actor->getStats()->crit;
            $speedBonus = 0;
            $speed += $speedBonus;
            if ($set == null) {
                $this->isCrit = $speed > $rand;
            } else {
                $this->isCrit = $set;
            }
        }
        return $this->isCrit;
    }

    /**
     * 
     * @param FightMember $member
     * @return type
     */
    public function isAvoid($member, $set = null) {

       
   
        if (!array_key_exists($member->getActorId(), $this->isAvoid)) {
            $rand = rand(1, 100);
            $avoid = $member->getStats()->avoid;
            $avoidBonus = 0;
            $avoid += $avoidBonus;
            if ($set == null) {
                $this->isAvoid[$member->getActorId()] = $avoid > $rand;
            } else {
                $this->isAvoid[$member->getActorId()] = $set;
            }
        }

        return $this->isAvoid[$member->getActorId()];
    }

    public function getDmgPlain() {

        $defaultMinDmg = $this->actor->getStats()->minDmg;
        $defaultMaxDmg = $this->actor->getStats()->maxDmg;
        $bonusDmg = 0;

        $plainDmg = rand($defaultMinDmg + $bonusDmg, $defaultMaxDmg + $bonusDmg);
        return $plainDmg;
    }

    public function calculateAttackDmgBonus($dmgtype, $value, $dmgPlain) {

        $isReduce = $value < 0;



        if ($dmgtype == DmgAttackAddType::LIKEVALUE) {
            return $value;
        } else {
            if ($isReduce) {
                $value *= -1;
            }
            $res = $dmgPlain * (((100 - $value) / 100));
        
            if ($isReduce) {
                return $res * -1;
            }

            return $res;
        }
    }

    /**
     * 
     * @param FightMember $target
     */
    private function processHit($target) {

        $dmgTotal = 0;
        $dmgResist = 0;

        if (!$this->isAvoid($target)) {
            $pDmg = $this->getDmgPlain();
            $dmgAttackAdditional = $this->calculateAttackDmgBonus($this->configuration->dmgtype, $this->configuration->attackvalue, $pDmg);
            $dmgTotal = $pDmg + $dmgAttackAdditional;
            if ($this->isCrit()) {
                $dmgTotal *= $this->actor->getStats()->critMulti;
            }

            $dmgRestist = ceil($dmgTotal / 100 * $target->getStats()->armoreRed);
        }

        //reduce dmg by armor
        $dmgTotal = ceil($dmgTotal - $dmgResist);
        $aggro = $dmgTotal / $this->actor->getStats()->aggro;
        if ($this->isCrit()) {
            $aggro *= 2.5;
        }


        $this->actor->addAggro(floor($aggro));
        $target->changeHp($dmgTotal);
        $this->getFightLogObject($target->getActorName(), $target, $dmgTotal, $dmgResist);
    }

    /**
     * 
     * @param FightMember $target
     */
    private function processAuto($target) {

        $dmgTotal = 0;
        $dmgResist = 0;

        if (!$this->isAvoid($target)) {
            $pDmg = $this->getDmgPlain();
            $dmgTotal = $pDmg;
            if ($this->isCrit()) {
                $dmgTotal *= $this->actor->getStats()->critMulti;
            }

            $dmgRestist = ceil($dmgTotal / 100 * $target->getStats()->armoreRed);
        }

        //reduce dmg by armor
        $dmgTotal = ceil($dmgTotal - $dmgResist);

        $aggro = $dmgTotal / $this->actor->getStats()->aggro;

        if ($this->isCrit()) {
            $aggro *= 2.5;
        }


        $this->actor->addAggro(floor($aggro));
        $target->changeHp($dmgTotal);
        $this->getFightLogObject($target->getActorName(), $target, $dmgTotal, $dmgResist);
    }

    public function processHeal($target) {

        $healTotal = 0;

        if (!$this->isAvoid($target, false)) {
            $plainSpell = $this->actor->getStats()->spell;
            $attackSpellHealValue = $this->calculateAttackDmgBonus($this->configuration->dmgtype, $this->configuration->attackvalue, $plainSpell);
            $healTotal = $attackSpellHealValue + $plainSpell;
            if ($this->isCrit()) {
                $healTotal *= $this->actor->getStats()->critMulti;
            }
        }

        //reduce dmg by armor
        $healTotal = ceil($healTotal);

        $aggro = ($healTotal / 2) / $this->actor->getStats()->aggro;

        if ($this->isCrit()) {
            $aggro *= 2.5;
        }


        $this->actor->addAggro(floor($aggro));
        $target->changeHp($healTotal * -1);
        $this->getFightLogObject($target->getActorName(), $target, $healTotal, 0, "heal");
    }

    /**
     * 
     * @param FightMember $target
     */
    private function processSpell($target) {

        $dmgTotal = 0;
        $dmgResist = 0;

        if (!$this->isAvoid($target)) {
            $pDmg = $this->actor->getStats()->spell;
            $dmgAttackAdditional = $this->calculateAttackDmgBonus($this->configuration->dmgtype, $this->configuration->attackvalue, $pDmg);
            $dmgTotal = $pDmg + $dmgAttackAdditional;
            if ($this->isCrit()) {
                $dmgTotal *= $this->actor->getStats()->critMulti;
            }

            $dmgResist = ceil(($dmgTotal / 100 * $target->getStats()->armoreRed) / 2);
        }

        //reduce dmg by armor
        $dmgTotal = ceil($dmgTotal - $dmgResist);
        $aggro = ($dmgTotal) / $this->actor->getStats()->aggro;

        if ($this->isCrit()) {
            $aggro *= 2.5;
        }


        $this->actor->addAggro(floor($aggro));
        $target->changeHp($dmgTotal);
        $this->getFightLogObject($target->getActorName(), $target, $dmgTotal, $dmgResist);
    }
    
    
      /**
     * 
     * @param FightMember $target
     */
    private function processStatus($target) {


        if (!$this->isAvoid($target)) {
            $value = explode(";", $this->configuration->attackvalue);
            $eff = new Effects();
            $eff->leftrounds = $this->configuration->rounds;
            $eff->status = $value[0];
            $eff->value = $value[1];
            $eff->name = $this->configuration->name;
            $eff->id = $this->actor->getActorId().";".$this->configuration->key;
            $target->addEffect($eff);
            $this->getFightLogObject($target->getActorName(), $target, 0, 0, "status",$this->configuration->rounds);
        
        }

        $this->getFightLogObject($target->getActorName(), $target, $dmgTotal, $dmgResist);
    }
    
    
     /**
     * 
     * @param FightMember $target
     */
    private function processDot($target) {

        $dmgTotal = 0;
        $dmgResist = 0;

        if (!$this->isAvoid($target)) {
            $pDmg = $this->actor->getStats()->spell/$this->configuration->rounds;
            $dmgAttackAdditional = $this->calculateAttackDmgBonus($this->configuration->dmgtype, $this->configuration->attackvalue, $pDmg);
            $dmgTotal = $pDmg + $dmgAttackAdditional;
            
            $dmgResist = ceil(($dmgTotal / 100 * $target->getStats()->armoreRed) / 2);
        }

        //reduce dmg by armor
        $dmgTotal = ceil($dmgTotal - $dmgResist);
       
       
        $eff = new Effects();
        $eff->leftrounds = $this->configuration->rounds;
        $eff->status = FightType::DOT;
    
        $eff->value = $dmgTotal;
        $eff->name = $this->configuration->name;
        $eff->id = $this->actor->getActorId().";".$this->configuration->key;
        $target->addEffect($eff);
        $this->getFightLogObject($target->getActorName(), $target, $dmgTotal, $dmgResist,"dot",$this->configuration->rounds);
    }
    
    /**
     * 
     * @param FightMember $target
     */
    private function processHot($target){
        
           $healTotal = 0;

        if (!$this->isAvoid($target, false)) {
            $plainSpell = $this->actor->getStats()->spell/$this->configuration->rounds;
            $attackSpellHealValue = $this->calculateAttackDmgBonus($this->configuration->dmgtype, $this->configuration->attackvalue, $plainSpell);
            $healTotal = $attackSpellHealValue + $plainSpell;
        }

        //reduce dmg by armor
        $healTotal = ceil($healTotal);

        if($healTotal == 0)            throw new Exception($healTotal);

        $eff = new Effects();
        $eff->leftrounds = $this->configuration->rounds;
        $eff->status = FightType::HOT;
        $eff->value = $healTotal;
        $eff->name = $this->configuration->name;
        $eff->id = $this->actor->getActorId().";".$this->configuration->key;
        $target->addEffect($eff);
        $this->getFightLogObject($target->getActorName(), $target, $healTotal, 0, "hot",$this->configuration->rounds);
        
    }

    private function getSleepEffectOnActor(){
        
        $ef = $this->actor->getEffects();
        return null;
        foreach ($ef as $effects) {
            if($ef->status == EffectTypes::SLEEP){
                return $ef;
            }
        }
        return null;
    }
    
    public function process() {

        $sleep = $this->getSleepEffectOnActor();
        if($sleep){
            
            $this->getFightLogObjectct($this->actor->getActorName(), $this->actor, 0, 0,"sleep",$sleep->leftrounds);
        }else{
            $this->doAttack();
        }

       
        $this->processEffectsOnActor();
        
        $this->actor->save();
    }
    
    private function doAttack(){
                if ($this->configuration->type != FightType::AUTO) {
           
            $this->actor->changeMana($this->configuration->manacost);
        }

        switch ($this->configuration->type) {

            case FightType::AUTO:
                $this->processAuto($this->targets[0]);
                break;
            case FightType::HIT:

                $this->processHit($this->targets[0]);
                break;
            case FightType::HITAOE:
                foreach ($this->targets as $t) {
                    $this->processHit($t);
                    $this->isCrit = null;
                }
                break;
            case FightType::HEAL:
                $this->processHeal($this->targets[0]);
                break;
            case FightType::HEALAOE:
                foreach ($this->targets as $t) {

                    $this->isCrit = null;
                    $this->processHeal($t);
                }
                break;
            case FightType::SPELL:
                $this->processSpell($this->targets[0]);
                break;
            case FightType::SPELLAOE:
                foreach ($this->targets as $t) {
                    $this->isCrit = null;
                    $this->processSpell($t);
                }
                break;
            case FightType::HOT:
                $this->processHot($this->targets[0]);
                break;
            case FightType::DOT:
                $this->processDot($this->targets[0]);
                break;
            case FightType::STATUS:
                break;
        }
    }

    private function processEffectsOnActor(){
     
        foreach ($this->actor->getEffects() as $eff) {
            $eff->leftrounds--;
            switch ($eff->status){
                
                case FightType::HOT:
                  
                    $this->actor->changeHp($eff->value*-1);
                    $this->getFightLogObject($this->actor->getActorName(), $this->actor, $eff->value, 0, "phot",$eff->leftrounds,$eff->name);
                    break;
                case FightType::DOT:
                    $this->actor->changeHp($eff->value);
                    $this->getFightLogObject($this->actor->getActorName(), $this->actor, $eff->value, 0, "pdot",$eff->leftrounds,$eff->name);
                    break;
                case FightType::STATUS:
                    
                    break;
            }
        }
        
        $this->actor->removeOldEffects();
    }
    
    public function getFightLogObject($targetsName, $target, $dmg, $dmgR, $type = "dmg",$rounds = 0,$name = null) {

        
        if ($this->configuration->name != null && $name == null) {
            $name = $this->configuration->name;
        }elseif($name == null){
            $name = "Standard";
        }
        
        

        $data = ["actor" => $this->actor->getActorName(),
            "target" => $targetsName,
            "attack" => $name,
            "dmg" => $dmg,
            "crit" => $this->isCrit(),
            "avoid" => $this->isAvoid($target),
            "ai" => $this->actor->getIndex(),
            "ti" => $this->targets[0]->getIndex(),
            "dmgr" => $dmgR,
            "type" => $type,
            "round" => $rounds
        ];
        $v = json_encode($data);


        $m = R::dispense(DBTables::FIGHT_LOG);
        $m->msg = $v;
        $m->ctime = time() + 2;
        $m->actor = $this->actor->getActorId();
        $m->target = $this->targets[0]->getActorId();
        $m->fightId = $this->fightId;
        R::store($m);
    }

}
