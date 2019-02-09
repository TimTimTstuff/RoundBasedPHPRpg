<?php


abstract class GetStatsCalculator{
    
    /**
     * 
     * @param Charakter $char
     */
    public static function getCalculator($char){
        $class = $char->getClass();
        if(isset($_GET["c"])){
            $class = $_GET["c"];
        }
        switch ($class){
            case 0:
                return new MilizValues($char->getStats(), $char);
            case 1:
                return new ProtectionWarriorValues($char->getStats(), $char);
            case 2: //berserker
                return new BerserkerValues($char->getStats(),$char);
            case 3: //spellcaster
                return new SpellCasterValues($char->getStats(), $char);
            case 4: //Pries
                return new PriestValues($char->getStats(), $char);
            case 5: //Bowsman
                return new BowsmanValues($char->getStats(), $char);
            case 6: //assasine
                return new AssasineValues($char->getStats(),$char);
        }
        
    }
    
}


abstract class FightValues {
    /**
     *
     * @var Stats
     */
    protected $stats;
    
    /**
     *
     * @var Charakter 
     */
    protected $char;

    public function __construct($userStats,$char) {
        $this->stats = $userStats;
        $this->char = $char;
    }
    
    public abstract function getCritMult();
    public abstract function getMaxHp();
    public abstract function getMaxMana();
    public abstract function getCritChance();
    public abstract function getAvoidChance();
    public abstract function getSpeedPoints();
    public abstract function getMaxDmg();
    public abstract function getMinDmg();
    public abstract function getArmorReduce();
    public abstract function getHpRegPerSec();
    public abstract function getManaRegPerSec();
    public abstract function getHp();
    public abstract function getMana();
    public abstract function aggroDiv();
    public abstract function getSpell();

}

class BerserkerValues extends FightValues{
    
    public function getArmorReduce() {
        $armor = $this->stats->armourTotal;
        $str = $this->stats->strengthTotal;
        $lvl = $this->char->getLevel();
        $red = (($armor+($str*2))/$lvl)/5;
        
        if($red>75)return 75;
        
        return ceil($red);
        
        
    }

    public function getAvoidChance() {
        $level = $this->char->getLevel();
        $aus = $this->stats->avoidTotal;
        $tem = $this->stats->speedTotal;
        
        $ausw = ($aus*2+($tem/2))/($level);
        
        if($ausw>75)return 75;
        
        return ceil($ausw);
        
    }

    public function getCritChance() {
        $level = $this->char->getLevel();
        $cr = $this->stats->critTotal;
        $tem = $this->stats->speedTotal;
        
        $crit = (($cr*2)+$tem)/($level);
        
        if($crit>75)return 75;
        
        return ceil($crit);
        
    }

    public function getHpRegPerSec() {
        $ausd = $this->stats->vitalityTotal;
        $wis = $this->stats->wisdomTotal;
        
        $reg = (($ausd*2)+($wis/5))/5;
        
        return ceil($reg);
    }

    public function getManaRegPerSec() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $reg = (($wis)+(($wis+$int)/10))/10;
        
        return ceil($reg);
    }

    public function getMaxDmg() {
      $w = $this->char->getEquip()->wapon;
      
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->maxdmg;}
      
      $dmg = $this->stats->strengthTotal*3+$addDmg;
      
      return ceil($dmg+($dmg/10));
      
    }

    public function getMaxHp() {
        $str = $this->stats->strengthTotal;
        $ausd = $this->stats->vitalityTotal;
        
        $hp = $ausd*12+((($str/10)+1)*$ausd);
        return ceil($hp);
        
    }

    public function getMaxMana() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $mana = ($int*5)*(1+($wis/100));
        
        return ceil($mana);
        
    }

    public function getMinDmg() {
        
         $w = $this->char->getEquip()->wapon;
      
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->mindmg;}
      $dmg = $this->stats->strengthTotal*3+$addDmg;
      
      return ceil($dmg-($dmg/10));  
    }

    public function getSpeedPoints() {
        $speed = $this->stats->speed;
        
        return ceil($speed);
    }

    public function getHp() {
        return $this->char->getCurrentHp();
    }

    public function getMana() {
        return $this->char->getCurrentMana();
    }

    public function aggroDiv() {
        return 2;
    }

    public function getCritMult() {
        return 1.5;
    }

    public function getSpell() {
        
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        return ceil($int+($wis/3));
        
    }

}

