<?php

require('config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
/* Exception class. */
require 'modules\PHPMailer\Exception.php';
/* The main PHPMailer class. */
require 'modules\PHPMailer\PHPMailer.php';
/* SMTP class, needed if you want to use SMTP. */
require 'modules\PHPMailer\SMTP.php';


if(!empty($_POST["email"])){
	$link = mysqli_connect(HOSTNAME,USERNAME,PASSWORD,DATABASE);
	$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
	if (!$email) {
	    $error ="Invalid email address please type a valid email address!";
	    }else{
			//Nettoyer les données entrantes
			$email = mysqli_real_escape_string($email);
			
		    $query = "SELECT * FROM `users` WHERE email='$email' LIMIT 1";
		    $result = mysqli_query($link,$query);
		    $row = mysqli_num_rows($result);
		    if ($row==0){
				$error = "No user is registered with this email address!";
		    }
	    }
	    if($error!=""){
			$expFormat = mktime(date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y"));
			$expDate = date("Y-m-d H:i:s",$expFormat);
			$key = md5(2418*2+$email);
			$addKey = md5(uniqid(rand(),true));
			$key = $key . $addKey;
			// Insert Temp Table
			$query = "INSERT INTO `password_reset_temp` (`email`, `key`, `expDate`)
					VALUES ('$email', '$key', '$expDate')";
			mysqli_query($link, $query);
			
			$resetPasswordLink = SITE_URL.'/pwd_reset.php?key='.$key.'&email='.$email.'&action=reset';
			$message='<p>Dear user,</p>';
			$message.='<p>Please click on the following link to reset your password.</p>';
			$message.='<p>-------------------------------------------------------------</p>';
			$message.='<p><a href="'.$resetPasswordLink.'" target="_blank">
			'.$resetPasswordLink.'</a></p>'; 
			$message.='<p>-------------------------------------------------------------</p>';
			$message.='<p>Please be sure to copy the entire link into your browser.
			The link will expire after 1 day for security reason.</p>';
			
			$subject = "Password Recovery";
			
			//Avec Telenet smtp=> need	ids
			/*			
			$mail = new PHPMailer(TRUE);
			$mail->addAddress($email,'Simon oldenhove');
			$mail->setFrom("simonoldenhove@gmail.com",'Simon Oldenhove');
			$mail->Subject = $subject;
			$mail->Body = $message;
			$mail->isSMTP();
			$mail->Host = 'smtp.telenet.com';
			$mail->SMTPAuth = TRUE;
			$mail->SMTPSecure = 'tls';
		    $mail->Username = 'telenetMail';
		    $mail->Password = 'telenetPassword';
			$mail->Port = 587;
			*/
			
			//Avec Google smtp
			$mail = new PHPMailer(); 
			$mail->IsSMTP(); // enable SMTP
			$mail->SMTPDebug = 1; // 
			$mail->SMTPAuth = true; // authentication enabled
			$mail->SMTPSecure = 'tls'; // or ssl secure transfer enabled REQUIRED for Gmail
			$mail->Host = "smtp.gmail.com";
			$mail->Port = 587; // or 465
			$mail->IsHTML(true);
 			$mail->Username = "";
			$mail->Password = ""; 
			$mail->SetFrom("example@gmail.com");
			$mail->Subject = "Test";
			$mail->Body = $message;
			$mail->AddAddress($email);
			if($mail->send()){
				$output="An email has been sent to you with instructions on how to reset your password.";
			}else{
				$output="Error while sending the email.";
			}
		}
}
?>
<form method="post" action="" id="reset">
	<label>Enter Your Email Address:</label>
	<input type="email" name="email" placeholder="username@email.com" />
	<br />
	<input type="submit" value="Reset Password"/>
</form>
<?php 
if(isset($output)){
	echo $output;
}
?>
