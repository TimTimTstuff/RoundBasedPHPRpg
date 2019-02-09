function Fight(){
     drawStatusBars();

    
    $(".attack").hover((el)=>{
        var id = $(el.target).data("slot");
        showAttackInfo(id);
        $(".attack_info_box").show();
    },(el)=>{
        $(".attack_info_box").html("");
        $(".attack_info_box").hide();
    })
    
    $(".fighter").click((e)=>{
        $(".fighter").removeClass("target");
        $(e.currentTarget).addClass("target");
       
    });
    loadData();
    setInterval(loadData,2333);
    
    $(".attack").click((e)=>{
        
        var myMana = $("div[data-id='"+myId+"']").find(".fighter_mana").parent().data("act"); 
        var id = $(e.currentTarget).data("slot");
        
        var activ = $(e.currentTarget).data("content");
        var needMana = $(e.currentTarget).data("mana");
       
        if(activ == 0 || myMana < needMana)return;
        
        
        DoAction(id,"a");
    });
    
}
var time = 0;
var timerId = null;
var currentActiv = null;
var isFinished = false;
var myId = 0;

function drawAttack(){
        
    var myMana = $("div[data-id='"+myId+"']").find(".fighter_mana").parent().data("act"); 
       
    $(".attack[data-content='1']").each((i,e)=>{
       var needMana = $(e).data("mana");
       $(e).removeClass("attack_activ");
       $(e).removeClass("attack_inactiv");
       $(e).text("");
     
       
       if(myMana>=needMana){
           $(e).addClass("attack_activ");
       }else{
           $(e).addClass("attack_inactiv");
           $(e).text("MANA");
       }
        
    });
    
}

function finishFight(win){
    if(win){
        console.log("Win");
    }else{
        console.log("Lose");
    }
}

function setActivPlayer(id){
    
    if(currentActiv == id)return;
    
  
   
    var el = $(".fighter[data-id='"+id+"']");
    $(".fighter").removeClass("active_player");
    el.addClass("active_player");

    currentActiv = id;
    time = 30;
    clearInterval(timerId);
    timerId = setInterval(startActivPlayerTime,1000);
}

function drawStatusBars(){
    
    $(".fighter_status>div").each((i,el)=>{
        var act = $(el).data("act");
        var max = $(el).data("max");
        
        var data = (100/max)*act;
    
        var chEl = $(el).find("div");
        chEl.css("width",data+"%");
        chEl.html("<span style='white-space: nowrap;' >"+act+" / "+max+"</span>");
        
        if($(chEl).hasClass("fighter_mana")){
            return;
        }
        
        if(data > 75){
            chEl.css("background-color","lime");
        }else if(data > 50){
            chEl.css("background-color","greenyellow");
        }else if (data > 25){
            chEl.css("background-color","yellow");
        }else{
            chEl.css("background-color","red");
        }
        
    });
    
    
}

function startActivPlayerTime(){
    time--;
    $(".time_l").text("Zeit: "+time);
    if(time<=0){
        clearInterval(timerId);
    }
}

function showAttackInfo(slotId){
    
    var data = $("#info_slot_"+slotId).html();
    $(".attack_info_box").html(data);
     $(document).mousemove(function(event) {});
     $(".attack_info_box").css({top:event.pageY-100,left:event.pageX-10});
   
}

function startOwnTurn(){
   // $("#fight_footer").show();
}

function finishOwnTurn(){
   // $("#fight_footer").hide();
}

function setPlayerStatus(data){
    
    var element = $("div[data-id='"+data.id+"']");
    $(element).find(".fighter_hp").parent().data("act",data.hp);
    $(element).find(".fighter_mana").parent().data("act",data.mana);
    $(element).find(".f_speed").text("Speed: "+data.speed);
    $(element).find(".f_aggro").text("Aggro: "+data.aggro);
    
}

function loadData(){
    
        var v = new serviceObject();
        v.requestContent = {action:"getfighter",id:1};
        SendAction("fight",v,(c)=>{
            
           var data = JSON.parse(c.displayMessage);
           myId = data.me;
           for(var i = 0; i < data.player.length; i++){
            setPlayerStatus(data.player[i]);
           }
           if(data.me == data.current){
               
              startOwnTurn();
           }else{
               finishOwnTurn();
           }
           
           $("#fight_header").html("Am Zug: <span class='fighter_index_"+data.currindex+"'> "+data.currname+"</span>");
           
           
           drawStatusBars();
           setActivPlayer(data.current);
           drawAttack();
           LoadLog();
           
           if(data.pwin || data.mwin){
               finishFight(data.pwin);
           }
           
        },(c)=>{console.log(c);});
    
}

function FighterData(){
    
    this.FighterLeft = [];
    this.FighterRight = [];
    
}

function Fighter(){
    this.hp = 0;
    this.mana = 0;
    this.isActive = 0;
}

function FightAction(){
    this.type;
    this.id;
    this.target;
}

function DoAction(actionId,type){
    var selectId = $(".target").data("id");
    var a = new FightAction();
    a.target = selectId;
    a.type = type;
    a.id = actionId;
   
     var v = new serviceObject();
        v.requestContent = {action:"doattack",data:a};
    SendAction("fight",v,(c)=>{
          //  var data = JSON.parse(c.displayMessage);
         loadData();
    },(c)=>{
        loadData();
        console.log(c);
    });
}

