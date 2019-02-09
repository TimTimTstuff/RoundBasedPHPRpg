<?php
using(["game"]);
/** @var $content ServiceObject  */
$content = $serviceRequest->getContent();

$action = $content->requestContent->action;
/** @var $context Context */
$robj = new ServiceObject();
$robj->requestName = "fight";
$robj->error = true;
$robj->requestContent = "not_implemented";
$robj->displayMessage = "Die Kampffeatures sind noch nicht entwickelt: ".$action;
$fightId = $context->sessionData->getItem("fightid");
$fight = new FightProcessor($fightId, $context->getUserId(), $context);

if($action == "getfighter"){
    
    
    
      if($fight->isNextNpc() && $fight->getPlayer()[0]->getRealId() == $context->getUserId()){
           $fight->processNpcFight();
       }
       
      if($fight->isTimeOut()){
          
          $fight->skipRound();
      }
    
      
      if($fight->isFightWind() || $fight->isFightLost()){
          $fight->finishFight();
      }
      
      
   $set = [];
   $set["current"] = $fight->getCurrentActor();
   $set["currname"] = $fight->getMemberById($fight->getCurrentActor())->getActorName();
   $set["currindex"] = $fight->getMemberById($fight->getCurrentActor())->getIndex();
   $set["player"] = [];
   $set["pwin"] = $fight->isFightWind();
   $set["mwin"] = $fight->isFightLost();
   
   /**
    * @var FightMember $p
    */
    foreach (array_merge($fight->getPlayer(),$fight->getMobs()) as $pl) {
        $data = [];
        $data["hp"] = $pl->getCurrentHp();
        $data["mana"] = $pl->getCurrMana() ;
        $data["id"] = $pl->getActorId() ;
        $data["dead"] = $pl->isFainted();
        $data["aggro"] = $pl->getAggro();
        $data["speed"] = $pl->getSpeedValue();
        if($pl->isPlayerId($context->getUserId())){
            $set["me"] = $pl->getActorId();
        }
        $set["player"][] = $data;
        
    }
    
   
    $robj->error = false;
   $robj->requestContent = "not_implemented";
    $robj->displayMessage = json_encode($set);
    
}
elseif($action == "doattack"){

   
    $fa = $content->requestContent->data;
    
    if($fight->getMemberById($fight->getCurrentActor())->isPlayerId($context->getUserId())){
   
   if($fight->doFightActionAdvanced($fa)){
      
   
             $robj->error = false;
   $robj->requestContent = TRUE;
    $robj->displayMessage = "Attacke Erfolgreich";  
  
   }else{
    $robj->error = true;
    $robj->requestContent = false;
    $robj->displayMessage = "Die Attacke kann nicht durchgeführt werden!"; 
    
   }
    }
   
  
}
elseif($action == "log"){
    
    $msg = [];
    $d = $fight->getFightLog();
    
    foreach ($d as $m) {
        $msg[] = $m->msg;
    }
    $robj->requestContent = json_encode($msg);
    $robj->displayMessage = "chat geladen";
    $robj->error = false;
}

$serviceRequest->sendResponse($robj);