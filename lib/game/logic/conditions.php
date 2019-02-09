<?php

class ConditionParser {

    private $possibleValues = [
        "xp",
        "level",
        "name",
        "gold",
        "iteminbag",
        "itemequip",
        "var",
        "flag",
        "opennpc",
        "queststatus"
    ];
    private $possibleOperators = [
        "=",
        "!=",
        "<",
        ">"
    ];
    private $tempVars = [];

    /**
     *
     * @var Context
     */
    private $context;

    
    public $flagInfoList = [];
    
    /**
     *
     * @var Charakter 
     */
    private $char;

    public function __construct($context, $char) {
        $this->context = $context;
        $this->char = $char;
    }

    public function isValide($condition) {

        $elements = count($condition);

        if ($this->isCondition($condition)) {

            return true;
        } elseif ($this->isGroup($condition)) {

            foreach ($condition as $key => $value) {
                if (!$this->isValide($value)) {
                    return false;
                }
            }

            return true;
        } elseif ($this->isConditionGroup($condition)) {

            foreach ($condition as $c) {
                if (!$this->isValide($c)) {
                    return false;
                }
            }

            return true;
        }



        return false;
    }

    public function getResult($condition) {

        $this->tempVars = [];

        if ($this->isCondition($condition)) {
            
            return $this->doCondition($condition);
        } 
        elseif ($this->isGroup($condition)) {

            foreach ($condition as $key => $value) {

                if ($this->getGroupType($condition) == "and") {
                    return $this->doAndGroup($condition);
                } else {

                    return $this->doOrGroup($condition);
                }
            }
        }

        return false;
    }

    private function getValue($condition) {


        if ($this->isCondition($condition)) {

            if (!array_key_exists($condition[0], $this->tempVars)) {


                switch ($condition[0]) {
                    case "xp":
                        $this->tempVars["xp"] = $this->char->getXp();
                        break;
                    case "level":
                        $this->tempVars["level"] = $this->char->getLevel();
                        break;
                    case "name":
                        $this->tempVars["name"] = $this->context->getUserName();
                        break;
                    case "gold":
                        $this->tempVars["gold"] = $this->char->getMoneyBag()->getMoney();
                        break;
                    case "var":
                        $this->tempVars["var"] = $this->context->sessionData->getItem($condition[2][0]);
                        break;
                    case "iteminbag":
                        return null;
                        break;
                    case "itemequip":
                        return null;
                    case "flag":
                        $this->tempVars["flag"] = $this->context->getFlag($condition[2][0]);
                        break;
                    case "opennpc":
                        return null;
                    default:
                    case "queststatus":
                        $this->tempVars["queststatus"] = $this->char->getQuestLog()->getQuestStatus($condition[2][0]);
                        break;
                }
            }
        }
        return $this->tempVars[$condition[0]];
    }

    private function getGroupType($condition) {
        if (isset($condition["and"])) {
            return "and";
        }
        return "or";
    }

    private function isGroup($condition) {


        $keys = array_keys($condition);

        foreach ($keys as $value) {
            if (is_numeric($value)) {
                return false;
            }
        }

        return true;
    }

    private function isValidGroup($condition) {
        if (isset($condition["and"])) {
            if (count($condition["and"]) > 0) {
                return true;
            }
        } elseif (isset($condition["or"])) {

            if (count($condition["or"]) > 0) {
                return true;
            }
        }

        return false;
    }

    private function isCondition($condition) {
        if (count($condition) == 3) {
            return $this->isValidSearchAndOperator($condition[0], $condition[1]);
        }
        return false;
    }

    private function isConditionGroup($condition) {

        $keys = array_keys($condition);
        foreach ($keys as $value) {
            if (!is_numeric($value)) {
                return false;
            }
        }
        return true;
    }

    private function isValidSearchAndOperator($search, $operator) {
        return in_array($operator, $this->possibleOperators) && in_array($search, $this->possibleValues);
    }