function LoadLog(){
     var v = new serviceObject();
        v.requestContent = {action:"log",id:0};
    SendAction("fight",v,(c)=>{
        var data = JSON.parse(c.requestContent);
        
        if(data.length == 0)return;
        
         $("#fight_log").html("");
         data.forEach((e)=>{
             
            try{
                var d = JSON.parse(e);
                switch(d.type){
                    case "dmg":
                        buildLog(d);
                    break;
                case "heal":
                        buildHealLog(d);
                    break;
                case "hot":
                        buildHotLog(d);
                    break;
                case "dot":
                        buildDotLog(d);
                    break;
                    case "phot":
                        buildPHotLog(d);
                    break;
                case "pdot":
                        buildPDotLog(d);
                    break;
                }
                
            }catch(x){
                 $("#fight_log").append(e);
            }
             
         });
         
    },(c)=>{
        
        console.log(c);
    });
}

function buildLog(d){
    
    var attImage = "dmg_img";
    
    if(d.avoid){
        attImage = "avoid_image";
    }else if(d.crit){
        attImage = "crit_image";
    }
    
    
    
       var content = "<div class='log_action'>\n\
<div class='att_image "+attImage+"'></div> <span class='fighter_index_"+d.ai+"'>"+d.actor+"</span> greift\n\
<span class='fighter_index_"+d.ti+"'>"+d.target+"</span> mit\n\
 <span class='attack_name'>"+d.attack+"</span> an.<br/>";
   
   if(d.avoid){
       content+="<span class='avoid'><span class='fighter_index_"+d.ti+"'>"+d.target+"</span> weicht aus!</span>";
       
   }else if(d.crit){
       content+="<span class='crit'>Verursacht <span class='attack_name'>"+d.dmg+"</span> kritischen schaden.</span>";
   }
   else{
       content+="<span class='dmg'>Verursacht <span class='attack_name'>"+d.dmg+"</span> schaden.</span>";
   }
   
content+="</div></div>";
             $("#fight_log").append(content);
}
function buildHealLog(d){
    
    var attImage = "heal_image";
    
    if(d.avoid){
        attImage = "avoid_image";
    }else if(d.crit){
        attImage = "crit_image";
    }
    
    
    
       var content = "<div class='log_action'>\n\
<div class='att_image "+attImage+"'></div> <span class='fighter_index_"+d.ai+"'>"+d.actor+"</span> heilt\n\
<span class='fighter_index_"+d.ti+"'>"+d.target+"</span>  mit\n\
 <span class='attack_name'>"+d.attack+"</span> an.<br/>";
   
   if(d.avoid){
       content+="<span class='avoid'><span class='fighter_index_"+d.ti+"'>"+d.target+"</span> weicht aus!</span>";
       
   }else if(d.crit){
       content+="<span class='crit'>Verursacht <span class='attack_name'>"+d.dmg+"</span> kritische heilungt.</span>";
   }
   else{
       content+="<span class='dmg'>Verursacht <span class='attack_name'>"+d.dmg+"</span> heilung.</span>";
   }
   
content+="</div></div>";
             $("#fight_log").append(content);
}
function buildDotLog(d){
    
    var attImage = "dot_image";
    
    if(d.avoid){
        attImage = "avoid_image";
    }else if(d.crit){
        attImage = "crit_image";
    }
    
    
    
       var content = "<div class='log_action'>\n\
<div class='att_image "+attImage+"'></div> <span class='fighter_index_"+d.ai+"'>"+d.actor+"</span> wirkt\n\
<span class='attack_name'>"+d.attack+"</span> auf<span class='fighter_index_"+d.ti+"'>"+d.target+"</span> \n\
 <br/>Hält <span class='attack_name'>"+d.round+"</span> Runden<br/>";
   
 
  
      
   
   
content+="</div></div>";
             $("#fight_log").append(content);
}
function buildHotLog(d){
    
    var attImage = "hot_image";
    
   
    
    
    
       var content = "<div class='log_action'>\n\
<div class='att_image "+attImage+"'></div> <span class='fighter_index_"+d.ai+"'>"+d.actor+"</span> wirkt\n\
<span class='attack_name'>"+d.attack+"</span> auf<span class='fighter_index_"+d.ti+"'>"+d.target+"</span> \n\
  <br/>Hält <span class='attack_name'>"+d.round+"</span> Runden<br/>";
   
content+="</div></div>";
             $("#fight_log").append(content);
}
function buildPDotLog(d){
    
    var attImage = "dot_image";
    

       var content = "<div class='log_action'>\n\
<div class='att_image "+attImage+"'></div> <span class='fighter_index_"+d.ai+"'>"+d.actor+"</span> erhält\n\
<span class='attack_name'>"+d.dmg+"</span> Schaden von <span class='attack_name'>"+d.attack+"</span>\n\
  <br/>Hält <span class='attack_name'>"+d.round+"</span> Runden<br/>";
   
content+="</div></div>";
             $("#fight_log").append(content);
}
function buildPHotLog(d){
    
    var attImage = "hot_image";
    

       var content = "<div class='log_action'>\n\
<div class='att_image "+attImage+"'></div> <span class='fighter_index_"+d.ai+"'>"+d.actor+"</span> erhält\n\
<span class='attack_name'>"+d.dmg+"</span> Heilung von <span class='attack_name'>"+d.attack+"</span>\n\
  <br/>Hält <span class='attack_name'>"+d.round+"</span> Runden<br/>";
   
content+="</div></div>";
             $("#fight_log").append(content);
}