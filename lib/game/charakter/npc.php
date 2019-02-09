<?php

class Npc{
    
    public $npcData;
    public $npcId;
    public $text;
    public $condition;
    public $randomText;
    public $conditionText;
    
    /**
     *
     * @var ConditionParser
     */
    private $condtitionParser;
    
    public function __construct($npcId,$conditionParser) {
        $this->npcId = $npcId;
        $this->condtitionParser = $conditionParser; 
        $this->load();
        
    
        
    }
    
    
    public function load(){
        $this->npcData = R::load(DBTables::NPC, $this->npcId);
        $this->condition = json_decode($this->npcData->conditions,true);
        
        $this->text = json_decode($this->npcData->randomtext,true);
        foreach ($this->text as $key => $value) {
            if($key[0] == "r" and is_numeric($key[1])){
                $this->randomText[] = $value;
            }else{
                $this->conditionText[$key] = $value;
            }
        }
    }
   
    public function save(){
        R::store($this->npcData);
    }
    
    public function getType(){
        return $this->npcData->type;
    }
    
    private function getRandomText(){
        shuffle($this->randomText);
        return "<b>".$this->npcData->name." </b>sagt: ".$this->randomText[0];
        
    }
    
    public function getText(){
        
        if(count($this->condition) > 0){
        
            foreach ($this->condition as $cond) {
                $condition = $cond["c"];
             
                if(isset($cond["t"]) 
                        && $this->condtitionParser->isValide($condition) 
                        && $this->condtitionParser->getResult($condition)){
                    return $this->conditionText[$cond["t"]];
                }
            }
        }
        
        return $this->getRandomText();
    }
    
    public static function createNew($name,$npcType,$npcRandomText,$faction,$guild,$gender,$conditions,$description){
        
       $npc = R::dispense(DBTables::NPC);
       $npc->name = $name;
       $npc->type = $npcType;
       $npc->randomtext = json_encode($npcRandomText);
       $npc->faction = $faction;
       $npc->guild = $guild;
       $npc->gender = $gender;
       $npc->description = $description;
       $npc->conditions = json_encode($conditions);

       $npcId = R::store($npc);
       
       return $npcId;
       
    }
}

class Person{
    
    /**
     *
     * @var Npc 
     */
    private $npc;
    private $npcId;
    private $conditionParser = null;
    public function __construct($npcId,$cpars) {
        $this->npcId = $npcId;
        $this->conditionParser = $cpars;
        $this->load();
    }
    
    public function load(){   
        $this->npc = new Npc($this->npcId,$this->conditionParser);  
    }
    
    public function getType(){
      
        return $this->npc->getType();
    }
    
    public function getText(){
        
        return $this->npc->getText();
        
    }
    
    /**
     * @return Npc Description
     */
    public function getNpc(){
        return $this->npc;
    }
    
    public function save(){
        $this->npc->save();
    }
    
    public static function createNew($name, $npcRandomText, $faction,$guild,$gender,$conditions,$description){
        Npc::createNew($name, NpcType::Person, $npcRandomText, $faction, $guild, $gender,  $conditions,$description);
    }
    
}

class Vendor{
    
    /**
     *
     * @var Npc 
     */
    private $npc;
    
    /**
     *
     * @var ItemBag 
     */
    private $bag;
    
    private $data;
    
    private $vendorId;
    
    private $itemConfig;
    
    private $conditionParser = null;
    
    public function __construct($vendorId,$conditionParser) {
        
        $this->vendorId = $vendorId;
        $this->conditionParser = $conditionParser;
        $this->load();
        
    }
    
    public function restock(){
        
        $this->bag->wipeBag();
        $this->bag->load();

        foreach ($this->itemConfig as $item) {        
            $id = $item[0];
            $w = $item[1];
            $amount = $item[2];
            
            if(rand(1, 100) < $w){
              
               $r = $this->bag->addItem($id, $amount);
         
            } 
        }
    }
    
    /**
     * 
     * @param Item $item
     * @return int
     */
    public function getSellPriceMultiplier(){
        
        return 10;
    }
    
    public function getBuyPriceMultiplier(){
        return 2.5;
    }
    
    public function getType(){
        
        return $this->npc->npcData->type;
    }
    
    public function save(){
    
        R::store($this->data);
        $this->npc->save();
        $this->bag->save();
        
        
        
    }
    
  
    private function load(){
        $this->data = R::load(DBTables::NPC_VENDOR, $this->vendorId);
        $this->itemConfig = json_decode($this->data->itemconf,TRUE);
        $this->npc = new Npc($this->data->npcId,$this->conditionParser);
        $this->bag = new ItemBag(BagTypes::Merchant, $this->data->id);
        
            if($this->data->lastrestock < time()-$this->data->restocktime){
                $this->data->lastrestock = time();
            $this->restock();
            $this->save();
        }
        
    }
    
