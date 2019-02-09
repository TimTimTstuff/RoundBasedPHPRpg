<?php

class NpcRenderer implements IRenderer {

    private $npc;

    /**
     *
     * @var Person 
     */
    private $person;

    /**
     *
     * @var Vendor 
     */
    private $vendor;

    /**
     *
     * @var QuestNpc
     */
    private $quest;

    /**
     *
     * @var TrainerNpc
     */
    private $trainer;
    private $type;
    private $template;

    public function __construct($npc) {

        $this->type = $npc->getType();

        $this->template = file_get_contents("pages/gametemplates/npc_frame.html");
        $this->npc = $npc;
    }

    public function getNpcHtml() {

        if ($this->type == NpcType::Person) {
            $this->person = $this->npc;
            return $this->getPersonHtml();
        } elseif ($this->type == NpcType::Vendor) {
            $this->vendor = $this->npc;
            return $this->getVendorHtml();
        } elseif ($this->type == NpcType::Quest) {

            $this->quest = $this->npc;
            return $this->getQuestNpcHtml();
        } elseif ($this->type == NpcType::Trainer) {
            $this->trainer = $this->npc;
            return $this->getTrainerNpcHtml();
        }
    }

    public function getPersonHtml() {
        $this->template = str_replace("{{npc_randtext}}", $this->person->getText(), $this->template);
        $this->template = str_replace("{{npc_extra}}", "", $this->template);
        return $this->template = str_replace("{{npc_id}}", $this->person->getNpc()->npcId, $this->template);
    }

    public function getVendorHtml() {
        $this->template = str_replace("{{npc_randtext}}", $this->vendor->getText(), $this->template);
        $this->template = str_replace("{{npc_id}}", $this->vendor->getNpc()->npcId, $this->template);
        $bagRender = new BagRenderer($this->vendor->getBag(), 6, "vendor_bag.html", "vendor_slot", $this->vendor->getBuyPriceMultiplier(), $this->vendor->getSellPriceMultiplier());
        $this->template = str_replace("{{npc_extra}}", $bagRender->getBagHtml(), $this->template);

        return str_replace("{{vendor_gold}}", $this->vendor->getVendorGold(), $this->template);
    }

    public function getQuestNpcHtml() {



        $this->template = str_replace("{{npc_randtext}}", $this->quest->getText(), $this->template);
        $this->template = str_replace("{{npc_id}}", $this->quest->getNpc()->npcId, $this->template);

        $questRender = new QuestWindowRenderer($this->quest);

        $this->template = str_replace("{{npc_extra}}", $questRender->getHtml(), $this->template);

        return $this->template;
    }

    public function getTrainerNpcHtml() {
        $this->template = str_replace("{{npc_randtext}}", $this->trainer->getText(), $this->template);
        $this->template = str_replace("{{npc_id}}", $this->trainer->getNpc()->npcId, $this->template);

        $questRender = new TrainerWindowRenderer($this->trainer->getCostList(), $this->trainer->getConfig(),$this->trainer);

        $this->template = str_replace("{{npc_extra}}", $questRender->getHtml(), $this->template);

        return $this->template;
    }

    public function getHtml() {
        return $this->getNpcHtml();
    }

}

class QuestWindowRenderer implements IRenderer {

    /**
     *
     * @var QuestNpc 
     */
    private $npc;
    private $template;
    private $questTemplate;

    public function __construct($npc) {
        $this->npc = $npc;
        $this->template = RenderMain::loadGameTemplate("quest_window");
        $this->questTemplate = RenderMain::loadGameTemplate("quest");
    }

