<?PHP

require_once(__DIR__."/config/database.php");


if (isset($_POST['reset']) && $_POST['reset'] == "password" && ($camUser->loginStatus()) || isset($_POST['user'])) {
		try {
			$user = ($camUser->loginStatus()) ? $_SESSION['logged_in_user'] : $_POST['user'];
			$hash = md5(rand(0, 9999));
			$email_query = $DB_connect->prepare("SELECT * FROM `UserAccounts` WHERE username=:uname;");
			$email_query->bindParam(":uname", $user, PDO::PARAM_STR);
			$email_query->execute();
			$user_account = $email_query->fetch(PDO::FETCH_ASSOC);
			$user_email = $user_account['email'];
			$update_query = $DB_connect->prepare("UPDATE `UserAccounts` SET temphash=:hash WHERE username=:uname;");
			$update_query->bindParam(":hash", $hash, PDO::PARAM_STR);
			$update_query->bindParam(":uname", $user, PDO::PARAM_STR);
			$update_query->execute();

			$subject= 'Password Reset';
			$message = "\r\n
				$user, use the link below to activate your account.\r\n\n
				Camagru/reset_password.php?user=$user&hash=$hash\r\n
				";
			$headers = "";
			$headers .= "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-Type: text/html;charset=iso-8859-1" . "\r\n";
			$headers .= "From: noreply@localhost" . "\r\n";
			if (mail($user_email, $subject, $message, $headers)) {
				echo "Pass reset email sent";
			} else {
				$_SESSION['error'] = "Invalid Email";
				echo "Invalid Email";
				return false;
			}
		} catch (PDOException $err) {
			echo $err->getMessage();
		}
	}
}

if (isset($_GET['user']) && isset($_GET['hash'])) {
	try {
		$hash = $_GET['hash'];
		$user = $_GET['user'];
		$query = $DB_connect->prepare("SELECT * FROM `UserAccounts` WHERE username=:uname AND temphash=:hash");
		$query->bindParam(":uname", $user, PDO::PARAM_STR);
		$query->bindParam(":hash", $hash, PDO::PARAM_STR);
		$query->execute();
		$row = $query->fetch(PDO::FETCH_ASSOC);
		if ($query->rowCount() > 0) {
			if ($row['temphash'] != $hash) {
				$camUser->redirect("index.php");
			} else {
				$_SESSION['user'] = $_GET['user'];	
				$_SESSION['hash'] = $_GET['hash'];
			}
		}
	} catch (PDOException $err) {
		echo $err->getMessage();
	}
}

if (isset($_SESSION['user']) && isset($_SESSION['hash'])) {
	if (isset($_POST['submit']) && isset($_POST['newpass']) && isset($_POST['confirm'])) {
		$newpass = filter_input(INPUT_POST, 'newpass', FILTER_SANITIZE_EMAIL);
		$confirm = filter_input(INPUT_POST, 'confirm', FILTER_SANITIZE_EMAIL);
		$user = $_SESSION['user'];
		if ($camUser->password_complexity($newpass)) {
			if ($newpass == $confirm) {
				if ($camUser->resetPassword($user, $newpass))
					$camUser->redirect("login.php");
				else
					echo "Error reseting password";
			} else {
				$_SESSION['error'] = "nonMatch";
				$_SESSION['placeholder-text'] = "Passwords do not match";
			}
		} else {
			$_SESSION['error'] = "simplePass";
			$_SESSION['placeholder-text'] = "Password is not complex enough";
		}
	}
}

?>

<html>
<head>
	<title>Reset Password</title>
	<link rel="stylesheet" type="text/css" href="styles/layouts.css">
	<link rel="stylesheet" type="text/css" href="styles/navbar.css">
	<link rel="stylesheet" type="text/css" href="styles/reset_password.css">
</head>
<body>
	<div class="flex-container" id="reset-layout">
		<?PHP include("includes/header.php"); ?>
		<form class="flex-container" id="pass-reset-form" method="POST" action="reset_password.php">
			<input type="password" name="newpass" placeholder=
			<?PHP
			 if (isset($_SESSION['error'])) {
			 	if ($_SESSION['error'] == "nonMatch" || $_SESSION['error'] == "simplePass") {
			 		echo "\"" . $_SESSION['placeholder-text'] . "\"";
			 	}
			 } else {echo "\"New Password\"";}
			?>>
			<input type="password" name="confirm" placeholder=
			<?PHP
			 if (isset($_SESSION['error'])) {
			 	if ($_SESSION['error'] == "nonMatch" || $_SESSION['error'] == "simplePass")
			 		echo "\"" . $_SESSION['placeholder-text'] . "\"";
			 } else {echo "\"Confirm Password\"";}
			?>
			/>
			<input type="submit" name="submit" value="submit"/>
		</form>
	</div>
</body>
</html>