/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function lootBuilder() {

    $("[class^='loot_']").each((i, e) => {
        $(e).blur(buildLootData);
    });

}

function buildLootData() {

    var content = [];
    if ($(".loot_xp_mi").val() != "") {
        var xp = ["xp", [$(".loot_xp_mi").val(), $(".loot_xp_ma").val()], $(".loot_xp_w").val()]
        content[content.length] = xp;
    }

    if ($(".loot_gold_mi").val() != "") {
        var xp = ["gold", [$(".loot_gold_mi").val(), $(".loot_gold_ma").val()], $(".loot_gold_w").val()]
        content[content.length] = xp;
    }

    if ($(".loot_honor_mi").val() != "") {
        var xp = ["honor", [$(".loot_honor_mi").val(), $(".loot_honor_ma").val()], $(".loot_honor_w").val()]
        content[content.length] = xp;
    }

    for (var i = 1; i <= 4; i++) {
        if ($(".loot_item"+i).val() != "") {
            var xp = ["item", "[" + $(".loot_item"+i).val() + "]", $(".loot_item"+i+"_w").val()]
            content[content.length] = xp;
        }
    }

   $("input[name='loot']").val(JSON.stringify(content));
}

function lootRender() {

    var lootValue = $('input[name="loot"]').val();
    var lObj = JSON.parse(lootValue);

    var itemIndex = 1;
    $(lObj).each((i, e) => {

        switch (e[0]) {

            case "xp":
                var mima = (e[1] + "").split(",");
                $(".loot_xp_mi").val(mima[0]);
                $(".loot_xp_ma").val(mima[1]);
                $(".loot_xp_w").val(e[2]);
                break;
            case "gold":
                var mima = (e[1] + "").split(",");
                $(".loot_gold_mi").val(mima[0]);
                $(".loot_gold_ma").val(mima[1]);
                $(".loot_gold_w").val(e[2]);

                break;
            case "item":
                $(".loot_item" + itemIndex).val(e[1]);
                $(".loot_item" + itemIndex + "_w").val(e[2]);
                itemIndex++;
                break;
            case "honor":
                var mima = (e[1] + "").split(",");
                $(".loot_honor_mi").val(mima[0]);
                $(".loot_honor_ma").val(mima[1]);
                $(".loot_honor_w").val(e[2]);

                break;
            default:
                console.log("Unknown loot type")
                break;

        }

    });

}

function statsRender(){
    
    var stats = $("input[name='fightstats']").val();
    
    var statsObj = JSON.parse(stats);
    
    var keys = Object.keys(statsObj);
    
    $(keys).each((i,e)=>{
       
        $("input.m[data-name='"+e+"']").val(statsObj[e]);
        
    });
    
             $(".m").change((e)=>{
              
        var obj = {};
        $(".m").each((i,et)=>{
                    
                   
                        obj[$(et).data("name")] = $(et).val();
                   
                    
                });
                
                $("input[name='fightstats']").val(JSON.stringify(obj));
            });
    
}

function actionBuilder(){
    
    var box = "<div id='action_{i}'>{c}</div>";
    var actionTemplate= "Type: <select class='li' value='{t}' data-name='type'>  <option value='0'>ADDITEM</option>\n\
 <option value='1'>REMITEM</option>\n\
 <option value='2'>ADDGOLD</option>\n\
 <option value='3'>REMGOLD</option>\n\
 <option value='4'>ADDXP</option>\n\
 <option value='5'>REMXP</option>\n\
 <option value='6'>ADDHONOR</option>\n\
 <option value='7'>REMHONOR</option>\n\
 <option value='8'>ADDREPUTATION</option>\n\
 <option value='9'>REMREPUTATION</option>\n\
 <option value='10'>CHANGEVAR</option>\n\
 <option value='11'>REMVAR</option>\n\
 <option value='12'>CHANGEFLAG</option>\n\
 <option value='13'>REMFLAG</option>\n\
 <option value='14'>CHANGEQUESTSTATUS</option>\n\
 <option value='15'>REMQUEST</option>\n\
 <option value='16'>CHANGENPC</option>\n\
 <option value='17'>REMNPC</option>\n\
 <option value='18'>STARTFIGHT</option>\n\
 <option value='19'>ADDTOFLAG</option>\n\
 <option value='20'>ADDTOVAR</option>\n\
 <option value='21'>STARTRANDFIGHT</option>\n\
 <option value='22'>CHANGEHP</option>\n\
 <option value='23'>CHANGEMANA</option>\n\ \n\
</select><br/>\n\
Bedingung: <input class='li' type='text' value='{c}' data-name='cond' /><br/>\n\
Key: <input class='li' type='text' value='{k}' data-name='key' /><br/>\n\
Value: <input class='li' type='text' value='{v}' data-name='value' /><div class='rem'>Rem</div>";
    
   var data = $("input[name='actions']").val();
   var dObj = JSON.parse(data);
   
   $(dObj).each((i,e)=>{
       var iC = actionTemplate
               .replace("{t}",e.type)
               .replace("{c}",JSON.stringify(e.cond))
               .replace("{k}",e.key)
               .replace("{v}",e.value);
       var hCont = box.replace("{i}",i).replace("{c}",iC);
       $("#builder").append(hCont);
       $("#action_"+i).find("select").val(e.type);
       
   });
   
   $(".addAction").click((e)=>{
       var iC = actionTemplate
               .replace("{t}",0)
               .replace("{c}","[]")
               .replace("{k}","")
               .replace("{v}","");
       var hCont = box.replace("{i}",$("div[id^='action_']").length).replace("{c}",iC);
       $("#builder").append(hCont); $(".li").change(()=>{buildAction();});
        $(".rem").click((e)=>{
       
       $(e.target).parent().remove();
       
   });
   });
   
   $(".rem").click((e)=>{
       
       $(e.target).parent().remove();
       
   });
   
   function buildAction(){
      
       var mu = [];
    $("div[id^='action_']").each((i,e)=>{
       
        var lu = {};
        $(e).find("input,select").each((x,el)=>{
            if($(el).data("name") == "cond"){
                lu[$(el).data("name")] = JSON.parse($(el).val());
            }else{
             lu[$(el).data("name")] = $(el).val();
            }
           
            
        });
        console.log(lu);
        mu[mu.length] = lu;
    });
    $("input[name='actions']").val(JSON.stringify(mu));
   }
   
      $(".li").change(()=>{buildAction();});
}

function showItemLookup(){
    
    $("#lookup").draggable();
    $("button.s").click((e)=>{
            load($("select.sm").val());
        });

    
    function load(name){
        var html = "";
      var data =  $.get("/game/service/info?a=meta&e="+name+"&s="+$("input[data-name='search']").val(),(data)=>{
        
            var key = Object.keys(data);
            for(var i = 0; i < key.length; i++)  
              html += "<div>"+data[key[i]].id+" - "+data[key[i]].name+"</div>";
          
          $(".lookup_output").html(html);
      });
      
        
    }
    
}

