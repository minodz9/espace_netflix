<?php



if (isset($_COOKIE['auth']) && !isset($_SESSION['connect'])) {
    $secret = htmlspecialchars($_COOKIE['auth']);

    // Vérification avec requete si le cookie lié un compté

    require('src/db.php');



    $req = $db->prepare('SELECT count(*) as numberAccount FROM user WHERE secret =?');

    $req->execute(array($secret));

    while ($user = $req->fetch()) {
        if ($user['numberAccount'] == 1) {
            $reqUser = $db->prepare('SELECT * FROM user WHERE secret = ?');
            $reqUser->execute(array($secret));

            while ($userAccount = $reqUser->fetch()) {
                $_SESSION['connect'] = 1;
                $_SESSION['email'] = $userAccount['email'];
            }
        }
    }
}

// Bloquer un utilisateurs

if (isset($_SESSION['connect'])) {
    require('./src/db.php');

    $reqUser = $db->prepare('SELECT * FROM user WHERE email = ?');
    $reqUser->execute(array($_SESSION['email']));

    while ($userAccount = $reqUser->fetch()) {
        if ($userAccount['blocked'] == 1) {
            header('location: logout.php');
            exit();
        }
    }
}
