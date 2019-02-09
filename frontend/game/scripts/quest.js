function Quest(){
    
    if($("#quest_window").length == 0)return;
    
    if($(".quest_item").length == 0){$("#quest_window").hide();}
    
    $(".quest_item").click((e)=>{
        var quid = $(e.target).data("id");
        $("#quest_list").hide();
        $(".quest[data-id='"+quid+"']").show();
        
    });
    
    $("a[data-action='accept'").click((e)=>{
       var quid = $(e.target).parent().parent().data("id");
       var so = new serviceObject();
       so.requestName = "quest";
       so.requestContent = {action:"accept",id:quid};
        SendAction("quest",so,(c)=>{
            if(c.requestContent == true){
                location.reload();
            }else{
                alert(c.displayMessage);
            }
        },(c)=>{
            alert(c.displayMessage);
        });
    });
    
     $("a[data-action='solve']").click((e)=>{
        var quid = $(e.target).parent().parent().data("id");
       var so = new serviceObject();
       so.requestName = "quest";
       so.requestContent = {action:"solve",id:quid};
       SendAction("quest",so,(c)=>{
            if(c.requestContent == true){
                location.reload();
            }else{
                alert(c.displayMessage);
            }
        },(c)=>{
            alert(c.displayMessage);
        });
    });
    
    $("a[data-action='decline'").click((e)=>{
        $(".quest").hide();
        $("#quest_list").show();
    });
}