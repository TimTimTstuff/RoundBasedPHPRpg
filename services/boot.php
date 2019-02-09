<?php
header('Content-Type: application/json');
R::freeze( FALSE );
R::fancyDebug(FALSE);
/**
 * @var ServiceObjectParser Description
 */

$serviceRequest = new ServiceObjectParser($context->request);   


 
if(!$context->isLoggedIn() && $serviceRequest->getAction() != "login" && $serviceRequest->getAction() != "register" ){
    $sobj = new ServiceObject();
    $sobj->requestName = "connect";
    $sobj->error = "not_login";
    $sobj->displayMessage = "Sie sind nicht eingelogt";
    echo json_encode($sobj);
}
$action = "";
if(isset($_GET["a"])){
    $action = $_GET["a"];
}else{
    $action = $serviceRequest->getAction();
}

switch ($action){
    case "login":
        include 'route/login.php';
        break;
    case "register":
        
        break;
    case "bagequip":
        include 'route/bagequip.php';
        break;
    case "chat":
        include 'route/chat.php';
        break;
    case "fmap":
        include 'route/fmap.php';
        break;
    case "quest":
        include 'route/quest.php';
        break;
    case "fight":
        include 'route/fightservice.php';
        break;
    case "group":
        include 'route/groupservice.php';
        break;
    case "stat":
        include "route/trainingservice.php";
        break;
    case "info":
        include "route/gameinfo.php";
        break;
    case "meta":
        include 'route/meta.php';
    default:
          $sobj = new ServiceObject();
        $sobj->requestContent = "unknown_action";
        $sobj->requestName = "connect";
        $sobj->error = true;
        $sobj->displayMessage = "Unbekannte Aktion";
        echo json_encode($sobj);
        break;
}


