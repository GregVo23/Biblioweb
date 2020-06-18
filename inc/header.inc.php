<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title><?= $title; ?></title>
<link rel="stylesheet" href="<?= SITE_URL ?>/design/style.css">
<!-- Bootstrap -->
<link rel="stylesheet" href="<?= SITE_URL ?>/modules/bootstrap-4.4.1/css/bootstrap.min.css">
<?php 
if(isset($extra_css)){
	echo $extra_css;
}
if(isset($extra_js)){
	echo $extra_js;
}
?>
</head>
<body>
<div class="container mt-5">
<?php
// var_dump($_SERVER);
// die();
?>

<div class="container-fluid">
	  <header class="row mb-6">
			<div class="col-3">
			<?php
				$logo = "pictures/logo_biblioweb.png";
				if(isset($_SESSION['status']) && $_SESSION['status']=='admin'){
					if(strstr($_SERVER['PHP_SELF'] , "admin")){
						$logo = "../pictures/logo_biblioweb.png";
					} else {
						$logo = "pictures/logo_biblioweb.png";
					} 	
				} ?>
				<img class="logo img-fluid" src=<?= $logo ?> alt="logo de biblioweb">
			</div>
			<nav class="col-7">
				<ul>
					<li><a href="<?= SITE_URL ?>/index.php">Accueil</a></li>
					<li><a href="<?= SITE_URL ?>/books.php">Catalogue</a></li>
			<?php if(isset($_SESSION['login'])) { ?>	
					<li><a href="<?= SITE_URL ?>/profil.php">Profil</a></li>
			<?php } 
				if(isset($_SESSION['status']) && $_SESSION['status']=='admin') { ?>
					<li><a href="<?= SITE_URL ?>/admin/members.php">Membres</a></li>
			<?php } ?>	
					<li><a href="#">Contact</a></li>
				<?php if(empty($_SESSION['login'])) { ?>
					<li><a href="<?= SITE_URL ?>/login.php">Se connecter</a></li>
				<?php } ?>
			</nav>
			
			<?php if(isset($_SESSION['login'])) { ?>
			<form id="frmLogout" action="<?= SITE_URL ?>/login.php" method="post">
				<button name="btLogout" class="btn btn-secondary">Se déconnecter</button>
			</form>
			
		<!--	<a href="<?= SITE_URL ?>/login.php?btLogout">Se déconnecter</a> -->
			<?php } ?>
	  </header>
	  

	