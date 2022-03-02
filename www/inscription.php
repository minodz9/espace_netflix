<?php

    // Déclarer une session

session_start();

require('src/log.php');

if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_two'])) {
    require('src/db.php');

    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $password_two = htmlspecialchars($_POST['password_two']);

    // Vérifier si Password == password_two

    if ($password != $password_two) {
        header('location: inscription.php?error=1&message=Vos de mots de passe ne sont pas identiques.');
        exit();
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('location: inscription.php?error=1&message=Votre adresse mail est invalide.');
        exit();
    }

    // requete à la base de données

    $req = $db->prepare('SELECT count(*) as numberEmail
	                        FROM user
							WHERE email = ?');
    $req->execute(array($email));

    while ($email_verify = $req->fetch()) {
        if ($email_verify['numberEmail'] != 0) {
            header('location: inscription.php?error=1&message=Votre adresse mail est déjà utilisée');
            exit();
        }
    }

    // Crypter le mot de passe hash

    $secret = sha1($email) . time();
    $secret = sha1($secret) . time();

    // Chiffrage du mot de passe


    $password = "aq1" . sha1($password . "123") . "25";


    $req = $db->prepare('INSERT INTO user (email, password, secret)
	                        VALUES (?, ?, ?) ');
    $req->execute(array($email, $password, $secret));

    header('location: inscription.php?success=1');
    exit();
}



?>



<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Netflix</title>
	<link rel="stylesheet" type="text/css" href="design/default.css">
	<link rel="icon" type="image/pngn" href="img/favicon.png">
</head>

<body>

	<?php include('src/header.php'); ?>

	<section>


		<div id="login-body">
			<h1>S'inscrire</h1>

			<?php

            if (isset($_GET['error'])) {
                if (isset($_GET['message'])) {
                    echo '<div class="alert error">' . htmlspecialchars($_GET['message']) . '</div>';
                }
            } elseif (isset($_GET['success'])) {
                echo '<div class="alert success"> Félicitation vous êtes inscrit.<a href="index.php">Connectez-vous</a></div>';
            }

            ?>

			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Confirmez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>

			<p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>

</html>