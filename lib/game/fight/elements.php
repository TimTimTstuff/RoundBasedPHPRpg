<?php

class FightInitializer {

    /**
     *
     * @var type 
     */
    private $listLefFighter;
    private $listRightFighter;

    /**
     *
     * @var Fighter[]
     */
    private $listFighters;
    private $activPlayer;
    private $attackOrder;
    private $fightRecord;

    /**
     * 
     * @param Mob[] $mobList
     * @param Context $context
     * @param PlayerGroup $group
     * @param type $userId
     */
    public function __construct($mobList, $context, $group = null, $userId = null) {

        $players = [];
        if ($group != null) {
            foreach ($group->getGroupMember() as $m) {
                $players[] = $m->userid;
            }
        } else {
            $players = [$userId];
        }

        $mobIds = [];
        foreach ($mobList as $mob) {
            $mobIds[] = $mob->getId();
        }



        //create fight record
        $this->createFightRecord();

        //create fighter
        //mob
        $mobFighterList = $this->createMobFighterList($mobList);

        //player
        $playerFighterList = [];
        foreach ($players as $pId) {
            $m = new Charakter($context, $pId);
            $playerFighterList[] = $this->createFighterForPlayer($m, $this->createFightStatsForPlayer($m->getFightValues()), $pId);
        }

        $startValue = 0;
        $startPlayer = 0;
        //store fighter mob
        $this->listRightFighter = [];
        foreach ($mobFighterList as $mob) {
            $f = $this->createFighter($mob, $this->fightRecord->id);
            $this->listRightFighter[] = $f->id;
            if ($mob->fightValues->speed > $startValue) {
                $startValue = $mob->fightValues->speed;
                $startPlayer = $f->id;
            }
        }

        //store fighter player
        $this->listLefFighter = [];
        foreach ($playerFighterList as $p) {
            $f = $this->createFighter($p, $this->fightRecord->id);
            $this->listLefFighter[] = $f->id;
            if ($p->fightValues->speed > $startValue) {
                $startValue = $p->fightValues->speed;
                $startPlayer = $f->id;
            }
        }



        //add fighter to Fight
        $this->saveMember($startValue, $startPlayer);
    }

    private function createFightRecord() {
        $fr = R::dispense(DBTables::FIGHT);
        $fr->moblist = json_encode([]);
        $fr->playerlist = json_encode([]);
        $fr->currentactor = 0;
        $fr->order = json_encode([]);
        $fr->isfinished = 0;
        $fr->speedvalue = 0;
        $fr->lastaction = time();
        $fr->id = R::store($fr);
        $this->fightRecord = $fr;
    }

    private function saveMember($speed, $startPlayer) {
        $this->fightRecord->moblist = json_encode($this->listRightFighter);
        $this->fightRecord->playerlist = json_encode($this->listLefFighter);
        $this->fightRecord->speedvalue = $speed * 8.3;
        $this->fightRecord->currentactor = $startPlayer;
        R::store($this->fightRecord);
    }

    /**
     * 
     * @param Mob[] $mobList
     */
    private function createMobFighterList($mobList) {
        $list = [];
        $id = 1;
        foreach ($mobList as $mob) {

            $f = new Fighter();
            $f->attackList = $mob->getAttackList();
            $f->currentHp = $mob->getFightStats()->maxHp;
            $f->currentMana = $mob->getFightStats()->maxMana;
            $f->fightValues = $mob->getFightStats();
            $f->healthPotions = [0, 0];
            $f->manaPotions = [0, 0];
            $f->name = $id . " " . $mob->getName();
            $f->isPlayer = false;
            $f->id = $mob->getId();
            $f->speedvalue = 0;
            $f->lastaction = time() + 10;
            
            $list[] = $f;
            $id++;
        }

        return $list;
    }

