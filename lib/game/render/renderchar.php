<?php

class StatsRenderer implements IRenderer {

    private $template;

    /**
     *
     * @var Stats 
     */
    private $baseStats;
    private $armorStats;

    /**
     *
     * @var FightValues 
     */
    private $valueRender;

    public function __construct($baseStats, $armorStats, $valueCalc) {
        $this->baseStats = $baseStats;
        $this->armorStats = $armorStats;
        $this->template = file_get_contents("pages/gametemplates/char_stats.html");
        $this->valueRender = $valueCalc;
    }

    public function getStatsHtml() {

        $stats = array_keys(DICT::STATS_NAME);
        $temp = "";

        foreach ($stats as $statName) {
            $equipStat = 0;
            if (array_key_exists($statName, $this->armorStats)) {
                $equipStat = $this->armorStats[$statName];
            }
            $total = $this->baseStats->$statName + $equipStat;
            $temp .= $this->fillTemplateData(DICT::STATS_NAME[$statName], $total, $this->baseStats->$statName, $equipStat);
        }

        $temp .= $this->fillTemplateData("Hp",$this->valueRender->getHp()." / ". $this->valueRender->getMaxHp());
        $temp .= $this->fillTemplateData("Mp",$this->valueRender->getMana()." / ". $this->valueRender->getMaxMana());
        $temp .= $this->fillTemplateData("Schaden", $this->valueRender->getMinDmg() . " - " . $this->valueRender->getMaxDmg());
        $temp .= $this->fillTemplateData("Sch. Mind. %", $this->valueRender->getArmorReduce());
        $temp .= $this->fillTemplateData("Ausw. %", $this->valueRender->getAvoidChance());
        $temp .= $this->fillTemplateData("Krit. % ", $this->valueRender->getCritChance());
        $temp .= $this->fillTemplateData("Hp/s", $this->valueRender->getHpRegPerSec());
        $temp .= $this->fillTemplateData("Mp/s", $this->valueRender->getManaRegPerSec());
        $temp .= $this->fillTemplateData("Krit Mult.", $this->valueRender->getCritMult());
        $temp .= $this->fillTemplateData("Zaubersch.", $this->valueRender->getSpell());
        
        return str_replace("{{content}}", $temp, $this->template);
    }

    private function fillTemplateData($displayName, $total, $base = null, $equip = null) {

        $temp = "<div class='stats_row'>";
        if ($base == null && $equip == null) {
            $temp .= "<small>";
        }
        $temp .= "<span class='stat_label'>$displayName: </span>
        <span class='stat_value'>$total</span>";
        if ($base == null && $equip == null) {
            $temp .= "</small>";
        }
        if ($base != null || $equip != null) {
            $temp .= "
        <small>
            (<span class='stat_base_value'>$base</span>
            <span class='stat_bonus_value'>+ $equip</span>)
        </small>";
        }
        $temp .= "</div>";


        return $temp;
    }

    public function getHtml() {
        return $this->getStatsHtml();
    }

}

class BagRenderer implements IRenderer {

    /**
     *
     * @var ItemBag 
     */
    private $bag;
    private $size;
    private $cols = 6;
    private $rows;
    private $template;
    private $slotclass;
    private $sellMult;
    private $buyMult;

    /**
     * 
     * @param ItemBag $bag
     */
    public function __construct($bag, $cols = 2, $templateName = "char_bag.html", $slotClass = "bag_slot", $sellMult = 1, $buyMult = 1) {
        $this->bag = $bag;
        $this->cols = $cols;
        $info = $this->bag->getBagInfo();
        $this->rows = ceil($info["slots"] / $this->cols);
        $this->size = $info["slots"];
        $this->template = file_get_contents("pages/gametemplates/" . $templateName);
        $this->slotclass = $slotClass;
        $this->sellMult = $sellMult;
        $this->buyMult = $buyMult;
    }

