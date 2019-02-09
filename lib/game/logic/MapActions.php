<?php

class OpenVendor{
    
    /**
     * 
     * @param type $id
     * @param Session $session
     */
    public function __construct($id,$session) {
        $session->addItem("space", null);
        $session->addItem("npc", "vendor;".$id);
    }
}
class OpenPerson{
    
    /**
     * 
     * @param type $id
     * @param Session $session
     */
    public function __construct($id,$session) {
        
        $session->addItem("npc", "person;".$id);
        $session->addItem("space", null);
    }
}
class OpenQuest{
    
    /**
     * 
     * @param type $id
     * @param Session $session
     */
    public function __construct($id,$session) {
        
        $session->addItem("npc", "quest;".$id);
        $session->addItem("space", null);
        
    }
}

class OpenTrainer{
    
    /**
     * 
     * @param type $id
     * @param Session $session
     */
    public function __construct($id,$session) {
        
        $session->addItem("npc", "trainer;".$id);
        $session->addItem("space", null);
        
    }
}

class OpenFmap{
    
    /**
     * 
     * @param type $id
     * @param Session $session
     * @param Charakter $char
     */
    public function __construct($id,$session,$char) {
        
        $session->addItem("map", MapType::FMAP.";".$id);
        $char->setPosition(MapType::FMAP, $id);
        $session->addItem("npc", null);
        $session->addItem("space", null);
    }
}

class OpenSpace{
    
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
    
    private $id;
    
    public function __construct($id,$context,$char) {
        $this->context =$context;
        $this->char = $char;
        $this->id = $id;
        $this->context->sessionData->addItem("space", $id);
        $this->context->sessionData->addItem("npc", null);
    }
}