    /**
     * 
     * @param FightValues $stats
     */
    private function createFightStatsForPlayer($stats) {
        $fiStats = new FightStats();

        $fiStats->armoreRed = $stats->getArmorReduce();
        $fiStats->avoid = $stats->getAvoidChance();
        $fiStats->crit = $stats->getCritChance();
        $fiStats->maxDmg = $stats->getMaxDmg();
        $fiStats->maxHp = $stats->getMaxHp();
        $fiStats->maxMana = $stats->getMaxMana();
        $fiStats->minDmg = $stats->getMinDmg();
        $fiStats->speed = $stats->getSpeedPoints();
        $fiStats->manaReg = $stats->getManaRegPerSec();
        $fiStats->aggro = $stats->aggroDiv();
        $fiStats->critMulti = $stats->getCritMult();
        $fiStats->spell = $stats->getSpell();

        return $fiStats;
    }

    /**
     * 
     * @param Charakter $char
     * @param FightStats $fightStats
     */
    private function createFighterForPlayer($char, $fightStats, $uid) {
        $f = new Fighter();
        $f->attackList = $char->getAttackList();
        $f->currentHp = $char->getCurrentHp();
        $f->currentMana = $char->getCurrentMana();
        $f->fightValues = $fightStats;
        $f->healthPotions = [0, 0];
        $f->manaPotions = [0, 0];
        $f->name = $char->getName();
        $f->isPlayer = true;
        $f->id = $uid;
        $f->aggro = 0;
        return $f;
    }

    /**
     * 
     * @param Fighter $fighter
     */
    private function createFighter($fighter, $fid) {
        $fg = R::dispense(DBTables::FIGHT_MEMBER);
        $fg->actorId = $fighter->id;
        $fg->isuser = $fighter->isPlayer;
        $fg->stats = json_encode($fighter->fightValues);
        $fg->currhp = $fighter->currentHp;
        $fg->currmana = $fighter->currentMana;
        $fg->attacks = json_encode($fighter->attackList);
        $fg->name = $fighter->name;
        $fg->fightId = $fid;
        $fg->speedvalue = 0;
        $fg->aggro = 0;
        $fg->effects = json_encode([]);
        $fg->id = R::store($fg);

        return $fg;
    }

    public function getFightId() {
        return $this->fightRecord->id;
    }

    public static function searchForActivFight($userId) {

        $id = R::getRow("select fight.* from fight  join fightmember on fight.id = fightmember.fight_id where fightmember.actor_id = ? and fightmember.isuser = 1 and fight.isfinished = 0", [$userId]);

        if ($id == null)
            return null;



        return $id["id"];
    }

}

class FightProcessor {

    private $fightData;
    private $userId;
    private $fightId;

    /**
     *
     * @var FightMember[]
     */
    private $players;

    /**
     *
     * @var FightMember[]
     */
    private $mobs;

    /**
     *
     * @var Context
     */
    private $context;

    /**
     *
     * @var ManipulationActionParser
     */
    private $actionParser;

    public function __construct($fightId, $userId, $context) {
        $this->context = $context;
        $this->userId = $userId;
        $this->fightId = $fightId;

        $this->loadData();
    }

    private function loadData() {
        $this->fightData = R::load(DBTables::FIGHT, $this->fightId);

        $memberList = R::find(DBTables::FIGHT_MEMBER, " where fight_id = ?", [$this->fightId]);

        $mindex = 1;
        $pindex = 1;
        
        foreach ($memberList as $member) {
            if ($member->isuser == 1) {
              
                
                $this->players[] = new FightMember($member, "p" . $pindex);
                $pindex++;
            } else {
                $this->mobs[] = new FightMember($member, "m" . $mindex);
                $mindex++;
            }
        }
    }

    /**
     * 
     * @return FightMember[]
     */
    public function getMobs() {
        return $this->mobs;
    }

    public function findPlayerByRealId($id){
        
        foreach ($this->players as $value) {
            
            if($value->getRealId() == $id)
            {
                return $value;
            }
            
          
        }
          return null;
    }
    
    /**
     * 
     * @return FightMember[]
     */
    public function getPlayer() {
        return $this->players;
    }

    /**
     * 
     * @return FightMember
     */
    public function getCurrentActor() {
        return $this->fightData->currentactor;
    }

    public function getMemberById($id) {
        foreach ($this->players as $m) {
            if ($m->getActorId() == $id) {
                return $m;
            }
        }
        foreach ($this->mobs as $m) {
            if ($m->getActorId() == $id) {
                return $m;
            }
        }

        return null;
    }

