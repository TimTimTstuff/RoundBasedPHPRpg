<?php

class Charakter {

    private $charInfo = null;
    private $playerId;
    private $baseStats = null;
    private $equipment = null;
    private $bag = null;
    private $moneyBag = null;
    private $name;
    private $questLog = null;
    private $conditionParser = null;
    
    private $goalActionParser = null;
    /**
     *
     * @var Statistics
     */
    private $statistics;

    
    /**
     *
     * @var ManipulationActionParser
     */
    private $manipulationActionParser;
    
    /**
     *
     * @var PlayerGroup
     */
    private $group;
    
    
    /**
     *
     * @var Context
     */
    private $context;

    private $attacks = [];

    /**
     * 
     * @param Context $context
     */
    public function __construct($context,$userId = null) {
        
        $this->context = $context;
        
        if($userId == null)
        {
            $this->playerId = $context->getUserId();
            $this->charInfo = $this->loadCharakter();
            $this->statistics = $context->getStatistics();
            $this->moneyBag = new MoneyBag($this->charInfo->money,$this->statistics);
            $this->name = $context->getUserName();
            $this->conditionParser = new ConditionParser($context,$this);
            $this->goalActionParser = new QuestGoalActionParser($context,$this);
            $this->statistics = $context->statistics;
            $this->manipulationActionParser = new ManipulationActionParser($context, $this);
        }
        else{
            $this->conditionParser = new ConditionParser($context,$this);
            $this->playerId = $userId;
            $this->charInfo = $this->loadCharakter();
            $u = R::load(DBTables::USER, $userId);
            $this->name = $u->name;
            $this->statistics = $context->statistics;
        }
        
        $this->attacks = json_decode($this->charInfo->attacklist);
    }
    
    public function getPosition(){
        return ["t"=>$this->charInfo->maptype, "id"=>$this->charInfo->mapid];
    }
    
    
    public function setPosition($type, $id){
        $this->charInfo->maptype = $type;
        $this->charInfo->mapid = $id;
        $this->saveChar();
    }
 
    
    public function addStatistics($key,$amount){  
       $this->statistics->add($key, $amount); 
    }

    public function getGroup(){
        
        if($this->group == null){
            
        $this->group = PlayerGroup::loadPlayerGroup($this->context->getUserId(), $this->context->sessionData);
     
       
        }
        $this->context->sessionData->addItem("group", null);
         return $this->group;
    }
    
    public function getId(){
        return $this->playerId;
    }
    
    public function getFightValues(){
       
        
        return GetStatsCalculator::getCalculator($this);
    }
    
    public function getAttackList(){
        return $this->attacks;
    }
    
    /**
     * 
     * @return ConditionParser
     */
    public function getConditionParser(){
        return $this->conditionParser;
    }
    
    /**
     * 
     * @return QuestGoalActionParser
     */
    public function getQuestGoalActionParser(){
        return $this->goalActionParser;
    }
    
    public function getManipulationActionParser(){
        return $this->manipulationActionParser;
    }

    private function loadCharakter() {

        return R::findOne(DBTables::CHAR, " player_id = ? ", [$this->playerId]);
    }

    public function getStats() {
        
        if($this->baseStats == null){
              $this->baseStats = new Stats($this->playerId, $this->getEquip()->getStatsValueArray());
        }
        
        
        return $this->baseStats;
    }
    
    /**
     * 
     * @return CharQuests
     * @throws Missing condition parser
     */
    public function getQuestLog(){
        
        if($this->conditionParser == null){
            throw new Exception("NO CONDITION PARSER SET IN CHARAKTER");
        }
       
        if($this->questLog == null){
            $this->questLog = new CharQuests($this->playerId, $this,$this->context);
        }
        
        return $this->questLog;
    }

    public function getClass(){
        return $this->charInfo->heroClass;
    }
    
    
    /**
     * 
     * @return ItemBag
     */
    public function getBag() {
        if($this->bag == null){
            $this->bag = new ItemBag(BagTypes::Player, $this->playerId);
        }
        return $this->bag;
    }

    /**
     * @return MoneyBag Description
     */
    public function getMoneyBag(){
        return $this->moneyBag;
    }
    
    /**
     * 
     * @return PlayerEquipment
     */
    public function getEquip() {
        
        if($this->equipment == null){
            $this->equipment = new PlayerEquipment($this->playerId);
      
        }
        return $this->equipment;
    }

    public function saveChar() {
        $this->charInfo->money = $this->moneyBag->getMoney();
        R::store($this->charInfo);
    }
    
    public function getLevel(){
        return $this->charInfo->level;
    }
    
    public function getXp(){
        return $this->charInfo->xp;
    }
    
    public function addXp($xp){
        $this->charInfo->xp += $xp;
        $this->statistics->add(StatisticItems::XPEARND, $xp);
    }
    
    public function addHonor($honor){
        $this->charInfo->honor += $honor;
        $this->statistics->add(StatisticItems::EARNDHONOR, $honor);
    }
    
    public function removeXp($xp){
        if($this->getXp() >= $xp){
            $this->charInfo->xp -=$xp;
            
        }   
    }
    
