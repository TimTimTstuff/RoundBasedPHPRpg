<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Context
 *
 * @author omitk
 */
class Context {
   /** @var User */
   public $userData;
   /** @var Session */
   public $sessionData;
   /** @var RequestParser */
   public $request;
   
   /**
    *
    * @var Statistics
    */
   public $statistics;
   
   public $userId;
   public $fake;
   
   public function __construct($fake = null,$userId = null) {
       $this->sessionData = new Session();
       $this->userData = new User();
       $this->request = new RequestParser();    
       $this->fake = $fake;
       $this->userId = $userId;
       $this->statistics = new Statistics($this->getUserId());
   
   }
   
   public function isLoggedIn(){
       return $this->sessionData->isLoggedIn();
   }
   
   public function isService(){
       return $this->request->isService();
   }
   
   public function loginUser($name){
      $tempU = $this->userData->loadByName($name);
      $this->sessionData->login($tempU);
      $this->getStatistics()->setUserId($this->getUserId());
      $this->getStatistics()->add(StatisticItems::TIMESLOGIN, 1);
      $this->log("login", "name=".$this->getUserName(), $this->getUserId());
   }
   
   public function getUserId(){
       if($this->fake != null){
           return $this->userId;
       }
       return $this->sessionData->getUserId();
   }
   
   public function getUserName(){
       $this->userData->loadById($this->getUserId());
       return $this->userData->data->name;
   }
   
   public function log($channel,$content,$user){
      $log = R::dispense(DBTables::LOG);
      $log->channel = $channel;
      $log->content = $content;
      $log->user_id = $user;
      $log->createtime = date("Y-m-d H:i:s");
      R::store($log);
   }
   
   public function getFlag($key){
       $flag = R::findOne(DBTables::FLAGS," flag_key = ? and user_id = ?", [$key,$this->getUserId()]);
       if($flag == null) return null;
       return $flag->flagValue;
   }
   
   public function setFlag($key,$value){
       
       $flag = R::findOne(DBTables::FLAGS," flag_key = ? and user_id = ?", [$key,$this->getUserId()]);
       
       if($flag == null){
        $flag = R::dispense(DBTables::FLAGS);
       }
       
       $flag->userId = $this->getUserId();
       $flag->flagKey = $key;
       $flag->flagValue = $value;
       $flag->modifiedon = date("Y-m-d H:i:s");
       R::store($flag);
       
   }

   /**
    * 
    * @return Statistics
    */
   public function getStatistics(){
       return $this->statistics;
   }
}


class Statistics{
    
    
    private $data = [];
    private $userId;
    
    public function __construct($userId) {
        $this->userId = $userId; 
    }
    
    public function setUserId($id){
        $this->userId = $id;
    }
    
    public function add($key,$value){
        
        if(!array_key_exists($key, $this->data))
        {
            $item = R::findOne(DBTables::STATISTICS," user_id = ? and statkey = ?",[$this->userId, $key]);
            if($item == null){
                $item = R::dispense(DBTables::STATISTICS);
                $item->statkey = $key;
                $item->userId = $this->userId;
                $item->value = 0;
                $this->data[$key] = $item;
            }else{
                $this->data[$key] = $item;
            }
        }
        
        $this->data[$key]->value += $value;
        $this->save();
    }
    
    public function get($key){
        if(!array_key_exists($key, $this->data)){
            $item = R::findOne(DBTables::STATISTICS," user_id = ? and statkey = ?",[$this->userId,$key]);
            if($item == null){
                return 0;
            }
            $this->data[$key] = $item;
        }
        
        return $this->data[$key];
    }
    
    public function save(){
        R::storeAll($this->data);
    }
    
}