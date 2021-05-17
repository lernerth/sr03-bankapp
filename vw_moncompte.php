<?php
  session_start();
?>

<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Mon Compte</title>
  <link rel="stylesheet" type="text/css" media="all"  href="css/mystyle.css" />
</head>
<body>
    <header>
        <form method="POST" action="myController.php">
            <input type="hidden" name="action" value="disconnect">
            <input type="hidden" name="loginPage" value="vw_login.php?disconnect">
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
              ?>
          </div>
        </form>
        </article>
        
        <article>
        <form method="POST" action="myController.php">
          <input type="hidden" name="action" value="sendmsg">
          <div class="fieldset">
              <div class="fieldset_label">
                  <span>Envoyer un message</span>
              </div>
              <div class="field">
                  <label>Destinataire : </label>
                  <select name="to">
                    <?php
                    foreach ($_SESSION['listeUsers'] as $id => $user) {
                      echo '<option value="'.$id.'">'.$user['nom'].' '.$user['prenom'].'</option>';
                    }
                    ?>
                  </select>
              </div>
              <div class="field">
                  <label>Sujet : </label><input type="text" size="20" name="sujet">
              </div>
              <div class="field">
                  <label>Message : </label><textarea name="corps" cols="25" rows="3""></textarea>
              </div>
              <button class="form-btn">Envoyer</button>
              <?php
              if (isset($_REQUEST["msg_ok"])) {
                echo '<p>Message envoyé avec succès.</p>';
              }
              ?>
              <p><a href="myController.php?action=msglist&userid=<?php echo $_SESSION["connected_user"]["id_user"];?>" target="_blank">Mes messages reçus</a></p>
          </div>
        </form>
        </article>
        
    </section>

</body>
</html>
