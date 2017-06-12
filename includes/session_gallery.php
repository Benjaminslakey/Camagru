<div class="flex-container" id="session-gallery">
	<?PHP
	try {
		$user = $_SESSION['logged_in_user'];
		$query = $DB_connect->prepare("SELECT * FROM `UserPosts` WHERE username=:uname AND album=:album ORDER BY `imageID` DESC;");
		$query->bindParam(":uname", $user, PDO::PARAM_STR);
		$query->bindValue(":album", "current-session");
		$query->execute();
		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			echo "<div class=\"session-gallery-item\">\n";
			echo "<img src=\"";
			echo $row['imagepath'];
			echo "\">\n";
			echo "</div>\n";
		}
	} catch (PDOException $err) {
		echo $err;
	}
	?>
</div>