    public function isTimeOut() {

        return $this->fightData->lastaction < time() - 30;
    }

    private function calculateSpeed($member, $speedvalue) {

        $mSpeedValue = $member->getSpeedValue() + (10 - ($member->getSpeed() / 100));

        $member->setSpeedValue($mSpeedValue);
    }

    public function skipRound() {
        $member = $this->getMemberById($this->getCurrentActor());

        $speedvalue = $this->fightData->speedvalue;

        $this->calculateSpeed($member, $speedvalue);

        $this->fightData->lastaction = time();

        $member->save();
        $this->setNextPlayer();
        R::store($this->fightData);
    }

 

    public function isLeftGroup($mId) {
        foreach ($this->players as $value) {
            if ($value->getActorId() == $mId) {
                return true;
            }
        }

        return false;
    }

    public function getTargets($targetId, $isMulit = false) {
        $targets = [];
     
        
        if ($isMulit) {
            if ($this->isLeftGroup($this->getCurrentActor())) {
                $targets = $this->mobs;
            } else {
                $targets = $this->players;
            }
        } else {
         
            $targets[] = $this->getMemberById($targetId);
        }

        return $targets;
    }

    
    
    /**
     * 
     * @param FightAction $action
     */
    public function doFightActionAdvanced($action) {
        $fid = 0;
        
        $member = $this->getMemberById($this->getCurrentActor());
        if($action->id != 0 && $member->getAttack($action->id) != null){
            
            $fid = $member->getAttack($action->id);
        }
        
        
        
        $skillAttack = new SkillAttackProcessor($fid);
        
        if($skillAttack->getConfig()->manacost > $member->getCurrMana()){
            return false;
        }
        
        
        $targets = $this->getTargets($action->target, $skillAttack->getTargetType() == 1);
     
        $this->fightData->lastaction = time();
        $speedvalue = $this->fightData->speedvalue;
        $this->calculateSpeed($member, $speedvalue);

        
        
        
        $pr = new AttackProcessorAdvanced($member, $targets, $skillAttack->getConfig(),$this->fightId);
       
        $pr->process();
        
        $member->regMana();
        $member->save();
        foreach ($targets as $t) {
            $t->save();
            $this->checkFaintedMob($t);
        }
        R::store($this->fightData);
        
        $this->setNextPlayer();
        return true;
    }

    private function checkFaintedMob($target) {

        if ($target->isFainted()) {

            if (!$target->isPlayer()) {
                $mob = new Mob($target->getRealId(), null, null);

                $fa = [];
                foreach ($this->players as $player) {
                    $fakeContext = new Context(true, $player->getRealId());
                    $char = new Charakter($fakeContext, $player->getRealId());
                    $fa[] = new ManipulationActionParser($fakeContext, $char);
                    $char->addStatistics(StatisticItems::MOBSKILLED, 1);
                }

                $mob->onDefeat($fa);
            } else {
                $fakeContext = new Context(true, $target->getRealId());
                $char = new Charakter($fakeContext, $target->getRealId());
                $char->addStatistics(StatisticItems::PLAYERFAINTED, 1);
            }
        }
    }

    public function processNpcFight() {

        $targetLis = [];

        if ($this->fightData->lastaction > time() - 2)
            return;


        foreach ($this->players as $value) {

            if (!$value->isFainted()) {
                $targetLis[] = $value;
            }
        }
        if (count($targetLis) == 0)
            return;
        
        
        
        $mId = $this->getMemberById($this->getCurrentActor())->getRealId();
        $mob = new Mob($mId, null,null);
        $ki = NpcKiFactory::getKi($mob->getKiName(), $this->mobs, $targetLis,$this->getMemberById($this->getCurrentActor()));
        $this->doFightActionAdvanced($ki->getAction());
    }

    public function isNextNpc() {
        foreach ($this->players as $p) {
            if ($p->getActorId() == $this->getCurrentActor() && $p->isPlayer()) {
                return false;
            }
        }

        return true;
    }

    private function writeFightLog($msg, $actorId, $targetId) {

        $m = R::dispense(DBTables::FIGHT_LOG);
        $m->msg = $msg;
        $m->ctime = time() + 2;
        $m->actor = $actorId;
        $m->target = $targetId;
        $m->fightId = $this->fightId;
        R::store($m);
    }

