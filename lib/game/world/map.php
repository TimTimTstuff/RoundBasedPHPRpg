<?php

 class MapFactory{
     
    public static function getMapConfiguration($mapid, $maptype){
    
        
        
        return null;
    }
    
}

//Welt zum erkunden..  städte, häuser wäler dungeons
//serverrender
class WorldMap{}

//clientrender
//Wälder, Instanzen, Hölen...
class DungeonMap{}

//server render
//Ort wo aktionen statfinden, Händler, questgeber... 
class FaceMap{
    
    private $mapdata;
    
    private $mapid;
    private $mapActions;
    private $name;
    
    
    public function __construct($fmapId) {
        
     $l = R::load(DBTables::WORLD_FMAP, $fmapId);
     $this->mapdata = json_decode($l->mapdata,true);
     $this->mapActions = json_decode($l->mapactions,true);
     for ($i = 0; $i < count($this->mapdata); $i++) {
         if($this->mapdata[$i]["akey"] != null){
             $this->mapdata[$i]["data"] = $this->mapActions[$this->mapdata[$i]["akey"]];
            
         }
     }
    
    }
    
    public function getMapForRender(){
        $mapRenderData = [];
        foreach ($this->mapdata as $value) {
            $mapRenderData[$value["x"]][$value["y"]]=$value["name"];
        }
        return $mapRenderData;
    }
    
    private function getItemById($x,$y){
        foreach ($this->mapdata as $value) {
            if($value["x"] == $x && $value["y"] == $y){
                return $value;
            }
        }
        return null;
    }

    public function gotoItem($id){
        $pos = explode(";", $id);
        if(count($pos) != 2)return false;
        
        $item = $this->getItemById($pos[0], $pos[1]);
        if($item == null)return false;
        
        return $item["data"];
        
    }
    
    public static function createNew($name,$imgKey, $actions, $mapdata,$chatkey){
        
        $map = R::dispense(DBTables::WORLD_FMAP);
        $map->name = $name;
        $map->mapactions = json_encode($actions);
        $map->mapdata = json_encode($mapdata);
        $map->imgkey = $imgKey;
        $map->chatkey = $chatkey;
        
        R::store($map);
        
    }
}

class FaceMapCreator{
    
    private $actions = [];
    private $mapSpace = [];
    private $name;
    private $imgKey = "";
    private $key = 65;
    
    private $chatKey;
    public function __construct($mapName,$imageKey,$chatkey) {
        $this->name = $mapName;
        $this->imgKey = $imageKey;
        $this->chatKey = $chatkey;
    }
    
    /**
     * 
     * @param int $x
     * @param int $y
     * @param type $name
     * @param MapAction $mapAction
     */
    public function addAction($x,$y, $name, $mapAction){
        
        
        $this->actions[chr($this->key)] = $mapAction;
        $this->mapSpace[] = ["x"=>$x,"y"=>$y,"name"=>$name,"akey"=>chr($this->key)];
        $this->key++;
        
    }
    
  
  

    
    public function create(){
        
        FaceMap::createNew($this->name,$this->imgKey, $this->actions, $this->mapSpace,$this->chatKey);
        
    }
}

class MapActionProcessor{
    
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


    public function __construct($context,$char) {
        $this->context = $context;
        $this->char = $char;
    }
    
    /**
     * 
     * @param MapAction $action
     */
    public function doAction($action){
        switch($action["name"]){
            case "openvendor":
                new OpenVendor($action["id"], $this->context->sessionData);
                break;
            case "openperson":
                new OpenPerson($action["id"],$this->context->sessionData);
                break;
             case "openquest":
                new OpenQuest($action["id"],$this->context->sessionData);
                break;
            case "openspace":
                new OpenSpace($action["id"], $this->context,$this->char);
                break;
            case "opentrainer":
                new OpenTrainer($action["id"], $this->context->sessionData);
                break;
            case FMapAction::MOVE_TOFMAP :
                new OpenFmap($action["id"],$this->context->sessionData,$this->char);
                break;
        }
    }
    
    
}

class MapAction{
    public $name;
    public $id;
}

/*
 * [
 *  0=>type=vendor;id=1,type=quest;id=1
 *  1=>"taverne",
 *  2=>"trainer",
 *  3=>"vendor",
 *  4=>"quest"
 * ]
 * [
 *  [0,0,0,0,0,0],
 *  [0,0,0,0,0,0],
 *  [0,0,2,4,0,0],
 *  [0,0,0,0,0,0],
 *  [1,0,0,0,3,0],
 *  [0,0,0,0,0,0],
 * ]
 * 
 * 
 */