    private function doOrGroup($condition) {

        $oneTrue = false;

        foreach ($condition["or"] as $value) {

            if ($this->isGroup($value)) {
                if ($this->getResult($value)) {
                    $oneTrue = TRUE;
                }
            } else {
                if ($this->isCondition($value)) {
                    if ($this->doCondition($value)) {
                        $oneTrue = true;
                    }
                } else {
                    foreach ($value as $cv) {
                        if ($this->getResult($cv)) {
                            $oneTrue = TRUE;
                        }
                    }
                }
            }
        }
        return $oneTrue;
    }

    private function doAndGroup($condition) {

        $oneFalse = false;

        foreach ($condition["and"] as $value) {

            if ($this->isGroup($value)) {
                if (!$this->getResult($value)) {
                    $oneFalse = TRUE;
                }
            } else {
                if ($this->isCondition($value)) {
                    if (!$this->doCondition($value)) {
                        $oneFalse = true;
                    }
                } else {
                    foreach ($value as $cv) {
                        if (!$this->getResult($cv)) {
                            $oneFalse = TRUE;
                        }
                    }
                }
            }
        }
        return !$oneFalse;
    }

    private function bagItemCondition($condition) {
        if ($condition[1] == "<" || $condition[1] == ">") {
            return false;
        }
        
        
       
        $item = $this->char->getBag()->hasAmountOfItem($condition[2][0], $condition[2][1]);
                
        if ($condition[1] == "=") {
            return $item;
        } else {
            return !$item;
        }
    }

    private function equipCondition($condition) {
        if ($condition[1] == "<" || $condition[1] == ">") {
            return false;
        }

        $item = $this->char->getEquip()->hasEquip($condition[2]);

        if ($condition[1] == "=") {
            return $item;
        } else {
            return !$item;
        }
    }

    private function openNpcCondition($condition) {

        if ($condition[1] == "<" || $condition[1] == ">") {
            return false;
        }

        $npc = $this->context->sessionData->getItem("npc");

        if ($npc == null)
            return false;

        $npcData = explode(";", $npc);

        $npcInfo = $npcData[1] == $condition[2];

        if ($condition[1] == "=") {
            return $npcInfo;
        } else {
            return !$npcInfo;
        }
    }

    private function doCondition($condition) {

        $value = $this->getValue($condition);

        if ($condition[0] == "iteminbag") {
           
            return $this->bagItemCondition($condition);
        } 
        elseif ($condition[0] == "itemequip") {

            return$this->equipCondition($condition);
        } 
        elseif ($condition[0] == "var") {
            return $this->simpleCondition($value, $condition[1], $condition[2][1]);
        } 
        elseif ($condition[0] == "opennpc") {

            return $this->openNpcCondition($condition);
        } 
        elseif ($condition[0] == "queststatus") {
            return $this->simpleCondition($value, $condition[1], $condition[2][1]);
        }
        elseif($condition[0] == "flag"){
            
            return $this->simpleCondition($value, $condition[1], $condition[2][1]);
        }

        return $this->simpleCondition($value, $condition[1], $condition[2]);
    }

    private function simpleCondition($value, $cond, $need) {

        switch ($cond) {
            case "=":
                return $value == $need;
            case "!=":
                
                return $value != $need;
            case ">":
                return $value > $need;
            case "<":
                return $value < $need;
        }

        return false;
    }

}

class ManipulationActionParser {

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

    /**
     *
     * @var ActionResultObject
     */
    private $result;

    public function __construct($context, $char) {
        $this->context = $context;
        $this->char = $char;
    }

    /**
     * 
     * @param ManipulationActionItem $action
     * @return boolean
     */
    public function validateAction($action) {
        $this->result = new ActionResultObject();
        $this->result->isValidation = true;
        
        if($this->isValidValueToAction($action->type, $action->value) ){
            
            if(count($action->cond) != 0){
                
                if($this->char->getConditionParser()->isValide($action->cond)){
                    if(!$this->char->getConditionParser()->getResult($action->cond)){
                        $this->result->isError = true;
                        $this->result->displayMessage = "Nicht nötig";
                        return false;
                    }
                }else{throw new Exception("Action Condition is Invalide");}
                
            }
            
            if($this->validateActionCanBeDone($action))
            {
                return true;
            }
            return false;
        }
        
        $this->result->isError = true;
        $this->result->displayMessage = "Ungültige konfiguration!";
            
        
        
        return false;
    }