    public function getVendorGold(){
   
        return $this->data->gold;
    }
    
    public function addGold($am){
        $this->data->gold+=$am;
    }
    
    public function removeGold($am){
        
        if($this->data->gold < $am){
            return false;
        }
        
        $this->data->gold-= $am;
        return true;
    }
    
    /**
     * 
     * @return ItemBag
     */
    public function getBag(){
        return $this->bag;
    }
    
    /**
     * @return Npc Description
     */
    public function getNpc(){
        return $this->npc;
    }
    
    public function getVendorData(){
        return $this->data;
    }
    
    public function getText(){
        return $this->npc->getText();
    }
    
    
    public static function createNew($name, $npcRandomText, $faction,$guild,$gender,$conditions,$gold, $mingold, $maxgold, $restockTime, $itemconf,$bagSize,$description){
        
        
        $npcId = Npc::createNew($name, NpcType::Vendor, $npcRandomText, $faction, $guild, $gender, $conditions,$description);
        $vendor = R::dispense(DBTables::NPC_VENDOR);
        $vendor->npcId = $npcId;
        $vendor->gold = $gold;
        $vendor->mingold = $mingold;
        $vendor->maxgold = $maxgold;
        $vendor->restocktime = $restockTime;
        $vendor->lastrestock = time();
        $vendor->itemconf = json_encode($itemconf);
        $vendorId = R::store($vendor);
        ItemBag::createNewBag(BagTypes::Merchant, $vendorId, $bagSize);
    }
    
}

class QuestNpc{
    
    /**
     *
     * @var Npc 
     */
    private $npc;
    private $npcId;
    private $data;
    private $questList;
    private $userId;
    
    private $openQuests;
    private $solvedQuests;
    private $activQuests;
    
    /**
     *
     * @var Quest[] 
     */
    private $newQuestList = null;
    /**
     *
     * @var Quest[] 
     */
    private $solvedQuestList = null;
    /**
     *
     * @var Quest[] 
     */
    private $activQuestList = null;
    
    /**
     *
     * @var ConditionParser 
     */
    private $conditionParser;
    
    /**
     *
     * @var QuestGoalActionParser
     */
    private $actionParser;
    
    public function __construct($questNpcId,$userId, $conditionParser,$acitonParser) {
        $this->npcId = $questNpcId;
       $this->actionParser = $acitonParser;
        $this->userId = $userId;
        $this->conditionParser = $conditionParser;
        $this->npc = $this->load();
    }
    
    public function load(){
    
        $this->data = R::load(DBTables::NPC_QUEST, $this->npcId);
        $this->questList = json_decode($this->data->quests);
        
        $this->getRelatedQuests();
        return new Npc($this->data->npcId,$this->conditionParser);
    }
    
    public static function createNew($name, $npcRandomText, $faction,$guild,$gender,$conditions,$quests,$description){
        
        $npcId = Npc::createNew($name, NpcType::Vendor, $npcRandomText, $faction, $guild, $gender, $conditions,$description);
         $questNpc = R::dispense(DBTables::NPC_QUEST);
         $questNpc->npcId = $npcId;
         $questNpc->quests = json_encode($quests);
         R::store($questNpc);
    }
    
    public function getNpc(){
        return $this->npc;
    }

    public function getType(){
      
        return $this->npc->getType();
    }
    
    private function getRelatedQuests(){
        
        $relatedQuests = R::find(DBTables::QUEST_LOG," user_id = ? and quest_id IN (".R::genSlots($this->questList).")", array_merge([$this->userId],$this->questList));
        $openQuests = [];
        $activQuests = [];
        $solvedQuests = [];
        $aQuests = $this->questList;
        foreach ($relatedQuests as $quest) {           
            $isActivOrMore = array_search($quest->quest_id, $this->questList);
            
            if($isActivOrMore !== false){
                $aQuests[$isActivOrMore] = null;
                $status = $quest->status;
                
                if($status == QuestStatus::ACTIVE){
                    $activQuests[] = $quest->quest_id;
                }elseif($status == QuestStatus::SOLVED)
                    $solvedQuests[] = $quest->quest_id;
                
            }
        }
      
        
        foreach ($aQuests as $quid) {
            if($quid != null){
            $openQuests[] = $quid;
            }
        }
       
        $this->openQuests = $openQuests;
        $this->solvedQuests = $solvedQuests;
        $this->activQuests = $activQuests;
        
        //quest new
    }

    public function getText(){
        return $this->npc->getText();
    }
    
