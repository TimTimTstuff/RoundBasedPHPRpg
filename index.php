<?php
session_start();
include_once 'lib/include.php';
include_once 'configuration.php';
using(["db", "user"]);

R::setup('mysql:host=' . $CONFIGURATION["server"] . ';dbname=' . $CONFIGURATION["database"], $CONFIGURATION["user"], $CONFIGURATION["password"]);
//$dbConnection = new DBConnection($CONFIGURATION["server"],$CONFIGURATION["database"], $CONFIGURATION["password"],$CONFIGURATION["user"]);
//$dbConnection->connect();
/**
 * @var Context
 */
$context = new Context();
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */if ($context->isService()) {
    using(['service']);
    include_once 'services/boot.php';
} else {
    ?> 
    <?php include_once 'pages/boot.php'; ?>
    <html>
        <head>
            <script src="frontend/scripts/jquery-3.1.1.js" type="text/javascript"></script>
            <script src="frontend/scripts/jqui/jquery-ui.min.js" type="text/javascript"></script>
            <script src="frontend/scripts/uit.js" type="text/javascript"></script>
            <script src="frontend/scripts/general.js" type="text/javascript"></script> 

            <link href="frontend/scripts/jqui/jquery-ui.structure.min.css" rel="stylesheet" type="text/css"/>
            <link href="frontend/scripts/jqui/jquery-ui.theme.min.css" rel="stylesheet" type="text/css"/>
            <link href="frontend/scripts/jqui/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
            <link href="frontend/style/main.css" rel="stylesheet" type="text/css"/>
            <link href="https://fonts.googleapis.com/css?family=MedievalSharp" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css?family=Medula+One" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css?family=Inknut+Antiqua|Itim" rel="stylesheet">
        </head>
        <body>
            <div id="content">
                <div id="head">
                    <?php echo $head; ?>
                </div>
                <div id="play">
                    <?php
                    echo $content;
                    if (!$context->isLoggedIn()) {
                        //start login
                        include 'pages/login/login.php';
                    }
                    ?>
                </div>
                <div id="footer">
    <?php echo $footer ?>
                </div>
            </div>
        </body>
    </html>
    <?php
}



if (isset($_GET['debug'])) {
    print_r($context);
}

//$user = R::Dispense("user");
//$user->name = "timo";
//$user->password = "test";
//$id = R::store($user);
?>

