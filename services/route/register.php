<?php

$robj = new ServiceObject();
$robj->requestName = "register";

/** @var User */
$user = $context->userData;

/** @var $content ServiceObject  */
$content = $serviceRequest->getContent();



if($user->userExistByName($content->requestContent->username)){
    $robj->error = true;
    $robj->displayMessage = "Der User existiert schon";
    $robj->requestContent = false;
    
}else{
    $robj->error = false;
    $robj->displayMessage = "Dein User wurde angelegt";
    $robj->requestContent = true;
    
}


$serviceRequest->sendResponse($robj);
