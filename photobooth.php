<?PHP
 
require_once(__DIR__."/config/database.php");

if ($camUser->loginStatus() == false) {
	$camUser->redirect("login.php");
}

?>

<!DOCTYPE html>
	<head>
		<meta charset="UTF-8">
		<title>Photobooth</title>
		<link rel="stylesheet" type="text/css" href="styles/photobooth.css">
		<link rel="stylesheet" type="text/css" href="styles/navbar.css">
		<link rel="stylesheet" type="text/css" href="styles/overlay_list.css">
		<link rel="stylesheet" type="text/css" href="styles/session_gallery.css">
		<link rel="stylesheet" type="text/css" href="styles/layouts.css">
		<link href='//fonts.googleapis.com/css?family=Arizonia' rel='stylesheet'>
		<script src="photobooth.js"></script>
	</head>
	<body>
		<div class="modal-box" id="savepic-popup">
			<div class="flex-container" id="picbox">
				<img id="photo" alt="This is the photo that was taken">
				<canvas id="photo-canvas">
				</canvas>
				<button id="save-button">Save Photo</button>
			</div>
		</div>
		<div class="modal-box" id="upload-popup">
			<form id="upload-form" class="flex-container" enctype="multipart/form-data">
				<input type="hidden" name="MAX_FILE_SIZE" value="100000"/>
				<input id="userphoto" type="file" name="userphoto"/>
				<input id="submit-upload" type="submit" name="submit"/>
			</form>
		</div>
		<div class="flex-container" id="mainpage-layout">
			<?PHP include("includes/header.php");?>
			<div class="flex-container" id="middle-section">
				<?PHP include("includes/overlay_list.php");?>
				<div class="flex-container" id="camera-zone">
					<div id="camera-view">
						<video id="video" src="">Video stream not available.
						</video>
					</div>
					<div class="flex-container" id="camera-options">
						<button id="take-photo" name="capture_button">Capture</button>
						<button id="upload-button" name="upload_button">Upload</button>
					</div>
				</div>
				<?PHP include("includes/session_gallery.php");?>
			</div>
		</div>
	</body>
</html> 