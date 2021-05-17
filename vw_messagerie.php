<?php
  session_start();
?>

<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Messages</title>
  <link rel="stylesheet" type="text/css" media="all"  href="css/mystyle.css" />
</head>
<body>
    <header>
        <h2><?php echo $_SESSION["connected_user"]["prenom"];?> <?php echo $_SESSION["connected_user"]["nom"];?> - Messages reçus</h2>
    </header>

    <section>
        <article>
        
          <div class="liste">
            <table>
              <tr><th>Expéditeur</th><th>Sujet</th><th>Message</th></tr>
              <?php
              foreach ($_SESSION['messagesRecus'] as $cle => $message) {
                echo '<tr>';
                echo '<td>'.$message['nom'].' '.$message['prenom'].'</td>';
                echo '<td>'.$message['sujet_msg'].'</td>';
                echo '<td>'.$message['corps_msg'].'</td>';
                echo '</tr>';
              }
               ?>
            </table>
          </div>
    
        </article>
    </section>
</body>
</html>
