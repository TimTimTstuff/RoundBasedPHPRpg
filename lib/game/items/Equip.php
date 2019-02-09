<?php

class PlayerEquipment {

    private $data;

    /**
     *
     * @var ServiceObject 
     */
    private $serviceError;

    /**
     *
     * @var Equip[] 
     */
    private $loadedItems = [];

    public function __construct($playerId) {
        $this->data = R::findOne(DBTables::CHAR_EQUIP, " user_id = ?", [$playerId]);
        $this->serviceError = new ServiceObject();
    }

    public function __get($name) {
        if (!isset($this->data->$name))
            return false;

        if ($this->data->$name == 0)
            return null;

        if (!isset($this->loadedItems[$name])) {
            $this->loadedItems[$name] = new Equip($this->data->$name);
        }

        return $this->loadedItems[$name];
    }

    public function __set($name, $value) {
        $this->data->$name = $value;
        $this->loadedItems[$name] = new Equip($this->data->$name);
    }

    public function save() {
        R::store($this->data);
    }

    public function hasEquip($id) {
        
        $allEquip = [
            $this->data->head,
            $this->data->breast,
            $this->data->shoulder,
            $this->data->legs,
            $this->data->feet,
            $this->data->hand,
            $this->data->wapon
        ];
        
        return in_array($id, $allEquip);
        
    }
    
    
    /**
     * 
     * @param int $slot
     * @param ItemBag $userBag
     */
    public function canUnequip($slot, $userBag) {
        //valid Slot
        if (!array_key_exists($slot, DICT::EQUIP_SLOTS)) {
            $this->serviceError->error = true;
            $this->serviceError->requestContent = "no_slot";
            $this->serviceError->displayMessage = "Ungültiger Slot";
            return false;
        }

        $slotName = DICT::EQUIP_SLOTS[$slot];

        //has equip on

        if ($this->$slotName == NULL) {
            $this->serviceError->error = true;
            $this->serviceError->requestContent = "no_item_in_slot";
            $this->serviceError->displayMessage = "Der Slot ist nicht belegt";

            return false;
        }


        //space in bag
        if (!$userBag->hasFreeSlot()) {
            $this->serviceError->error = true;
            $this->serviceError->requestContent = "no_bag_space";
            $this->serviceError->displayMessage = "Die Tasche ist Voll";
            return false;
        }


        return true;
    }

    /**
     * 
     * @param int $slot
     * @param ItemBag $userBag
     */
    public function unequip($slot, $userBag) {

        $slotName = DICT::EQUIP_SLOTS[$slot];

        $currentItemId = $this->data->$slotName;
        $this->data->$slotName = 0;
        if ($userBag->addItem($currentItemId, 1)) {

            $this->save();
        }
    }

    public function canEquip($itemId, $userBag,$playerLevel) {
        $item = new Item($itemId);
     
        
        
        if ($item->type != 0) {
            $this->serviceError->error = true;
            $this->serviceError->requestContent = "no_equip";
            $this->serviceError->displayMessage = "Das item ist kein Equip";
            return false;
        }

        if (!$userBag->hasAmountOfItem($itemId, 1)) {
            $this->serviceError->error = true;
            $this->serviceError->requestContent = "no_item_in_bag";
            $this->serviceError->displayMessage = "Das Item befindet sich nicht in der Tasche";
            return false;
        }

        $equip = new Equip($itemId);
        
          
        if($equip->getExtData()->minlevel > $playerLevel){
            
            $this->serviceError->error = true;
            $this->serviceError->requestContent ="low_level";
            $this->serviceError->displayMessage = $equip->name." hat ein Mindestlevel von ".$equip->getExtData()->minlevel;
            
            return false;
        }
        
        $slot = DICT::EQUIP_SLOTS[$equip->getExtData()->slot];
        $currEquip = $this->$slot;

        if ($currEquip != null) {
            if (!$this->canUnequip($equip->getExtData()->slot, $userBag)) {

                return false;
            }
        }

        return true;
    }

    public function equip($itemId, $userBag) {


        $equip = new Equip($itemId);
        $slot = DICT::EQUIP_SLOTS[$equip->getExtData()->slot];

        $currEquip = $this->$slot;

        if ($currEquip != null) {
            if (!$this->canUnequip($equip->getExtData()->slot, $userBag)) {
                return false;
            } else {

                $this->unequip($equip->getExtData()->slot, $userBag);
            }
        }

        $this->data->$slot = $itemId;

        if ($userBag->removeItem($itemId, 1)) {
            $this->save();
        }
    }

    public static function createNew($playerId) {
        $equip = R::dispense(DBTables::CHAR_EQUIP);
        $equip->head = 0;
        $equip->breast = 0;
        $equip->shoulder = 0;
        $equip->legs = 0;
        $equip->feet = 0;
        $equip->hand = 0;
        $equip->wapon = 0;
        $equip->userId = $playerId;
        R::store($equip);
    }

    public function getServiceObject() {
        return $this->serviceError;
    }

    public function getStatsValueArray() {
        $statsArray = array_keys(DICT::STATS_NAME);
        $valueArray = [];
        foreach (DICT::EQUIP_SLOTS as $slotName) {

            /**
             * @var Equip Description
             */
            if ($this->$slotName != null) {

                $itemValues = $this->$slotName->stat;

                foreach ($statsArray as $stasName) {
                    if ($itemValues->$stasName != null) {

                        if (!array_key_exists($stasName, $valueArray)) {
                            $valueArray[$stasName] = 0;
                        }
                        $valueArray[$stasName] += $itemValues->$stasName;
                    }
                }
            }
        }

        return $valueArray;
    }

}