class MilizValues extends FightValues{
    
    public function getArmorReduce() {
        
        $rValue = ceil($this->stats->armourTotal/(1+($this->char->getLevel()/2)));
        $pVal = ceil($rValue/9);
        
        if($pVal > 50){
            return 50;
        }
        return $pVal;
        
    }

    public function getAvoidChance() {
        $level = $this->char->getLevel();
        $aus = $this->stats->avoidTotal;
       
        
        $ausw = $aus/(1+($level/2));
        
        if($ausw>75)return 75;
        
        return ceil($ausw);
        
    }

    public function getCritChance() {
        $level = $this->char->getLevel();
        $cr = $this->stats->critTotal;
       
        $crit = $cr/(1+($level/2));
        
        if($crit>75)return 75;
        
        return ceil($crit);
        
    }

    public function getHpRegPerSec() {
        $ausd = $this->stats->vitalityTotal;
        $wis = $this->stats->wisdomTotal;
        $level = $this->char->getLevel();
        $reg = ($ausd/($level*6))+($wis*2);
        
        return ceil($reg);
    }

    public function getManaRegPerSec() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $reg = (($int)+($wis/10))/10;
        
        return ceil($reg);
    }

    public function getMaxDmg() {
      $w = $this->char->getEquip()->wapon;
      
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->maxdmg;}
      
      $dmg = $this->stats->strengthTotal*1.5+$addDmg;
      
      return ceil($dmg+($dmg/10));
      
    }

    public function getMaxHp() {
        
        $ausd = $this->stats->vitalityTotal;
      
        $hp = $ausd*12;
        return ceil($hp);
        
    }

    public function getMaxMana() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $mana = ($int*5);
        
        return ceil($mana);
        
    }

    public function getMinDmg() {
        
         $w = $this->char->getEquip()->wapon;
      
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->mindmg;}
      $dmg = $this->stats->strengthTotal*1.5+$addDmg;
      
      return ceil($dmg-($dmg/10));  
    }

    public function getSpeedPoints() {
        
        $speed = $this->stats->speed;
        
        return ceil($speed);
    }

    public function getHp() {
        return $this->char->getCurrentHp();
    }

    public function getMana() {
        return $this->char->getCurrentMana();
    }

    public function aggroDiv() {
        return 2.5;
    }

    public function getCritMult() {
        return 1.3;
    }
 public function getSpell() {
        
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        return ceil($int+$wis);
        
    }
}

class ProtectionWarriorValues extends FightValues{
    
    public function getArmorReduce() {
        $armor = $this->stats->armourTotal;
        $vit = $this->stats->vitalityTotal;
        $lvl = $this->char->getLevel();
        $red = ($armor+($vit*1.1))/(1+($lvl*1.7));
        
        if($red>75)return 75;
        
        return ceil($red);
        
        
    }

    public function getAvoidChance() {
        $level = $this->char->getLevel();
        $aus = $this->stats->avoidTotal;
        $tem = $this->stats->speedTotal;
        
        $ausw = ($aus*2.8+($tem/1.1))/(1+($level/2));
        
        if($ausw>75)return 75;
        
        return ceil($ausw);
        
    }

    public function getCritChance() {
        $level = $this->char->getLevel();
        $cr = $this->stats->critTotal;
        $tem = $this->stats->speedTotal;
        
        $crit = (($cr*2.2)+($tem/2))/(1+($level/1.4));
        
        if($crit>75)return 75;
        
        return ceil($crit);
        
    }

    public function getHpRegPerSec() {
        $ausd = $this->stats->vitalityTotal;
        $wis = $this->stats->wisdomTotal;
        
        $reg = (($ausd*2)+($wis/5))/2.5;
        
        return ceil($reg);
    }

    public function getManaRegPerSec() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $reg = (($int/2)+($wis/10))/4;
        
