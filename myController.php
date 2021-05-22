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
              $url_redirect = "login.php?nullvalue";
              
          } else {
          
              $utilisateur = findUserByLoginPwd($_REQUEST['login'], $_REQUEST['mdp']);
              
              if ($utilisateur == false) {
                // echec authentification
                $url_redirect = "login.php?badvalue";
                
              } else {
                // authentification réussie
                $_SESSION["connected_user"] = $utilisateur;
                $_SESSION["listeUsers"] = findAllUsers();
                $url_redirect = "moncompte.php";
              }
          }
          
      } else if ($_REQUEST['action'] == 'disconnect') {
          /* ======== DISCONNECT ======== */
          unset($_SESSION["connected_user"]);
          $url_redirect = $_REQUEST['loginPage'] ;
          
      } else if ($_REQUEST['action'] == 'transfert') {
          /* ======== TRANSFERT ======== */
          if (is_numeric ($_REQUEST['montant'])) {
              transfert($_REQUEST['destination'],$_SESSION["connected_user"]["numero_compte"], $_REQUEST['montant']);
              $_SESSION["connected_user"]["solde_compte"] = $_SESSION["connected_user"]["solde_compte"] -  $_REQUEST['montant'];
              $url_redirect = "moncompte.php?trf_ok";
              
          } else {
              $url_redirect = "moncompte.php?bad_mt=".$_REQUEST['montant'];
          }
       
      } else if ($_REQUEST['action'] == 'sendmsg') {
          /* ======== MESSAGE ======== */
          addMessage($_REQUEST['to'],$_SESSION["connected_user"]["id_user"],$_REQUEST['sujet'],$_REQUEST['corps']);
          $url_redirect = "messagerie.php?msg_ok";
              
      } else if ($_REQUEST['action'] == 'msglist') {
          /* ======== MESSAGE ======== */
          $_SESSION['messagesRecus'] = findMessagesInbox($_REQUEST["userid"]);
          $url_redirect = "messagerie.php";          
      } 

       
  }  
  
  header("Location: $url_redirect");