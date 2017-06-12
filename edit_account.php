<?PHP

require_once(__DIR__."/config/database.php");

if ($camUser->loginStatus() == false) {
	$camUser->redirect("login.php");
}

if (isset($_POST) && isset($_POST['delete'])) {
	try {
		$user = $_SESSION['logged_in_user'];
		$imageID = $_POST['delete'];
		$query = $DB_connect->prepare("SELECT * FROM `UserPosts` WHERE username=:uname AND imageID=:id;");
		$query->bindParam(":uname", $user, PDO::PARAM_STR);
		$query->bindParam(":id", $imageID, PDO::PARAM_STR);
		$query->execute();
		$row = $query->fetch(PDO::FETCH_ASSOC);
		$filepath =$row['imagepath'];
		unlink($filepath);
		unset($_POST);
		$query1 = $DB_connect->prepare("DELETE FROM `UserPosts` WHERE username=:uname AND imageID=:id LIMIT 1;");
		$query1->bindParam(":uname", $user, PDO::PARAM_STR);
		$query1->bindParam(":id", $imageID, PDO::PARAM_INT);
		$query1->execute();
		$query2 = $DB_connect->prepare("DELETE FROM `PostComments` WHERE imageID=:id;");
		$query2->bindParam(":id", $imageID, PDO::PARAM_INT);
		$query2->execute();
		$query3 = $DB_connect->prepare("DELETE FROM `PostLikes` WHERE imageID=:id;");
		$query3->bindParam(":id", $imageID, PDO::PARAM_INT);
		$query3->execute();
		echo "<script type=\"text/javascript\">alert(\"deleted\");</script>";
	} catch (PDOException $err) {
		echo $err->getMessage();
	}
}

?>

<html>
<head>
	<title>Edit Profile</title>
	<link rel="stylesheet" type="text/css" href="styles/layouts.css">
	<link rel="stylesheet" type="text/css" href="styles/navbar.css">
	<link rel="stylesheet" type="text/css" href="styles/edit_account.css">
</head>
<body>
	<div class="flex-container" id="edit-profile-layout">
		<?PHP include("includes/header.php"); ?>
		<div class="flex-container" id="middle">
			<div class="flex-container" id="user-pictures">
				<div class="edit-picture" style="display:none;">
					<button class="delete"></button>
				</div>
			<?PHP
			try {
				$user = $_SESSION['logged_in_user'];
				$query = $DB_connect->prepare("SELECT * FROM `UserPosts` WHERE username=:uname ORDER BY `imageID` DESC");
				$query->bindParam(":uname", $user, PDO::PARAM_STR);
				$query->execute();
				while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
					echo "<div class=\"edit-picture\">\n";
					echo "<img src=\"";
					echo $row['imagepath'];
					echo "\">\n";
					echo "<button class=\"delete\" id=\"";
					echo $row['imageID']; 
					echo "\">Delete</button>\n";
					echo "</div>\n";
				}
			} catch (PDOException $err) {
				echo $err->msg;
			}

			?>
			</div>
			<div class="flex-container" id="account-options">
				<button name="reset" id="reset-pass">Reset Password</button>
				<button name="delete_account" id="delete-account">Delete Account</button>
			</div>
		</div>
	</div>
	<script src="profile_edits.js"></script>
</body>
</html>