<html>
    <head>
        <script src="frontend/scripts/jquery-3.1.1.js" type="text/javascript"></script>
        <script src="frontend/scripts/jqui/jquery-ui.min.js" type="text/javascript"></script>
        <script src="frontend/scripts/uit.js" type="text/javascript"></script>
        <script src="frontend/scripts/admin.js" type="text/javascript"></script>
        <link href="frontend/style/admin.css" rel="stylesheet" type="text/css"/>
        <link href="frontend/scripts/jqui/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        
  
<?php
session_start();
include_once 'lib/include.php';
include_once 'configuration.php';
using(["db", "user","game"]);
R::setup('mysql:host=' . $CONFIGURATION["server"] . ';dbname=' . $CONFIGURATION["database"], $CONFIGURATION["user"], $CONFIGURATION["password"]);
include 'pages/admin/functions.php';

if(isset($_GET['page'])){
    
    include "pages/admin/".$_GET["page"].".php";
    
}else{
    include "pages/admin/start.php";
} ?>
    </body>
</html>