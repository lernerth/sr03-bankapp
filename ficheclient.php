<?php
session_start();
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Clients</title>
    <link rel="stylesheet" type="text/css" media="all" href="css/messagerie.css" />
</head>
<header>
        <a class="moncompte" href="moncompte.php">
            <span class="tool_name">Mon Compte</span>
        </a>
        <h2><?php echo $_SESSION["connected_user"]["prenom"]; ?> <?php echo $_SESSION["connected_user"]["nom"]; ?> - Fiches clients</h2>
</header>

<body>
<section>
        <div class="fieldset message_received">
            <article>
                <div class="fieldset_label">
                    <span>Utilisateurs</span>
                </div>
                <div class="liste">
                    <table>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Login</th>
                            <th>ID utilisateur</th>
                            <th>Profil</th>
                            <th>Solde bancaire</th>
                            <th>Numéro de compte</th>
                        </tr>
                        <?php
                        foreach ($_SESSION['listeUsers'] as $id => $user) {
                            if (strcmp($_SESSION['connected_user']['profil_user'], 'CLIENT') == 0) {
                                if (strcmp($user['profil_user'], 'CLIENT') == 0)
                                    echo "<errmsg> Vous n'êtes pas un employé, et n'avez donc pas à cette page. <br></errmsg><br>";
                            } else {
                                if (strcmp($user['id_user'], $_SESSION['connected_user']['id_user']) != 0)
                                    echo '<tr>';
                                    echo '<td>' . $user['nom'] . '</td>';
                                    echo '<td>' . $user['prenom'] . '</td>';
                                    echo '<td>' . $user['login'] . '</td>';
                                    echo '<td>' . $user['id_user'] . '</td>';
                                    echo '<td>' . $user['profil_user'] . '</td>';
                                    echo '<td>' . $user['solde_compte'] . ' € </td>';
                                    $numcompte =  $user['numero_compte'];
                                    echo '<td>' . $user['numero_compte'] . 
                                    '   <form method="POST" action="myController.php">
                                            <input type="hidden" name="action" value="load_virement">
                                             <div class="center">
                                                    <button name ="$numcompte" value=" '. $user['id_user'] . '"> Faire un virement </a>
                                             </div>
                                            </form>
                                            </td>';
                                    echo '</tr>';
                            }
                        }
                        ?>
                    </table>
                </div>
            </article>

        </div>
        
    </section>
</body>