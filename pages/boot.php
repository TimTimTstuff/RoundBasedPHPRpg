<?php
//

class Game{
    
    /** @var Context */
    private $context;
    /** @var Charakter Description */
    private $char;
    



    /**
     *
     * @var RenderMain
     */
    private $mainRender;
    
    public function __construct($context) {
           
        $this->context = $context;
        $this->mainRender = new RenderMain();
        $this->createNewUserIfMissing();
        $this->char = new Charakter($this->context);
        $this->getCurrentView();
        $pos = $this->char->getPosition();
        $this->context->sessionData->addItem("map", $pos["t"].";".$pos["id"]);
        
       
        
    }
    
    private function getCurrentView(){
         $sess =  $this->context->sessionData;
        $page = $sess->getItem("page");
        if($page == null){
            $sess->addItem("page", "start");
        }
        return $page;
    }
    
    private function createNewUserIfMissing(){
         $pl = R::findOne(DBTables::CHAR," player_id = ?" ,[$this->context->getUserId()]);

            if($pl == null){
                Charakter::createNew($this->context->getUserId());
            }
    }
    
    private function changeChannel($newChannel){
        $oldChan = $this->context->sessionData->getItem("channel");
        
        if($oldChan == $newChannel)return;
        
        $chat = new Chat("", 2);
        if($oldChan != null){
            $chat->addSystemMessage($this->context->getUserName()." kommt in die gegend ", $newChannel);
          
        }
        $chat->addSystemMessage($this->context->getUserName()." verlässt die gegend ", $oldChan);
        $this->context->sessionData->addItem("channel", $newChannel);
    }


    public function start(){

        
        $this->char->getGroup();
        
        $f = FightInitializer::searchForActivFight($this->context->getUserId());
        
       
        if($f != null){
            
            $this->context->sessionData->addItem("fight", true);
            $this->context->sessionData->addItem("fightid", $f);
            
        }else{
            $this->context->sessionData->addItem("fight",false);
            $this->context->sessionData->addItem("fightid", null);
        }
        
        if($this->context->sessionData->getItem("fight") == true){
            $this->context->sessionData->addItem("page", "fight");
        }else{
            $this->context->sessionData->addItem("page", "start");
        }
        
        $view = null;
        
        if($this->getCurrentView() == "start")
        {
            $view = new MainView($this->context, $this->char, $this->mainRender);
        }elseif($this->getCurrentView() == "fight"){
            
            $view = new FightView($this->context, $this->char, $this->mainRender);
        }
        
        $this->changeChannel($view->getChannel());
            $view->show();
    }
    
    public function admin(){
           $d = $this->context->request->getDebugParam();
     if($d != null){
        $action = explode(";", $d);
        if(count($action) == 1){
            
            
        }elseif(count($action) == 2){
            if($action[0] == "remchar"){
                $userId = $action[1];
                
                $char = R::findOne(DBTables::CHAR,"player_id = ?",[$userId]);
                $bag = R::findOne(DBTables::BAG," owner_id = ? and owner_type = 1",[$userId]);
                $bagItems = R::find(DBTables::BAG_ITEMS," bag_id = ?",[$bag->id]);
                $charEquip = R::findOne(DBTables::CHAR_EQUIP, " user_id = ? ",[$userId]);
                $charStats = R::findOne(DBTables::STATS, " player_id = ? and type = 'main' ",[$userId]);
                
                
                R::trashAll($bagItems);
                R::trashAll([$char,$bag,$charEquip,$charStats]);
                echo "DELETE ALL OF USER ".$userId;
            }
            
            if($action[0] == "setvar"){
                $d = explode("-", $action[1]);
                $v = $d[1];
                if($v == "null"){
                    $v = null;
                    
                }
                $this->context->sessionData->addItem($d[0], $v);
            }
        }
     }
    }
    
