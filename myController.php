<?php
  require_once('myModel.php');
  
  session_start();
  
  // URL de redirection par défaut (si pas d'action ou action non reconnue)
  $url_redirect = "index.php";
  
  if (isset($_REQUEST['action'])) {
  
      if ($_REQUEST['action'] == 'authenticate') {
          /* ======== AUTHENT ======== */
          if (!isset($_REQUEST['login']) || !isset($_REQUEST['mdp']) || $_REQUEST['login'] == "" || $_REQUEST['mdp'] == "") {
              // manque login ou mot de passe
              $url_redirect = "vw_login.php?nullvalue";
              
          } else {
          
              $utilisateur = findUserByLoginPwd($_REQUEST['login'], $_REQUEST['mdp']);
              
              if ($utilisateur == false) {
                // echec authentification
                $url_redirect = "vw_login.php?badvalue";
                
              } else {
                // authentification réussie
                $_SESSION["connected_user"] = $utilisateur;
                $_SESSION["listeUsers"] = findAllUsers();
                $url_redirect = "vw_moncompte.php";
              }
          }
          
      } else if ($_REQUEST['action'] == 'disconnect') {
          /* ======== DISCONNECT ======== */
          unset($_SESSION["connected_user"]);
          $url_redirect = $_REQUEST['loginPage'] ;
          
      } else if ($_REQUEST['action'] == 'transfert') {
          /* ======== TRANSFERT ======== */
          if (is_numeric ($_REQUEST['montant']) ) {
              $utilisateur = false;
              $utilisateur = transfert($_REQUEST['destination'],$_SESSION["connected_user"]["numero_compte"], $_REQUEST['montant']);
              if( $utilisateur ){
                $_SESSION["connected_user"]["solde_compte"] = getSoldeCompte($_SESSION["connected_user"]["numero_compte"]);
                $url_redirect = "vw_moncompte.php?trf_ok";
            }else {
                $url_redirect = "vw_moncompte.php?bad_mt=".$_REQUEST['montant'];
            }
              
          } else {
              $url_redirect = "vw_moncompte.php?bad_mt=".$_REQUEST['montant'];
          }
       
      } else if ($_REQUEST['action'] == 'sendmsg') {
          /* ======== MESSAGE ======== */
          addMessage($_REQUEST['to'],$_SESSION["connected_user"]["id_user"],$_REQUEST['sujet'],$_REQUEST['corps']);
          $url_redirect = "vw_moncompte.php?msg_ok";
              
      } else if ($_REQUEST['action'] == 'msglist') {
          /* ======== MESSAGE ======== */
          $_SESSION['messagesRecus'] = findMessagesInbox($_REQUEST["userid"]);
          $url_redirect = "vw_messagerie.php";
              
      } 

       
  }  
  
  header("Location: $url_redirect");

?>
