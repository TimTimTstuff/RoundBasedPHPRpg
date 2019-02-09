 
 function Game(){
 $(".footer_button_64").click((eh)=>{
       var btn = $(eh.target).data("name");
       
       if(btn == "bag"){
           $("#char_bag").css("display","inline-block");
           localStorage.setItem("showBag",true);
           localStorage.setItem("showStats",false);
            localStorage.setItem("showEquip",false  );
            localStorage.setItem("showQuest",false);
           $("#char_sheet").hide();
           $("#char_equip").hide();
           $("#quest_log").hide();
              localStorage.setItem("charGroup",false); 
            $("#char_group").hide();
       }else if(btn == "stats"){
           $("#char_sheet").css("display","inline-block");
             localStorage.setItem("showBag",false);
           localStorage.setItem("showStats",true);
            localStorage.setItem("showEquip",false  );
            localStorage.setItem("showQuest",false);
            $("#char_bag").hide();
           $("#char_equip").hide();
           $("#quest_log").hide();
              localStorage.setItem("charGroup",false); 
            $("#char_group").hide();
       }
       else if (btn == "equip"){
           $("#char_equip").css("display","inline-block");
             localStorage.setItem("showBag",false);
           localStorage.setItem("showStats",false);
            localStorage.setItem("showEquip",true  );
            localStorage.setItem("showQuest",false);
            $("#char_sheet").hide();
           $("#char_bag").hide();
           $("#quest_log").hide();
              localStorage.setItem("charGroup",false); 
            $("#char_group").hide();
           
       }else if (btn == "quest"){
           $("#quest_log").css("display","inline-block");
           localStorage.setItem("showBag",false);
           localStorage.setItem("showStats",false);
            localStorage.setItem("showEquip",false);
            localStorage.setItem("showQuest",true);
            $("#char_sheet").hide();
           $("#char_equip").hide();
            $("#char_bag").hide();
            
             localStorage.setItem("charGroup",false); 
            $("#char_group").hide();
            
       }else if(btn == "group"){
            $("#char_group").css("display","inline-block");
           localStorage.setItem("showBag",false);
           localStorage.setItem("showStats",false);
            localStorage.setItem("showEquip",false);
            localStorage.setItem("showQuest",false);
            localStorage.setItem("charGroup",true); 
            $("#quest_log").hide();
            
            $("#char_sheet").hide();
           $("#char_equip").hide();
            $("#char_bag").hide();
            location.reload();
          
       }
       
    });
  
     //Load

     //Show user items
     if(localStorage.getItem("showBag")=="true"){
          $("#char_bag").css("display","inline-block");
     }
     
     if(localStorage.getItem("showStats")=="true"){
          $("#char_sheet").css("display","inline-block");
     }
     
     if(localStorage.getItem("showEquip")=="true"){
          $("#char_equip").css("display","inline-block");

     }
      if(localStorage.getItem("showQuest")=="true"){
          $("#quest_log").css("display","inline-block");

     }
     if(localStorage.getItem("charGroup")=="true"){
          $("#char_group").css("display","inline-block");

     }
     
     $(".group_accept").click((e)=>{
          var v = new serviceObject();
        v.requestContent = {action:"accept",id:1};
        SendAction("group",v,(c)=>{  location.reload();
        },(c)=>{console.log(c);});
     });
     $(".group_decline").click((e)=>{
            var v = new serviceObject();
        v.requestContent = {action:"decline",id:1};
        SendAction("group",v,(c)=>{  location.reload();
        },(c)=>{console.log(c);});
     });
     $(".leave_group").click((e)=>{
            var v = new serviceObject();
        v.requestContent = {action:"leave",id:1};
        SendAction("group",v,(c)=>{  location.reload();
        },(c)=>{console.log(c);});
     });
     $(".remove_user").click((e)=>{
            var v = new serviceObject();
            var id = $(e.target).parent().data("id");
            console.log(id);
        v.requestContent = {action:"remove",id:id};
        SendAction("group",v,(c)=>{  location.reload();
        },(c)=>{console.log(c);});
     });
     
      $(".add_group").click((e)=>{
            var v = new serviceObject();
            var name = $("input[name='membername']").val();
            if(name == "")return;
            console.log("false");
        v.requestContent = {action:"add",name:name};
        SendAction("group",v,(c)=>{
            location.reload();
        },(c)=>{alert(c.displayMessage);});
     });
     
 }
     
     
function SendAction(actionName,serviceObject,successCallback,errorCallback){
    
      var stringRequest = JSON.stringify(serviceObject);
   
        $.post("service/"+actionName,{service:stringRequest})
                .done(function(c){
                  if(c.error == true || c.error == null){
                      errorCallback(c);
                  }else{
                      successCallback(c);
                  }
                })
                .fail(function(c){console.log("Fehler");console.log(c);})
    
}
    
    
function serviceObject(){ 
    this.requestName;
    this.requestContent;
    this.error;
    this.displayMessage;
}