    public function doAction($action) {
        $this->result = new ActionResultObject();
        $this->result->isValidation = FALSE;
        
        if($this->performAction($action)){
            return TRUE;
        }
        
        $this->result->isError = true;
        $this->result->displayMessage = "Not implemented for DoAction";
        return false;
    }

    public function getResultObject() {
        return $this->result;
    }

   

    /**
     * Gets the type of the input value
     * 
     * @param mixed $value {single in value or mixed array}
     * @return int 0 = simplel int value, 1 = double int (additem..) 2 string,int (flags vars)
     * @throws Exception
     */
    private function getValueType($value) {
        if (is_numeric($value) || is_bool($value)) {
            return 0;
        }
        
        if(count($value) == 0)return false;

        if (count($value) == 2) {

            if (is_numeric($value[0]) && is_numeric($value[1])) {
                return 1;
            } elseif (is_numeric($value[0]) && is_string($value[1])) {
                return 2;
            }
        }
        
        
        
        if(is_array($value)){
            if(is_array($value[0])){
                return 3;
            }
        }

        return 4;

        throw new Exception("Unknown value type");
    } 
    private function isValidValueToAction($action, $value) {

        switch ($action) {
            case ManipulationActionTypes::ADDITEM :
            case ManipulationActionTypes::REMITEM :
            case ManipulationActionTypes::ADDREPUTATION :
            case ManipulationActionTypes::REMREPUTATION :
            case ManipulationActionTypes::CHANGEQUESTSTATUS :
            case ManipulationActionTypes::REMNPC :
            case ManipulationActionTypes::ADDGOLD :
            case ManipulationActionTypes::REMGOLD :
            case ManipulationActionTypes::ADDXP :
            case ManipulationActionTypes::REMXP :
            case ManipulationActionTypes::ADDHONOR :
            case ManipulationActionTypes::REMHONOR :
            case ManipulationActionTypes::REMQUEST :
            case ManipulationActionTypes::CHANGEVAR :
            case ManipulationActionTypes::REMVAR :
            case ManipulationActionTypes::CHANGEFLAG :
            case ManipulationActionTypes::REMFLAG :
            case ManipulationActionTypes::CHANGENPC :
            case ManipulationActionTypes::ADDTOVAR:
            case ManipulationActionTypes::ADDTOFLAG:
                return $this->getValueType($value) == 0;
            case ManipulationActionTypes::STARTFIGHT:
            case ManipulationActionTypes::STARTRANDFIGHT:
                return $this->getValueType($value) == 4 || $this->getValueType($value) == 1;
            case ManipulationActionTypes::CHANGEHP:
            case ManipulationActionTypes::CHANGEMANA:
                return $this->getValueType($value) == 0;
            
        }
    }
    
    /**
     * 
     * @param ManipulationActionItem $action
     * @return boolean
     */
    private function validateActionCanBeDone($action){
        switch ($action->type) {
            case ManipulationActionTypes::ADDITEM :
                //hase space
                $bag = $this->char->getBag();
                $can = $bag->canAddItem($action->key, $action->value);
                if(!$can){ $this->result->isError = true; $this->result->displayMessage = "Kein platz im Inventar";}
                return $can;
            case ManipulationActionTypes::STARTFIGHT:
            case ManipulationActionTypes::CHANGEFLAG :
            case ManipulationActionTypes::ADDTOFLAG:
            case ManipulationActionTypes::ADDTOVAR:
            case ManipulationActionTypes::STARTRANDFIGHT:
            case ManipulationActionTypes::CHANGEHP:
            case ManipulationActionTypes::CHANGEMANA:
                return true;
            case ManipulationActionTypes::REMITEM :
            case ManipulationActionTypes::ADDREPUTATION :
            case ManipulationActionTypes::REMREPUTATION :
            case ManipulationActionTypes::CHANGEQUESTSTATUS :
            case ManipulationActionTypes::REMNPC :
            case ManipulationActionTypes::ADDGOLD :
            case ManipulationActionTypes::REMGOLD :
            case ManipulationActionTypes::ADDXP :
            case ManipulationActionTypes::REMXP :
            case ManipulationActionTypes::ADDHONOR :
            case ManipulationActionTypes::REMHONOR :
            case ManipulationActionTypes::REMQUEST :
            case ManipulationActionTypes::CHANGEVAR :
            case ManipulationActionTypes::REMVAR :

            case ManipulationActionTypes::REMFLAG :
            case ManipulationActionTypes::CHANGENPC :
            default :
                $this->result->isError = true;
                $this->result->displayMessage = "NOT IMPLEMENTED ACTION Type: ".$action->type;
                return false;
        }
        return true;
    }
    
