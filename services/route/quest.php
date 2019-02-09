<?php
using(["game"]);
/** @var $content ServiceObject  */
$content = $serviceRequest->getContent();
$requestId = $content->requestContent->id;
$action = $content->requestContent->action;

/** @var $context Context */
$robj = new ServiceObject();
$robj->requestName = "quest";
$robj->error = true;
$robj->requestContent = "not_implemented";
$robj->displayMessage = "Die Questfeatures sind noch nicht entwickelt ".$action." - ".$requestId;


$char = new Charakter($context);
$questLog = $char->getQuestLog();

$npcInfo = $context->sessionData->getItem("npc");



if($npcInfo != null || explode(";", $npcInfo)[0] != "quest")
{

    $npc = new QuestNpc(explode(";", $npcInfo)[1], $context->getUserId(), $char->getConditionParser(),$char->getQuestGoalActionParser());
    
    if($npc->hasQuest($requestId)){
        
        if($action == "accept"){
            if($questLog->getQuestStatus($requestId) == QuestStatus::NOTACCEPTED){
            
            $questLog->addQuest($requestId);
            $robj->requestContent = true;
            $robj->displayMessage = "Ihr habt die Quest erfolgreich angenommen!";
            $robj->error = false;
            }else{

            $robj->requestContent = "quest_allready_accepted";
            $robj->displayMessage = "Ihr habt diese quest bereits!";
            }
        }
        elseif($action == "solve"){
            
          if($questLog->getQuestStatus($requestId) == QuestStatus::SOLVED){
            
            $questLog->finishQuest($requestId);
            $robj->requestContent = true;
            $robj->displayMessage = "Ihr habt die Quest erfolgreich abgeschlossen!";
            $robj->error = false;
            }else{

            $robj->requestContent = "quest_not_solved";
            $robj->displayMessage = "Die Quest ist noch nicht zur Abgabe bereit!";
            }
            
        }
        
        
        
    }else{
        
        $robj->requestContent = "questgiver_noquest";
        $robj->displayMessage = "Dieser Questgeber bietet diese quest nicht an!"; 
    }
    
    
    
}
else{
    $robj->requestContent = "no_questgiver";
    $robj->displayMessage = "Ihr seid derzeit bei niemandem, der diese Quest anbietet";    

}




$serviceRequest->sendResponse($robj);