    public function regPlayer($sec){
        
        $fv = $this->getFightValues();
        
        $hp = $sec*$fv->getHpRegPerSec();
        $mana = $sec*$fv->getManaRegPerSec();
        
        $newHp = $this->getCurrentHp()+$hp;
        $newMana = $this->getCurrentMana()+$mana;
        
        if($newHp > $fv->getMaxHp()){
            $newHp = $fv->getMaxHp();
        }
        
        if($newMana > $fv->getMaxMana()){
            $newMana = $fv->getMaxMana();
        }
        
        $this->setHp($newHp);
        $this->setMana($newMana);
        
        
    }
    
    public function dolvlUp(){
        $this->charInfo->level++;
    }
    
    public function getTitle(){
        return $this->charInfo->title;
    }
    
    public function getHonor(){
        return $this->charInfo->honor;
    }

    public function getName(){
        return $this->name;
    }
    
    public function getCurrentMapId(){
        return $this->mapid;
    }
    
    public function getCurrentMapType(){
        return $this->maptype;
    }
    
    public function getCurrentHp(){
        return $this->charInfo->currentHp;
    }
    
    public function getCurrentMana(){
        return $this->charInfo->currentMana;
    }
    
    public function setHp($v){
        
        if($v < 0){$v = 0;}
        if($v > $this->getFightValues()->getMaxHp()){$v = $this->getFightValues()->getMaxHp();}
        
        $this->charInfo->currentHp = $v;
    }
    
    public function setMana($v){
        
        if($v < 0){$v = 0;}
        if($v > $this->getFightValues()->getMaxMana()){$v = $this->getFightValues()->getMaxMana();}
        
        
        $this->charInfo->currentMana = $v;
    }
    
    public static function createNew($playerId) {

        $char = R::dispense(DBTables::CHAR);
        $char->title = "Der Frische";
        $char->money = 100;
        $char->honor = 0;
        $char->playerId = $playerId;
        $char->heroClass = 1;
        $char->currentMana = 0;
        $char->currentHp = 0;
        $char->level = 1;
        $char->xp = 0;
        $char->mapid = 3;
        $char->maptype = MapType::FMAP;
        $char->mappos = "0,0";
        $char->attacklist = "[]";
       

      $char->id =  R::store($char);
        Stats::createNew($playerId);
        ItemBag::createNewBag(BagTypes::Player, $playerId, 5);
        PlayerEquipment::createNew($playerId);
        
    }

}

class Stats {

    private $stats;
    private $playerId;
    private $isLoaded = false;
    private $isChanged = false;

  
    private $equip;

    public function __construct($playerId, $equip) {
        $this->playerId = $playerId;
        $this->equip = $equip;
    }

    public function __get($name) {
        if (!$this->isLoaded) {
            $this->load($this->playerId);
            $this->isLoaded = TRUE;
        }
            $add = 0;
        if ($this->endsWith($name, "Total")){
            $name = str_replace("Total", "", $name);
            if(array_key_exists($name, $this->equip)){
                $add = $this->equip[$name];
            }
        }
            return $this->stats->$name+$add;
    }

    public function __set($name, $value) {
        if (!$this->isLoaded) {
            $this->load($this->playerId);
            $this->isLoaded = TRUE;
        }
        $this->isChanged = true;
        $this->stats->$name = $value;
    }

    public function save() {
        if ($this->isLoaded == true && $this->isChanged == true) {
            R::store($this->stats);
        }
    }

    public function load($playerId) {

        $stat = R::findOne(DBTables::STATS, " player_id = ? and type = ? ", [$playerId, "main"]);
        if ($stat == null) {
            self::createNew($playerId);
            $stat = R::findOne(DBTables::STATS, " player_id = ? and type = ? ", [$playerId, "main"]);
        }
        $this->stats = $stat;
        $this->stats->playerId = $playerId;
    }

    public static function getValueArray($str,$vit,$speed,$arm,$avo,$crit,$int,$wis){
        
        $arr = [
            "strength"=>$str,
            "vitality"=>$vit,
            "speed"=>$speed,
            "armour"=>$arm,
            "avoid" =>$avo,
            "crit" =>$crit,
            "inteligent"=>$int,
            "wisdom"=>$wis
        ];
        
        return $arr;
    }
    
    
    public static function createNew($playerId) {

        $stats = R::dispense(DBTables::STATS);
        $stats->playerId = $playerId;
        $stats->strength = 1;
        $stats->vitality = 1;
        $stats->speed = 1;
        $stats->armour = 0;
        $stats->avoid = 1;
        $stats->crit = 1;
        $stats->inteligent = 1;
        $stats->wisdom = 1;
        $stats->type = "main";
        R::store($stats);
    }
    
    public static function createNewMob($mobId,$str,$vit,$speed,$arm,$avo,$crit,$int,$wis,$type){
        
       $stats = R::dispense(DBTables::STATS);
        $stats->playerId = $mobId;
        $stats->strength = $str;
        $stats->vitality = $vit;
        $stats->speed = $speed;
        $stats->armour = $arm;
        $stats->avoid = $avo;
        $stats->crit = $crit;
        $stats->inteligent = $int;
        $stats->wisdom = $wis;
        $stats->type = $type;
        R::store($stats);
        
    }

    public function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

}

class StatsPriceCalculator{

    public function getStatValueCost($statLvl,$playerLvl){
        
        $statPrice = 15;
        $mul = 4;
        
        return ($statPrice/($playerLvl/$mul)*$statLvl);
    }
    
    public function getLevelCost($level){
        $start = 100;
        return $level*($level+1)*$start;
        
    }
}


