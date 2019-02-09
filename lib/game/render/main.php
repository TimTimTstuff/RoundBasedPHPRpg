<?php
class RenderMain{
    
    public $header = "";
    public $footer = "";
    
    
    public $contentLeft = "";
    public $contentCenter = "";
    public $contentRight = "";
    public $contentHidden = "";
  
    
    
    /**
     *
     * @var IRenderer 
     */
    private $contentRendererLeft = [];
      /**
     *
     * @var IRenderer 
     */
    private $contentRendererCenter = [];
      /**
     *
     * @var IRenderer 
     */
    private $contentRendererRight = [];
      /**
     *
     * @var IRenderer 
     */
    private $contentRendererHidden = [];
    /**
     *
     * @var IRenderer 
     */
    private $footerRenderer = [];
    /**
     *
     * @var IRenderer 
     */
    private $headerRenderer = [];
    
    public function addHeader($content){
    $this->header.= $content;    
    }
    
    public static function loadGameTemplate($name){
        
        return file_get_contents("pages/gametemplates/$name.html");
    }
    
    
    public function addContentLeft($content){   
        $this->contentLeft.=$content;
    }

      
    public function addContentRight($content){   
        $this->contentRight.=$content;
    }
      
    public function addContentCenter($content){   
        $this->contentCenter.=$content;
    }
      
    public function addContentHidden($content){   
        $this->contentHidden.=$content;
    }
     
     public function addContentLeftRender($render){   
        $this->contentRendererLeft[] =$render;
    }

      
    public function addContentCenterRender($render){  
        $this->contentRendererCenter[] =$render;
    }
      
    public function addContentRightRender($render){  
        $this->contentRendererRight[] =$render;
    }
      
    public function addContentHiddenRender($render){  
        $this->contentRendererHidden[] =$render;
    }
    
    public function addFooter($content){
        $this->footer.=$content;
    }
    
    public function getHeader(){
         foreach ($this->headerRenderer as $r) {
            $this->header.=$r->getHtml();
        }
        return $this->header;
    }
    
    public function addHeadRenderer($render){
        $this->headerRenderer[] = $render;
    }
    
    public function addFooterRenderer($render){
        $this->footerRenderer[] = $render;
    }
    
    public function addContentRenderer($render){
        $this->contentRenderer[] = $render;
    }
    
    public function getContent(){
        
        foreach ($this->contentRendererCenter as $r) {
            $this->contentCenter.=$r->getHtml();
        }
        
         foreach ($this->contentRendererRight as $r) {
            $this->contentRight.=$r->getHtml();
        }
        
         foreach ($this->contentRendererHidden as $r) {
            $this->contentHidden.=$r->getHtml();
        }
        
         foreach ($this->contentRendererLeft as $r) {
            $this->contentLeft.=$r->getHtml();
        }
       
        $template = self::loadGameTemplate("playtemplate");
        $template = str_replace("{{left}}", $this->contentLeft, $template);
          $template = str_replace("{{center}}", $this->contentCenter, $template);
            $template = str_replace("{{right}}", $this->contentRight, $template);
              $template = str_replace("{{hidden}}", $this->contentHidden, $template);
        return $template;
    }
    
    public function getFooter(){
            foreach ($this->footerRenderer as $r) {
            $this->footer.=$r->getHtml();
        }
        return $this->footer;
    }
    
}

interface IRenderer{
    public function getHtml();
}