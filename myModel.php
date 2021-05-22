<?php
require_once('include.php');
require_once('config/config.php');

function getMySqliConnection()
{
  return new mysqli(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
}

function findUserByLoginPwd($login, $pwd, $ip)
{
  $mysqli = getMySqliConnection();

  if ($mysqli->connect_error) {
    trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error, E_USER_ERROR);
    $utilisateur = false;
  } else {
    // Pour faire vraiment propre, on devrait tester si le prepare et le execute se passent bien
    $stmt = $mysqli->prepare("select nom,prenom,login,id_user,numero_compte,profil_user,solde_compte from users where login=? and mot_de_passe=?");
    $stmt->bind_param("ss", $login, $pwd); // on lie les paramètres de la requête préparée avec les variables
    $stmt->execute();
    $stmt->bind_result($nom, $prenom, $username, $id_user, $numero_compte, $profil_user, $solde_compte); // on prépare les variables qui recevront le résultat
    if ($stmt->fetch()) {
      // les identifiants sont corrects => on renvoie les infos de l'utilisateur
      $utilisateur = array(
        "nom" => $nom,
        "prenom" => $prenom,
        "login" => $username,
        "id_user" => $id_user,
        "numero_compte" => $numero_compte,
        "profil_user" => $profil_user,
        "solde_compte" => $solde_compte
      );
    } else {
      // les identifiants sont incorrects
      $utilisateur = false;

      // on log l'IP ayant généré l'erreur
      $stmt_insert = $mysqli->prepare("insert into connection_errors(ip,error_date) values(?,CURTIME())");
      $stmt_insert->bind_param("s", $ip); // Eventuellement, gérer le cas où l'utilisateur on est derrière un proxy en utilisant $_SERVER['HTTP_X_FORWARDED_FOR'] 
      $stmt_insert->execute();
      $stmt_insert->close();
    }
    $stmt->close();

    $mysqli->close();
  }

  return $utilisateur;
}


function findAllUsers()
{
  $mysqli = getMySqliConnection();

  $listeUsers = array();

  if ($mysqli->connect_error) {
    trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error, E_USER_ERROR);
  } else {
    $req = "select nom,prenom,login,id_user,profil_user from users";
    if (!$result = $mysqli->query($req)) {
      trigger_error('Erreur requête BDD [' . $req . '] (' . $mysqli->errno . ') ' . $mysqli->error, E_USER_ERROR);
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

function getSoldeCompte($src) {
  $mysqli = getMySqliConnection();
  $req="select solde_compte from users where numero_compte='$src'";
    if (!$result = $mysqli->query($req)) {
      echo 'Erreur requête BDD ['.$req.'] (' . $mysqli->errno . ') '. $mysqli->error;
    } else {
      $solde_compte = $result->fetch_assoc();
      $solde_compte =intval($solde_compte["solde_compte"]);
    }
    $result->free();
    $mysqli->close();
    return $solde_compte;
}

function transfert($dest, $src, $mt) {
  $mysqli = getMySqliConnection();
  if ($mysqli->connect_error) {
      trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error, E_USER_ERROR);
      return false;
  } 
  if($mt>0 && $dest!=$src){
    $solde_compte =getSoldeCompte($src);
    
    if ($solde_compte < $mt) return false;
    //need a transaction here
      $req="START TRANSACTION;
      update users set solde_compte=solde_compte+$mt where numero_compte='$dest';
      update users set solde_compte=solde_compte-$mt where numero_compte='$src';
      COMMIT;
      ";
      if (!$result = $mysqli->multi_query($req)) {
          echo 'Erreur requête BDD ['.$req.'] (' . $mysqli->errno . ') '. $mysqli->error;
          return false;
      }
      $mysqli->close();
      return true;
  }
  return false;
}


function findMessagesInbox($userid)
{
  $mysqli = getMySqliConnection();

  $listeMessages = array();

  if ($mysqli->connect_error) {
    trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error, E_USER_ERROR);
  } else {
    // Pour faire vraiment propre, on devrait tester si le prepare et le execute se passen bien
    $stmt = $mysqli->prepare("select id_msg,sujet_msg,corps_msg,u.nom,u.prenom from messages m, users u where m.id_user_from=u.id_user and id_user_to=?");
    $stmt->bind_param("i", $userid); // on lie les paramètres de la requête préparée avec les variables
    $stmt->execute();
    $stmt->bind_result($id_msg, $sujet_msg, $corps_msg, $nom, $prenom); // on prépare les variables qui recevront le résultat
    while ($stmt->fetch()) {
      $unMessage = array("id_msg" => $id_msg, "sujet_msg" => $sujet_msg, "corps_msg" => $corps_msg, "nom" => $nom, "prenom" => $prenom);
      $listeMessages[$id_msg] = $unMessage;
    }
    $stmt->close();

    $mysqli->close();
  }

  return $listeMessages;
}

function addMessage($to,$from,$subject,$body)
{
  $mysqli = getMySqliConnection();

  if ($mysqli->connect_error) {
      trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error, E_USER_ERROR);
  } else {
      // Pour faire vraiment propre, on devrait tester si le execute et le prepare se passent bien
      $stmt = $mysqli->prepare("insert into messages(id_user_to,id_user_from,sujet_msg,corps_msg) values(?,?,?,?)");  
      $stmt->bind_param("iiss", $to,$from,$subject,$body); // on lie les paramètres de la requête préparée avec les variables
      $stmt->execute(); 
      $stmt->close();

      $mysqli->close();
  }

}

function ipIsBanned($ip)
{
  $mysqli = getMySqliConnection();

  if ($mysqli->connect_error) {
    trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error, E_USER_ERROR);
    return false;
  } else {
    $stmt = $mysqli->prepare("select count(*) as nb_tentatives from connection_errors where ip=?");
    $stmt->bind_param("s",  $ip);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    if ($count > 9) {
      return true; // cette IP a atteint le nombre maxi de 10 tentatives infructueuses
    } else {
      return false;
    }
    $mysqli->close();
  }
}

function mlog()
{
  $args = func_get_args();
  foreach ($args as $arg) {
    file_put_contents('debug.txt', var_export($arg, true) . "\n", FILE_APPEND);
  }
}

?>