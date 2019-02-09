<?php

class Session{

    /**
     *
     * @var StSession 
     */
    public $sessionData;
    public $vars = [];
    
    public function __construct() {
        if(!isset($_SESSION) || !isset($_SESSION['game'])){
            $this->createNewSession();
        }else{
            $this->loadSession();
        }
    }
    
    private function createNewSession(){
        $this->sessionData = new StSession();
        $this->sessionData->loggedIn = false;
        $this->sessionData->userId = -1;
        $_SESSION['vars'] = json_encode($this->vars);
        $_SESSION['game'] = json_encode($this->sessionData);
    }
    
    private function loadSession(){
        $this->sessionData = json_decode($_SESSION['game']);
        $this->vars = json_decode($_SESSION['vars'],TRUE);
    }
    
    public function addItem($key,$value){
        $this->vars[$key] = $value;
        $this->saveSession();
    }
    
    public function getItem($key){
        if(array_key_exists($key, $this->vars)){
            return $this->vars[$key];
        }
        return null;
    }
    
    public function saveSession(){
        
         $_SESSION["vars"] = json_encode($this->vars);
         $_SESSION['game'] = json_encode($this->sessionData);
        
    }
    
    public function isLoggedIn(){
        return $this->sessionData->loggedIn;
    }
    
    public function getUserId(){
        return $this->sessionData->userId;
    }
    
    
    /**
     * 
     * @param DbPlayer $user
     */
    public function login($user){
        $this->sessionData->loggedIn = TRUE;
        $this->sessionData->userId = $user->id;
        //$this->sessionData->tempVars["name"] = $user->name;
        $_SESSION['game'] = json_encode($this->sessionData);
    }
}