    public function getBagHtml() {

        $table = "";
        $infoBox = "";
        $slots = 0;

        $items = $this->bag->getItems();

        for ($c = 0; $c < $this->rows; $c++) {
            $table .= "<tr>\r\n";
            for ($r = 0; $r < $this->cols; $r++) {
                $item = null;
                $id = 0;
                $amount = "";
                $type = "";
                $slotId = "";
                $sellPrice = 0;
                $buyPrice = 0;
                $img = "0";
                if (isset($items[$slots])) {
                    $item = $items[$slots];
                    $id = $items[$slots]->id;
                    $img = $items[$slots]->imagekey;
                    $amount = $items[$slots]->amount;
                    $type = $item->type;
                    $sellPrice = floor($item->sellPrice * $this->sellMult);
                    $buyPrice = ceil($item->sellPrice * $this->buyMult);

                    $allSlots = $this->bag->getAllItemsWithId($id);

                    if (count($allSlots) == 1) {
                        $slotId = $allSlots[0]->id;
                    } else {
                        foreach ($allSlots as $itemSlot) {
                            if ($itemSlot->amount == $amount) {
                                $slotId = $itemSlot->id;
                                break;
                            }
                        }
                    }
                    $infoBox .= $this->addInfoBoxForItem($item);
                }
                if ($slots < $this->size) {
                    $table .= "<td class='$this->slotclass' data-sell='$sellPrice' data-buy='$buyPrice' data-slotid='$slotId' data-type='$type' data-id='$id' style=\"background-image:url(frontend/game/items/$img.png)\">$amount</td>";
                }
                $slots++;
            }
            $table .= "\r\n</tr>\r\n";
        }
        $this->template = str_replace("{{slot_info_bag}}", $infoBox, $this->template);
        return str_replace("{{bag_table}}", $table, $this->template);
    }

    public function addInfoBoxForItem($item) {
        if ($item->type == 0) {

            if ($item->getExtData()->waponType == -1) {
                return $this->slotInfoArmor($item);
            }
            else {
                return $this->slotInfoWeapon($item);
            }
        } 
        else {
            return $this->slotInfoBaseItem($item->name, $item->rarity, $item->id, $item->description, DICT::ITEM_TYPE[$item->type], "X", $item->sellPrice,0);
        }
    }

    private function slotInfoBaseItem($name, $rare, $id, $description, $type, $material, $price,$level, $values = "", $actions = "") {


        $doc = " <div style='display:none' class='slot_info' id='item_$id'>
        <div class='bag_slot_head rare_$rare'>
            $name
        </div>
        <div class='bag_slot_content'>
            <span class='description'><small>$description</small></span>
            <span class='item_values'><small>$values</small></span>
            <span class='item_actions'><small>$actions</small></span>
        </div>
        <div class='bag_slot_footer'>
            <small>T: $type M:$material S: $price L: $level</small>
        </div>
    </div>";

        return $doc;
    }

    /**
     * 
     * @param Equip $item
     * @return type
     */
    private function slotInfoArmor($item) {
        $bonus = "";
        $stat = $item->stat;
        
        foreach (DICT::STATS_NAME as $key => $value) {
            if (isset($stat->data[$key])) {
                $bonus .= "<br/>" . $value . ": " . $stat->data[$key];
            }
        }

        return $this->slotInfoBaseItem($item->name, $item->rarity, $item->id, $item->description, DICT::ARMOR_NAME[$item->getExtData()->slot], DICT::MATERIAL_NAME[$item->getExtData()->material], $item->sellPrice,$item->getExtData()->minlevel, $bonus);
    }

    private function slotInfoWeapon($item) {

        $bonus = "";
        $stat = $item->stat;
        foreach (DICT::STATS_NAME as $key => $value) {
            if (isset($stat->data[$key])) {
                $bonus .= "<br/>" . $value . ": " . $stat->data[$key];
            }
        }

        $bonus .= "<br/>Dmg: " . $stat->data["mindmg"] . " - " . $stat->data["maxdmg"];

        return $this->slotInfoBaseItem($item->name, $item->rarity, $item->id, $item->description, DICT::WEAPON_NAME[$item->getExtData()->waponType], DICT::MATERIAL_NAME[$item->getExtData()->material], $item->sellPrice,$item->getExtData()->minlevel, $bonus);
    }

