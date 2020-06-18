<?php

require('config.php');

$message = '';

$title= 'Nouveau Mot de Passe';

if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) 
	&& ($_GET["action"]=="reset") && !isset($_POST["action"])){
	$link =mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);
	$key = mysqli_real_escape_string($link, $_GET["key"]);
	$email = $_GET["email"];
	$curDate = date("Y-m-d H:i:s");
	$query="SELECT * FROM `password_reset_temp` WHERE `key`='$key' and `email`='$email'";
	$result = mysqli_query($con,$query);
	    $row = mysqli_num_rows($query);
	    if ($row==""){
			$error .= "Lien non valide";
		}else{
			$row = mysqli_fetch_assoc($query);
			$expDate = $row['expDate'];
			if ($expDate >= $curDate){
				?>
				
				<?php
			}else{
				$error .= "Lien expiré";
			}
        }
	if($error!=""){
		echo "<div class='error'>".$error."</div><br />";
	} 
} // isset email key validate end
 
 
if(isset($_POST["email"]) && isset($_POST["action"]) &&
 ($_POST["action"]=="update")){
	$error="";
	$pass1 = mysqli_real_escape_string($con,$_POST["pass1"]);
	$pass2 = mysqli_real_escape_string($con,$_POST["pass2"]);
	$email = $_POST["email"];
	$curDate = date("Y-m-d H:i:s");
	if ($pass1!=$pass2){
		$error.= "<p>Password do not match, both password should be same.<br /><br /></p>";
	}
	if($error!=""){
		echo "<div class='error'>".$error."</div><br />";
	}else{
		$pass1 = md5($pass1);
		$query="UPDATE `users` SET `password`='$pass1', `trn_date`='$curDate' WHERE `email`='$email';"
		mysqli_query($con,$query);
	);
	$query="DELETE FROM `password_reset_temp` WHERE `email`='$email';"
	mysqli_query($con,$query);
	 
	echo '<div class="error"><p>Congratulations! Your password has been updated successfully.</p>
	<p><a href="https://www.allphptricks.com/forgot-password/login.php">
	Click here</a> to Login.</p></div><br />';
   } 
}
?>
<?php include 'inc/'?>
<form method="post" action="" name="update">
	<input type="hidden" name="action" value="update" />
	<br /><br />
	<label><strong>Enter New Password:</strong></label><br />
	<input type="password" name="pass1" maxlength="15" required />
	<br /><br />
	<label><strong>Re-Enter New Password:</strong></label><br />
	<input type="password" name="pass2" maxlength="15" required/>
	<br /><br />
	<input type="hidden" name="email" value="<?php echo $email;?>"/>
	<input type="submit" value="Reset Password" />
</form>

<?php if (isset($error) || isset($success)){?>
	<div class="alert <?php $error ? ?>alert-danger <?php :?>alert-success " role="alert">
		<?php $error ? $error : $success ?>
	</div>
<?php } ?>