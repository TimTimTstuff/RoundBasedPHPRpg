<?php

class MainView {

    /**
     *
     * @var Context
     */
    private $context;

    /**
     *
     * @var Charakter
     */
    private $char;
    private $mainRender;

    public function __construct($context, $char, $render) {
        $this->context = $context;
        $this->char = $char;
        $this->mainRender = $render;
    }

    private function addQuestLog() {
        $qlr = new QuestLogRenderer($this->char);
        $this->mainRender->addContentRightRender($qlr);
    }

    private function addCharBag() {
        $npcData = $this->context->sessionData->getItem("npc");
        if ($npcData != null) {
            $npC = explode(";", $npcData);
        } else {
            $npC = ["", ""];
        }
        if ($npC[0] == "vendor") {
            $currVendor = new Vendor($npC[1], $this->char->getConditionParser());

            $br = new BagRenderer(
                    $this->char->getBag(), 5, "char_bag.html", "bag_slot", $currVendor->getBuyPriceMultiplier(), $currVendor->getSellPriceMultiplier());
        } else {
            $br = new BagRenderer(
                    $this->char->getBag(), 5);
        }


        $this->mainRender->addContentRightRender($br);
    }

    private function addCharInfo() {
        $sc = new StatsRenderer($this->char->getStats(), $this->char->getEquip()->getStatsValueArray(), $this->char->getFightValues());
        $this->mainRender->addContentRightRender($sc);
    }

    private function addCharEquip() {
        $cr = new EquipRenderer($this->char->getEquip());
        $this->mainRender->addContentRightRender($cr);
    }

    private function addCharInfoHead() {

        $cr = new CharInfoRenderer($this->char);
        $this->mainRender->addHeadRenderer($cr);
    }

    private function addNpc() {

        $ses = $this->context->sessionData;
        $nr = null;

        $currentNpc = $ses->getItem("npc");

        if ($currentNpc != null) {
            $npcData = explode(";", $currentNpc);
            if ($npcData[0] == "vendor") {
                $nr = new NpcRenderer(new Vendor($npcData[1], $this->char->getConditionParser()));
            } elseif ($npcData[0] == "person") {
                $nr = new NpcRenderer(new Person($npcData[1], $this->char->getConditionParser()));
            } elseif ($npcData[0] == "quest") {
                //find better way
                $this->char->getQuestLog();
                $nr = new NpcRenderer(new QuestNpc($npcData[1], $this->context->getUserId(), $this->char->getConditionParser(), $this->char->getQuestGoalActionParser()));
            } elseif ($npcData[0] == "trainer") {
                $nr = new NpcRenderer(new TrainerNpc($npcData[1], $this->char, $this->char->getConditionParser()));
            }
        }

        if ($nr != null) {
            $this->mainRender->addContentLeftRender($nr);
        }
    }

    private function addGroup() {

        $r = new GroupRenderer($this->char);

        $this->mainRender->addContentRightRender($r);
    }

    private function addSpace() {

        $ses = $this->context->sessionData;
        $nr = null;

        $currentSpace = $ses->getItem("space");

        if ($currentSpace != null) {
            $nr = new SpaceRenderer(new SpaceHandler($currentSpace, $this->char->getConditionParser(), $this->char->getManipulationActionParser()));
            $this->mainRender->addContentLeftRender($nr);
        }
    }

    public function getChannel() {
        return "admin";
    }

    public function show() {

        $map = $this->context->sessionData->getItem("map");
        $mapData = explode(";", $map);
        if ($mapData[0] == MapType::FMAP) {
            $mapId = $mapData[1];
            $map = new FaceMap($mapId);
            $this->mainRender->addContentCenterRender(new RenderFaceMap($map));
        }


$this->mainRender->addFooter(RenderMain::loadGameTemplate("footer"));
        $this->addNpc();
        $this->addSpace();

        //allways
        $this->addQuestLog();
        $this->addCharBag();
        $this->addCharEquip();
        $this->addCharInfo();
        $this->addCharInfoHead();
        $this->addGroup();
    }

}
