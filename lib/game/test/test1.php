<?php

abstract class Test{
    
    public $context;
    public $char;
    
    public function __construct($context, $char) {
        $this->context = $context;
        $this->char = $char;
    }
    
    public abstract function Run();
}


class ConditionTest extends Test{
    
    
    
    public function Run() {
     
 
      
        
    }

    
    public function testCondition(){
            $this->context->sessionData->addItem("test", "nein");
     $cond = new ConditionParser($this->context,$this->char);
     
     $condI[] = ["gold","<",10000];//true
     $condI[] = ["level",">",0];//true
     $condI[] = ["xp","=",0];//true
     $condI[] = ["name","!=","peter"];//true
     $condI[] = ["iteminbag","=",[10,1]];//true
     $condI[] = ["itemequip","=",10];//true
     $condI[] = ["var","=",["test","nein"]];//true
     
    
     $condI[] = ["and"=>[ //false
         ["gold","<",10000],
         ["level",">",0],
     ]];
    
     $condI[] = ["or"=>[ //true
         ["gold","<",10000],
         ["level",">",1],
     ]];
     
     
     
     $condI[] = 
             ["or"=>[
                 "and" =>[
                     ["gold","<",10000],
                     ["level",">",0],
                 ],
                 "and" =>[
                     ["itemequip","=",10]
                 ],
              ]];
  
     $condI[] = ["opennpc","=",1];
     $condI[] = ["queststatus","=",[1,2]];
        
     $x = 0;
     foreach ($condI as $k=>$c){
         
         if($cond->isValide($c)){
             if($cond->getResult($c)){
                 echo $x." is true<br/>";
             }else{
                 echo $x." is false<br/>";
             }
         }else{
             echo $x." is not Valide<br/>";
         }
         
         $x++;
         
     }
    }
    
}