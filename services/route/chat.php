<?php

using(["game"]);

/** @var $context Context */
$robj = new ServiceObject();
$robj->requestName = "chat";


$userId = $context->getUserId();

$channel = $context->sessionData->getItem("channel");

/** @var $content ServiceObject  */
$content = $serviceRequest->getContent();

$group = PlayerGroup::loadPlayerGroup($context->getUserId(), $context->sessionData);


$actions = ["send","read"];
$selectedAction = $content->requestContent->action;
if (!in_array($selectedAction, $actions)) {
    $robj->displayMessage = "Unbekannte Aktion in der Aktion";
    $robj->error = true;
} else {

    $chat = new Chat($channel, $userId);
    
    if($selectedAction == "send"){
      $msg = $content->requestContent->msg;
      if(substr($msg, 0,2) == "/p" && $group != null){
        $prefix = "[P]";
          if($group->groupLeet == $context->getUserId()){
            $prefix = "[PL]";
        }
        
        $msgGroup = str_replace("/p", $prefix, $msg);
        $chat->addMessage($msgGroup, $group->groupKey);
      }else{
       $chat->addMessage($msg);
      }
       $robj->displayMessage = "";
       $robj->error = false;
       $robj->requestContent = true;
        
    }elseif($selectedAction == "read"){
        
      $time =  $context->sessionData->getItem("lastchat");
      
      if($time == null){
          $time = date("Y-m-d H:i:s");
      }
      
      $loadAll = $content->requestContent->time;
      
      if($loadAll == TRUE){$time = null;}
      $groupKey = null;
      if($group != null){
          $groupKey = $group->groupKey;
      }
        $c = $chat->getMessages($time,$groupKey);
        $context->sessionData->addItem("lastchat",date("Y-m-d H:i:s"));
        $msgs = [];
       
        foreach($c as $m){
            $msgs[] = [
                "u"=>User::getNameById($m->userId),
                "t"=>$m->createtime,
                "m"=>$m->msg
            ];
        }
       $robj->displayMessage = "";
       $robj->error = false;
       $robj->requestContent = json_encode($msgs);
        
    }
  
}


$serviceRequest->sendResponse($robj);
