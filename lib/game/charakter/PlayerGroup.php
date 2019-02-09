<?php

class PlayerGroup{
    
    public $groupLeet;
    /**
     *
     * @var GroupMember[]
     */
    public $groupMember;
    public $groupKey;
    private $maxGroupSize = 3;

    private $data;
    private $selfUserId;

    /**
     *
     * @var Session
     */
    private $session;
    /**
     * 
     * @param Context $context
     */
    public function __construct($userid, $groupId) {
        $this->selfUserId = $userid;
        
        $this->load($groupId);
    }
    
    private function load($groupId){
         $this->data = R::load(DBTables::USER_GROUP, $groupId);
        $this->groupKey = $this->data->groupkey;
        $this->groupLeet = $this->data->leetUserId;
        $this->groupMember = json_decode($this->data->memberlist);
    }
    
    public static function loadPlayerGroup($userId, $session){
        
        $hasUserGroup = $session->getItem("group");
     
      if($hasUserGroup == null){
          
          $pl = PlayerGroup::getPlayerGroup($userId);
         
          if($pl != null){
              $group = $pl;
              $session->addItem("group", $group->getId());
              return $group;
          } 
      }
      else{
          return new PlayerGroup($userId,$hasUserGroup);
      }
      return null;
    }
    
    public static function isUserInGroup($userId){
        
        return PlayerGroup::getPlayerGroup($userId) != null;
    }



    public function save(){
        
        $this->data->leetUserId = $this->groupLeet;
        $this->data->memberlist = json_encode($this->groupMember);

        R::store($this->data);
    }
     
    public function getGroupMember(){
        $data = [];
        
        foreach ($this->groupMember as $member) {
            if($member->accepted == 1){
                $data[] = $member;
            }
        }
        
        return $data;
    }
    
    
    public function getAllGroupMember(){
      
        return $this->groupMember;
    }
    
    public function isFull(){
        return $this->maxGroupSize >= count($this->groupMember);
    }
    
    public function getId(){
        return $this->data->id;
    }
    
    public function addMember($userId) {
        $userId = intval($userId);
        if(count($this->groupMember) > $this->maxGroupSize){
            return false;
        }
        
        foreach ($this->groupMember as $member) {
            if($member->userid == $userId){
                return true;
            }
        }
        
        $gm = new GroupMember();
        $gm->userid = $userId;
        $gm->accepted = false;
        $this->groupMember[] = $gm; 
        
        $this->save();
    }
     
    public function removeMember($userId){
        $newGroup = [];
        foreach ($this->groupMember as $member){
            if($member->userid != $userId){
                $newGroup[] = $member;
            }
        }
        
        $this->groupMember = $newGroup;
        $this->save();
        
    }
    
    public function acceptInvitation($userId){
        foreach($this->groupMember as $member){
            if($member->userid == $userId){
             $member->accepted = true;   
            }
        }
        $this->save();
    }
    
    public function declineInvitation($userId){
        $this->removeMember($userId);
    }
    
    public function setGroupLeet($userId){
        $this->groupLeet = $userId;
    }
    
    private static function generateGroupKey($userId){
        $key = $userId."_";
        $randNr = rand(1000, 9999);
        $randNr2 = rand(1000, 9999);
        $key.=$randNr.$randNr2;
        return $key;
    }
    
    private function deleteGroup(){
        R::trash($this->data);
    }
    
    public static function createNew($leetUserId){
        
        $leetUserId = intval($leetUserId);
        
        $group = R::dispense(DBTables::USER_GROUP);
        $group->leetUserId = $leetUserId;
        $groupMember = new GroupMember();
        $groupMember->userid =  $leetUserId;
        $groupMember->accepted = true;
        $group->memberlist = json_encode([$groupMember]);
        $group->groupkey = self::generateGroupKey($leetUserId);
        $group->inFight = false;
        return R::store($group);
    }
    
    public function setIsFight($fight){
        $this->data->inFight = $fight;
        $this->save();
    }
    public function isInFight(){
     return $this->data->inFight;   
    }
    
    private static function getPlayerGroup($userId){
        $userIdString = "%\"userid\":$userId%";
    
        $g = R::findOne(DBTables::USER_GROUP," memberlist like ? ",[$userIdString]);
        
        
        
        if($g != null){
            return new PlayerGroup($userId,$g->id);
        }
        
        
         return null;
    }
    
}


class GroupMember{
    public $userid;
    public $accepted;
}
