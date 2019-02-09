<?php

/** @var $context Context */

$robj = new ServiceObject();
$robj->requestName = "login";

/** @var User */
$user = $context->userData;

/** @var $content ServiceObject  */
$content = $serviceRequest->getContent();



if($user->checkUserWithPassword($content->requestContent->username,$content->requestContent->password)){
    $robj->error = false;
    $robj->displayMessage = "Login erfolgreich";
    $robj->requestContent = true;
    
    $context->loginUser($content->requestContent->username);
    
    
}else{
    $robj->error = true;
    $robj->displayMessage = "Ein User mit diesem Passwort wurde nicht gefunden";
    $robj->requestContent = false;
    
}


$serviceRequest->sendResponse($robj);
