<?PHP

require_once(__DIR__."/config/database.php");

?>

<html>
	<head>
	<title>Camvas</title>
		<link rel="stylesheet" type="text/css" href="styles/index.css">
		<link rel="stylesheet" type="text/css" href="styles/navbar.css">
		<link rel="stylesheet" type="text/css" href="styles/layouts.css">
		<link href='//fonts.googleapis.com/css?family=Arizonia' rel="stylesheet">
		<script type="text/javascript" src="gallery.js"></script>
	</head>
	<body>
	<?PHP if (isset($_SESSION['error'])) {echo $_SESSION['error']; unset($_SESSION['error']);}?>
		<div class="flex-container" id="index-layout">
			<?PHP include("includes/header.php");?>
			<div class="flex-container" id="index-middle">
				<div class="flex-container slideshow">
				<div class="flex-container parent">
					<a class="arrow" onclick="plusSlides(-1)"><div class="prev"></div></a>
				</div>
					<div class="flex-container" id="slideshow-middle">
					<div class="index-slide"><img class="slide" src=""></div>
					<?PHP
					try {
						$query = $DB_connect->prepare("SELECT * FROM `UserPosts` ORDER BY `imageID` DESC");
						$query->execute();
						while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
							echo "<div class=\"index-slide\"";
							echo "id=\"" . $row['imageID'] . "\">";
							if ($camUser->loginStatus()) {
								echo "<a class=\"post-links flex-container\" value=\"{$row['imageID']}\"><img class=\"slide\" src=\"{$row['imagepath']}\"></a></div>";
							} else {
								echo "<img class=\"slide\" src=\"{$row['imagepath']}\"></div>";
							}
						}
					} catch (PDOException $err) {
						echo $err->getMessage();
					}
					?>
					</div>
					<div class="flex-container parent">
						<a class="arrow" onclick="plusSlides(1)"><div class="next"></div></a>
					</div>
				</div>
			</div>
			<div class="modal-box" id="social">
				<button name="like" id="like-button" value="like">&hearts;</button>
				<div class="flex-container" id="comment-form">
					<input type="text" name="comment" id="comment-input"/>
					<input type="submit" name="submit" id="comment-submit" value="submit"/>
				</div>
			</div>
			<div class="flex-container" id="post-likes"><div id="like-counter"></div></div>
			<div class="flex-container" id="post-comments">
			</div>
		</div>
		<script type="text/javascript" src="social.js"></script>
	</body>
</html>