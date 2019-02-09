
/**
 * Login page
 */

function LoginPage(){
    $("#show_register").click(function (){
        $("#login_window").hide();
        $("#register_window").show();
    });
    
    $("#show_login").click(function (){
        $("#login_window").show();
        $("#register_window").hide();
    });
    
    
    $("#submit_login").click(function(){
        
        var name = $("#login_window").find("input.username").val();
        var pass = $("#login_window").find("input.password").val();
        
        var u = {requestContent:{username:name, password:pass}};
        var xx = JSON.stringify(u);
        console.log(u);
        $.post("service/login",{service:xx})
                .done(function(c){
                  alert(c.displayMessage);
                  if(c.error == false){
                      location.reload();
                  }
                })
                .fail(function(c){alert("Unbekannter Fehler..  hurr durrr")});
    });
    
    $("#submit_register").click(function(){
         $.post("service/register",{})
                 .done(function(c){
                  alert(c.displayMessage);                 
                })
                .fail(function(c){alert("Unbekannter Fehler..  hurr durrr")});
    });
    
}
