<?PHP

require_once(__DIR__."/config/database.php");

if ($camUser->loginStatus()) {
	if (isset($_POST)){
		if(isset($_POST['submit']) && isset($_POST['imageID']) && $_POST['submit'] == "submit") {
			$commenter = $_SESSION['logged_in_user'];
			$postID = $_POST['imageID'];
			$tempDate = new DateTime();
			$dateString = $tempDate->format('g:i a n/j/Y');
			if (isset($_POST['comment'])) {
				$comment = $_POST['comment'];
				if ($camUser->post_comment($commenter, $postID, $dateString, $comment)) {
					echo "<div class=\"flex-container comment-container\">
						<div class=\"flex-container commenter-info\">
							<div>{$commenter} said: </div>
							<div>{$dateString}</div>
						</div>
						<div class=\"comment-text\">{$comment}</div>
						</div>";
				} else {
					$_SESSION['error'] = "postCommentFailure";
					echo "failure";
				}
			} else if (isset($_POST['like'])) {
				if ($_POST['like'] == "yes") {
					$user = $_SESSION['logged_in_user'];
					echo $camUser->like_post($user, $postID, $dateString) . " likes";
				} else if ($_POST['like'] == "fill") {
					echo $camUser->like_post(null, $postID, null) . " likes";
				}
			}
		} else if (isset($_POST['comments']) && isset($_POST['imageID']) && $_POST['comments'] == "fill") {
			$imageID = filter_input(INPUT_POST, "imageID");
			try {
				$query = $DB_connect->prepare("SELECT * FROM `PostComments` WHERE imageID=:imageID ORDER BY `post_date` DESC;");
				$query->bindParam(":imageID", $imageID, PDO::PARAM_INT);
				$query->execute();
				while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
					$comment = $row['comment'];
					$commenter = $row['commenter'];
					$date = $row['post_date'];
					echo "<div class=\"flex-container comment-container\">
						<div class=\"flex-container commenter-info\">
							<div>{$commenter}</div>
							<div>{$date}</div>
						</div>
						<div class=\"comment-text\">{$comment}</div>
						</div>";
				}
			} catch (PDOException $err) {
				echo $err->getMsg();
			}
		}
	}
}

?>