<div class="flex-container navbar">
	<div id="home-link">
	<?PHP
	if ($camUser->loginStatus()) {
		echo "<a class=\"header-link\" href=\"photobooth.php\">Home</a>";
	}
	?>
	</div>
	<div class="spacing"></div>
	<div class="spacing"></div>
	<div class="spacing"></div>
	<div id="mainpage-link">
		<a class="header-link" href="index.php">Camagru</a>
	</div>
	<div class="spacing"></div>
	<div class="spacing"></div>	
	<div class="flex-container" id="user-options">
	<?PHP 
		if (isset($_SESSION['logged_in_user'])) {
			echo "<a class=\"header-link\" href=\"edit_account.php\">Edit Account</a>\n";
		} else {
			echo "<a class=\"header-link\" href=\"sign_up.php\">Sign up!</a>\n";
		}
	?>
	</div>
	<div class="flex-container" id="login-out">
	 	<?PHP
	 		if (isset($_SESSION['logged_in_user'])) {
	 			echo "<a class=\"header-link\" href=\"logout.php\"><button id=\"logout-button\">Logout</button></a>\n"; 
	 		} else {
	 			echo "<a class=\"header-link\" href=\"login.php\"><button id=\"login-button\">Login</button></a>\n"; 
	 		}
	 	?>
	 </div>
</div>