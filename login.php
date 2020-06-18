<?php
session_start();

require('config.php');

$message = '';
$alertClass = 'alert-info';

$title= 'Connexion';

if(isset($_POST['btLogin'])) {
	if(!empty($_POST['login']) && !empty($_POST['pwd'])) {
		//Connexion à la DB
		$mysql = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DATABASE);
		
		$login = mysqli_real_escape_string($mysql,$_POST['login']);
		$pwd = $_POST['pwd'];
		
		$query = "SELECT id,login, password, status, nb_loans, image FROM `users` WHERE login='$login'";
		
		$result = mysqli_query($mysql, $query);
		
		//if($result && mysqli_affected_rows($result)>0) {	//INSERT, UPDATE, DELETE
			
		if($result && mysqli_num_rows($result)>0) {			//SELECT, SHOW
			$user = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
		}
		
		mysqli_close($mysql);

		if(isset($user['password']) && password_verify($pwd, $user['password'])) {	
			//Authentification
			$_SESSION['id'] = $user['id'];
			$_SESSION['login'] = $login;
			$_SESSION['status'] = $user['status'];
			$_SESSION['nbLoans'] = $user['nb_loans'];
			$_SESSION['image'] = $user['image'];
			
			//Rediriger vers l'administration
			if($_SESSION['status']=='admin') {
				header('Location: '.SITE_URL.'/admin/index.php');
			} else {
				header('Location: '.SITE_URL.'/index.php');
			}
			
			header('Status: 302 Temporary');
			exit;
		} else {
			$message = 'Login/mot de passe incorrect!';
			$alertClass = 'alert-danger';
		}
	} else {
		$message = 'Veuillez remplir tous les champs!';
		$alertClass = 'alert-danger';
	}
} elseif(isset($_POST['btLogout'])) {	//Déconnexion
	unset($_SESSION['login']);
	session_destroy();
	
	header('Location: '.SITE_URL.'/index.php');
	header('Status: 302 Temporary');
	exit;
} else {	//Je viens d'arriver
	//$message = 'Bienvenue';
}
?>
<?php include "inc/header.inc.php" ;?>

  <div class="row">
	<div class="col-3">
		<!-- Formulaire de connexion/déconnexion -->
		<?php if(!isset($_SESSION['login'])) { ?>
			<form id="frmLogin" action="<?= $_SERVER['PHP_SELF'];?>" method="post">
				<div class="form-group">
					<label>Login</label>
					<input class="form-control" type="text" name="login" value="<?php 
					echo (isset($_POST['keepLogin']) ? $login : '');
					?>" required>
				</div>
				<div class="form-group">
					<label>Password</label> <input class="form-control" type="password" name="pwd" required>
				</div>
				<div class="form-group form-check">
					<input class="form-check-input" type="checkbox" name="keepLogin">
					<label class="form-check-label">Retenir mon login</label>
				</div>
					<button class="btn btn-primary" name="btLogin">Se connecter</button>
					<a class="btn btn-secondary" href="register.php">S'inscrire</a>
		<p><a href="pwd_recovery.php">Mot de passe oublié ?</a></p>
		<?php } ?>
	</div>
  </div>
  <?php include "inc/footer.inc.php" ?>
</div>
