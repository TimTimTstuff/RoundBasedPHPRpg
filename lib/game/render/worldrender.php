<?php

class RenderFaceMap implements IRenderer{
    
    private $cols = 6;
    private $rows = 6;

    
    private $mapData;

   /**
    *
    * @var FaceMap 
    */
    private $map;
    
    public function __construct($map) {
        $this->map = $map;
        $this->mapData = $this->map->getMapForRender();
    }
    
    private function getActiveTile($name,$data){
        return "<td class='action' data-id='$data'>$name</td>\r\n";
    }
    
    private function getInactivTile(){
        
        return "<td></td>\r\n";
    }
    
    public function getHtml() {
        $templ = "";
        for($x = 0; $x < $this->cols; $x++){
            $templ.= "<tr/>\r\n";
            for($y = 0; $y < $this->rows; $y++){
                if(isset($this->mapData[$x]) && isset($this->mapData[$x][$y])){
                    $templ.=$this->getActiveTile($this->mapData[$x][$y],$x.";".$y);
                }else{
                    $templ.=$this->getInactivTile();
                }
            }
            $templ.="</tr>\r\n";
        }
       
        
        $center = RenderMain::loadGameTemplate("center_face");
        $center = str_replace("{{map}}", $templ, $center);
        return $center = str_replace("{{chat}}", RenderMain::loadGameTemplate("chat"), $center);
        
    }

}

class SpaceRenderer implements IRenderer{
    
    private $template;
    
    /**
     *
     * @var SpaceHandler
     */
    private $space;
    
    public function __construct($space) {
        $this->space = $space;
        $this->template = RenderMain::loadGameTemplate("space_frame");
    }
    
    private function getActionItem($name,$text){
        
        return "<a class='spaceaction' href='javascript:;' data-name='$name'>$text</a><br/>";
    }


    public function getHtml() {
        
        $textBox = "<b>".$this->space->name."</b></br>";
        
        $temp = str_replace("{{space_id}}", $this->space->id, $this->template);
        $temp = str_replace("{{space_text}}",$textBox.$this->space->getText(), $temp);
        
        $action = "";
        foreach ($this->space->getActivActions() as $key => $value) {
            $action.= $this->getActionItem($key, $this->space->getActionText($key));
        }
        
        return str_replace("{{space_extra}}", $action, $temp);
    }
}