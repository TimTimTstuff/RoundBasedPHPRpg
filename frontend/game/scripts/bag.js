

function Bag(){

  
    $(".bag_slot").hover((hin)=>{
        var id = $(hin.target).data("id");
        if(id == 0)return;
 
        $("#item_"+id).show();
        $("#item_"+id).addClass("center_pos");
        
        $(document).mousemove(function(event) {});
        
        $("#item_"+id).css({top:event.pageY+10,left:event.pageX+10});
    
    },
    (hout)=>{
         var id = $(hout.target).data("id");
        if(id == 0)return;
        $("#item_"+id).removeClass("center_pos");
        $("#item_"+id).hide();
    });
    
      
    $(".equip_slot").hover((hin)=>{
        var id = $(hin.target).data("id");
        if(id == 0)return;
 
        $("#item_"+id).show();
        $("#item_"+id).addClass("center_pos");
        
        $(document).mousemove(function(event) {});
        
        $("#item_"+id).css({top:event.pageY+10,left:event.pageX-200});
    
    },
    (hout)=>{
         var id = $(hout.target).data("id");
        if(id == 0)return;
        $("#item_"+id).removeClass("center_pos");
        $("#item_"+id).hide();
    });
    
 
    
    $(".bag_slot").click((eh)=>{
        var type = $(eh.target).data("type");
        var merchant = $("#char_bag").data("merchant");
        $(".bag_slot").removeClass("selected_bag_slot");
        $("#item_options").html("<span></span>");
        if(type === ""){return;}
     
     var itemOptions = '';
     
        if(merchant == "yes"){
            
            var sellprice = $(eh.target).data("sell");
            itemOptions+='<button id="sell_item">Verkaufen ('+sellprice+' G)</button>\n\
                            <button id="sell_item_all">Verkaufe Stapel</button>';
        }
        
        if(type == 0){
            
            itemOptions+='<button id="equip_item">Equip</button>';
        }
        
        if(type == 4){
            itemOptions+='<button id="use_item">Benutzen</button>';
        }
        
        itemOptions += '<button id="delte_item_all">Müll Stapel</button><button id="delte_item">Müll</button>';
        $("#item_options").html(itemOptions);
        
        $(eh.target).addClass("selected_bag_slot");
        bindButtons();
    });
    
    $(".equip_slot").click((eh)=>{
        var type = $(eh.target).data("type");
      
        $(".equip_slot").removeClass("selected_bag_slot");
        $("#equip_options").html("<span></span>");
        if(type === ""){return;}
        
      
            $("#equip_options").html('<button id="unequip_item">Unequip</button>');
       
        
        $(eh.target).addClass("selected_bag_slot");
        bindButtons();
       
    });
    
    $(".quest_log_item").click((e)=>{
        
        $(".quest_objectiv").hide();
        $(e.target).find(".quest_objectiv").show();
    });
    
    
    function sendAction(contentObj){
         
   // var u = {requestContent:{action:"equip",itemid:4}};
        var xx = JSON.stringify(contentObj);
   
        $.post("service/bagequip",{service:xx})
                .done(function(c){
                  if(c.error == true || c.error == null){
                      alert(c.displayMessage);
                  }else{
                      location.reload();
                  }
                })
                .fail(function(c){console.log("Fehler");console.log(c);})
    }
    
     function bindButtons(){
     
     $("#unequip_item").click((eh)=>{
         var slot = $(".equip_slot.selected_bag_slot");
         
         if(slot.length == 1){
          
             var slotNum = $(slot).data("slot");
             var u = {requestContent:{action:"unequip",slot:slotNum}};
                sendAction(u);
         }
         
     });
     
     $("#delte_item").click((eh)=>{
         var slot = $(".bag_slot.selected_bag_slot");
         
         if(slot.length == 1){
             var slotNum = $(slot).data("slotid");
             var u = {requestContent:{action:"delete",slotid:slotNum,amount:1}};
                sendAction(u);
         }
         
         
     });
     $("#delte_item_all").click((eh)=>{
           var slot = $(".bag_slot.selected_bag_slot");
         
         if(slot.length == 1){
             var slotNum = $(slot).data("slotid");
             var u = {requestContent:{action:"deleteall",slotid:slotNum,amount:-1}};
                sendAction(u);
         }
     });
     
     
     
     $("#equip_item").click((eh)=>{
          var slot = $(".bag_slot.selected_bag_slot");
         
         if(slot.length == 1){
             var slotNum = $(slot).data("id");
             var u = {requestContent:{action:"equip",itemid:slotNum}};
                sendAction(u);
         }
     });
     $("#sell_item").click((eh)=>{
         
           var slot = $(".bag_slot.selected_bag_slot");
         
         if(slot.length == 1){
             var slotNum = $(slot).data("id");
             var u = {requestContent:{action:"sell",itemid:slotNum,amount:1}};
                sendAction(u);
         }
         
     });
     
     $("#sell_item_all").click((eh)=>{
         
           var slot = $(".bag_slot.selected_bag_slot");
         
         if(slot.length == 1){
             var slotNum = $(slot).data("slotid");
   
             var u = {requestContent:{action:"sellstack",slotid:slotNum}};
                sendAction(u);
         }
         
     });
     
     $("#use_item").click((eh)=>{
         
         
           var slot = $(".bag_slot.selected_bag_slot");
         
         if(slot.length == 1){
             var slotNum = $(slot).data("slotid");
   
             var u = {requestContent:{action:"use",slotid:slotNum}};
                sendAction(u);
         }
     });
     
 }
    
}

