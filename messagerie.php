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
    <title>Messages</title>
    <link rel="stylesheet" type="text/css" media="all" href="css/messagerie.css" />
</head>

<body>
    <header>
        <a class="moncompte" href="moncompte.php">
            <span class="tool_name">Mon Compte</span>
        </a>
        <h2><?php echo $_SESSION["connected_user"]["prenom"]; ?> <?php echo $_SESSION["connected_user"]["nom"]; ?> - Messagerie</h2>
    </header>

    <section>

        <article>
            <div class="fieldset message_sent">
                <form method="POST" action="myController.php">
                    <input type="hidden" name="action" value="sendmsg">

                    <div class="fieldset_label">
                        <span>Envoyer un message</span>
                    </div>
                    <div class="field">
                        <label>Destinataire : </label>
                        <select name="to">
                            <?php
                            foreach ($_SESSION['listeUsers'] as $id => $user) {
                                if (strcmp($_SESSION['connected_user']['profil_user'], 'CLIENT') == 0) {
                                    if (strcmp($user['profil_user'], 'EMPLOYE') == 0)
                                        echo '<option value="' . $id . '">' . $user['nom'] . ' ' . $user['prenom'] . '</option>';
                                } else {
                                    if (strcmp($user['id_user'], $_SESSION['connected_user']['id_user']) != 0)
                                        echo '<option value="' . $id . '">' . $user['nom'] . ' ' . $user['prenom'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Sujet : </label><input type="text" size="20" name="sujet">
                    </div>
                    <div class="field">
                        <label>Message : </label><textarea name="corps" cols="25" rows="3"></textarea>
                    </div>
                    <button class=" form-btn">Envoyer</button>
                        <?php
                        if (isset($_REQUEST["msg_ok"])) {
                            echo '<p>Message envoyé avec succès.</p>';
                        }
                        ?>
                </form>
            </div>  
        </article>   
    </section>


    <section>
        <div class="fieldset message_received">
            <article>
                <div class="fieldset_label">
                    <span>Messages reçus</span>
                </div>
                <div class="liste">
                    <table>
                        <tr>
                            <th>Expéditeur</th>
                            <th>Sujet</th>
                            <th>Message</th>
                        </tr>
                        <?php
                        foreach ($_SESSION['messagesRecus'] as $cle => $message) {
                            echo '<tr>';
                            echo '<td>' . $message['nom'] . ' ' . $message['prenom'] . '</td>';
                            echo '<td>'.htmlentities($message['sujet_msg'], ENT_QUOTES).'</td>';
                            echo '<td>'.htmlentities($message['corps_msg'], ENT_QUOTES).'</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>
            </article>
        </div>
        
    </section>
</body>

</html>