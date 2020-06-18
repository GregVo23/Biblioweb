<?php

session_start();

$title = 'Accueil - Livres';
$message;

require 'config.php';

//Afficher les livres
$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);
if($link) {
	mysqli_set_charset($link, "utf8");
	$query = "SELECT b.ref, b.title, a.firstname, a.lastname, b.description, b.cover_url 
				FROM books b, authors a 
				WHERE b.author_id = a.id
				ORDER BY a.firstname, a.lastname, b.title ";
	$result = mysqli_query($link,$query);
	
	$count=0;
	while($row = $result->fetch_assoc()) { 
		$books[$count] = $row;
		$count++;
	}
	
	//Récupérer les ref des livres empruntés
	$rentedBooks = [];
	$query = "SELECT book_id FROM loans";
	$result = mysqli_query($link, $query);
	if($result) {
		while(($rentedBook = mysqli_fetch_assoc($result)) !== null) {
			$rentedBooks[] = $rentedBook['book_id'];
		}
	}
	//var_dump($rentedBooks);
	mysqli_free_result($result);
	mysqli_close($link);
}


if(isset($_GET['btSearch'])){
	$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);
	mysqli_set_charset($link, "utf8");
	$books=[];
	if($link) {
		if(!empty($_GET['search'])){
			$word = mysqli_real_escape_string($link,$_GET['search']);
			$query = "SELECT b.ref, b.title, a.firstname, a.lastname, b.description, b.cover_url 
				FROM books b, authors a 
				WHERE b.author_id = a.id
				AND (b.ref LIKE '%$word%'
				OR b.title LIKE '%$word%'
				OR a.firstname LIKE '%$word%'
				OR a.lastname LIKE '%$word%'
				OR CONCAT(a.firstname,' ',a.lastname) LIKE '%$word%'
				OR b.description LIKE '%$word%'
				OR b.cover_url LIKE '%$word%')
				ORDER BY a.firstname, a.lastname, b.title";
		}
		else{
			$query = "SELECT b.ref, b.title, a.firstname, a.lastname, b.description, b.cover_url 
				FROM books b, authors a 
				WHERE b.author_id = a.id
				ORDER BY a.firstname, a.lastname, b.title";
		}
		$result = mysqli_query($link,$query);
		$count=0;
		while($row = $result->fetch_assoc()) { 
			$books[$count] = $row;
			$count++;
		}
		mysqli_free_result($result);
		mysqli_close($link);
	}
}
// var_dump($_SESSION);
// die;
//Emprunter un livre
if(isset($_POST['btBorrow'])) {
	if(isset($_POST['ref'])) {
		$bookId = $_POST['ref'];
		$userId = $_SESSION['id'];
		//var_dump($_SESSION['id']);
		$dateRetour = date('Y-m-d',mktime(23,59,59,date('m'),date('j')+14));
	
		//Insérer dans la table loans
			//Se connecter
		$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);

		if($link) {
				//Nettoyer
			$bookId = mysqli_real_escape_string($link,$_POST['ref']);
			
				//Vérifier si le livre n'est pas déjà emprunté
			if(!in_array($bookId,$rentedBooks)) {//si oui
					//Préparer
				$query = "INSERT INTO loans(book_id,user_id,return_date) VALUES('$bookId','$userId','$dateRetour')";
					//Envoyer
				$result = mysqli_query($link, $query);
				
				if($result && mysqli_affected_rows($link)==1) {
					$message = "Livre emprunté avec succès.";
					$alertClass = 'alert-success';
					$rentedBooks[] = $bookId;
					
					//Màj table users
					$query = "SET NAMES utf8";
					$result = mysqli_query($link,$query);
					
					$query = "SELECT * FROM status ORDER BY min_loans DESC";
					$result = mysqli_query($link,$query);
					$status = [];

					if($result) {
						while($row = $result->fetch_assoc()) { 
							$status[$row['status']] = $row['min_loans'];
						}
						mysqli_free_result($result);
					}
					
					//Incrémenter son nombre total d'emprunts (nb_loans)
					$nbLoans = $_SESSION['nbLoans'];
					$nbLoans++;
					// var_dump($_SESSION['nbLoans']);
					// die;
					
					$query = "UPDATE users SET nb_loans='$nbLoans'";
					
					//Eventuellement élever son statut
					if(!in_array($_SESSION['status'],['admin',current($status)])) {
						$newStatus = array_search($nbLoans,$status);
						
						if($newStatus) {
							$query .= ", status='$newStatus'";
							$message .= "<p>Félicitations, vous êtes devenu <strong>$newStatus</strong>.</p>";
							$alertClass = 'alert-success';
						}
					}
					
					$query .= " WHERE id='{$_SESSION['id']}'";
					
					$result = mysqli_query($link, $query);
					
					if($result && mysqli_affected_rows($link)===1) {
						$_SESSION['nbLoans'] = $nbLoans;
						
						if(!empty($newStatus)) {
							$_SESSION['status'] = $newStatus;
						}	
					}
				} else {	//Sinon
					$message = "Problème survenu lors de l'emprunt!<br>".mysqli_error($link);
					$alertClass = 'alert-danger';
				}
			} else {
				$message = "Livre déjà emprunté!";
				$alertClass = 'alert-danger';
			}
			mysqli_close($link);
		}
	}
}
?>

<?php 
include 'inc/header.inc.php';
?>

<form action="<?= $_SERVER['PHP_SELF'];?>" method="get">
	<div class="input-group mt-5 mb-3">
		<input type="text" class="col-4 form-control" placeholder="Recherchez..." name="search">
		<button class="btn btn-outline-primary" name="btSearch">Envoyer</button>
	</div>
</form>

<!-- Affichage des Livres -->
<h2>Nos Livres</h2>
<table class="table">
	<thead>
		<tr>
		  <th scope="col">Ref.</th>
		  <th scope="col">Titre</th>
		  <th scope="col">Auteur</th>
		  <th scope="col">Description</th>
		  <th scope="col">Cover</th>
		<?php if(isset($_SESSION['login'])) { ?>
		  <th scope="col">Actions</th>
		<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach($books as $book){ ?>
			<tr>
				<td><?= $book['ref'] ?></td>
				<td><?= $book['title'] ?></td>
				<td><?php echo $book['firstname'].' '. $book['lastname']?></td>
				<td><?= $book['description'] ?></td>
				<td><?= $book['cover_url'] ?></td>
			<?php if(isset($_SESSION['login'])) { ?>
				<td>
				<?php if(!in_array($book['ref'],$rentedBooks)) { ?>
					<form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
						<input type="hidden" name="ref" value="<?= $book['ref'] ?>">
						<button name="btBorrow" class="btn btn-primary">Emprunter</button>
					</form>
				<?php } else { ?>                       
					<form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
						<input type="hidden" name="ref" value="<?= $book['ref'] ?>">
						<p class="loans">Ce livre n'est pas disponible actuellement</p>
					</form> 
				<?php } ?>
				</td>
			<?php } ?>
			</tr>
	<?php } ?>
	</tbody>
</table>
<?php include 'inc/footer.inc.php'; ?>