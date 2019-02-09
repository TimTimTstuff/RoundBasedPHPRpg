<?php
class FightView{
    
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
    
    /**
     *
     * @var RenderMain
     */
    private $mainRender;
    
    /**
     *
     * @var FightProcessor
     */
    private $fight;
    
    public function __construct($context, $char, $render) {
        $this->context = $context;
        $this->char = $char;
        $this->mainRender = $render;
        
        $fightId = $this->context->getFlag("fightid");
        
       
        
        if($fightId == null){
            $fightId = FightInitializer::searchForActivFight($this->context->getUserId());
        }
        
        $this->fight = new FightProcessor($fightId,$this->context->getUserId(),$context);
        
      
    }
    
    
    public function getChannel(){
        return "fight";
    }

    public function show(){
        
        $fSelf = $this->fight->findPlayerByRealId($this->context->getUserId());

        $rLeft = new RenderFighterGroup("left",$this->fight->getPlayer(),true);
        $rRight = new RenderFighterGroup("right",$this->fight->getMobs(),false);
        $center = new RenderFightMain();
        $this->mainRender->addContentCenterRender($center);
        $this->mainRender->addContentLeftRender($rLeft);
        $this->mainRender->addContentRightRender($rRight);
        $this->mainRender->addFooterRenderer(new RenderFightFooter($fSelf));
        
    }
}
