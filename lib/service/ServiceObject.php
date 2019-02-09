<?php
class ServiceObject{
 
    public $requestName;
    public $requestContent;
    public $error;
    public $displayMessage;
    
}


class ServiceObjectParser{
    
    /* @var $request RequestParser */
    private $request;
    private $route;
    private $requestContent;
    
    /* @param $requestParser RequestParser */
    public function __construct($requestParser) {
        $this->request = $requestParser;
        $this->route = $this->request->getServiceRoute();
        $this->requestContent = $this->request->getPostService();
    }
    
    public function getContent(){
        return json_decode($this->requestContent);
    }
    
    public function getAction(){
        return $this->route;
    }
    
    public function sendResponse($serviceObject){
        echo json_encode($serviceObject);
    }
}


