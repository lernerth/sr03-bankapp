<?php
require_once('include.php');
session_start();

if(!isset($_SESSION["connected_user"]) || $_SESSION["connected_user"] == "") {
    // utilisateur non connecté
    header('Location: login.php');      
    exit();
} 
 
$mytoken = bin2hex(random_bytes(128)); // token qui va servir à prévenir des attaques CSRF 
$_SESSION["mytoken"] = $mytoken;
$_SESSION['virementOpened_time'] = time(); 
?>

<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Virement</title>
 
  <link rel="stylesheet" type="text/css" media="all" href="css/messagerie.css" />
  <style>
    .btn-logout {position: absolute; right: 15px; top: 15px;}
  </style>
</head>
<body>
    <header>
        <a class="moncompte" href="moncompte.php">
            <span class="tool_name">Mon Compte</span>
        </a>
        <form method="POST" action="myController.php">
            <input type="hidden" name="action" value="disconnect">
            <input type="hidden" name="loginPage" value="login.php?disconnect">
            <button class="btn-logout form-btn">Déconnexion</button>
        </form>
        
        <h2><?php echo $_SESSION["connected_user"]["prenom"];?> <?php echo $_SESSION["connected_user"]["nom"];?> - Mon compte</h2>
    </header>
    
    <section>
      
        <article>
          <div class="fieldset">
              <div class="fieldset_label">
                  <span>Vos informations personnelles</span>
              </div>
              <div class="field">
                  <label>Login : </label><span><?php echo $_SESSION["connected_user"]["login"];?></span>
              </div>
              <div class="field">
                  <label>Profil : </label><span><?php echo $_SESSION["connected_user"]["profil_user"];?></span>
              </div>
          </div>
        </article>
        
        <article>
          <div class="fieldset">
              <div class="fieldset_label">
                  <span>Votre compte</span>
              </div>
              <div class="field">
                  <label>N° compte : </label><span><?php echo $_SESSION["connected_user"]["numero_compte"];?></span>
              </div>
              <div class="field">
                  <label>Solde : </label><span><?php echo $_SESSION["connected_user"]["solde_compte"];?> &euro;</span>
              </div>
          </div>
        </article>
        
        <article>
        <form method="POST" action="myController.php">
          <input type="hidden" name="action" value="transfert">
          <input type="hidden" name="mytoken" value="<?php echo $mytoken; ?>">
          <div class="fieldset">
              <div class="fieldset_label">
                  <span>Transférer de l'argent</span>
              </div>
              <div class="field">
                  <label>N° compte destinataire : </label><input type="text" size="20" name="destination">
              </div>
              <div class="field">
                  <label>Montant à transférer : </label><input type="text" size="10" name="montant">
              </div>
              <button class="form-btn">Transférer</button>
              <?php
              if (isset($_REQUEST["trf_ok"])) {
                echo '<p>Virement effectué avec succès.</p>';
              }
              if (isset($_REQUEST["bad_mt"])) {
                echo '<p>Le montant saisi est incorrect : '.$_REQUEST["bad_mt"].'</p>';
              }
              if (isset($_REQUEST["bad_mtordest"])) {
                echo '<p>Le compte ou le montant saisi est incorrect. </p>';
              }
              ?>
          </div>
        </form>
        </article>

        
    </section>

</body>
</html>
