<?PHP

require_once(__DIR__."/config/database.php");

if (isset($_POST['register']) && $_POST['register'] == "submit") {
	$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_EMAIL);
	if ($camUser->password_complexity($password)) {
		$confirmpass = filter_input(INPUT_POST, 'confirmpass', FILTER_SANITIZE_EMAIL);
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
		$fname = filter_input(INPUT_POST, 'first-name');
		$lname = filter_input(INPUT_POST, 'last-name');
		if ($password == $confirmpass) {
			if ($camUser->register($username, $password, $email, $fname, $lname)) {
				$camUser->redirect("index.php");
			}
		} else {
			$_SESSION['error'] = "nonMatch";
			$_SESSION['placeholder-text'] = "Passwords do not match";
		}
	} else {
		$_SESSION['error'] = "simplePass";
		$_SESSION['placeholder-text'] = "Password is not complex enough";
	}
}

?>

<html>
	<head>
	<title>Sign Up</title>
		<link rel="stylesheet" type="text/css" href="styles/layouts.css">
		<link rel="stylesheet" type="text/css" href="styles/navbar.css">
		<link rel="stylesheet" type="text/css" href="styles/signup.css">
		<link href='//fonts.googleapis.com/css?family=Arizonia' rel='stylesheet'>
	</head>
	<body>
		<div class="flex-container" id="signup-layout">
			<?PHP include("includes/header.php");?>
			<form action="sign_up.php" method="POST">
			<div class="flex-container"  id="user-registration">
				<div class="usrReg-field" id="email">
					<input type="text" name="email" placeholder=
					<?PHP
					if (isset($_SESSION['error']) && $_SESSION['error'] == "Email already exists") {
							echo "\"That Email is taken\"";
							unset($_SESSION['error']);
					} else { echo "\"Email\"";}
					?>  value="" required>
				</div>
				<div class="usrReg-field" id="username">
					<input type="text" name="username" 
					placeholder=
					<?PHP
					if (isset($_SESSION['error']) && $_SESSION['error'] == "Username already exists") {
							echo "\"Username already exists\"";
							unset($_SESSION['error']);
					} else { echo "\"Username\"";}
					?> value="" required>
				</div>
				<div class="usrReg-field" id="password">
					<input type="password" name="password" placeholder=
					<?PHP
					if (isset($_SESSION['error'])) {
			 			if ($_SESSION['error'] == "nonMatch" || $_SESSION['error'] == "simplePass") {
			 				echo "\"" . $_SESSION['placeholder-text'] . "\"";
			 				unset($_SESSION['error']);
			 				unset($_SESSION['placeholder-text']);
			 			}
					} else {echo "\"Password\"";}
					?> value="" required>
				</div>
				<div class="usrReg-field" id="confirmpass">
					<input type="password" name="confirmpass" placeholder=
					<?PHP
					if (isset($_SESSION['error'])) {
			 			if ($_SESSION['error'] == "nonMatch" || $_SESSION['error'] == "simplePass") {
			 				echo "\"" . $_SESSION['placeholder-text'] . "\"";
			 				unset($_SESSION['error']);
			 				unset($_SESSION['placeholder-text']);
			 			}
					} else {echo "\"Confirm Password\"";}
					?> value="" required>
				</div>
				<div class="usrReg-field" id="first-name">	
					<input type="text" name="first-name" placeholder="First Name" value="" required>
				</div>
				<div class="usrReg-field" id="last-name">	
					<input type="text" name="last-name" placeholder="Last Name" value="" required>
				</div>
				<div class="usrReg-field">
					<button type="submit" name="register" value="submit">Submit</button>
				</div>
			</div>
			</form>
		</div>
		</div>
	</body>
</html>