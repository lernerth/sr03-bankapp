<?php
require_once('include.php');
require_once('myModel.php');

session_start();

// URL de redirection par défaut (si pas d'action ou action non reconnue)
$url_redirect = "index.php";

if (isset($_REQUEST['action'])) {

    if ($_REQUEST['action'] == 'authenticate') {
        /* ======== AUTHENT ======== */
        if (ipIsBanned($_SERVER['REMOTE_ADDR'])) {
            // cette IP est bloquée
            $url_redirect = "login.php?ipbanned";
        } else if (!isset($_REQUEST['login']) || !isset($_REQUEST['mdp']) || $_REQUEST['login'] == "" || $_REQUEST['mdp'] == "") {
            // manque login ou mot de passe
            $url_redirect = "login.php?nullvalue";
        } else {
            $car_interdits = array("'", "\"", ";", "%"); // une liste de caractères interdites
            $utilisateur = findUserByLoginPwd(str_replace($car_interdits, "", $_REQUEST['login']), str_replace($car_interdits, "", $_REQUEST['mdp']), $_SERVER['REMOTE_ADDR']);

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
        $url_redirect = 'login.php?disconnect';
    } else if ($_REQUEST['action'] == 'transfert') {
        /* ======== TRANSFERT ======== */
        if (!isset($_REQUEST['mytoken']) || $_REQUEST['mytoken'] != $_SESSION['mytoken']) {
            // echec vérification du token (ex : attaque CSRF)
            $url_redirect = "vw_virement.php?err_token";
        } else if (isVirementSessionExpired()) {
            $url_redirect = "login.php?disconnect";
        } else {
            if (!isPwdCorrect($_SESSION['connected_user']['id_user'], $_REQUEST['password']))
                $url_redirect = "vw_virement.php?bad_mdp";
            else if (is_numeric($_REQUEST['montant'])) {
                $utilisateur = false;
                $utilisateur = transfert($_REQUEST['destination'], $_SESSION["connected_user"]["numero_compte"], $_REQUEST['montant']);
                if ($utilisateur) {
                    $_SESSION["connected_user"]["solde_compte"] = getSoldeCompte($_SESSION["connected_user"]["numero_compte"]);
                    $url_redirect = "vw_virement.php?trf_ok";
                } else {
                    $url_redirect = "vw_virement.php?bad_mtordest";
                }
            } else {
                $url_redirect = "vw_virement.php?bad_mt=" . $_REQUEST['montant'];
            }
        }
    } else if ($_REQUEST['action'] == 'sendmsg') {
        /* ======== MESSAGE ======== */
        addMessage($_REQUEST['to'], $_SESSION["connected_user"]["id_user"], $_REQUEST['sujet'], $_REQUEST['corps']);
        $url_redirect = "messagerie.php?msg_ok";
    } else if ($_REQUEST['action'] == 'msglist') {
        /* ======== MESSAGE ======== */
        $_SESSION['messagesRecus'] = findMessagesInbox($_SESSION["connected_user"]["id_user"]);
        $url_redirect = "messagerie.php";
    } else if ($_REQUEST['action'] == 'usrlist') {
        /* ======== MESSAGE ======== */
        $_SESSION['listeUsers'] = findAllUsers();
        $url_redirect = "ficheclient.php";
    } else if ($_REQUEST['action'] == 'load_virement') {
        /* ======== MESSAGE ======== */
        $_SESSION['numcompte'] = getNumero_compte($id_user);
        $url_redirect = "vw_virement.php";
    }
}
header("Location: $url_redirect");
?>
