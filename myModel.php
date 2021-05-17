<?php

function getMySqliConnection() {
  $db_connection_array = parse_ini_file("config/config.ini");
  return new mysqli($db_connection_array['DB_HOST'], $db_connection_array['DB_USER'], $db_connection_array['DB_PASSWD'], $db_connection_array['DB_NAME']);
}

function findUserByLoginPwd($login, $pwd) {
  $mysqli = getMySqliConnection();

  if ($mysqli->connect_error) {
      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
      $utilisateur = false;
  } else {
      $req="select nom,prenom,login,id_user,numero_compte,profil_user,solde_compte from users where login='$login' and mot_de_passe='$pwd'";
      if (!$result = $mysqli->query($req)) {
          echo 'Erreur requête BDD ['.$req.'] (' . $mysqli->errno . ') '. $mysqli->error;
          $utilisateur = false;
      } else {
          if ($result->num_rows === 0) {
            $utilisateur = false;
          } else {
            $utilisateur = $result->fetch_assoc();
          }
          $result->free();
      }
      $mysqli->close();
  }

  return $utilisateur;
}


function findAllUsers() {
  $mysqli = getMySqliConnection();

  $listeUsers = array();

  if ($mysqli->connect_error) {
      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
  } else {
      $req="select nom,prenom,login,id_user from users";
      if (!$result = $mysqli->query($req)) {
          echo 'Erreur requête BDD ['.$req.'] (' . $mysqli->errno . ') '. $mysqli->error;
      } else {
          while ($unUser = $result->fetch_assoc()) {
            $listeUsers[$unUser['id_user']] = $unUser;
          }
          $result->free();
      }
      $mysqli->close();
  }

  return $listeUsers;
}



function transfert($dest, $src, $mt) {
  $mysqli = getMySqliConnection();

  if ($mysqli->connect_error) {
      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
      $utilisateur = false;
  } else {
      $req="update users set solde_compte=solde_compte+$mt where numero_compte='$dest'";
      if (!$result = $mysqli->query($req)) {
          echo 'Erreur requête BDD ['.$req.'] (' . $mysqli->errno . ') '. $mysqli->error;
      }
      $req="update users set solde_compte=solde_compte-$mt where numero_compte='$src'";
      if (!$result = $mysqli->query($req)) {
          echo 'Erreur requête BDD ['.$req.'] (' . $mysqli->errno . ') '. $mysqli->error;
      }
      $mysqli->close();
  }

  return $utilisateur;
}


function findMessagesInbox($userid) {
  $mysqli = getMySqliConnection();

  $listeMessages = array();

  if ($mysqli->connect_error) {
      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
  } else {
      $req="select id_msg,sujet_msg,corps_msg,u.nom,u.prenom from messages m, users u where m.id_user_from=u.id_user and id_user_to=".$userid;
      if (!$result = $mysqli->query($req)) {
          echo 'Erreur requête BDD ['.$req.'] (' . $mysqli->errno . ') '. $mysqli->error;
      } else {
          while ($unMessage = $result->fetch_assoc()) {
            $listeMessages[$unMessage['id_msg']] = $unMessage;
          }
          $result->free();
      }
      $mysqli->close();
  }

  return $listeMessages;
}


function addMessage($to,$from,$subject,$body) {
  $mysqli = getMySqliConnection();

  if ($mysqli->connect_error) {
      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
  } else {
      $req="insert into messages(id_user_to,id_user_from,sujet_msg,corps_msg) values($to,$from,'$subject','$body')";
      if (!$result = $mysqli->query($req)) {
          echo 'Erreur requête BDD ['.$req.'] (' . $mysqli->errno . ') '. $mysqli->error;
      }
      $mysqli->close();
  }

}

?>
