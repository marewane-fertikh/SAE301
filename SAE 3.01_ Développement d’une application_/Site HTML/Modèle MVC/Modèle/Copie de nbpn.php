<?php
$dbhost = 'localhost'; //url de l'host
$dbname= 'database_jeu'; //name of the database
$dbuser = "postgres"; // username
$dbpass= "dalla"; //mdp

$connection = new PDO('pgsql:host='.$dbhost.";dbname=".$dbname, $dbuser, $dbpass); // pr se connecter à la bd 

$requete = $connection->prepare('SELECT count(*) FROM jeux;');
$requete->execute();
//$tab = $requete->fetch(PDO::FETCH_ASSOC); //:: j'accède a la classe PDO, j'accède à l'attribut dePDO, j'accède à fetch assoc
//echo $tab["count"]; //on echo pas le tab direct




//fetch = je recup la requete
?>