        return ceil($reg);
    }

    public function getMaxDmg() {
      $w = $this->char->getEquip()->wapon;
      
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->maxdmg;}
      
      $dmg = $this->stats->strengthTotal*2.5+$addDmg;
      
      return ceil($dmg+($dmg/12));
      
    }

    public function getMaxHp() {
        $str = $this->stats->strengthTotal;
        $ausd = $this->stats->vitalityTotal;
        
        $hp = $ausd*14+((($str/5)+1)*($ausd*1.2));
        return ceil($hp);
        
    }

    public function getMaxMana() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $mana = ($int*5)*(1+($wis/100));
        
        return ceil($mana);
        
    }

    public function getMinDmg() {
        
         $w = $this->char->getEquip()->wapon;
      
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->mindmg;}
      $dmg = $this->stats->strengthTotal*2.5+$addDmg;
      
      return ceil($dmg-($dmg/12));  
    }

    public function getSpeedPoints() {
        $speed = $this->stats->speed*1.2;
        
        return ceil($speed);
    }

    public function getHp() {
        return $this->char->getCurrentHp();
    }

    public function getMana() {
        return $this->char->getCurrentMana();
    }

    public function aggroDiv() {
        return 1.1;
    }

    public function getCritMult() {
        return 3.5;
    }
 public function getSpell() {
        
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        return ceil($int*1.1+$wis);
        
    }
}

class SpellCasterValues extends FightValues{
    
    public function getArmorReduce() {
        $armor = $this->stats->armourTotal;
        $vit = $this->stats->vitalityTotal;
        $lvl = $this->char->getLevel();
        $red = ($armor/10)/(1+($lvl*2));
        
        if($red>40)return 40;
        
        return ceil($red);
        
        
    }

    public function getAvoidChance() {
        $level = $this->char->getLevel();
        $aus = $this->stats->avoidTotal;
        $tem = $this->stats->speedTotal;
        
        $ausw = ($aus*1.2+($tem/3))/(1+($level/1.5));
        
        if($ausw>75)return 75;
        
        return ceil($ausw);
        
    }

    public function getCritChance() {
        $level = $this->char->getLevel();
        $cr = $this->stats->critTotal;
        $tem = $this->stats->speedTotal;
        
        $crit = (($cr*3)+($tem/2))/(1+($level/2));
        
        if($crit>75)return 75;
        
        return ceil($crit);
        
    }

    public function getHpRegPerSec() {
        $ausd = $this->stats->vitalityTotal;
        $wis = $this->stats->wisdomTotal;
        
        $reg = (($ausd*2)+($wis/5))/3;
        
        return ceil($reg);
    }

    public function getManaRegPerSec() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $reg = (($int/4)+($wis/4))/2;
        
        return ceil($reg);
    }

    public function getMaxDmg() {
      $w = $this->char->getEquip()->wapon;
      $int = $this->stats->inteligentTotal;
      $wis = $this->stats->wisdomTotal;
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->maxdmg;}
      
       $dmg = ($int+($wis/5))*1;
      
      return ceil($dmg+($dmg/6));
      
    }

    public function getMaxHp() {
        
        $ausd = $this->stats->vitalityTotal;
        $wis = $this->stats->wisdomTotal;
        $hp = $ausd*7+((($wis/5)+1)*($ausd*1.1));
        return ceil($hp);
        
    }

    public function getMaxMana() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $mana = ($wis*4)*(1+($int/100));
        
        return ceil($mana);
        
    }

    public function getMinDmg() {
        
      $w = $this->char->getEquip()->wapon;
      $int = $this->stats->inteligentTotal;
      $wis = $this->stats->wisdomTotal;
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->maxdmg;}
      
      $dmg = ($int+($wis/5))*1;
      
      return ceil($dmg-($dmg/6));  
    }

    public function getSpeedPoints() {
        $speed = $this->stats->speed*0.5;
        
        return ceil($speed);
    }

    public function getHp() {
        return $this->char->getCurrentHp();
    }

    public function getMana() {
        return $this->char->getCurrentMana();
    }

    public function aggroDiv() {
        return 2.2;
    }

    public function getCritMult() {
        return 2.5;
    }
 public function getSpell() {
        
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        return ceil(($int*1.2)*(1+($wis/100)));
        
    }
}

class PriestValues extends FightValues{
    
    public function getArmorReduce() {
        $armor = $this->stats->armourTotal;
        $vit = $this->stats->vitalityTotal;
        $lvl = $this->char->getLevel();
        $red = ($armor/8)/(1+($lvl*2));
        
        if($red>55)return 55;
        
        return ceil($red);
        
        
    }

