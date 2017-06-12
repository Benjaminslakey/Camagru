<?PHP

require_once(__DIR__."/config/database.php");

if ($camUser->loginStatus()) {
	if (isset($_POST) && isset($_POST['delete']) && $_POST['delete'] == "account") {
		try {
			$postIDs = array();
			$user = $_SESSION['logged_in_user'];
			$query = $DB_connect->prepare("SELECT * FROM `UserPosts` WHERE username=:user;");
			$query->bindParam(":user", $user, PDO::PARAM_STR);
			$query->execute();

			while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
				array_push($postIDs, $row['imageID']);
			}
			if ($postIDs != null) {
				foreach ($postIDs as $id) {
					$commentQuery = $DB_connect->prepare("DELETE FROM `PostComments` WHERE imageID=:id;");
					$commentQuery->bindParam(":id", $id, PDO::PARAM_INT);
					$commentQuery->execute();
					$likeQuery = $DB_connect->prepare("DELETE FROM `PostLikes` WHERE imageID=:id;");
					$likeQuery->bindParam(":id", $id, PDO::PARAM_INT);
					$likeQuery->execute();
				}
			}
				$postQuery = $DB_connect->prepare("DELETE FROM `UserPosts` WHERE username=:user;");
				$postQuery->bindParam(":user", $user, PDO::PARAM_STR);
				$postQuery->execute();
				$accountQuery = $DB_connect->prepare("DELETE FROM `UserAccounts` WHERE username=:user LIMIT 1;");
				$accountQuery->bindParam(":user", $user, PDO::PARAM_STR);
				$accountQuery->execute();
				echo "success";
		} catch (PDOException $err) {
			echo $err->getMessage();
			echo "failure";
		}
	}
}

?>