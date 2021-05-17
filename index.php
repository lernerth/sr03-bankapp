<?php

  // test connection mySQL
  $db_connection_array = parse_ini_file("config/config.ini");
    
  $mysqli = new mysqli($db_connection_array['DB_HOST'], $db_connection_array['DB_USER'], $db_connection_array['DB_PASSWD'], $db_connection_array['DB_NAME']);
  
  if ($mysqli->connect_error) {
        // problème connection mySQL =>STOP
        echo '<html><head><meta charset="utf-8"><title>MySQL Error</title><link rel="stylesheet" type="text/css" media="all"  href="css/mystyle.css" /></head><body>'.
             '<p>Impossible de se connecter à MySQL.</p>'.
             '<p>Voici le message d\'erreur : <b>'. utf8_encode($mysqli->connect_error) . '</b></p>'.
             '<br/>Vérifiez vos paramètres dans le config.ini'.
             '</body></html>';        
  } else {
        // mySQL répond bien
        session_start();

        if(!isset($_SESSION["connected_user"]) || $_SESSION["connected_user"] == "") {
            // utilisateur non connecté
            header('Location: vw_login.php');      

        } else {
            header('Location: vw_moncompte.php');      
        }
  }

?>

<html><head><meta charset="utf-8"><title>MySQL Error</title><link rel="stylesheet" type="text/css" media="all"  href="css/mystyle.css" /></head><body>