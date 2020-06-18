<?php
session_start();
// var_dump($_SESSION);
// die();
require 'utils/validators.php';

$title = 'Profil';

//Sécurité
if(empty($_SESSION['login'])) {
	header('Location: index.php', null, 302);
	exit;
}

require 'config.php';

$message;
$showPasswordFields = false;
$titleLoans = "";
$SendNewPict = "display:none;";
$buttonNewpic = "block;";

if(isset($_SESSION['image'])){
	$avatar = $_SESSION['image'];
} else {
	$avatar = "pictures/avatar.jpg";
}


$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

if($link) {
	$login = $_SESSION['login'];
	$query = "SELECT * FROM users WHERE login='$login'";
	
	$result = mysqli_query($link,$query);

	if($result) {
		$user = mysqli_fetch_assoc($result);
		
		mysqli_free_result($result);
	}

	mysqli_close($link);
	
	//Formatage des données
	$user['login'] = strtoupper($user['login']);
	$user['created_at'] = substr($user['created_at'],0,10);
}

	//Rendre des livres empruntés
	if(isset($_POST['btReturn'])){
		if(isset($_POST['back'])){
			$bookIdReturn = $_POST['back'];
			
			$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);
			if($link) {

				//Récupérer un tableau avec les infos des livres loué par l'utilisateur
				$returnLoans = "DELETE FROM `loans` WHERE book_id = '$bookIdReturn';"; 

				mysqli_query($link, $returnLoans);

				mysqli_close($link);

				$message = "Votre livre a été rendu avec succès";
				$alertClass = 'alert-success';
			}
		} else { 
			$message = "'erreur lors de la remise'.$bookIdReturn";
			$alertClass = 'alert-danger';
		}
	}

	//Récupération des livres empruntés

	$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

	if($link) {
		mysqli_set_charset($link, "utf8");
		$login = $_SESSION['login'];
		$userId = $_SESSION['id'];


		//Récupérer un tableau avec les infos des livres loué par l'utilisateur
		$queryLoans = "SELECT loans.book_id, books.title, authors.lastname, authors.firstname, books.cover_url
						FROM loans, books,authors
						WHERE loans.book_id = books.ref
						AND authors.id = books.author_id
						AND loans.user_id = '$userId';"; 

		$resultLoansId = mysqli_query($link,$queryLoans);

		if($resultLoansId) {                // Changer le titre H2
			$userLoans = mysqli_fetch_all($resultLoansId, MYSQLI_ASSOC);

			mysqli_free_result($resultLoansId);
			$nbOnLoans = 0;
			$titleLoans = "Aucun emprunt";
		if($userLoans != NULL){
			//Affichage du titre de la page en fonction de s'il y a un/des emprunt(s)
			if(isset($nbOnLoans)){
					$nbOnLoans = sizeof($userLoans);
				if($nbOnLoans > 1){
					$titleLoans = "Vos emprunts";
				} elseif($nbOnLoans === 1) {
					$titleLoans = "Votre emprunt";
				}
			}
		} 
	}

		mysqli_close($link);
}	
	//Rendre des livres empruntés
	if(isset($_POST['btReturn'])){
		if(isset($_POST['back'])){
			$bookIdReturn = $_POST['back'];
			
			$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);
			if($link) {

				//Récupérer un tableau avec les infos des livres loué par l'utilisateur
				$returnLoans = "DELETE FROM `loans` WHERE book_id = '$bookIdReturn';"; 

				mysqli_query($link, $returnLoans);

				mysqli_close($link);

				$message = "Votre livre a été rendu avec succès";
			}
	} else { 
		$message = "'erreur lors de la remise'.$bookIdReturn";
	}
} 

//Traitement des commandes

if(isset($_POST['btUpdate'])) {
	//die("mise à jour");
	//Afficher les champs
	$showPasswordFields = true;
	
	if(!empty($_POST['new_pass']) && !empty($_POST['conf_pass'])) {	//Modifier dans la base de données
		//die('mise à jour');
		if($_POST['new_pass'] == $_POST['conf_pass']) {
			if(validerPassword($_POST['new_pass'])) {
				//die('mise à jour');
				$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

				if($link) {
					$login = $_SESSION['login'];
					
					$pass = password_hash($_POST['new_pass'], PASSWORD_BCRYPT);

					$query = "UPDATE users SET password='$pass', updated_at=NOW() WHERE login='$login'";
				
					$result = mysqli_query($link,$query);

					if(mysqli_affected_rows($link)==1) {
						$message = "Mot de passe modifié";
						$alertClass = 'alert-success';
						
						$showPasswordFields = false;
					} else {
						$message = "Erreur lors de la modification!";
						$alertClass = 'alert-danger';
					}

					mysqli_close($link);
				}
			} else {
				$message = "Mot de passe invalide!";
				$alertClass = 'alert-danger';
			}		
		} else {
			$message = "Mots de passe différents!";
			$alertClass = 'alert-danger';
		}
	}
	
} elseif(isset($_POST['btDelete'])) {
	//die("suppression");
	$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

	if($link) {
		$login = $_SESSION['login'];
		$query = "DELETE FROM users WHERE login='$login'";
		
		$result = mysqli_query($link,$query);

		if(mysqli_affected_rows($link)==1) {
			$message = "Compte supprimé";
			$alertClass = 'alert-success';
			
			//Déconnexion
			session_destroy();
			
			//Redirection
			header('Location: index.php', null, 302);
			exit;
		} else {
			$alertClass = 'alert-danger';
			$message = "Erreur lors de la suppression!";
		}

		mysqli_close($link);
	}
}
/* MOCK
$user = [
	'nom' => 'ced',
	'status' => 'admin',
	'dateInscr' => '22/09/1945',
	'email' => 'ceruth@epfc.eu',
];*/

