<?php

class RequestParser{
    
    private $root = 'game';
    private $params = [];
    
    private $requestPath;
    private $requestGet;
    private $requestPost;
    
    
    public function __construct() {
        $this->requestPath = explode("?",$_SERVER['REQUEST_URI'])[0];
        $this->requestGet = $_GET;
        $this->requestPost = $_POST;
        $this->getRequestParams();
    }
    
    private function getRequestParams(){
        $this->params = explode("/", substr($this->requestPath,1));
    }
    
    public function isGame(){
        if(count($this->params) <= 1) return true;
        
        if($this->params[1] != "service") return true;
        
        return false;
    }
    
    public function isService(){
        return isset($this->params[1]) && $this->params[1] == "service";
    }
    
    public function getServiceRoute(){
        $route = "none";
        if(isset($this->params[2])){
            return $this->params[2];
        }
    }
    
    public function getPostService(){
        if(isset($this->requestPost['service'])){
            return $this->requestPost['service'];
        }
        return "{}";
    }
    
    public function getViewRequest(){
        if(array_key_exists("view", $this->requestGet)){
            return $this->requestGet["view"];
        }
        return null;
    }
    
    public function getDebugParam(){
        if(array_key_exists("d", $this->requestGet)){
            return $this->requestGet["d"];
        }
        return null;
    }
    
    
    
}