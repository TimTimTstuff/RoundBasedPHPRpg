<?php

class SpaceHandler{
    
    public $id;
    
    public $conditions;
    public $actions;
    public $name;
    
    public $randomText;
    public $conditionText;
    public $actionText;
    
    /**
     *
     * @var ConditionParser
     */
    public $condParser;
    /**
     *
     * @var ManipulationActionParser
     */
    public $actionHandl;
    
    
    public function __construct($spaceId,$conditionParser, $actionHandler) {
    
        $this->condParser = $conditionParser;
        $this->actionHandl = $actionHandler;
        $this->id = $spaceId;
        $this->load();
    }
    
    public function load(){
        $f = R::load(DBTables::WORLD_SPACE,$this->id);
        
        $tl = json_decode($f->textList,true);
        $this->conditions = json_decode($f->startconditions,true);
        $this->actions = json_decode($f->actions);
        $this->name = $f->name;
       
        foreach ($tl as $key => $textItem) {
            
            if(is_string($key[0]) && is_numeric($key[1])){
                $this->randomText[] = $textItem;
            }else{
                $this->conditionText[$key] = $textItem;
            }
        }
    }
    
    public static function createNew($name, $textList, $conditions,$actions){
        
        $sh = R::dispense(DBTables::WORLD_SPACE);
        $sh->name = $name;
        $sh->textList = json_encode($textList);
        $sh->startconditions = json_encode($conditions);
        $sh->actions = json_encode($actions);
        
        R::store($sh);
        
    }
    
    public function getText(){
        
      
        foreach ($this->conditions as $cond) {
            
            if(isset($cond["c"]) && isset($cond["t"])){
                
                if($this->condParser->isValide($cond["c"]) && $this->condParser->getResult($cond["c"])){
                   
                    return $this->conditionText[$cond["t"]];
                }
            }
        }
        
        shuffle($this->randomText);
        return $this->randomText[0];
        
    }

    public function getActionText($key){
        
       foreach ($this->conditions as $cond) {
            if(isset($cond["c"]) && isset($cond["a"]) && $cond["a"] == $key && isset($cond["at"])){
               return $this->conditionText[$cond["at"]];
            }
        }
        return "NONE TEXT FOR ACTION";
    }
    
    public function getActivActions(){
         $actions = [];
        foreach ($this->conditions as $cond) {
            if(isset($cond["c"]) && isset($cond["a"])){
                if($this->condParser->isValide($cond["c"]) && $this->condParser->getResult($cond["c"])){
                    $actions[$cond["a"]] = $this->actions->$cond["a"];
                }
            }
        }
        
        return $actions;
    }
    
    /**
     * 
     * @param type $name
     * @return \ActionResultObject
     */
    public function doAction($name){
        $activ = $this->getActivActions();
        
        if(array_key_exists($name, $activ)){
            if($this->actionHandl->validateAction($activ[$name])){
                
               $this->actionHandl->doAction($activ[$name]);
            }
                return $this->actionHandl->getResultObject();
            
        }
        
        $res = new ActionResultObject();
        $res->displayMessage = "Diese Aktion könnt ihr nicht durchführen.";
        $res->isError = true;
        $res->isValidation = true;
        return $res;
    }
    
}


class SpaceConfiguration{
    
    private $randomText = [];
    private $startCond = [];
    private $textList = [];
    private $actionList = [];
    
    
    public function addRandomText($text){
        
        $key = "r".count($this->textList);
        $this->randomText[$key] = $text;
        return $this;
    }
    
    public function getKey(){
        $start = 97;
        $add = $start + count($this->startCond);
        
        return "a".chr($add);
        
    }
    
    /**
     * 
     * @param array $cond
     * @param string $text
     * @param ManipulationActionItem $action
     * @param string $actionText
     */
    public function addStartCond($cond, $text, $action,$actionText){
        
        $key = $this->getKey();
        $this->textList[$key] = $text;
        $this->textList[$key."a"] = $actionText;
        $this->startCond[] =
         [
            "c"=>$cond,
            "a"=>$key,
            "at"=>$key."a",
            "t"=>$key
        ];
        
        $this->actionList[$key] = $action;
        
        return $this;
    }
    
    public function getConf(){
        return [
            "text"=> json_encode(array_merge($this->randomText,$this->textList)),
            "start"=> json_encode($this->startCond),
            "action"=> json_encode($this->actionList)
        ];
    }
    
    public function updateSpace($spaceId){
        
        $data = $this->getConf();
        $b = R::load(DBTables::WORLD_SPACE, $spaceId);
        $b->textList = $data["text"];
        $b->startconditions = $data["start"];
        $b->actions = $data["action"];
        R::store($b);
    }
    
    
}