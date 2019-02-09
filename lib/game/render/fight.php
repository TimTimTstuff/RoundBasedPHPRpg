<?php

class RenderFighterGroup implements IRenderer {

    private $template;
    private $side;

    /**
     *
     * @var FightMember[]
     */
    private $fighers;
    private $isPlayer;

    public function __construct($side, $fighter, $isPlayer) {
        $this->side = $side;
        $this->template = RenderMain::loadGameTemplate("fighter_group");
        $this->fighers = $fighter;
        $this->isPlayer = $isPlayer;
    }

    public function getHtml() {
        $t = RenderMain::loadGameTemplate("fighter_single");

        $adT = "";


        if (!$this->isPlayer) {
            $token = "m";
        }
        foreach ($this->fighers as $member) {
            $fighters = new RenderFighter($t, $member, $member->getIndex());
            $adT .= str_replace("{{fighter_id}}", 1, $fighters->getHtml());
        }

        $this->template = str_replace("{{type}}", $this->side, $this->template);
        return str_replace("{{groupdata}}", $adT, $this->template);
    }

}

class RenderFighter implements IRenderer {

    private $template;

    /**
     *
     * @var FightMember
     */
    private $figher;
    private $index;

    public function __construct($templ, $figher, $index) {

        $this->template = $templ;
        $this->figher = $figher;
        $this->index = $index;
    }

    public function getHtml() {




        $tmp = $this->template;
        $tmp = str_replace("{{index}}", $this->index, $tmp);
        $tmp = str_replace("{{speed}}", $this->figher->getSpeedValue(), $tmp);
        $tmp = str_replace("{{aggro}}", $this->figher->getAggro(), $tmp);
        $tmp = str_replace("{{fighter_id}}", $this->figher->getActorId(), $tmp);
        $tmp = str_replace("{{name}}", $this->figher->getActorName(), $tmp);
        $tmp = str_replace("{{hp}}", $this->figher->getMaxHp(), $tmp);
        $tmp = str_replace("{{mana}}", $this->figher->getMaxMana(), $tmp);

        if (!$this->figher->isPlayer()) {
            $x = new Mob($this->figher->getRealId(), null, null);
            $tmp = str_replace("{{mob_key}}", $x->getKey(), $tmp);
        } else {
            $tmp = str_replace("{{mob_key}}", $this->figher->getActorName(), $tmp);
        }
        return $tmp;
    }

}

class RenderFightMain implements IRenderer {

    public function getHtml() {
        return RenderMain::loadGameTemplate("fight_center");
    }

}

class RenderFightFooter implements IRenderer {

    private $template;

    /**
     *
     * @var FightMember
     */
    private $fighter;

    public function __construct($fighter) {
        $this->fighter = $fighter;
        $this->template = RenderMain::loadGameTemplate("fight_footer");
    }

    public function getHtml() {

        $attackList = json_decode($this->fighter->getAttackList());

        $temp = $this->template;

        $temp = str_replace("{{mana_potion}}", "Leer", $temp);
        $temp = str_replace("{{health_potion}}", "Leer", $temp);
        $info = "";

        for ($i = 0; $i < 6; $i++) {

            if (isset($attackList[$i])) {
                $attack = AttackConfiguration::load($attackList[$i]);
                $temp = str_replace("{{c_" . ($i + 1) . "}}", 1, $temp);
                $name = $attack->name . " <br/> <small>MP: " . $attack->manacost . "</small>";
                $name = "";
                        
                $temp = str_replace("{{attack_" . ($i + 1) . "}}", $name, $temp);
                $temp = str_replace("{{slot_" . ($i + 1) . "}}", $i+1, $temp);
                $temp = str_replace("{{m_" . ($i + 1) . "}}", $attack->manacost, $temp);
                $temp = str_replace("{{attid_".($i+1)."}}", $attackList[$i], $temp);
                $info.= $this->getAttackInfo($i+1, $attack->name, $attack->manacost, $attack->description);
                
            } else {
                $info.= $this->getAttackInfo($i+1, "Leer", 0, "Nicht belegt");
                $temp = str_replace("{{attack_" . ($i + 1) . "}}",  "  ", $temp);
                 $temp = str_replace("{{c_" . ($i + 1) . "}}", 0, $temp);
                $temp = str_replace("{{slot_" . ($i + 1) . "}}", $i+1, $temp);
                   $temp = str_replace("{{attid_".($i+1)."}}", "x", $temp);
                $temp = str_replace("{{m_" . ($i + 1) . "}}", 0, $temp);
            }
        }
        $temp = str_replace("{{attack_info}}", $info, $temp);
        return $temp;
    }
    
    private function getAttackInfo($slot,$name,$mana,$desc){
        $t = "<div id='info_slot_$slot'>
            $name - Mana: $mana<br/>
            Beschreibung: $desc 
        </div>";
        
        return $t;
    }

}
