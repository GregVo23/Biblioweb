<?php
session_start();

$title = 'Admin - Membres';

require '../config.php';
require BASE_URL.'/../utils/validators.php';

//Sécurité
if(empty($_SESSION['login'])) {
	header('Location: '.SITE_URL.'/login.php');
	//header('Status: 403 Forbidden');
	exit;
}else if($_SESSION['status']!=='admin'){
	//Déconnexion car tentative d'infiltration dans la page admin
	session_destroy();
	header('Location: '.SITE_URL.'/login.php');
	//header('Status: 403 Forbidden');
	exit;
}

$message = "";
$displayForm = false;
$alertClass = "";
$showUsersData = false;

//Afficher les utilisateurs
$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);
mysqli_set_charset($link, "utf8");
if($link) {
	
	$login = $_SESSION['login'];
	$login = mysqli_real_escape_string($link,$login);
	
	$query = "SELECT * FROM users WHERE login NOT LIKE '$login'";
	$result = mysqli_query($link,$query);
	
	$count=0;
	while($row = $result->fetch_assoc()) { 
		$users[$count] = $row;
		$count++;
	}
	
	mysqli_free_result($result);
	mysqli_close($link);
}

//Supprimer un ou plusieurs utilisateurs
if(isset($_POST['btDelete'])) {
	
	if(!empty($_POST["user_checkbox"])){
		$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);
		mysqli_set_charset($link, "utf8");
		if($link) {
			$affected_rows=0;
			foreach ($_POST["user_checkbox"] as $index => $value){
				$query = "DELETE FROM users WHERE login='$value'";
				$result = mysqli_query($link,$query);
				if(mysqli_affected_rows($link)==1){
					$affected_rows++;
				}
			}
			if($affected_rows==count($_POST["user_checkbox"])) {
				$message = "Compte\(s\) supprimé\(s\)";
				$alertClass = 'alert-success';
				//Redirection
				header('Location: '.SITE_URL.'/admin/members.php');
				header('Status: 302 Temporary');
				
			} else {
				$alertClass = 'alert-success';
				$message = "Erreur lors de la suppression!";
			}
		}
		mysqli_close($link);
	} else {
		$message = "Veuillez sélectionner un ou plusieurs utilisateurs";
	}	
}

//Séléctionner le ou les utilisateurs à update
if(isset($_POST['btSelect'])) {
	if(!empty($_POST["user_checkbox"])){
		$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);
		mysqli_set_charset($link, "utf8");
		
		if($link) {
			//Chercher les users à afficher pour la modification dans la db
			$first_value= $_POST["user_checkbox"][0];
			$query = "SELECT * FROM users WHERE login='$first_value' ";
			for ($i = 1; $i < count($_POST["user_checkbox"]); $i++) {
				$value = $_POST["user_checkbox"][$i];
				$query .= "OR login='$value' ";
			}
			$result = mysqli_query($link,$query);
			$count=0;
			
			while($row = $result->fetch_assoc()) { 
				$usersToUpdate[$count] = $row;
				$count++;
			}
			//On affiche si il existe bien, au moins, un élément
			if($count>=1){
				$showUsersData = true;
			}
			mysqli_free_result($result);
			mysqli_close($link);
		}
	}else {
		$message = "Veuillez sélectionner un ou plusieurs utilisateurs";
	}	
}