    public function getHtml() {
        return $this->getBagHtml();
    }

}

class EquipRenderer implements IRenderer {

    /**
     *
     * @var PlayerEquipment 
     */
    private $equip;
    private $template;

    public function __construct($equip) {
        $this->equip = $equip;
        $this->template = file_get_contents("pages/gametemplates/char_equip.html");
    }

    public function getEquipHtml() {

        $equipHtml = $this->template;
        $infoBox = "";
        foreach (DICT::EQUIP_SLOTS as $slot) {

            $e = $this->equip->$slot;


            if ($e === false) {
                echo "MISSING " . $slot;
            } 
            else {

                $id = 0;
                $type = "";
                $img = 0;
                if ($e != null) 
                    {
                    $img = $e->imagekey;
                    $id = $e->id;
                    $type = $e->type;
                    $infoBox .= $this->addInfoBoxForItem($e);
                }
                $equipHtml = str_replace("{{" . $slot . "_id}}", $id, $equipHtml);
                $equipHtml = str_replace("{{" . $slot . "_id_img}}", $img, $equipHtml);
                $equipHtml = str_replace("{{" . $slot . "_type}}", $type, $equipHtml);
            }
        }

        return $equipHtml . $infoBox;
    }

    public function addInfoBoxForItem($item) {
        if ($item->type == 0) {

            if ($item->getExtData()->waponType == -1) {
                return $this->slotInfoArmor($item);
            }
            else {
                return $this->slotInfoWeapon($item);
            }
        } 
        else {
            return $this->slotInfoBaseItem($item->name, $item->rarity, $item->id, $item->description, DICT::ITEM_TYPE[$item->type], "X", $item->sellPrice,$item->minlevel);
        }
    }

    private function slotInfoBaseItem($name, $rare, $id, $description, $type, $material, $price,$level, $values = "", $actions = "") {


        $doc = " <div style='display:none' class='slot_info' id='item_$id'>
        <div class='bag_slot_head rare_$rare'>
            $name
        </div>
        <div class='bag_slot_content'>
            <span class='description'><small>$description</small></span>
            <span class='item_values'><small>$values</small></span>
            <span class='item_actions'><small>$actions</small></span>
        </div>
        <div class='bag_slot_footer'>
            <small>T: $type M:$material S: $price L:$level</small>
        </div>
    </div>";

        return $doc;
    }

    /**
     * 
     * @param Equip $item
     * @return type
     */
    private function slotInfoArmor($item) {
        $bonus = "";
        $stat = $item->stat;
        foreach (DICT::STATS_NAME as $key => $value) {
            if (isset($stat->data[$key])) {
                $bonus .= "<br/>" . $value . ": " . $stat->data[$key];
            }
        }

        return $this->slotInfoBaseItem($item->name, $item->rarity, $item->id, $item->description, DICT::ARMOR_NAME[$item->getExtData()->slot], DICT::MATERIAL_NAME[$item->getExtData()->material], $item->sellPrice,$item->getExtData()->minlevel, $bonus);
    }

    private function slotInfoWeapon($item) {

        $bonus = "";
        $stat = $item->stat;
        foreach (DICT::STATS_NAME as $key => $value) {
            if (isset($stat->data[$key])) {
                $bonus .= "<br/>" . $value . ": " . $stat->data[$key];
            }
        }

        return $this->slotInfoBaseItem($item->name, $item->rarity, $item->id, $item->description, DICT::WEAPON_NAME[$item->getExtData()->waponType], DICT::MATERIAL_NAME[$item->getExtData()->material], $item->sellPrice,$item->getExtData()->minlevel, $bonus);
    }

    public function getHtml() {
        return $this->getEquipHtml();
    }

}

class CharInfoRenderer implements IRenderer {

    /**
     * @var Charakter;
     */
    private $char;
    private $template;

