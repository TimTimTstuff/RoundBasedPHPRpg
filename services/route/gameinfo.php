<?php

using(["game"]);
/** @var $content ServiceObject  */
$content = $serviceRequest->getContent();

$action = $content->requestContent->action;

/** @var $context Context */
$robj = new ServiceObject();
$robj->requestName = "info";
$robj->error = false;
$robj->requestContent = false;
$robj->displayMessage = "none";

$char = new Charakter($context);

$la = $context->sessionData->getItem("la");
if($la == null) $la = time();
$diff = time()-$la;
$context->sessionData->addItem("la",time());

$char->regPlayer($diff);
$char->saveChar();

 $f = FightInitializer::searchForActivFight($context->getUserId());
if($f != null){
    $robj->requestContent = "reload";
}

$serviceRequest->sendResponse($robj);

