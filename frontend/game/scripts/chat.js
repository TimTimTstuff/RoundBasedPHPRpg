function Chat(){
    
    
    
    
    $("#chat_input").keydown((e)=>{
        
        if(event.which != 13 && event.keyCode != 13)return;
        
      var msga =  $(e.target).val()
        
        if(msga == "")return;
        
        
        var v = new serviceObject();
        v.requestContent = {action:"send",msg:msga};
        
        
        SendAction("chat",v,chatSendCallback,(c)=>{console.log(c)});
        
        
    });
    
    function chatSendCallback(c){
        var elam = $("#chat_input>input");
        elam.val("");
        elam.attr("disabled",true);
        elam.css("background","darkgray");
        setTimeout(()=>{
            elam.attr("disabled",false);
            elam.focus();
            elam.css("background","darkslategray");
        },2000);
        
        var r = {requestContent:{action:"read",time:false}};
        sendActionRead(r);
    }
    
    
    var ev = true;
    function displayMessages(msgs){
        
        console.log(msgs);
        var te = "";
       
       
       for(var m in msgs){
           var exClass = "";
           if(ev){
               exClass = " light_chat";
           }
           
           var sender = msgs[m].u;
           var message = msgs[m].m;
           var t = msgs[m].t.split(" ")[1];
           
           te += "<div class='chat_msg"+exClass+"'><span class='chat_sender'><small>["+
                   t+"] </small>"+
                   sender+": </span><span class='chat_message'>"+message+"</div>";
           ev = !ev;
       }
       
       $("#chat_output").append(te);
       $("#chat_output").scrollTop($("#chat_output")[0].scrollHeight);
    }
    
      function sendActionRead(contentObj){
         
   // var u = {requestContent:{action:"equip",itemid:4}};
        var xx = JSON.stringify(contentObj);
   
        $.post("service/chat",{service:xx})
                .done(function(c){
                  if(c.error == true || c.error == null){
                      alert(c.displayMessage);
                  }else{
                        displayMessages(JSON.parse(c.requestContent));
                  }
                })
                .fail(function(c){console.log("Fehler");console.log(c);})
    } 
   
    function sendActionWrite(contentObj){
         
   // var u = {requestContent:{action:"equip",itemid:4}};
        var xx = JSON.stringify(contentObj);
   
        $.post("service/chat",{service:xx})
                .done(function(c){
                  if(c.error == true || c.error == null){
                      alert(c.displayMessage);
                  }else{
                      //  displayMessages(JSON.parse(c.requestContent));
                  }
                })
                .fail(function(c){console.log("Fehler");console.log(c);})
    } 
    
    setInterval(()=>{
        var r = {requestContent:{action:"read",time:false}};
        sendActionRead(r);
       
   },4000);
    
        var r = {requestContent:{action:"read",time:true}};
        sendActionRead(r);
}