    public function __construct($char) {
        $this->char = $char;
        $this->template = RenderMain::loadGameTemplate("char_info");
    }

    public function getHtml() {





        $b = $this->getInfoRow("Name", $this->char->getName() . " <small>" . $this->char->getTitle() . "</small>");
        $b .= $this->getInfoRow("Klasse", "<span class='class_".$this->char->getClass()."'>".DICT::CLASSNAME[$this->char->getClass()]."</span>");
        $b .= "<br/>";
        $b .= $this->getInfoRow("HP", $this->char->getCurrentHp()." / ");
        $b .= $this->getInfoRow("Mana", $this->char->getCurrentMana());
        $b .= $this->getInfoRow("Level", $this->char->getLevel());

        $b .= $this->getInfoRow("Xp", $this->char->getXp());

        $b .= $this->getInfoRow("Geld", $this->char->getMoneyBag()->getDisplayValue());
        $b .= $this->getInfoRow("Ehre", $this->char->getHonor());
       
        return str_replace("{{content}}", $b, $this->template);
    }

    private function getInfoRow($label, $value) {

        return "<div class='info_element'><span class='info_label'>$label:</span> <span class='info_data'>$value</span></div>";
    }

}

class QuestLogRenderer implements IRenderer {

    /**
     * @var Charakter;
     */
    private $char;
    private $template;

    public function __construct($char) {
        $this->char = $char;
        $this->template = RenderMain::loadGameTemplate("quest_log");
    }

    public function getHtml() {

        $temp = "";

        $activQ = $this->char->getQuestLog()->getActivQuests();
        $solvedQ = $this->char->getQuestLog()->getSolvedQuests();

        if ($activQ != null) {
            foreach ($activQ as $q) {
                $temp .= $this->getQuestRow($q, 'Offen');
            }
        }

        if ($solvedQ != null) {

            foreach ($solvedQ as $q) {
                $temp .= $this->getQuestRow($q, 'Erledigt');
            }
        }





        if ($temp == "") {
            $temp = "Keine offenen Quests!";
        }

        return str_replace("{{questlog}}", $temp, $this->template);
    }

    
    
    public function getQuestRow($quest, $status) {
        return "<div class='quest_log_item'> <span class='quest_log_name'>$quest->name <small>$status</small><div class='quest_objectiv'>$quest->objectiv</div></span> </div>";
    }

}

class GroupRenderer implements IRenderer {

    /**
     * @var Charakter;
     */
    private $char;
    private $template;

    public function __construct($char) {
        $this->char = $char;
        $this->template = RenderMain::loadGameTemplate("char_group");
    }

    public function getHtml() {

        $t = "";
        
        if($this->char->getGroup() == null || $this->char->getGroup()->groupLeet == $this->char->getId()){
            $t = "<input type='text' name='membername'><button class='add_group'>Einladen</button>";
         
            if($this->char->getGroup() == null){
            
                return str_replace("{{group}}", $t, $this->template);
            }
        }
        
        foreach ($this->char->getGroup()->getAllGroupMember() as $member) {

            $name = User::getNameById($member->userid);
            
            $t .= "<div class='groupmember' data-id='$member->userid'>" . $name;
            if (!$member->accepted) {
                if ($member->userid == $this->char->getId()) {
                    $t .= " <small class='invited_member'>eingeladen</small> <span class='group_accept'>[A]</span> <span class='group_decline'>[D]</span>  ";
                } else {
                    $t .= " <small class='invited_member'>eingeladen</small>  ";
                }
            } else {

                if ($this->char->getId() == $member->userid ) {
                    $t .= "<span class='leave_group' >[L]</span>";
                }
            }
            if ($this->char->getGroup()->groupLeet == $this->char->getId() && $this->char->getGroup()->groupLeet != $member->userid) {
                $t .= "<span class='remove_user'>[R]</span>";
            } else {
                
            }
            $t .= "</div>";
        }

        return str_replace("{{group}}", $t, $this->template);
    }

}
