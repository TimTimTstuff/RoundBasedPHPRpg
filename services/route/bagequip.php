<?php

using(["game"]);

/** @var $context Context */
$robj = new ServiceObject();
$robj->requestName = "bagequip";

/** @var User */
$user = $context->userData;

/** @var $content ServiceObject  */
$content = $serviceRequest->getContent();



$actions = ["equip", "unequip", "delete", "sell", "buy", "deleteall", "buystack", "sellstack","use"];
$selectedAction = $content->requestContent->action;
if (!in_array($selectedAction, $actions)) {
    $robj->displayMessage = "Unbekannte Aktion in der Aktion";
    $robj->error = true;
} else {

    $pl = R::findOne(DBTables::CHAR, " player_id = ?", [$context->getUserId()]);
    if ($pl == null) {
        Charakter::createNew($context->getUserId());
    }

    $char = new Charakter($context);

    if($selectedAction == "use"){
        $slot = $content->requestContent->slotid;
        $bag = $char->getBag();
        $ap = $char->getManipulationActionParser();
        $item = $bag->getItemBySlot($slot);
        $item = new Item($item->itemId);
        if($item->canUse($bag,$ap)){
            $item->useItem($bag, $ap);
            $robj->error = false;
            $robj->requestContent = true;
            $robj->displayMessage = "Erfolgreich benutzt";
        }else{
             $robj->error = true;
            $robj->requestContent = false;
            $robj->displayMessage = "Item kann nicht benutzt werden!";
        }
        
        
    }
    elseif ($selectedAction == "unequip") {
        $slot = $content->requestContent->slot;
        $equip = $char->getEquip();
        $bag = $char->getBag();

        if ($equip->canUnequip($slot, $bag)) {
            $equip->unequip($slot, $bag);
            $robj->error = false;
            $robj->requestContent = true;
            $robj->displayMessage = "Erfolgreich unequiped";
        } else {
            $robj = $equip->getServiceObject();
        }
    }
    else if ($selectedAction == "equip") {

        $itemId = $content->requestContent->itemid;
        $equip = $char->getEquip();
        $bag = $char->getBag();
        if ($equip->canEquip($itemId, $bag,$char->getLevel())) {
            $equip->equip($itemId, $bag);
            $robj->error = false;
            $robj->requestContent = true;
            $robj->displayMessage = "Erfolgreich equiped";
        } else {
            $robj = $equip->getServiceObject();
        }
    } 
    else if ($selectedAction == "delete" || $selectedAction == "deleteall") {


        $bag = $char->getBag();

        $slotId = $content->requestContent->slotid;
        $amount = $content->requestContent->amount;
        if ($amount < 1) {
            $amount = 0;
        }
        $items = $bag->getItemBySlot($slotId);
        $itemId = $items->itemId;
        if ($selectedAction == "deleteall") {
            $amount = $items->amount;
        } 
            $inf = $bag->removeItem($items->itemId, $amount);
        

        if ($inf == false) {
            $robj->error = true;
            $robj->requestContent = "cant_remove_item";
            $robj->displayMessage = "Item/Items konntent nicht Entfernt werden";
        } else {
            
            $context->log("delete_item", "ID=".$itemId.";AMOUNT=".$amount,$context->getUserId());
            $context->getStatistics()->add(StatisticItems::ITEMSDELETED,$amount);
            $robj->error = false;
            $robj->requestContent = true;
            $robj->displayMessage = "Items erfolgreich entfernt";
        }
    } 
    else if ($selectedAction == "sell" || $selectedAction == "sellstack") {

        $vendorId = null;
        
        $npcData = $context->sessionData->getItem("npc");
        if($npcData != null){
            $npcInfo = explode(";", $npcData);
            if($npcInfo[0] == "vendor"){
                $vendorId = $npcInfo[1];
            }
        }
        

        if ($vendorId == null) {
            $robj->error = true;
            $robj->requestContent = "no_vendor";
            $robj->displayMessage = "Es ist kein Händler da, dem ihr was Verkaufen könntet!";
        } else {


            $vendor = new Vendor($vendorId,$context->getStatistics());
            $bag = $char->getBag();

            if ($selectedAction == "sellstack") {

                $slotNum = $content->requestContent->slotid;
                $item = $bag->getItemBySlot($slotNum);
                $itemId = $item->itemId;
                $amount = $bag->getAmountForSlot($slotNum);
            } else {
                $itemId = $content->requestContent->itemid;
                $amount = $content->requestContent->amount;
            }
            $item = new Item($itemId);
            $money = $char->getMoneyBag();
            $itemsInBag = $bag->getAmountOfItem($itemId);
            $sellPrice = floor($item->sellPrice * $vendor->getBuyPriceMultiplier()) * $amount;
            $vendorGold = $vendor->getVendorGold();




            //Validierung
            if ($itemsInBag < $amount) {
                $robj->error = true;
                $robj->requestContent = "to_less_items";
                $robj->displayMessage = "Das Item befindet sich nicht in der Tasche";
            } elseif ($vendorGold < $sellPrice) {
                $robj->error = true;
                $robj->requestContent = "vendor_no_gold";
                $robj->displayMessage = "Der Händler hat zu wenig geld " . $vendorGold . " - " . $sellPrice;
            }elseif(!$bag->canRemoveItem($itemId, $amount)){
                  $robj->error = true;
                $robj->requestContent = "hasnt_item";
                $robj->displayMessage = "Du hast das Item nicht(mehr)";
            
            }elseif(!$vendor->getBag()->canAddItem($itemId, $amount)){
                 $robj->error = true;
                $robj->requestContent = "no_space";
                $robj->displayMessage = "Der Händler hat kein Platz mehr in der Tasche";
            }elseif (!$bag->removeItem($itemId, $amount)) {
                $robj->error = true;
                $robj->requestContent = "unknown_item_remove";
                $robj->displayMessage = "Unbekannter fehler beim entfernen des Items aus der Tasche";
            } elseif (!$vendor->getBag()->addItem($itemId, $amount)) {
                $robj->error = true;
                $robj->requestContent = "unknown_add";
                $robj->displayMessage = "Unbekannter fehler beim hinzufügen zur Tasche";
            } else {


                if (!$vendor->removeGold($sellPrice)) {
                    $robj->error = true;
                    $robj->requestContent = "to_less_money";
                    $robj->displayMessage = "Der Vendor hat zu wenig Geld";
                }
                $money->addMoney($sellPrice,$context->getStatistics());
                
                $context->getStatistics()->add(StatisticItems::ITEMSSELLED,$amount);
                $vendor->save();
                $char->saveChar();
                $robj->error = false;
                $robj->requestContent = true;
                $robj->displayMessage = "Item erfolgreich verkauft";
            }
        }
    } 
    else if ($selectedAction == "buy" || $selectedAction == "buystack") {

        $vendorId = null;
        
        $npcData = $context->sessionData->getItem("npc");
        if($npcData != null){
            $npcInfo = explode(";", $npcData);
            if($npcInfo[0] == "vendor"){
                $vendorId = $npcInfo[1];
            }
        }
        


        if ($vendorId == null) {
            $robj->error = true;
            $robj->requestContent = "no_vendor";
            $robj->displayMessage = "Es ist kein Händler da, von dem ihr was kaufen könntet!";
        } else {
            $vendor = new Vendor($vendorId,$context->getStatistics());
            $bag = $char->getBag();
            $vendorBag = $vendor->getBag();
            if ($selectedAction == "buystack") {

                $slotNum = $content->requestContent->slotid;
                $item = $vendorBag->getItemBySlot($slotNum);
                $itemId = $item->itemId;
                $amount = $vendorBag->getAmountForSlot($slotNum);
            } else {
                $itemId = $content->requestContent->itemid;
                $amount = $content->requestContent->amount;
            }

            $item = new Item($itemId);
            $money = $char->getMoneyBag();
            $itemsInBag = $vendorBag->getAmountOfItem($itemId);
            $buyPrice = floor($item->sellPrice * $vendor->getSellPriceMultiplier()) * $amount;
            $vendorGold = $vendor->getVendorGold();



            //Validierung
            if ($itemsInBag < $amount) {
                $robj->error = true;
                $robj->requestContent = "to_less_items";
                $robj->displayMessage = "Es sind keine oder zuwenig items in der Tasche";
            } 
            elseif ($money->getMoney() < $buyPrice) {
                $robj->error = true;
                $robj->requestContent = "vendor_no_gold";
                $robj->displayMessage = "Du hast zu wenig geld " . $money->getDisplayValue() . " - " . $buyPrice;
            }elseif(!$vendorBag->canRemoveItem($itemId, $amount)){
                  $robj->error = true;
                $robj->requestContent = "hasnt_item";
                $robj->displayMessage = "Der Händler hat das Item nicht(mehr)";
            
            }elseif(!$bag->canAddItem($itemId, $amount)){
                 $robj->error = true;
                $robj->requestContent = "no_space";
                $robj->displayMessage = "Du hast kein Platz mehr in der Tasche";
            }
            elseif(!$vendorBag->removeItem($itemId, $amount)) {
                $robj->error = true;
                $robj->requestContent = "unknown_item_remove";
                $robj->displayMessage = "Unbekannter fehler beim entfernen des Items aus der Tasche";
            } elseif (!$bag->addItem($itemId, $amount)) {                
                $robj->error = true;
                $robj->requestContent = "no_space";
                $robj->displayMessage = "Kein platz in der Tasche";
            } else {


                //kaufen
                if (!$money->removeMoney($buyPrice,$context->getStatistics())) {
                    $robj->error = true;
                    $robj->requestContent = "to_less_money";
                    $robj->displayMessage = "Der Vendor hat zu wenig Geld";
                }
                $vendor->addGold($buyPrice);
                    $context->getStatistics()->add(StatisticItems::ITEMSBUYED,$amount);
                $vendor->save();
                $char->saveChar();
                $robj->error = false;
                $robj->requestContent = true;
                $robj->displayMessage = "Item erfolgreich verkauft";
            }

        }
    }
}


$serviceRequest->sendResponse($robj);
