<?PHP

require_once(__DIR__."/config/database.php");

if (isset($_POST['login']) && $_POST['login'] == "submit") {
	$uname = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);
	$pass = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_EMAIL);
	if ($camUser->login($uname, $pass)) {
		$camUser->redirect("photobooth.php");
	}
} else if (isset($_POST['loginStatus']) && $_POST['loginStatus'] == "check") {
	if ($camUser->loginStatus())
		echo "loggedIn";
}

?>
<html>
	<head>
	<title>Login</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="styles/login.css">
		<link rel="stylesheet" type="text/css" href="styles/navbar.css">
		<link rel="stylesheet" type="text/css" href="styles/layouts.css">
		<link href='//fonts.googleapis.com/css?family=Arizonia' rel='stylesheet'>
	</head>
	<body>
	<div class="flex-container" id="login-layout">
		<?PHP include("includes/header.php");?>
		<div class="flex-container" id="login-form-container">
			<form id="login-form" action="login.php" method="POST">
			<div class="flex-container" id="login-container">
				<div id="username">
					<input type="text" name="username"
					<?PHP
						if (isset($_SESSION['error']) && $_SESSION['error'] == "Invalid Username") {
							echo "placeholder=\"Invalid Username\"";
							unset($_SESSION['error']);
						} else if (isset($_SESSION['error']) && $_SESSION['error'] == "Account not activated") {
							echo "placeholder=\"Activate your account\"";
						} else {
							echo "placeholder=\"Username\"";
						}
					?>
					value="" required>
				</div>
				<div id="password">
					<input type="password" name="password"
					<?PHP
						if (isset($_SESSION['error']) && $_SESSION['error'] == "Invalid Password") {
							echo "placeholder=\"Invalid Password\"";
							unset($_SESSION['error']);
						} else if (isset($_SESSION['error']) && $_SESSION['error'] == "Account not activated") {
							echo "placeholder=\"Activate your account\"";
							unset($_SESSION['error']);
						} else {
							echo "placeholder=\"Password\"";
						}
					?>
					value="" required>
				</div>
				<div id="submit-button-box">
					<button id="submit-button" name="login" value="submit">Login</button>
				</div>
			</div>
			</form>
		</div>
		<div id="spacing-footer"></div>
	</div>
	</body>
</html>