    /**
     * 
     * @param Quest $quest
     * @param string $type new,active,solved
     * @param string $acceptText
     * @param string $declineText
     * @param string $solveText
     * @return string
     */
    private function getQuestHtml($quest, $type) {

        $acceptText = "Annehmen";
        $declineText = "Zurück";
        $solveText = "Abgeben";
        $questText = "";

        $questTextList = $quest->getText($type);
        $questText = $questTextList[0];
        if (count($questTextList) == 3) {
            $declineText = $questTextList[2];
        }
        if (count($questTextList) >= 2) {
            $acceptText = $questTextList[1];
        }



        $textAccept = "<a href='javascript:;' class='accept' data-action='accept'>$acceptText</a>";
        $textSolve = "<a href='javascript:;' class='accept' data-action='solve'>$acceptText</a>";
        $textDecline = "<a href='javascript:;' class='decline' data-action='decline'>$declineText</a>";



        $answer = "";

        if ($type == "new") {
            $answer = $textAccept;
        } elseif ($type == "solved") {
            $answer = $textSolve;
        }
        $answer .= "<br/>" . $textDecline;

        $rewardText = "<small>";

        foreach ($quest->getReward() as $reward) {

            switch ($reward[0]) {
                case "xp":
                    $rewardText .= "Xp: " . $reward[1];
                    break;
                case "item":
                    $item = new Item($reward[1][0]);
                    $rewardText .= $reward[1][1] . " x <span class='rare_$item->rarity'>" . $item->name . "</span> <small>[" . DICT::ITEM_TYPE[$item->type] . "]</small>";
                    break;
                case "gold":
                    $rewardText .= "Gold: " . $reward[1];
                    break;
                case "honor":
                    $rewardText .= "Ehre: " . $reward[1];
                    break;
                case "reputation":
                    $rewardText .= "Ruf: " . $reward[1][1] . " bei " . $reward[1][0];
                    break;
                default:
                    break;
            }

            $rewardText .= "<br/>";
        }
        $rewardText .= "</small>";

        $temp = $this->questTemplate;
        $temp = str_replace("{{quest_id}}", $quest->id, $temp);
        $temp = str_replace("{{quest_text}}", $questText, $temp);
        $temp = str_replace("{{quest_reward}}", $rewardText, $temp);
        $temp = str_replace("{{quest_answer}}", $answer, $temp);


        return $temp;
    }

    private function getQuestItem($id, $name, $marker) {
        return " <li class='quest_item' data-id='$id'> <span class='quest_marker'>$marker </span>$name</li>";
    }

    private function buildQuestContent($questList, $type) {
        if (count($questList) <= 0) {
            return "";
        }
        $c = "";
        foreach ($questList as $q) {
            $c .= $this->getQuestHtml($q, $type);
        }
        return $c;
    }

    public function getHtml() {


        $newQuest = $this->buildQuestList($this->npc->getNewQuests(), "!", "new");
        $activQuest = $this->buildQuestList($this->npc->getActivQuests(), "", "active");
        $solvedQuest = $this->buildQuestList($this->npc->getResolvedQuest(), "?", "sovled");

        $questContent = $this->buildQuestContent($this->npc->getNewQuests(), "new");
        $questContent .= $this->buildQuestContent($this->npc->getActivQuests(), "active");
        $questContent .= $this->buildQuestContent($this->npc->getResolvedQuest(), "solved");





        $temp = str_replace("{{new_quests}}", $newQuest, $this->template);
        $temp = str_replace("{{activ_quests}}", $activQuest, $temp);
        $temp = str_replace("{{solved_quests}}", $solvedQuest, $temp);
        return str_replace("{{quest_content}}", $questContent, $temp);
    }

    /**
     * 
     * @param Quest[] $questList
     * @param string $m
     * @return string
     */
    private function buildQuestList($questList, $m, $type) {

        if (count($questList) <= 0) {
            return "";
        }
        $tempQl = "<ul class='questlist'>";

        foreach ($questList as $quest) {
            $tempQl .= $this->getQuestItem($quest->id, $quest->name, $m);
        }
        $tempQl .= "</ul>";
        return $tempQl;
    }

}

class TrainerWindowRenderer implements IRenderer {

    private $template;
    private $priceList;
    private $config;

    /**
     *
     * @var TrainerNpc
     */
    private $char;
    
    public function __construct($priceList, $trainerConfig,$trainer) {
        $this->config = $trainerConfig;
        $this->priceList = $priceList;
        $this->template = RenderMain::loadGameTemplate("trainer_window");
        $this->char = $trainer;
    }

    public function getHtml() {

        $temp = "";
        foreach ($this->priceList as $key => $value) {
            $temp .= $this->getElement($key, $value[1], $value[0], $this->config[$key], $this->char->hasXp($value[1]) );
        }

        return str_replace("{{content}}", $temp, $this->template);
    }

    public function getElement($stat, $preis, $stufe, $max, $canAffort) {

        if ($stat != "level") {
            $name = DICT::STATS_NAME[$stat];
        } else {
            $name = "level";
        }
        $row = "<div class='buy_element' data-type='$stat'>
        <span class='title'> " . $name . " Stufe: $stufe <small>(max. $max}</small></span>
        Preis: $preis Xp ";
        if ($canAffort) {
            $row .= "<span class='buy_stat'>Lernen</span>";
        }
        //return $row . "<div style='clear:both;'></div></div>";
        
        
        $r = "<tr><td>$name</td><td>$stufe</td><td>$max</td><td>$preis</td><td>";
        if ($canAffort) {
            $r .= "<span data-stat='$stat' class='buy_stat'>Lernen</span>";
        }
        $r.="</td></tr>";
        return $r;
    }

}