    private function setNextPlayer() {
        $nexM = R::findOne(DBTables::FIGHT_MEMBER, " fight_id = ? and currhp > 0 order by speedvalue ", [$this->fightId]);

        $this->fightData->currentactor = $nexM->id;
        R::store($this->fightData);
    }

    public function isFightLost() {

        foreach ($this->players as $p) {
            if (!$p->isFainted()) {
                return false;
            }
        }
        return true;
    }

    private function createLootList() {
        $loot = [];
        foreach ($this->mobs as $mob) {
            $m = new Mob($mob->getRealId(), null, $this->actionParser);
            $loot = array_merge($loot, $m->getRandomLoot());
        }

        return $loot;
    }

    public function finishFight() {

        if ($this->fightData->isfinished == 1)
            return;
        $this->fightData->isfinished = true;

        R::store($this->fightData);

        if ($this->isFightLost()) {
            $temp = RenderMain::loadGameTemplate("fight_log");
            $temp = str_replace("{{head}}", "Kampf verloren. <a href='javascript:location.reload();'>Verlassen</a>", $temp);
            $this->writeFightLog(str_replace("{{extra}}", "", $temp), 0, 0);
            $this->fightData->isfinished = true;

            R::store($this->fightData);

            foreach ($this->players as $p) {
                $fc = new Context(true, $p->getRealId());
                $ch = new Charakter($fc);
                $ch->setHp($p->getCurrentHp());
                $ch->setMana($p->getCurrMana());
                $ch->saveChar();
            }
            return;
        }

        $loot = $this->createLootList();


        $gold = 0;
        $xp = 0;
        $items = [];
        $ehre = 0;


        foreach ($loot as $l) {

            switch ($l[0]) {
                case "gold":
                    $gold += rand($l[1][0], $l[1][1]);
                    break;
                case "honor":
                    $ehre += rand($l[1][0], $l[1][1]);
                    break;
                case "item":
                    $items[] = $l[1];
                    break;
                case "xp":
                    $xp += rand($l[1][0], $l[1][1]);
                    break;
            }
        }


        $playerCount = count($this->players);

        $itemIndex = 0;

        $itArr = [];

        foreach ($items as $it) {
            $r = rand(0, $playerCount - 1);
            $itArr[$r][] = $it;
        }

        $index = 0;
        foreach ($this->players as $p) {
            $fc = new Context(true, $p->getRealId());
            $ch = new Charakter($fc);
            $ch->setHp($p->getCurrentHp());
            $ch->setMana($p->getCurrMana());

            $textLog = "";
            if ($gold > 0) {
                $ch->getMoneyBag()->addMoney(ceil($gold / $playerCount));
                $textLog .= "Gold: " . ceil($gold / $playerCount) . "<br/>";
            }

            if ($xp > 0) {
                $ch->addXp(ceil($xp / $playerCount));
                $textLog .= "Xp: " . ceil($xp / $playerCount) . "<br/>";
            }

            if ($ehre > 0) {
                $ch->addHonor(ceil($ehre / $playerCount));
                $textLog .= "Ehre: " . ceil($ehre / $playerCount) . "<br/>";
            }

            if (array_key_exists($index, $itArr)) {

                foreach ($itArr[$index] as $item) {
                    $it = new Item($item[0]);
                    $textLog .= "Item  $item[1] x <span class='rare_" . $it->rarity . "'>" . $it->name . "</span> <small>[ " . $p->getActorName() . "]</small>";
                    $ch->getBag()->addItem($item[0], $item[1]);
                    $ch->getBag()->save();
                }
            }
            $index++;
            $ch->saveChar();

            $temp = RenderMain::loadGameTemplate("fight_log");
            $temp = str_replace("{{head}}", $p->getActorName() . " erhält: <br/>" . $textLog, $temp);
            $this->writeFightLog(str_replace("{{extra}}", "", $temp), $p->getActorId(), 0);
        }
        $temp = RenderMain::loadGameTemplate("fight_log");
        $temp = str_replace("{{head}}", "<a href='javascript:location.reload();'>Verlassen</a>", $temp);
        $this->writeFightLog(str_replace("{{extra}}", "", $temp), $p->getActorId(), 0);
        $this->fightData->isfinished = true;
        R::store($this->fightData);
    }