//Modifier le ou les utilisateurs séléctionnés
if(isset($_POST['btUpdate'])){
	//Modifier dans la base de données en parcourant les données
	$affected_rows=0;
	for($i=0; $i<count($_POST['id']); $i++){

		if(!empty($_POST['login'][$i]) 
			&& !empty($_POST['status'][$i]) 
			&& !empty($_POST['email'][$i])) {
			
			//vérification email
			if (filter_var($_POST['email'][$i], FILTER_VALIDATE_EMAIL)) {
				
				//connexion
				$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);
				mysqli_set_charset($link, "utf8");
				if($link) {
					
					$id = $_POST['id'][$i];
					$login = $_POST['login'][$i];
					$status = $_POST['status'][$i];
					$email = $_POST['email'][$i];
					
					$login = mysqli_real_escape_string($link,$login);
					$email = mysqli_real_escape_string($link,$email);
					
					$query = "UPDATE users SET login='$login', status='$status', email='$email', updated_at=NOW() WHERE id='$id'";
					$result = mysqli_query($link,$query);

					if(mysqli_affected_rows($link)==1) {
						$affected_rows++;
					}else{
						$message="Erreur sql";
					}
					mysqli_close($link);
				}
			}else{
				$message="Email non valide. ";
			}		
		}else {
			$message="Champs vides. ";
		}
	}
	if($affected_rows!=count($_POST['id'])){
		$alertClass = 'alert-danger';
		$message.="Une ou plusieurs lignes n'ont pas été modifiées";
	}else{
		$message="Modification effectuée avec succès";
		header('Location: '.SITE_URL.'/admin/members.php');
		header('Status: 302 Temporary');
	}
}

//Ajouter un utilisateur
if(isset($_POST['btConfirmAdd'])) {
	if(!empty($_POST['login']) 
		&& !empty($_POST['pwd']) 
		&& !empty($_POST['confPwd']) 
		&& !empty($_POST['email'])
		&& !empty($_POST['status'])) {
		//Récupérer les données envoyées
		$login = $_POST['login'];
		$pwd = $_POST['pwd'];
		$confPwd = $_POST['confPwd'];
		$email = $_POST['email'];
		$status = $_POST['status'];
		
		//Validation des données
		if(validerPassword($pwd)) { 
			if(filter_var($email,FILTER_VALIDATE_EMAIL)) {
				if(validerStatut($status)) {
					if($pwd===$confPwd) {
						//Inscrire le membre
						//Se connecter à la db
						$link = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DATABASE);
						
						$login = mysqli_real_escape_string($link,$login);
						$email = mysqli_real_escape_string($link,$email);
						$pwd = password_hash($_POST['pwd'],PASSWORD_BCRYPT);
						
						$query = "INSERT INTO `users` (`id`, `login`, `created_at`, `status`, `password`,`email`) VALUES (NULL, '$login', NOW(), '$status', '$pwd', '$email');";
						
						$result = mysqli_query($link, $query);
						
						if($result && mysqli_affected_rows($link)==1) {				
							$message = "Compte ajouté";
							$alertClass = 'alert-success';
							//Rediriger vers le formulaire de connexion
							header('Location: '.SITE_URL.'/admin/members.php');
							header('Status: 302 Temporary');
						} else {
							$message = 'Erreur lors de l\'ajout!';
							$displayForm=true;
						}
						
						mysqli_close($link);
					} else {
						$message = 'La confirmation du mot de passe ne correspond pas!';
						$displayForm=true;
					}
				} else {
					$message = 'Statut incorrect!';
					$displayForm=true;
				}
			} else {
				$message = 'Email incorrect!';
				$displayForm=true;
			}
		} else {
			$message = 'Le mot de passe n\'est pas valide (lg 8, chiffre, majuscules)!';
			$displayForm=true;
		}
		
	} else {
		$message = 'Veuillez remplir tous les champs obligatoires!';
		$displayForm=true;
	}
}
?>

<?php 
include '../inc/header.inc.php'; 
?>