    public function getAvoidChance() {
        $level = $this->char->getLevel();
        $aus = $this->stats->avoidTotal;
        $tem = $this->stats->speedTotal;
        
        $ausw = ($aus*1.2+($tem/3))/(1+($level/1.5));
        
        if($ausw>75)return 75;
        
        return ceil($ausw);
        
    }

    public function getCritChance() {
        $level = $this->char->getLevel();
        $cr = $this->stats->critTotal;
        $tem = $this->stats->speedTotal;
        
        $crit = (($cr*2)+($tem/3))/(1+($level/2));
        
        if($crit>75)return 75;
        
        return ceil($crit);
        
    }

    public function getHpRegPerSec() {
        $ausd = $this->stats->vitalityTotal;
        $wis = $this->stats->wisdomTotal;
        
        $reg = (($ausd*3)+($wis/4))/2.8;
        
        return ceil($reg);
    }

    public function getManaRegPerSec() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $reg = (($int/4)+($wis/4))/1.8;
        
        return ceil($reg);
    }

    public function getMaxDmg() {
      $w = $this->char->getEquip()->wapon;
      $int = $this->stats->inteligentTotal;
      $wis = $this->stats->wisdomTotal;
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->maxdmg;}
      
       $dmg = ($int+($wis/9))*1;
      
      return ceil($dmg+($dmg/12));
      
    }

    public function getMaxHp() {
        
        $ausd = $this->stats->vitalityTotal;
        $wis = $this->stats->wisdomTotal;
        $hp = $ausd*8+((($wis/4)+1)*($ausd*1.1));
        return ceil($hp);
        
    }

    public function getMaxMana() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $mana = ($wis*3)*(1+($int/100));
        
        return ceil($mana);
        
    }

    public function getMinDmg() {
        
      $w = $this->char->getEquip()->wapon;
      $int = $this->stats->inteligentTotal;
      $wis = $this->stats->wisdomTotal;
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->maxdmg;}
      
       $dmg = ($int+($wis/9))*1;
      
      
      return ceil($dmg-($dmg/12));  
    }

    public function getSpeedPoints() {
        $speed = $this->stats->speed*0.5;
        
        return ceil($speed);
    }

    public function getHp() {
        return $this->char->getCurrentHp();
    }

    public function getMana() {
        return $this->char->getCurrentMana();
    }

    public function aggroDiv() {
        return 2.4;
    }

    public function getCritMult() {
        return 1.4;
    }

    public function getSpell() {
        
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        return ceil(($int*1.3)*(1+($wis/80)));
        
    }
}

class BowsmanValues extends FightValues{
    
    public function getArmorReduce() {
        $armor = $this->stats->armourTotal;
        $vit = $this->stats->vitalityTotal;
        $lvl = $this->char->getLevel();
        $red = ($armor/4)/(1+($lvl*2));
        
        if($red>35)return 35;
        
        return ceil($red);
        
        
    }

    public function getAvoidChance() {
        $level = $this->char->getLevel();
        $aus = $this->stats->avoidTotal;
        $tem = $this->stats->speedTotal;
        
        $ausw = ($aus*3.7+($tem/2))/(1+($level/1.5));
        
        if($ausw>75)return 75;
        
        return ceil($ausw);
        
    }

    public function getCritChance() {
        $level = $this->char->getLevel();
        $cr = $this->stats->critTotal;
        $tem = $this->stats->speedTotal;
        
        $crit = (($cr*3.5)+($tem/2))/(1+($level/2));
        
        if($crit>75)return 75;
        
        return ceil($crit);
        
    }

    public function getHpRegPerSec() {
        $ausd = $this->stats->vitalityTotal;
        $wis = $this->stats->wisdomTotal;
        
        $reg = (($ausd*3)+($wis/4))/6;
        
        return ceil($reg);
    }

    public function getManaRegPerSec() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $reg = (($int/2.1)+($wis/1.1))/6;
        
