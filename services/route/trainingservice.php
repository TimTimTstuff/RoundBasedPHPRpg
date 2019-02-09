<?php
using(["game"]);
/** @var $content ServiceObject  */
$content = $serviceRequest->getContent();

$action = $content->requestContent->action;

/** @var $context Context */
$robj = new ServiceObject();
$robj->requestName = "train";
$robj->error = true;
$robj->requestContent = "not_implemented";
$robj->displayMessage = "Die Trainingsfeatures sind noch nicht entwickelt " . $action ;


$char = new Charakter($context);


$npcInfo = $context->sessionData->getItem("npc");
$npc = null;
$data = $content->requestContent->name;
if ($npcInfo != null || explode(";", $npcInfo)[0] != "quest") {

    $npc = new TrainerNpc(explode(";", $npcInfo)[1], $char, $char->getConditionParser());
    if ($npc->hasArticle($data)) {

        if ($action == "buy") {


            if ($npc->train($data)) {
                $robj = new ServiceObject();
                $robj->error = false;
                $robj->requestContent = true;
                $robj->displayMessage = "Erfolgreich gesteigert";
            } else {

                $robj = new ServiceObject();
                $robj->requestContent = "no_xp";
                $robj->displayMessage = "Nicht genügend XP für den Kauf";
            }
        }
    } else {

        $robj = new ServiceObject();
        $robj->requestContent = "no_npc";
        $robj->displayMessage = "Dieser Lehrer kann dir das nicht beibringen";
    }
} else {
    $robj = new ServiceObject();
    $robj->requestContent = "no_npc";
    $robj->displayMessage = "Es wurde kein Trainer gefunden";
}
$serviceRequest->sendResponse($robj);