<!-- Affichage des utilisateurs -->
<form action="<?= $_SERVER['PHP_SELF'];?>" method="post">
	<table class="table">
		<thead>
			<tr>
			  <th scope="col">#</th>
			  <th scope="col">Login</th>
			  <th scope="col">Email</th>
			  <th scope="col">Statut</th>
			  <th scope="col">Date de création</th>
			  <th scope="col">Dernière modification</th>
			  <th scope="col">Total emprunts</th>
			  <th scope="col"></th>
			</tr>
		</thead>
		<tbody>	
		<?php foreach($users as $user){ ?>
			<tr>
				<td><?= $user['id'] ?></td>
				<td><?= $user['login'] ?></td>
				<td><?= $user['email'] ?></td>
				<td><?= $user['status'] ?></td>
				<td><?php echo substr($user['created_at'],0,10) ?></td>
				<td><?php 
					if(isset($user['updated_at'])){
						echo substr($user['updated_at'],0,10);
					}
					?>
				</td>
				<td><?= $user['nb_loans']; ?></td>
				<td><input type="checkbox" name="user_checkbox[]" value="<?php echo $user['login']?>" /></td>
		</tr>
		<?php } ?>
		</tbody>
	</table>
	<button name="btAdd" class="btn btn-primary mb-2 mt-2">Ajouter un membre</button>
	<button name="btSelect" class="btn btn-info mb-2 mt-2">Modifier</button>
	<button name="btDelete" class="btn btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer ce(s) compte(s)?')">Supprimer le compte</button>
</form>

<!-- Affichage des données des utilisateurs séléctionnés dans des tableaux-->
<?php if($showUsersData){?>
	<form action="<?= $_SERVER['PHP_SELF'];?>" method="post">
	<?php foreach($usersToUpdate as $user){ ?>
		<table class="table">
			<thead>
				<tr>
				  <th scope="col">Login</th>
				  <th scope="col">Email</th>
				  <th scope="col">Status</th>
				</tr>
			</thead>
			<tbody>
				<input type="hidden" name="id[]" value="<?= $user['id'] ?>">
				<td><input type="text" name="login[]" value="<?= $user['login'] ?>" /></td>
				<td><input type="text" name="email[]" value="<?= $user['email'] ?>" /></td>
				<td><input type="text" name="status[]" value="<?= $user['status'] ?>" /></td>
			</tbody>	
		</table>
	<?php } ?>
	<button name="btUpdate" class="btn btn-info" onclick="return confirm('Etes-vous sûr de vouloir modifier ce(s) comptes(?)')">Modifier le(s) compte(s)</button>
	</form>
<?php } ?>

<!-- Ajouter un nouvel utilisateur -->
<?php if(isset($_POST['btAdd']) || $displayForm){?>
	<form action="<?= $_SERVER['PHP_SELF'];?>" method="post">
	<fieldset>
		<table class="table">
			<thead>
				<tr>
				  <th scope="col" style="display:none">ID</th>
				  <th scope="col">Login</th>
				  <th scope="col">Email</th>
				  <th scope="col">Statut</th>
				  <th scope="col">Mot de Passe</th>
				  <th scope="col">Conf. Mot de Passe</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="display:none"><input type="hidden" name="id"></td>
					<td><input type="text" name="login" required value="<?= !empty($_POST['login'])?$_POST['login']:""; ?>"></td>
					<td><input type="text" name="email" required value="<?= $_POST['email'] ?? "" ?>"></td>
					
					<td>
					<!--
					<input type="text" name="status" required value="<?= $_POST['status'] ?? "" ?>">
					-->
					<!-- UX/UI -->
					<select name="status" required>
						<option></option>
						<option>colibri</option>
						<option <?= (isset($_POST['status']) && $_POST['status']=="novice")?"selected":"" ?>>novice</option>
						<option <?= (isset($_POST['status']) && $_POST['status']=="admin")?"selected":"" ?>>admin</option>
						<option <?= (isset($_POST['status']) && $_POST['status']=="expert") ? "selected":"" ?>>expert</option>
						<option <?= (isset($_POST['status']) && $_POST['status']=="gourou")?"selected":"" ?>>gourou</option>
					</select>
					</td>
					
					<td><input type="password" name="pwd" required></td>
					<td><input type="password" name="confPwd" required></td>
				</tr>
			</tbody>	
		</table>
		</fieldset>
		<button name="btConfirmAdd" class="btn btn-success mb-2" onclick="return confirm('Etes-vous sûr de vouloir ajouter ce compte ?')">Ajouter</button>
	</form>

<?php } ?>
	

<?php if (!empty($message)){?>
	<div class="alert alert-danger" role="alert">
		<?= $message ?>
	</div>
<?php } ?>

<?php include '../inc/footer.inc.php'; ?>