// Upload image de profil
if(isset($_SESSION['login'])){
	if(isset($_POST['btUpload'])){
		if(isset($_FILES['image']) && !isset($_FILES['error'])){
			if($_FILES['image']['size'] <= 3000000){

				$infoImg = pathinfo($_FILES['image']['name']);
				$extensionImg = $infoImg['extension']; 
				$extensionOk = ['png','jpeg','jpg','JPG','JPEG','PNG'];
				
				if(in_array($extensionImg, $extensionOk)){

					$uploadFile = 'upload/'.time().basename($_FILES['image']['name']);
					move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);

					$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

					if($link) {
						
						$imageUrl = $uploadFile;
						
						$query = "update users set image = '$imageUrl' where users.login = '$login';";
						
						$result = mysqli_query($link, $query);

						$message = "L'envois de l'image a en principe réussi";
						$alertClass = 'alert-success';
						
						if($result) {

							$message = "L'envois de l'image a réussi";
							$alertClass = 'alert-success';

							//Enregistrer nom de l'image du membre dans la session
							$_SESSION['image'] = $uploadFile;
							$avatar = $uploadFile;
		
							mysqli_close($link);

						}
					}

				} else {
					$message = "Erreur ! Format d'image d'incorrect";
					$alertClass = 'alert-danger';
				}
			} else {
				$message = "taille de l'image trop volumineuse";
				$alertClass = 'alert-danger';
			}
		} else {
			$message = "L'envois de l'image n'a pas réussi";
			$alertClass = 'alert-danger';
		}
	}
}

?>
<?php
include 'inc/header.inc.php';
?>

<div class="row">
	<div class="col">
		<div class="card" style="width: 20rem; margin-bottom:40px; margin-top:30px;">
		 <!-- <img class="card-img-top" src="https://picsum.photos/150/150" alt=""> -->
		  <img src="<?= $avatar ?>" class="card-img-top" alt="picture">

			<div class="card-body">
				<!--Modifier la photo de profil-->
				
				<!--Affichage ou pas du bonton et lien upload img-->
				<?php if(isset($_GET['picture'])){
					$SendNewPict = "block;";
					$buttonNewpic = "display:none;";
				} ?>
				<a style="<?= $buttonNewpic ?>" href="profil.php?picture"><p class="btn btn-primary mb-2 mt-2" >Modifier photo de profil</p></a>
				<form style="<?= $SendNewPict ?>" method="post" action="" enctype="multipart/form-data">
					<input type="file" name="image">
					<button name="btUpload" type="submit" class="btn btn-primary mb-2 mt-2">Envoyer photo</button>
				</form>
				<h5 class="card-title"><?= $user['login']; ?> <em>(<?= $user['status']; ?>)</em></h5>
				<p class="card-text">
					<p><span>Email:</span> <?= $user['email']; ?></p>
					<p><span>Inscrit le:</span> <?= $user['created_at']; ?></p>
				</p>
				<form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
				
				<?php if($showPasswordFields) { ?>
					<label>Nouveau mot de passe</label>
					<input type="password" name="new_pass">
					<label>Confirmer mot de passe</label>
					<input type="password" name="conf_pass">
				<?php } ?>
					<button name="btUpdate" class="btn btn-primary mb-2 mt-2">Changer mot de passe</button>
					<button name="btDelete" class="btn btn-danger" onclick="return confirm('Etes-vous sûr de vouloir supprimer votre compte?')">Supprimer le compte</button>
			</form>
		  </div>
		</div>
	</div>
	<div class="col">
		<!-- Afficher les livres empruntés -->
		<h2><?= $titleLoans ?></h2>
		<p>Vous avez <?= $nbOnLoans ?> livres empruntés</p>
		<table class="table">
			<thead>
				<tr>
				<th scope="col">Ref.</th>
				<th scope="col">Titre</th>
				<th scope="col">Auteur</th>
				<th scope="col">Cover</th>
				<?php if(isset($_SESSION['login'])) { ?>
				<th scope="col">Actions</th>
				<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach($userLoans as $loansUser){ ?>
					<tr>
						<td><?= $loansUser['book_id'] ?></td>
						<td><?= $loansUser['title'] ?></td>
						<td><?php echo $loansUser['firstname'].' '. $loansUser['lastname']?></td>
						<td><?= $loansUser['cover_url'] ?></td>
						<td>
							<form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
								<input type="hidden" name="back" value="<?= $loansUser['book_id'] ?>">
								<button name="btReturn" class="btn btn-primary">Rendre</button>
							</form>
						</td>
					</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<?php include 'inc/footer.inc.php'; ?>