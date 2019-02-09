function Vendor(){

if($("#vendor_bag").length == 0){return;}


    $("#char_bag").data("merchant","yes");
    
        $(".vendor_slot").hover((hin)=>{
        var id = $(hin.target).data("id");
        if(id == 0)return;
 
        $("#item_"+id).show();
        $("#item_"+id).addClass("center_pos");
        
        $(document).mousemove(function(event) {});
        
        $("#item_"+id).css({top:event.pageY+10,left:event.pageX+10});
    
    },(hout)=>{
         var id = $(hout.target).data("id");
        if(id == 0)return;
        $("#item_"+id).removeClass("center_pos");
        $("#item_"+id).hide();
    });
    
 
    
    $(".vendor_slot").click((eh)=>{
       
        $(".vendor_slot").removeClass("selected_bag_slot");
        $("#item_options_vendor").html("<span></span>");
        var buyPrice = $(eh.target).data("buy");
        $("#item_options_vendor").html('<button id="buy">Kaufe 1</button><button id="buystack">Kaufe Stapel</button><span>Preis pro Item: '+buyPrice+'</span>');
            
        $(eh.target).addClass("selected_bag_slot");
        bindButtons();
    });
    
    
    function bindButtons(){
   
    
     $("#buy").click((eh)=>{
         
           var slot = $(".vendor_slot.selected_bag_slot");
         
         if(slot.length == 1){
             var slotNum = $(slot).data("id");
             var u = {requestContent:{action:"buy",itemid:slotNum,amount:1}};
                sendAction(u);
         }
         
     });
     
      $("#buystack").click((eh)=>{
         
           var slot = $(".vendor_slot.selected_bag_slot");
         
         if(slot.length == 1){
             var slotNum = $(slot).data("slotid");
          
             var u = {requestContent:{action:"buystack",slotid:slotNum}};
                sendAction(u);
         }
         
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
     
 }
    
}


function Trainer(){
    
    if($("#trainer_menue").length == 0)return;
    
    
    $(".buy_stat").click((e)=>{
         var stat = $(e.target).data("stat");
        
        var v = new serviceObject();
        v.requestContent = {action:"buy",name:stat};
        SendAction("stat",v,(c)=>{
           location.reload();
           
        },(c)=>{console.log(c);});
    });
    
    
    
}