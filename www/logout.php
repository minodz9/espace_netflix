<?php


session_start(); // INITIALISE LA SESSION

session_unset(); // DESACTIVER LA SESSION

session_destroy(); // DETRUIRE LA SESSION

setcookie('auth', '', time()-1, '/', null, false, true);



header('location: index.php');
exit();