      /**
     * 
     * @param ManipulationActionItem $action
     * @return boolean
     */
    private function performAction($action){
        switch ($action->type) {
            case ManipulationActionTypes::ADDITEM :
                //hase space
                $bag = $this->char->getBag();
               $bag->addItem($action->key, $action->value);
                return true;
                case ManipulationActionTypes::CHANGEFLAG :
                    $this->context->setFlag($action->key, $action->value);
                    return true;
            case ManipulationActionTypes::STARTFIGHT:
                
                if(!$this->context->sessionData->getItem("fight")){
                    $mobList = [];
                    foreach ($action->value as $mid) {
                        $mobList[] = new Mob($mid,null,$this);
                    }
                    
                  $fi =  new FightInitializer($mobList, $this->context, $this->char->getGroup() ,$this->context->getUserId());
                    $this->context->sessionData->addItem("fight", true);
                    $this->context->sessionData->addItem("fightid", $fi->getFightId());
                }
                return true;
            case ManipulationActionTypes::ADDTOFLAG:
                $flag = $this->context->getFlag($action->key);
                if($flag == null){
                    $flag = 0;
                }
                $flag+=$action->value;
                $this->context->setFlag($action->key, $flag);
                return true;
            case ManipulationActionTypes::STARTRANDFIGHT:
                  if(!$this->context->sessionData->getItem("fight")){
                    $mobList = [];
                    foreach ($action->value as $mid) {
                        if(rand(0, 1) == 1){
                        $mobList[] = new Mob($mid,null,$this);
                        }
                    }
                    
                    if(count($mobList) == 0){
                        $mobList[] = new Mob($action->value[0],null,$this);
                    }
                    
                  $fi =  new FightInitializer($mobList, $this->context, $this->char->getGroup() ,$this->context->getUserId());
                    $this->context->sessionData->addItem("fight", true);
                    $this->context->sessionData->addItem("fightid", $fi->getFightId());
                }
                return true;
            case ManipulationActionTypes::CHANGEHP:
            case ManipulationActionTypes::CHANGEMANA:
                
                if($action->key == "hp" || $action->key == "all"){
                    $v = $this->char->getCurrentHp()+$action->value;
                    $this->char->setHp($v);
                }
                
                if($action->key  == "mana" || $action->key == "all"){
                    $v = $this->char->getCurrentMana()+$action->value;
                    $this->char->setMana($v);
                }
                
                $this->char->saveChar();
                return true;
            case ManipulationActionTypes::ADDTOVAR:
            case ManipulationActionTypes::REMITEM :
            case ManipulationActionTypes::ADDREPUTATION :
            case ManipulationActionTypes::REMREPUTATION :
            case ManipulationActionTypes::CHANGEQUESTSTATUS :
            case ManipulationActionTypes::REMNPC :
            case ManipulationActionTypes::ADDGOLD :
            case ManipulationActionTypes::REMGOLD :
            case ManipulationActionTypes::ADDXP :
            case ManipulationActionTypes::REMXP :
            case ManipulationActionTypes::ADDHONOR :
            case ManipulationActionTypes::REMHONOR :
            case ManipulationActionTypes::REMQUEST :
            case ManipulationActionTypes::CHANGEVAR :
            case ManipulationActionTypes::REMVAR :
            case ManipulationActionTypes::REMFLAG :
            case ManipulationActionTypes::CHANGENPC :
            default :
                $this->result->isError = true;
                $this->result->displayMessage = "NOT IMPLEMENTED Type: "+$action;
                return false;
        }
        return FALSE;
    }   
}

class ManipulationActionItem {

    public $type;
    public $cond;
    public $key;
    public $value;

}

class ActionResultObject {
    public $isError;
    public $displayMessage;
    public $isValidation;

}