    public function test(){
      
       //$l = new EquipCsvImporter("ausset.csv");
        
           
        
        $vMa = new MapAction();
        $vMa->name = "openvendor";
        $vMa->id = 1;
     
        
        $vMa2 = new MapAction();
        $vMa2->name = "openquest";
        $vMa2->id = 1;
        
        $vMa3 = new MapAction();
        $vMa3->name = "openspace";
        $vMa3->id = 1;
        
        $vMa4 = new MapAction();
        $vMa4->name = "openspace";
        $vMa4->id = 2;
        
        $vMa5 = new MapAction();
        $vMa5->name = "opentrainer";
        $vMa5->id = 1;
        
        $vMa6 = new MapAction();
        $vMa6->name = FMapAction::MOVE_TOFMAP;
        $vMa6->id = 3;
        
  return;
   
       $mc = new FaceMapCreator("Tutorialien","forest");
      
        $mc->addAction(3, 3, "Die Wolfshöle", null);
        $mc->addAction(0, 4, "In das Dorf", $vMa6);
      
        $mc->create();
     
      
        $s = new SpaceConfiguration();
        
        $s->addRandomText("Der alte Turm. Was hier so alles haust!");
        $t = "Bekämpfe die Turm Etagen";
        
        $s->addStartCond(["level",">",0],$t , ["type"=>21, "key"=>0, "cond"=>[], "value"=>[1,4]], "Etage 1");
        
        $s->addStartCond(["flag",">",["tower_m1",9]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[1,1,4,4,5]], "Etage 2");
        $s->addStartCond(["flag",">",["tower_m1",49]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[1,1,4,4,5,1,4,4,5,1,4,4,5]], "Etage 2 Hard");
        $s->addStartCond(["flag",">",["tower_m2",9]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[1,4,5,5,6]], "Etage 3");
        $s->addStartCond(["flag",">",["tower_m2",49]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[1,4,5,5,6,4,5,5,6,4,5,5,6]], "Etage 3 Hard");
        $s->addStartCond(["flag",">",["tower_m3",9]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[4,5,6,6,7]], "Etage 4");
        $s->addStartCond(["flag",">",["tower_m3",49]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[4,5,6,6,7,5,6,6,7,5,6,6,7]], "Etage 4 Hard");
        $s->addStartCond(["flag",">",["tower_m4",9]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[9,6,6]], "Etage 5");
        $s->addStartCond(["flag",">",["tower_m4",49]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[9,6,6,6,6,9,9,9,9]], "Etage 5 Hard");
        
        $s->addStartCond(["flag",">",["tower_m5",9]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[7,6,7,6,10]], "Etage 6");
        $s->addStartCond(["flag",">",["tower_m5",49]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[7,6,7,6,10,6,7,6,10,6,7,6,10]], "Etage 6 Hard");
        $s->addStartCond(["flag",">",["tower_m6",9]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[7,7,10,10,11]], "Etage 7");
         $s->addStartCond(["flag",">",["tower_m6",49]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[11,11,10,10,11,7,11,10,11,7,10,10,11]], "Etage 7 Hard");
        $s->addStartCond(["flag",">",["tower_m7",9]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[10,10,11,11,12]], "Etage 8");
                $s->addStartCond(["flag",">",["tower_m7",49]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[10,12,11,11,12,10,12,12,12,12,11,11,12]], "Etage 8 Hard");
        $s->addStartCond(["flag",">",["tower_m8",9]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[7,10,11,12,12]], "Etage 9");
        $s->addStartCond(["flag",">",["tower_m8",49]], $t, ["type"=>21, "key"=>0, "cond"=>[], "value"=>[7,10,11,12,12,10,11,12,12,10,11,12,12,12,12,12,9,9,9,9]], "Etage 9 Hard");
        
        $s->updateSpace(2);
    }
    
    public function getRenderer(){
        return $this->mainRender;
    }
}

if(!$context->isLoggedIn()){
    //start login
   //include 'login/login.php';
     $head = "";
    $content = "";
    $footer =  "";
}else{
    //start game
    using(["game"]); 
    R::fancyDebug( FALSE );
    R::freeze( FALSE );
    //create charakter
    $game = new Game($context);
    $game->start();
    $game->admin();
    $game->test();
    
    $head = $game->getRenderer()->getHeader();
    $content = $game->getRenderer()->getContent();
    $footer = $game->getRenderer()->getFooter();
    
  
}
