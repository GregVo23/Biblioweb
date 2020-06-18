<?php
session_start();

require 'config.php';

$title = "Auteurs";
?>

<?php 

$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);
if($link) {
	mysqli_set_charset($link, "utf8");
	
	$query = "SELECT * FROM authors";
	$result = mysqli_query($link, $query);
	if($result) {
		$authors = mysqli_fetch_all($result, MYSQLI_ASSOC);
		mysqli_free_result($result);
	}

	mysqli_close($link);
}



//var_dump($_POST['authors']);

include BASE_URL.'/inc/header.inc.php';

?>
<div class="row">
	<h1 class="title">Auteurs</h1>

	<form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
		<fieldset><legend>Nationnalité auteurs</legend>
			<div>
				<label>Nationnalité auteurs</label>

				<select name="authors" value="">
				<?php foreach($authors as $author){ ?>
					<option value="<?= $author['nationality']; ?>"><?= $author['nationality']; ?></option>
				<?php } ?>
				<select>
			</div>
			<button class="btn btn-primary" name="bt">Confirmer</button>
		</fieldset>
	</form>

<?php


if(!empty($_POST['authors']) && isset($_POST['bt'])){ ?>
<ul>
<?php foreach($authors as $author){ 
	if($_POST['authors'] == $author['nationality'])?>
	<li><?= $author['firstname']; ?></li>
	<?php } 
	$message = "ok";
	$alertClass = 'alert-success';
	?>
</ul>	
<?php
} else {
	
	$message = "pas ok";
	$alertClass = 'alert-danger';
}


?>


</div>
<?php 
include BASE_URL.'/inc/footer.inc.php'; 
?>