        return ceil($reg);
    }

      public function getMaxDmg() {
      $w = $this->char->getEquip()->wapon;
      $level = $this->char->getLevel();
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->maxdmg;}
      $mult = ($this->stats->speedTotal/$level)*10;
      $dmg = ($this->stats->strengthTotal*2+$addDmg);
      $dmg = $dmg+($dmg/100*$mult);
      return ceil($dmg+($dmg/7));
      
    }

    public function getMaxHp() {
        $str = $this->stats->strengthTotal;
        $ausd = $this->stats->vitalityTotal;
        
        $hp = $ausd*10+((($str/10)+1)*$ausd);
        return ceil($hp);
        
    }

    public function getMaxMana() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $mana = ($int*5)*(1+($wis/100));
        
        return ceil($mana);
        
    }

    public function getMinDmg() {
        
         $w = $this->char->getEquip()->wapon;
      $level = $this->char->getLevel();
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->maxdmg;}
      $mult = ($this->stats->speedTotal/$level)*10;
      $dmg = ($this->stats->strengthTotal*2+$addDmg);
      $dmg = $dmg+($dmg/100*$mult);
      
      return ceil($dmg-($dmg/7));  
    }


    public function getSpeedPoints() {
        $speed = $this->stats->speed*2.5;
        
        return ceil($speed);
    }

    public function getHp() {
        return $this->char->getCurrentHp();
    }

    public function getMana() {
        return $this->char->getCurrentMana();
    }

    public function aggroDiv() {
        return 1.9;
    }

    public function getCritMult() {
        return 2.2;
    }

    public function getSpell() {
        
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        return ceil(($int*1)*(1+($wis/100)));
        
    }
}

class AssasineValues extends FightValues{
    
    public function getArmorReduce() {
        $armor = $this->stats->armourTotal;
        $vit = $this->stats->vitalityTotal;
        $lvl = $this->char->getLevel();
        $red = ($armor/11)/(1+($lvl*2));
        
        if($red>35)return 35;
        
        return ceil($red);
        
        
    }

    public function getAvoidChance() {
        $level = $this->char->getLevel();
        $aus = $this->stats->avoidTotal;
        $tem = $this->stats->speedTotal;
        
        $ausw = ($aus*5+($tem/2))/(1+($level/1.5));
        
        if($ausw>75)return 75;
        
        return ceil($ausw);
        
    }

    public function getCritChance() {
        $level = $this->char->getLevel();
        $cr = $this->stats->critTotal;
        $tem = $this->stats->speedTotal;
        
        $crit = ($cr*5.5)/(1+($level/2));
        
        if($crit>80)return 80;
        
        return ceil($crit);
        
    }

    public function getHpRegPerSec() {
        $ausd = $this->stats->vitalityTotal;
        $wis = $this->stats->wisdomTotal;
        
        $reg = (($ausd*2)+($wis/4))/6;
        
        return ceil($reg);
    }

    public function getManaRegPerSec() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $reg = (($int/1.1)+($wis/1.1))/6;
        
        return ceil($reg);
    }

      public function getMaxDmg() {
      $w = $this->char->getEquip()->wapon;
      $level = $this->char->getLevel();
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->maxdmg;}
      $mult = ($this->stats->speedTotal/$level)*5;
      $dmg = ($this->stats->strengthTotal*2.2+$addDmg);
      $dmg = $dmg+($dmg/100*$mult);
      return ceil($dmg+($dmg/7));
      
    }

    public function getMaxHp() {
        $str = $this->stats->strengthTotal;
        $ausd = $this->stats->vitalityTotal;
        
        $hp = $ausd*11+((($str/8)+1)*$ausd);
        return ceil($hp);
        
    }

    public function getMaxMana() {
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        $mana = ($int*3)*(1+($wis/100));
        
        return ceil($mana);
        
    }

    public function getMinDmg() {
        
         $w = $this->char->getEquip()->wapon;
      $level = $this->char->getLevel();
      $addDmg = 0;
     
      if($w != null){$addDmg = $w->stat->maxdmg;}
           $mult = ($this->stats->speedTotal/$level)*5;
      $dmg = ($this->stats->strengthTotal*2.2+$addDmg);
      $dmg = $dmg+($dmg/100*$mult);
      
      return ceil($dmg-($dmg/7));  
    }


    public function getSpeedPoints() {
        $speed = $this->stats->speed*1.5;
        
        return ceil($speed);
    }

    public function getHp() {
        return $this->char->getCurrentHp();
    }

    public function getMana() {
        return $this->char->getCurrentMana();
    }

    public function aggroDiv() {
        return 3;
    }

    public function getCritMult() {
     return 3.6;   
    }
    
    public function getSpell() {
        
        $int = $this->stats->inteligentTotal;
        $wis = $this->stats->wisdomTotal;
        
        return ceil(($int*1)*(1+($wis/100)));
        
    }

}


