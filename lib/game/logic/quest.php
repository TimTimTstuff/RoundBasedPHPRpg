<?php

class Quest{
    
    private $data;
    public $questText;
    public $startCondition;
    public $goalCondition;
    public $reward;
    public $id;
    public $name;
    public $key;
    public $goalActions;
    public $objectiv;
    public $prequest;
    public $type;
    
    
    /**
     *
     * @var QuestGoalActionParser 
     */
    private $goalActionParser;
    
    /**
     *
     * @var ConditionParser 
     */
    public $conditionParser;
    
    public function __construct($idOrName,$condParser,$gap) {
       $this->goalActionParser = $gap;
        $item = null;
        
        if(is_numeric($idOrName)){
            $item = R::load(DBTables::QUEST, $idOrName);
        }elseif(is_string($idOrName)){
            $item = R::findOne(DBTables::QUEST," key = ? ",[$idOrName]);
        }else{
            return;
        }
        
        $this->conditionParser = $condParser;
        
        $this->data = $item;
        $this->startCondition = json_decode($this->data->startcond,TRUE);
        $this->goalCondition =  json_decode($this->data->goalcond,TRUE);
        $this->questText =  json_decode($this->data->questtext,TRUE);
        $this->reward =  json_decode($this->data->reward,TRUE);
        $this->id = $this->data->id;
        $this->key = $this->data->key;
        $this->name = $this->data->name;
        $this->goalActions = json_decode($this->data->goalaction);
        $this->objectiv = $this->data->objectiv;
        $this->prequest = $this->data->preQuestId;
        $this->type = $this->data->type;
        
    }
   
    /**
     * 
     * @param string $name
     * @param string $key
     * @param int $previusQuestId
     * @param array $startCondition
     * @param array $finishCondition
     * @param array $reward
     * @param array $questText
     * @param string $objectiv
     * @param QuestGoalAction[] $goalActions
     */
    public static function createNew($name, $key, $previusQuestId,$startCondition,$finishCondition,$reward,$questText,$objectiv,$goalActions,$type){
        
        $quest = R::dispense(DBTables::QUEST);
        
        $quest->name = $name;
        $quest->key = $key;
        $quest->preQuestId = $previusQuestId;
        $quest->startcond = json_encode($startCondition);
        $quest->questtext = json_encode($questText);
        $quest->reward = json_encode($reward);
        $quest->goalcond = json_encode($finishCondition);
        $quest->objectiv = $objectiv;
        $quest->goalaction = json_encode($goalActions);
        $quest->type = $type;
        R::store($quest);
    }
    
    public function checkSolved(){
       
        if($this->conditionParser->isValide($this->goalCondition)){
         
            
            
            return $this->conditionParser->getResult($this->goalCondition);
        }
        
        throw new Exception("INVALID CONDITION: QuestId: ".$this->id." Type: GoalCondition");
    }
    
    public function checkForAccessible(){
      
        
        
          if($this->conditionParser->isValide($this->startCondition)){
            return $this->conditionParser->getResult($this->startCondition);
        }
        
        throw new Exception("INVALID CONDITION: QuestId: ".$this->id." Type: StartCondition");
    
    }
    
    public function getReward(){
        return $this->reward;
    }
    
    public function finishQuest(){
      
        $this->goalActionParser->doAction($this->goalActions);
    }


    /**
     * 
     * @param string $type "new", "active","solved"
     */
    public function getText($type){
     
       if($type == "new"){
           return $this->questText["new"];
       }elseif($type == "active"){
           return $this->questText["active"];
       }elseif($type == "solved"){
           return $this->questText["solved"];
       }
       return "UNKNOWN QUEST STATUS TYPE: ".$type;
    }
}

class CharQuests{   
    
    private $userId;


    private $activeQuest;
    private $solvedQuests;
    private $finishedQuests;
    
    private $activLoaded;
    private $solvedLoaded;
    private $finishedLoaded;
    
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
    
    /**
     *
     * @var Charakter 
     */
    private $char;
    
    /**
     *
     * @var Statistics 
     */
    private $statistics;
    
    /**
     *
     * @var Context
     */
    private $context;
    
    /**
     * 
     * @param int $userId
     * @param Charakter $char
     * @param Context $context
     */
    public function __construct($userId,$char,$context) {
        $this->context = $context;
        $this->statistics = $context->getStatistics();
        $this->char = $char;
        $this->actionParser = $char->getQuestGoalActionParser();
        $this->userId = $userId;
        $this->conditionParser = $char->getConditionParser();
        $this->activeQuest = R::find(DBTables::QUEST_LOG," user_id = ? and status = ? ", [$this->userId, QuestStatus::ACTIVE]);
        $this->solvedQuests = R::find(DBTables::QUEST_LOG," user_id = ? and status = ? ", [$this->userId, QuestStatus::SOLVED]);        
        $this->checkAllForSolved();
        
    }
    
    public function getQuestStatus($questId){
   
      $q =  R::findOne(DBTables::QUEST_LOG," user_id = ? and quest_id = ?",[$this->userId,$questId]);
        if($q == null)return -1;
         
        return $q->status;
    }
    
    public function getActivQuests(){
        
        if($this->activLoaded == null){
            foreach ($this->activeQuest as $q) {
                $this->activLoaded[] = new Quest($q->questId,$this->conditionParser,$this->actionParser);
            }
        }
        
        return $this->activLoaded;
        
    }
    
    public function getSolvedQuests(){
          if($this->solvedLoaded == null){
            foreach ($this->solvedQuests as $q) {
                $this->solvedLoaded[] = new Quest($q->questId,$this->conditionParser,$this->actionParser);
            }
        }
        
        return $this->solvedLoaded;
    }
    
