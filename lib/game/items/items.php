<?php

class Item {

    protected $data;
    protected $itemId;

    /** @var ItemStats Description */
    public $stat;
    public $amount;

    public function __construct($itemId, $amount = 0, $data = null) {
        $this->itemId = $itemId;
        $this->load($data);
        $this->amount = $amount;
    }

    public function __get($name) {
        return $this->data->$name;
    }

    public function __set($name, $value) {
        $this->data->$name = $value;
    }

    public function save() {
        $this->data->stats = $this->stat->toJson();
        R::store($this->data);
    }

    public function load($data = null) {
        if ($data == null) {
            $data = R::load(DBTables::ITEMS, $this->itemId);
        }

        $this->data = $data;
        $this->stat = new ItemStats();
        $this->stat->fromJson($this->data->stats);
    }

    public static function createNewItem($name, $description, $rarity, $stats, $type, $stackSize, $sellprice,$imagekey,$action = array("none"=>"")) {

        $item = R::dispense(DBTables::ITEMS);
        $item->name = $name;
        $item->description = $description;
        $item->rarity = $rarity;
        $item->stats = $stats;
        $item->type = $type;
        $item->stackSize = $stackSize;
        $item->sellPrice = $sellprice;
        $item->imagekey = $imagekey;
        $item->action = json_encode($action);
        $id = R::store($item);

        return new Item($id);
    }
    
    public function canUse($bag, $actionParser){
        
   
         $action = json_decode($this->data->action);
        if($this->data->type == TypeOfItem::USABLE 
                && $actionParser->validateAction($action) 
                && $bag->canRemoveItem($this->itemId, 1)
                )
        {
            return true;
        }
        
        return false;
    }

    /**
     * 
     * 
     * @param ItemBag $bag
     * @param ManipulationActionParser $actionParser
     */
    public function useItem($bag,$actionParser){
        
        
        $action = json_decode($this->data->action);
        if($actionParser->validateAction($action) && $bag->canRemoveItem($this->itemId, 1)){
            $actionParser->doAction($action);
            $bag->removeItem($this->itemId, 1);
            return true;
        }
        return false;
        
    }
}

class ItemStats {

    public $data = [];

    public function fromJson($data) {
        $this->data = json_decode($data, true);
    }

    public function toJson() {
        return json_encode($this->data);
    }

    public function __get($name) {
        if (!isset($this->data[$name]))
            return null;
        return $this->data[$name];
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

}

class Equip extends Item {

    private $extData;

    public function __construct($itemId, $amount = 0, $data = null) {
        parent::__construct($itemId, $amount, $data);
        $this->loadExtention();
    }

    public function getExtData() {
        return $this->extData;
    }

    public function save() {
        parent::save();
        R::store($this->extData);
    }

    private function loadExtention() {
        $this->extData = R::findOne(DBTables::ITEM_EQUIP, "item_id = ?", [$this->itemId]);
    }

    public static function createNewEquip($name, $description, $rarity, $stats, $sellprice, $equipType, $material, $waponType,$minLevel,$imagekey) {
        $type = TypeOfItem::Equip;
        $stackSize = 1;
        $item = parent::createNewItem($name, $description, $rarity, $stats, $type, $stackSize, $sellprice,$imagekey);
        $extendItem = R::dispense(DBTables::ITEM_EQUIP);
        $extendItem->itemId = $item->id;
        $extendItem->slot = $equipType;
        $extendItem->material = $material;
        $extendItem->waponType = $waponType;
        $extendItem->minlevel = $minLevel;
        R::store($extendItem);
    }

}