    public function isFightWind() {

        foreach ($this->mobs as $p) {
            if (!$p->isFainted()) {
                return false;
            }
        }
        return true;
    }

    public function getFightLog() {

        $msgs = R::find(DBTables::FIGHT_LOG, " fight_id = ? order by id desc limit 30", [$this->fightId]);

        rsort($msgs);

        return $msgs;
    }

}

class FightAction {

    public $type; //attack,potion,wait,leave
    public $id; //att id, potionid,
    public $target; //player

}

class Fighter {

    public $name;
    public $isPlayer;
    public $id;
    public $fightValues;
    public $currentMana;
    public $currentHp;
    public $attackList;
    public $manaPotions;
    public $healthPotions;

}

class FightMember {

    /**
     *
     * @var Fighter
     */
    private $data;
    /**
     *
     * @var FightStats
     */
    private $stats;
    /**
     *
     * @var Effects[]
     */
    private $effects;
    private $index;
    private $attacks;

    public function __construct($data, $index) {
        $this->data = $data;
        $this->index = $index;
        $this->stats = json_decode($data->stats);
        $this->effects = json_decode($data->effects);
        $this->attacks = json_decode($data->attacks);
    }

    public function getIndex() {
        return $this->index;
    }

    public function getRealId() {
        return $this->data->actorId;
    }

    public function getActorId() {
        return $this->data->id;
    }

    public function getActorName() {
        return $this->data->name;
    }

    public function getMaxHp() {
        return $this->stats->maxHp;
    }

    public function getMaxMana() {
        return $this->stats->maxMana;
    }

    public function getCurrMana() {
        return $this->data->currmana;
    }

    public function getCurrentHp() {
        return $this->data->currhp;
    }

    public function addAggro($value) {
        $this->data->aggro += $value;
    }

    public function getAggro() {
        return $this->data->aggro;
    }

    public function isPlayerId($id) {
        return $this->data->actorId == $id && $this->data->isuser == 1;
    }

    public function getAttackList() {
        return $this->data->attacks;
    }
    
    public function getEffects(){
        return $this->effects;
    }
    
    public function addEffect($effect){
        
        foreach ($this->effects as $e) {
            if($e->id == $effect->id){
                $e->leftrounds = $effect->leftrounds;
                return;
            }
        }
        
        $this->effects[] = $effect;
    }
    
    public function removeOldEffects(){
        $newE = [];
        foreach ($this->effects as $e) {
            if($e->leftrounds > 0){
                $newE[] = $e;
            }
        }
        
        $this->effects = $newE;
    }

    /**
     * 
     * @return FightStats
     */
    public function getStats() {
        return $this->stats;
    }

    public function isFainted() {
        return $this->getCurrentHp() <= 0;
    }

    public function getSpeedValue() {
        return $this->data->speedvalue;
    }

    public function getSpeed() {
        return $this->stats->speed;
    }

    public function setSpeedValue($v) {
        $this->data->speedvalue = $v;
    }

    public function save() {
        $this->data->effects = json_encode($this->effects);
        R::store($this->data);
    }

    public function changeHp($value) {
        $newVal = $this->getCurrentHp() - $value;

        if ($newVal < 0) {
            $newVal = 0;
        }

        if ($newVal > $this->getMaxHp()) {
            $newVal = $this->getMaxHp();
        }

        $this->data->currhp = $newVal;
    }

    public function isPlayer() {
        return $this->data->isuser == 1;
    }

    public function changeMana($value) {

        $newVal = $this->getCurrMana() - $value;

        if ($newVal < 0) {
            $newVal = 0;
        }

        if ($newVal > $this->getMaxMana()) {
            $newVal = $this->getMaxMana();
        }


        $this->data->currmana = $newVal;
    }

    public function regMana() {

        $this->changeMana($this->stats->manaReg * -1);
    }
    
    public function getAttack($slot){

        if(count($this->attacks) >= $slot){
            return $this->attacks[$slot-1];
        }
        
        return null;
    }

    
}