    public function getFinishedQuests(){
        
          $this->finishedQuests = R::find(DBTables::QUEST_LOG," user_id = ? and status = ? ", [$this->userId, QuestStatus::FINISHED]);
          if($this->finishedLoaded == null){
            foreach ($this->finishedQuests as $q) {
                $this->finishedLoaded[] = new Quest($q->questId,$this->conditionParser,$this->actionParser);
            }
        }
        return $this->finishedLoaded;
    }

    public function addQuest($questId){
        
        $q = R::dispense(DBTables::QUEST_LOG);
        $q->userId = $this->userId;
        $q->questId = $questId;
        $q->status = QuestStatus::ACTIVE;
        
        $id = R::store($q);
        
    }
    
    public function removeQuest($questId){
        
       $quest = R::findOne(DBTables::QUEST_LOG," user_id = ? and quest_id = ? and (status = ? or status = ?) ",[$this->userId,$questId, QuestStatus::ACTIVE, QuestStatus::SOLVED]);
       
       if($quest != null){
           R::trash($quest);
           return true;
       }
       return false;
    }
    
    public function changeQuestStatus($questId,$questStatus){
        
        foreach ($this->activeQuest as $q) {
            if($q->id == $questId){
                $q->status = $questStatus;
                R::store($q);
                return true;
            }
        }
        
         foreach ($this->solvedQuests as $q) {
            if($q->id == $questId){
                $q->status = $questStatus;
                R::store($q);
                return true;
            }
        }
        
    }
    
    public function getActivQuest($id){
        foreach ($this->getActivQuests() as $q) {
            if($q->id == $id)return $q;
        }
        return null;
    }
    
    /**
     * 
     * @param int $id
     * @return Quest
     */
    public function getSolvedQuest($id){
        foreach ($this->getSolvedQuests() as $q) {
            if($q->id == $id)return $q;
        }
        return null;
    }
    
    public function checkAllForSolved(){
       
        foreach ($this->activeQuest as $quest) {
          
           $isSolved = $this->getActivQuest($quest->questId)->checkSolved();
           if($isSolved){
            $quest->status = QuestStatus::SOLVED;
           }
           
        }
        
        foreach ($this->solvedQuests as $quest) {
      
           $isSolved = $this->getSolvedQuest($quest->questId)->checkSolved();
           
           if($isSolved){
               $quest->status = QuestStatus::SOLVED;
           }else{
               $quest->status = QuestStatus::ACTIVE;
           }
           
        }
        
        R::storeAll(array_merge($this->activeQuest,$this->solvedQuests));
        
    }
    
    public function finishQuest($questId){
        foreach ($this->solvedQuests as $quest) {
            if($quest->questId == $questId){
                $q = $this->getSolvedQuest($questId);
                if($q != null){
                    $quest->status = QuestStatus::FINISHED;
                    $q->finishQuest();
                   self::processReward($q->getReward(),$this->char,$this->context);
                    R::store($quest);
                    $this->statistics->add(StatisticItems::QUESTSFINISHED, 1);
                    return true;
                }
            }
        }
        return false;
    }
    
   /**
    * 
    * @param type $rewardList
    * @param Charakter $char
    * @param Context $context
    * @throws Exception
    */
    public static function processReward($rewardList,$char,$context){
        
        //item, xp, gold, honor, requtation
        
        foreach ($rewardList as $rw) {
            switch ($rw[0]){
                case "item":
                    $char->getBag()->addItem($rw[1][0], $rw[1][1]);
                    break;
                case "xp":
                    $char->addXp($rw[1]);
                    break;
                case "gold":
                    $char->getMoneyBag()->addMoney($rw[1],$context->statistics);
                    break;
                case "honor":
                    $char->addHonor($rw[1]);
                    break;
                case "reputation":
                    throw new Exception("NOT IMPLEMENTED ADD REPUTATION IN REWARD");
                    break;
                default :
                    throw new Exception("UNKNOWN REWARD ".$rw[0]);
                    break;
            }
        }
        
        $char->saveChar();
        
        
        
    }
}

class QuestGoalAction{
    
    //remitem, remvar, remgold, remflag
   public $action;
   public $key;
   public $amount;
    
}

class QuestGoalActionParser{
    
    /**
     *
     * @var QuestGoalAction[] 
     */
    private $data;
    /**
     *
     * @var Context 
     */
    private $context;
    /**
     *
     * @var Charakter 
     */
    private $char;
    
    public function __construct($context,$char) {
        $this->char = $char;
        $this->context = $context;
    }
    
    /**
     * 
     * @param QuestGoalAction[] $data
     */
    public function doAction($data){
              
        foreach ($data as $qga) {
            switch ($qga->action) {
                case "remvar":
                    $this->remVar($qga->key);
                    break;
                case "remitem":
                    $this->remItem($qga->key, $qga->amount);
                    break;
                case "remflag":
                    $this->remFlag($qga->key);
                    break;
                case "remgold":
                    $this->remGold($qga->amount);
                    break;
                default:
                    break;
            }
        }
        
    }
    
    private function remVar($key){
        $this->context->sessionData->addItem($key,null);
    }
    
    private function remItem($id,$amount){
        $bag = $this->char->getBag();
        $bag->removeItem($id, $amount);
    }
    
    private function remFlag($key){
        $this->context->setFlag($key,null);
    }
    
    private function remGold($amount){
        $this->char->getMoneyBag()->removeMoney($amount,$this->context->getStatistics());
    }
    
    
}