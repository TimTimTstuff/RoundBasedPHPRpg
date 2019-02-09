<?php
using(["game"]);
/** @var $content ServiceObject  */
$content = $serviceRequest->getContent();

$action = $content->requestContent->action;
/** @var $context Context */
$robj = new ServiceObject();
$robj->requestName = "fmap";
$robj->error = true;
$robj->requestContent = "not_implemented";
$robj->displayMessage = "Die Kartenfeatures sind noch nicht entwickelt: ".$action;
$char = new Charakter($context);
/**
 * @var Session
 */
$session = $context->sessionData;

$id = explode(";",$session->getItem("map"));
$map = new FaceMap($id[1]);
$maPr =new MapActionProcessor($context, $char );


if($action == "open"){
    $requestId = $content->requestContent->id;
    $action = $map->gotoItem($requestId);
    if($action != FALSE){
        $maPr->doAction($action);
        $robj->error = false;
        $robj->displayMessage = "";
        $robj->requestContent = true;
    }else{
         $robj->error = false;
        $robj->displayMessage = "Hier könnt ihr noch nicht hin";
        $robj->requestContent = FALSE;
    }
}
elseif($action == "spaceaction"){
    $spaceId = $session->getItem("space");
    $actionName = $content->requestContent->name;
    if($spaceId != null)
    {
        $space = new SpaceHandler($spaceId,$char->getConditionParser() ,$char->getManipulationActionParser());
        
        $response = $space->doAction($actionName);
        if($response->isError){
        
            $robj->error = true;
            $robj->displayMessage = $response->displayMessage;
            $robj->requestContent = FALSE;
        }else{
            
              $robj->error = false;
              $robj->displayMessage = "Aktion erfolgreich ausgeführt";
              $robj->requestContent = true;
            
        }
        
    }
      else  {
        $robj->error = false;
        $robj->displayMessage = "Ihr seid am falschen ort für diese aktion!";
        $robj->requestContent = FALSE;
    }
}



$serviceRequest->sendResponse($robj);
