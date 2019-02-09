<?php
using(["service"]);
class MoneyBag{
    
    private $money; 
    
    /**
     *
     * @var Statistics
     */
    private $statistics;
    
    public function __construct($userMoney,$statistics) {
        $this->money = $userMoney;
        $this->statistics = $statistics;
    }
    
    public function getDisplayValue(){
        return $this->money;
    }
    
    public function getMoney(){
        return $this->money;
    }
    
    /**
     * 
     * @param int $v
     * @param Statistics $stats
     */
    public function addMoney($v){
        $this->money+=$v;
        
            $this->statistics->add(StatisticItems::GOLDEARND, $v);
        
    }
    
    public function removeMoney($v){
        if($this->money < $v){
            return false;
        }
        $this->money-=$v;
       
            $this->statistics->add(StatisticItems::GOLDUSED, $v);
       
        return true;
    }
    
}

class ItemBag{
    
    private $bag;
    private $items;
    private $itemsResolved;
    private $itemsLoaded = false;
    
    
    private $bagType;
    private $ownerId;
    
    public function __construct($bagownerType, $ownerId) {
        $this->bagType = $bagownerType;
        $this->ownerId = $ownerId;
        $this->load();
    }
    
    public function load(){
         if(self::bagExist($this->bagType, $this->ownerId)){
            $this->bag = R::findOne(DBTables::BAG, " owner_type = ? and owner_id = ?",[$this->bagType, $this->ownerId]);
            $result = R::find(DBTables::BAG_ITEMS, " bag_id = ? ",[$this->bag->id]);
            $this->items = $result;
           
        }
    }
    
    public function getAmountOfItem($itemId){
        $items = $this->getAllItemsWithId($itemId);
       
        $totalAmount = 0;
        foreach ($items as $item) {
            $totalAmount+=$item->amount;
        }
        
        return $totalAmount;
    }
    
    public function hasAmountOfItem($itemId, $amount){
       
        
        
        return $amount<= $this->getAmountOfItem($itemId);
    }
    
    public function removeItem($itemId, $amount){
        
        $allStacks = $this->getAllItemsWithId($itemId);
        if(!$this->hasAmountOfItem($itemId, $amount))
        {
            return false;
        }
     
        
        $trashStack = [];
        foreach ($allStacks as $item) {
            if($item->amount <= $amount){
                $trashStack[] = $item;
                $amount-= $item->amount;
               
            }else{
                $item->amount -= $amount;
                $amount = 0;
                
            }
            
            if($amount <= 0)break;
        }
        $this->save();
        R::trashAll($trashStack);
        $this->load();
        return true;
    }
    
    public function canRemoveItem($itemId, $amount){
           $allStacks = $this->getAllItemsWithId($itemId);
        if(!$this->hasAmountOfItem($itemId, $amount))
        {
            return false;
        }
     
        
        $trashStack = [];
        
        foreach ($allStacks as $item) {
            $blendAmount = $item->amount;
            if($item->amount <= $amount){
                $amount-= $blendAmount;
               
            }else{
                $blendAmount -= $amount;
                $amount = 0;
                
            }
            
            if($amount <= 0)break;
        }
   
        return true;
    }
    
    public function getBagInfo(){
        return ["slots"=>$this->bag->slots,"used"=>count($this->items)];
    }
    
    public function getItems(){
        
        if($this->itemsLoaded == false){
           
            foreach ($this->items as $value){
                
                $tempItem = new Item($value->itemId, $value->amount);
                if($tempItem->type == 0 ){
                    $tempItem = new Equip($value->itemId, $value->amount);
                }
                $this->itemsResolved[] = $tempItem;
            }         
            $this->itemsLoaded = true;
        }
        
        return $this->itemsResolved;
    }
    
    public function hasFreeSlot(){
        return $this->getFreeSlots() > 0;
    }
    
    public function getFreeSlots(){
         $size = $this->bag->slots;
        $slotUseCount = count($this->items);
        return $size-$slotUseCount;
    }
    
    public function addItem($itemId, $amount){
        
       $freeSlots = $this->getFreeSlots();
     
       $item = new Item($itemId);
       
       if($item->type == 0){
           if($freeSlots >= $amount){
            for($i = 0; $i < $amount; $i++){
                 $bagItem = R::dispense(DBTables::BAG_ITEMS);
                 $bagItem->itemId = $itemId;
                 $bagItem->amount = 1;
                 $bagItem->bagId = $this->bag->id;
                 $this->items[] = $bagItem;
            }
            
           }else{
               
               return false;
           }
       }else{
        
           $slotsWithItem = $this->getAllSlotsWithItemIdAndSpace($itemId);
          
           
           $totalStore = 0;
           $stackSize = $this->getItemStackSize($itemId);
           foreach ($slotsWithItem as $slots) {
               $totalStore+=$this->getFreeSpaceForSlot($slots->id);
           }
          
           $rest = $amount - $totalStore;
           
           $neededFreeSlots = ceil($rest/$stackSize);
          
           if($neededFreeSlots <= $this->getFreeSlots()){
          
               foreach ($slotsWithItem as $swi){
                 $amount =  $this->addAmountToSlot($swi->id, $amount);
               }
               
               if($amount > 0){
                   while ($amount > 0){
                       $add = $stackSize;
                       if($amount < $stackSize){
                           $add = $amount;
                       }
                       
                       $bagItem = R::dispense(DBTables::BAG_ITEMS);
                        $bagItem->itemId = $itemId;
                        $bagItem->amount = $add;
                        $bagItem->bagId = $this->bag->id;
                        $this->items[] = $bagItem;
                       
                       $amount-= $add;
                   }
               }
           }else{
           return false;
           }
       }
     
        $this->save();
        $this->load();
        return true;
    }
    
