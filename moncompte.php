<?php
require_once('include.php');

session_start();

if(!isset($_SESSION["connected_user"]) || $_SESSION["connected_user"] == "") {
    // utilisateur non connecté
    header('Location: login.php');      
    exit();
}

?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Mon Compte</title>
    <link rel="stylesheet" type="text/css" media="all" href="css/moncompte.css" />
    <style>
        .icon {
            width: 2rem;
            height: 2em;
            fill: currentColor;
            overflow: hidden;
        }
    </style>
    <script src="iconfont/iconfont.js"></script>
</head>

<body>
    <header>
        <form method="POST" action="myController.php">
            <input type="hidden" name="action" value="disconnect">
            <button class="btn-logout form-btn">Déconnexion</button>
        </form>

        <h2><?php echo $_SESSION["connected_user"]["prenom"]; ?> <?php echo $_SESSION["connected_user"]["nom"]; ?> - Mon compte</h2>
    </header>

    <div class="fieldset_container">
        <article>
            <div class="fieldset user_info">
                <div class="fieldset_label">
                    <span>Votre compte</span>
                </div>
                <div class="field">
                    <label>Login : </label><span><?php echo $_SESSION["connected_user"]["login"]; ?></span>
                </div>
                <div class="field">
                    <label>Profil : </label><span><?php echo $_SESSION["connected_user"]["profil_user"]; ?></span>
                </div>
                <div class="field">
                    <label>N° compte : </label><span><?php echo $_SESSION["connected_user"]["numero_compte"]; ?></span>
                </div>
                <div class="field">
                    <label>Solde : </label><span><?php echo $_SESSION["connected_user"]["solde_compte"]; ?> &euro;</span>
                </div>
            </div>
        </article>

        <article>
            <div class="tools fieldset">
                <div class="fieldset_label">
                    <span>OUTILS</span>
                </div>
                <div class="tools_container">
                    <a class="messagerie" href="myController.php?action=msglist">
                        <svg class="icon" aria-hidden="true">
                            <use xlink:href="#icon-messagerie"></use>
                        </svg>
                        <span class="tool_name">Messagerie</span>
                    </a>
                    <a class="virement" href="vw_virement.php">
                        <svg class="icon" aria-hidden="true">
                            <use xlink:href="#icon-virement"></use>
                        </svg>
                        <span class="tool_name">Virement</span>
                    </a>
                    <a class="fiche_client" href="myController.php?action=usrlist&userid=<?php echo $_SESSION["connected_user"]["id_user"]; ?>">
                        <svg class="icon" aria-hidden="true">
                            <use xlink:href="#icon-clients"></use>
                        </svg>
                        <span class="tool_name">Fiche Client</span>
                    </a>
                </div>
            </div>
        </article>

    </div>

</body>

</html>