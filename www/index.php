<?php

session_start();

require('./src/log.php');


// if(!isset($_SESSION['connect'])){

// 	   header('location: index.php');
// 	   exit();
// }

if (!empty($_POST['email']) && !empty($_POST['password'])) {
    require('src/db.php');

    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    // Adresse  email syntaxe

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('location: index.php?error=1&message=Votre adresse email est invalide.');
        exit();
    }


    $password = "aq1" . sha1($password . "123") . "25";

    $req = $db->prepare('SELECT count(*) as numberEmail
	                        FROM user
							WHERE email = ?   ');
    $req->execute(array($email));

    while ($email_verify = $req->fetch()) {
        if ($email_verify['numberEmail'] != 1) {
            header('location: index.php?error=1&message=Impossible de vous authentifier correctement.');
            exit();
        }
    }

    // connexion

    $req = $db->prepare('SELECT * FROM user WHERE email=?');
    $req->execute(array($email));

    while ($user = $req->fetch()) {
        if ($password == $user['password']) {
            $_SESSION['connect'] = 1;
            $_SESSION['email'] = $user['email'];

            if (isset($_POST['auto'])) {
                setcookie('auth', $user['secret'], time() + 364 * 24 * 3600, '/', null, false, true);
            }

            header('location: index.php?success=1');
        } else {
            header('location: index.php?error=1&Impossible de vous authentifier correctement.');
            exit();
        }
    }
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

			<?php

            if (isset($_SESSION['connect'])) { ?>

				<h1>Bienvenue !</h1>

				<?php if (isset($_GET['success'])) {
                echo '<div class="alert success">Vous êtes maintenant connecté</div>';
            }
                ?>
				<h3>Qu'allez-vous visioner aujourd'hui ?</h3>
				<small><a href="logout.php" style="color:red">Déconnexion</a></small>

			<?php	} else { ?>

				<h1>S'identifier</h1>

				<?php

                if (!isset($userAccount['blocked']) == 0) {
                    echo '<div class="alert error"> Votre compte est bloqué </div>';
                }

                ?>

				<?php

                if (isset($_GET['error'])) {
                    if (isset($_GET['message'])) {
                        echo '<div class="alert error">' . htmlspecialchars($_GET['message']) . '</div>';
                    }
                }

                ?>

				<form method="post" action="index.php">
					<input type="email" name="email" placeholder="Votre adresse email" required />
					<input type="password" name="password" placeholder="Mot de passe" required />
					<button type="submit">S'identifier</button>
					<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
				</form>


				<p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
			<?php } ?>
		</div>
	</section>

	<?php include('src/footer.php'); ?>
</body>

</html>