    public function canAddItem($itemId, $amount){
        
               $freeSlots = $this->getFreeSlots();
     
       $item = new Item($itemId);
       if($item->type == 0){
           if($freeSlots >= $amount){
       
           }else{
               
               return false;
           }
       }else{
        
           $slotsWithItem = $this->getAllSlotsWithItemIdAndSpace($itemId);
          
           
           $totalStore = 0;
           $stackSize = $this->getItemStackSize($itemId);
           foreach ($slotsWithItem as $slots) {
               $totalStore+=$this->getFreeSpaceForSlot($slots->id);
           }
          
           $rest = $amount - $totalStore;
           
           $neededFreeSlots = ceil($rest/$stackSize);
           
           
           if($neededFreeSlots <= $this->getFreeSlots()){
          
               foreach ($slotsWithItem as $swi){
                 $amount =  $this->addAmountToSlot($swi->id, $amount,true);
               }
               
               if($amount > 0){
                   while ($amount > 0){
                       $add = $stackSize;
                       if($amount < $stackSize){
                           $add = $amount;
                       }
                       

                       $amount-= $add;
                   }
               }
           }else{
             return false;
           }
       }
     
        return true;
    }
    
    public function wipeBag(){
        R::trashAll($this->items);
    }
    
    public function save(){
        R::store($this->bag);
        R::storeAll($this->items);
    }
    
    public function addAmountToSlot($slotNum, $amount,$fake = false){
       
        
        $itemInSlot = $this->getItemBySlot($slotNum);

        $add = $this->getFreeSpaceForSlot($slotNum);
        if($amount > $add){
        if(!$fake){
            $itemInSlot->amount += $add;
        }
        return $amount-$add;
        
        
        }else{
            if(!$fake){
            $itemInSlot->amount += $amount;
            }
            return 0;
        }
        
    } 
    
    public function getAmountForSlot($slotNum){
        
        
       $slot = $this->getItemBySlot($slotNum);
       
       return $slot->amount;
    }
    
    /**
     * 
     * @param type $slotNum
     * @return 
     */
    public function getItemBySlot($slotNum){
       
        foreach ($this->items as $item) {
            if($item->id == $slotNum){
                return $item;
            }
        }
        
        
        
        return -1;
    }
    
    public function isItemInSlot($slot,$itemId){
        foreach ($this->items as $item){
            if($item->id == $slot && $item->itemId == $itemId){
                return true;
            }
        }
        return false;
    }
    
    public function getItemStackSize($itemId){
         $i =new Item($itemId);
         return $i->stackSize;
    }
    
    public function getAllSlotsWithItemIdAndSpace($itemId){
        $items = $this->getAllItemsWithId($itemId);
       
        if(count($items)<=0)return [];
        $stackSize = $this->getItemStackSize($itemId);
        
        $withSpace = [];
        foreach ($items as $item) {
            if($item->amount < $stackSize){
                $withSpace[] = $item;
            }
        }
        
        return $withSpace;
    }
    
    public function getStackSizeForSlot($slot){
        foreach ($this->items as $item) {
            if($item->id == $slot){
                return $item->amount;
            }
            return -1;
        }
    }
    
    public function getFreeSpaceForSlot($slot){
        $itemInSlot = $this->getItemBySlot($slot);
        $amountInSlot = $itemInSlot->amount;
        $stackSize = $this->getItemStackSize($itemInSlot->itemId);
        
        $add = $stackSize-$amountInSlot;
        return $add;
    }
    
    public function bagHasItem($itemId){
        foreach ($this->items as $i) {
            if($i->itemId == $itemId){return true;}
        }
        return false;
    }
    
    public function getAllItemsWithId($itemId){
        $withId = [];
        foreach ($this->items as $item) {
            
            if($item->itemId == $itemId){
                $withId[] = $item;
            }
        }
        return $withId;
    }
   
    public static function bagExist($ownerT, $ownerI){
        $bag = R::findOne(DBTables::BAG, " owner_type = ? and owner_id = ?",[$ownerT, $ownerI]);
        return $bag != null;
    }
    
    public static function createNewBag($ownerType, $ownerId, $slots){
        
        $bag = R::dispense(DBTables::BAG);
        $bag->ownerType = $ownerType;
        $bag->ownerId = $ownerId;
        $bag->slots = $slots;
        
        $bagId = R::store($bag);
        
        $bagItem = R::dispense(DBTables::BAG_ITEMS);
        $bagItem->itemId = R::findOne(DBTables::ITEMS)->id;
        $bagItem->amount = 1;
        $bagItem->bagId = $bagId;
        R::store($bagItem);
    }
   
}