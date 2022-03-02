<?php 



 require ('./www/index.php');
 require('./www/inscription.php');

session_start();
session_unset();
session_destroy();

 header ('location: ./index.php');


?>