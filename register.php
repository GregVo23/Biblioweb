<?php
session_start();

$message;

require 'config.php';
require 'utils/validators.php';

$title = "Biblioweb - Inscription";

$extra_css = '<link rel="stylesheet" href="register_style.css">';
$extra_js = '<script src="register.js"></script>';

if(isset($_POST['btSignin'])) {
	if(!empty($_POST['login']) 
		&& !empty($_POST['pwd']) 
		&& !empty($_POST['confPwd']) 
		&& !empty($_POST['email'])) {
		//Récupérer les données envoyées
		$login = $_POST['login'];
		$pwd = $_POST['pwd'];
		$confPwd = $_POST['confPwd'];
		$email = $_POST['email'];
		
		//Validation des données
		if(validerPassword($pwd) 
			&& filter_var($email,FILTER_VALIDATE_EMAIL)) {
			if($pwd===$confPwd) {
				//Inscrire le membre
				//Se connecter à la db
				$mysql = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DATABASE);
				
				$login = mysqli_real_escape_string($mysql,$login);
				$email = mysqli_real_escape_string($mysql,$email);
				$pwd = password_hash($_POST['pwd'],PASSWORD_BCRYPT);
				
				$query = "INSERT INTO `users` (`id`, `login`, `created_at`, `status`, `password`,`email`,`image`,`updated_at`) VALUES (NULL, '$login', NOW(), 'novice', '$pwd', '$email', NULL, NULL);";
				
				
				$result = mysqli_query($mysql, $query);
				
				if($result && mysqli_affected_rows($mysql)==1) {

					//Authentifier le nouveau membre
					$_SESSION['login'] = $login;
					$_SESSION['status'] = 'novice';

					mysqli_close($mysql);

					//Si connecté, récupérer l'id (normalement doir être dans register)
					if(isset($_SESSION['login'])){

						$mysql = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DATABASE);

						//Récupération de l'ID du nouveau membre
						$query = "SELECT id from users where login = '$login';";

						$result = mysqli_query($mysql, $query);

							if($result && mysqli_num_rows($result)>0){
								$row = mysqli_fetch_assoc($result);
								mysqli_free_result($result);
								$_SESSION['id'] = $row['id']; 

							} else {
								$message = 'Erreur inscription';
							}

					//Rediriger vers le formulaire de connexion
					header('Location: '.SITE_URL.'/index.php');
					header('Status: 302 Temporary');
					exit;
					} else {
						$message = 'Erreur lors de Votre inscription';
					}
				} else {
					$message = 'Erreur lors de l\'inscription!';
					mysqli_close($mysql);
				}
				
			} else {
				$message = 'La confirmation du mot de passe ne correspond pas!';
			}
		} else {
			$message = 'Votre mot de passe n\'est pas valide (lg 8, chiffre, majuscules)!';
		}
	} else {
		$message = 'Veuillez remplir tous les champs obligatoires!';
	}
}
?>
<?php include "inc/header.inc.php" ?>
<div class="container-fluid">
	<?php if (isset($message)){?>
		<div class="alert alert-danger" role="alert">
			<?= $message ?>
		</div>
	<?php } ?>

	<form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
		<fieldset><legend>Inscription</legend>
			<div>
				<label>Login</label>
				<input type="text" name="login" value="" required>
			</div>
			<div>
				<label>Email</label>
				<input type="email" name="email" value="" required>
			</div>
			<div>
				<label>Password</label>
				<input type="password" name="pwd" required>
			</div>
			<div>
				<label>Confirm Password</label>
				<input type="password" name="confPwd" required>
			</div>
			<button class="btn btn-primary" name="btSignin">Confirmer</button>
		</fieldset>
	</form>
<?php include "inc/footer.inc.php" ?>