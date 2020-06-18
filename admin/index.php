<?php
//Récupérer ou démarrer une session
session_start();

require '../config.php';

$title = "Accueil admin - Biblioweb";
?>

<?php
if(!isset($_SESSION['login'])) {
	//Rediriger vers le formulaire de connexion
	header('Location: '.SITE_URL.'/login.php');
	header('Status: 302 Temporary');
	exit;
} else if($_SESSION['status']!=='admin'){
	//Déconnecter car tentative d'infiltration dans la page admin
	session_destroy();
	header('Location: '.SITE_URL.'/login.php');
	header('Status: 403 Forbidden');
	exit;
}
?>

<!DOCTYPE html>
<?php include "../inc/header.inc.php" ?>
<div class="row">	
	<h1 class="title">Administration</h1>

<?php include "../inc/footer.inc.php" ?>
	</div>
</div>