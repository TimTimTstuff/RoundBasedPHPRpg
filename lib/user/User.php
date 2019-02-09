<?php

class User{
   

    /**
     *
     * @var DbPlayer 
     */
    public $data;
    /**
     * 
     * @param DBConnection $dbConnection
     */
    public function __construct() {
      
    }
    
    public function userExistsById($id){
        return false;
    }
       
    public function userExistByName($name){
        
          $user = R::find('user',' name = ? ',[$name]);
          if(count($user)>0)
              {
              return true;
              }
          return false;
    }
    
    public function checkUserWithPassword($uname, $pass){
        
      
        
        $user = R::find('user', "name = ? and password = ?", [$uname,$pass]);
  
        if(count($user)>0)
              {
              return true;
              }
          return false;
        
    }
    
    public function loadByName($name){
   
         $user = R::findOne('user', "name = ?",[$name]);
         $this->data = $user;
         return $user;
         
    }
    
    public static function getIdByName($name){
          $user = R::findOne('user', "name = ?",[$name]);
         if($user == null)return null;
         return $user->id;
    }
    
    public function loadById($id){
        $user = R::load(DBTables::USER, $id);
        $this->data = $user;
        return $user;
    }
    
    public static function getNameById($id){
        $u = R::load(DBTables::USER, $id);
        return $u->name;
    }
}