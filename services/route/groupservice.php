<?php
using(["game"]);
/** @var $content ServiceObject  */
$content = $serviceRequest->getContent();

$action = $content->requestContent->action;
/** @var $context Context */
$robj = new ServiceObject();
$robj->requestName = "group";
$robj->error = true;
$robj->requestContent = "not_implemented";
$robj->displayMessage = "Die Gruppenfeatures sind noch nicht entwickelt: ".$action;
$char = new Charakter($context);
/**
 * @var Session
 */
$session = $context->sessionData;

$group = $char->getGroup();

if($group == null){

   PlayerGroup::createNew($context->getUserId());
   $group = PlayerGroup::loadPlayerGroup($context->getUserId(), $context->sessionData);
}


if($action == "accept"){
    $group->acceptInvitation($context->getUserId());
    $robj->error = false;
            $robj->requestContent = true;
            $robj->displayMessage = "Member Angenommen";
}
elseif($action == "decline"){
    $group->removeMember($context->getUserId());
    $robj->error = false;
            $robj->requestContent = true;
            $robj->displayMessage = "Member Entfernt";
}elseif($action == "leave"){
    
    $group->removeMember($context->getUserId());
    $robj->error = false;
            $robj->requestContent = true;
            $robj->displayMessage = "Member Entfernt";
    
}elseif($action == "remove"){
     
    if($group->groupLeet == $context->getUserId()){
        $id = $content->requestContent->id;
    $group->removeMember($id);
    $robj->error = false;
            $robj->requestContent = true;
            $robj->displayMessage = "Member Entfernt";
    }else{
        
            $robj->requestContent = "no rights";
            $robj->displayMessage = "Ihr müsst gruppenmember sein um dies durchzuführen";
    }
    
    
}elseif($action == "add"){
    $name = $content->requestContent->name;
    $id = User::getIdByName($name);
    if($id != null){
        
        if(!PlayerGroup::isUserInGroup($id)){
            $group->addMember($id);
            $robj->error = false;
            $robj->requestContent = true;
            $robj->displayMessage = "Member hinzugefügt";
        }else{
            $robj->requestContent = "in_group";
            $robj->displayMessage = $name." ist bereits in einer Gruppe!";
        }
        
    }else{
        
        $robj->requestContent = "no_user";
        $robj->displayMessage = "Der User mit dem Namen: ".$name." wurde nicht gefunden!";
    }
}




$serviceRequest->sendResponse($robj);