    public function getNewQuests(){
        if($this->newQuestList == null){
            $this->newQuestList = [];
            foreach ($this->openQuests as $qId) {
                
                $quest = new Quest($qId,$this->conditionParser,$this->actionParser);
                if($quest->checkForAccessible()){
                    $this->newQuestList[] = $quest;
                }
            }
        }
        
        return $this->newQuestList;
    }
    
    public function getActivQuests(){
        if($this->activQuestList == null){
            $this->activQuestList = [];
            foreach ($this->activQuests as $quid) {
                $this->activQuestList[] = new Quest($quid, $this->conditionParser,$this->actionParser);
            }
        }
        
        return $this->activQuestList;
    }
    
    public function getResolvedQuest(){
        if($this->solvedQuestList == null){
            $this->solvedQuestList = [];
            foreach ($this->solvedQuests as $quid) {
                $this->solvedQuestList[] = new Quest($quid,$this->conditionParser,$this->actionParser);
            }
        }
        return $this->solvedQuestList;
    }
    
    public function hasQuest($id){
        foreach ($this->questList as $value) {
            if($value == $id){
                return true;
            }
        }
        return false;
    }
    
}


class TrainerNpc{
    
       /**
     *
     * @var Npc 
     */
    private $npc;
    private $npcId;
    private $data;
    private $userId;
    
  
    
    /**
     *
     * @var ConditionParser 
     */
    private $conditionParser;
    
  
    
    /**
     *
     * @var Charakter
     */
    private $char;
    
    private $config;
    
    public function __construct($questNpcId,$char, $conditionParser) {
        $this->npcId = $questNpcId;
        $this->conditionParser = $conditionParser;
        $this->char = $char;
        $this->npc = $this->load();
        
       
        
        
    }
    
    public function load(){
        $this->data = R::load(DBTables::NPC_TRAINER, $this->npcId);
        $this->config = json_decode($this->data->trainer,true);
        return new Npc($this->data->npcId,$this->conditionParser);
    }
    
    public function buyStat($stat){
        $calc = new StatsPriceCalculator();
        $currLevel = $this->char->getLevel();
        $statValue = $this->char->getStats()->$stat;
        $xpPrice = $calc->getStatValueCost($statValue, $currLevel);
        
        if($this->hasXp($xpPrice)){
            $this->payXp($xpPrice);
            $this->char->getStats()->$stat = $statValue+1;
            $this->char->getStats()->save();
            $this->char->saveChar();
            return true;
        }
        return false;
        
    }
    
     public function getText(){
        return $this->npc->getText();
    }
    
    public function hasArticle($name){
        return array_key_exists($name, $this->config);
    }
    
    public function getCostList(){
        
        
        $costList = [];
        $level = $this->char->getLevel();
        $stats = $this->char->getStats();
        $calc = new StatsPriceCalculator();
        
        foreach ($this->config as $key=>$v){
        
            if($key == "level"){
            $statName = "Level";
            $statValue = $level;
             $xpCost = $calc->getLevelCost($level);
             
            }else{
            $statName = DICT::STATS_NAME[$key];
            $statValue = $stats->$key;
            
            
                $xpCost = $calc->getStatValueCost($statValue, $level);
            }
            
            if($statValue >= $v){continue;}
            
           $costList[$key] = [$statValue+1,$xpCost];
            
        }
        return $costList;
    }
    
    public function train($name){
        if($name == "level"){
            return $this->buyLevel();
        }
        
        return $this->buyStat($name);
    }
    
    public function buyLevel(){
        $calc = new StatsPriceCalculator();
        $currLevel = $this->char->getLevel();
        $xpPrice = $calc->getLevelCost($currLevel);
        
        if($this->hasXp($xpPrice)){
            $this->payXp($xpPrice);
            $this->char->dolvlUp();
            $this->char->saveChar();
            return true;
        }
        return false;
    }
    
    private function payXp($need){
        $this->char->removeXp($need);
    }


    public function hasXp($need){
        return $this->char->getXp() >= $need;
    }
    
      public function getNpc(){
        return $this->npc;
    }

    public function getType(){
      
        return $this->npc->getType();
    }
    
    public function getConfig(){
        return $this->config;
    }
    
    public static function createNew($name, $npcRandomText, $faction,$guild,$gender,$conditions,$description,$trainerConfig){
        
        $npcId = Npc::createNew($name, NpcType::Trainer, $npcRandomText, $faction, $guild, $gender, $conditions,$description);
         $questNpc = R::dispense(DBTables::NPC_TRAINER);
         $questNpc->npcId = $npcId;
         $questNpc->trainer = json_encode($trainerConfig);
         
         R::store($questNpc);
    }
    
}
