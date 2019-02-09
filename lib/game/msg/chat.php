<?php

class Chat{
    
    private $channel;
    private $userId;

    public function __construct($currentChannel, $userId) {
        $this->userId = $userId;
        $this->channel = $currentChannel;
    }
    
    public function getMessages($time=null,$groupKey = null){
        
        if($time != null && $groupKey == null){
        
            $msgs = R::find(DBTables::CHAT, " channel = ? and createtime > ?", [$this->channel,$time]);
        }
        elseif($groupKey != null && $time != null){
             $msgs = R::find(DBTables::CHAT, " (channel = ? or channel = ? ) and createtime > ?", [$this->channel,$groupKey,$time]);
        }elseif($groupKey != null && $time != null){
                      $msgs = R::find(DBTables::CHAT, " (channel = ? or channel = ? )", [$this->channel,$groupKey]);
   
        }
        else
            {
            $msgs = R::find(DBTables::CHAT, " channel = ? and createtime > date_sub(now(), interval 10 minute)", [$this->channel]);
        
        }
        
        
        return $msgs;
    }
    
    
    public function addMessage($msg,$channel = null){
        
        $chat = R::dispense(DBTables::CHAT);
        $chat->userId = $this->userId;
        if($channel != null){
            $chat->channel = $channel;
        }else{
        $chat->channel = $this->channel;
        }
        $chat->msg = $msg;
        $chat->createtime = date("Y-m-d H:i:s");
        R::store($chat);
        
    }
    
    public function addSystemMessage($msg,$channel){
        
        $chat = R::dispense(DBTables::CHAT);
        $chat->userId = 2;
        $chat->channel = $channel;
        $chat->msg = "<span style='color:lime'>".$msg."</span>";
        $chat->createtime = date("Y-m-d H:i:s");
        R::store($chat);
        
    }
   
}