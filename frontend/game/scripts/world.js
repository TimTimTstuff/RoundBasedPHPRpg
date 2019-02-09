function World(){

    $("a.spaceaction").click((e)=>{
        
        var request = new serviceObject();
        var id = $(e.target).data("name");
        request.requestContent = {action:"spaceaction",name:id};
        SendAction("fmap",request,(c)=>{
            if(c.requestContent == true){
                location.reload();
            }else{
                alert(c.displayMessage);
            }
            //location.reload();
        },(c)=>{
            alert(c.displayMessage);
        });
        
        
    });
    
    $(".action").click((e)=>{
        
       
        var request = new serviceObject();
        var id = $(e.target).data("id");
        request.requestContent = {action:"open",id:id};
        SendAction("fmap",request,(c)=>{
            if(c.requestContent == true){
                location.reload();
            }else{
                alert(c.displayMessage);
            }
            //location.reload();
        },(c)=>{
            alert(c.displayMessage);
        });
        
    });
    
    setInterval(getWorldInf,2300);
}

function getWorldInf(){
    
    
        var v = new serviceObject();
        v.requestContent = {action:"info"};
        
        
        SendAction("info",v,(c)=>{
            
            if(c.requestContent == "reload"){
                location.reload();
            }
            
        },(c)=>{console.log(c)});
    
    
}