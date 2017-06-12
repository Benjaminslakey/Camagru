<?PHP

require_once(__DIR__."/config/database.php");

if (isset($_POST)) {
	if (isset($_FILES) && isset($_FILES['userphoto']) && isset($_POST['effect-type']) && isset($_POST['effect'])) {
		$updir = __DIR__."/uploads/";
		$tmp_name = $_FILES['userphoto']['tmp_name'];
		$fname = basename($_FILES['userphoto']['name']);
		$allowed_fnames = "/^[-_0-9a-z]+/i";
		if (preg_match($allowed_fnames, $fname) == true) {
			$pinfo = pathinfo($_FILES['userphoto']['name']);
			$allowed_ext = array("jpeg", "jpg", "png");
			if (in_array($pinfo['extension'], $allowed_ext) == true) {
				$up_name = uniqid() . "." . $pinfo['extension'];
				if (move_uploaded_file($tmp_name, $updir . $up_name))	
					$upload_path = "uploads/".$up_name;
				else
					$_SESSION['error'] = "Possible upload attack";
			} else
				$_SESSION['error'] = "not an accepted file extension/type";
		} else {
			$_SESSION['error'] = "not an allowed filename";
		}

		switch ($_FILES['userphoto']['error']) {
			case 0:
				echo "Your photo was uploaded";
				break;
			case 1:
			case 2:
				echo "The photo you're trying to upload is too large";
				break;
			case 3:
				echo "The upload was interrupted";
				break;
			case 4:
				echo "No file uploaded";
				break;
			default:
				break;
			}
		unset($_FILES);
	}
	if (isset($upload_path)) {
		$filepath = 'images/' . uniqid() . '.png';
		$img = imagecreatefrompng($upload_path);
		unlink($upload_path);
		if ($_POST['effect-type'] == "filter") {
				if ($_POST['effect'] == "black-and-white") {
						$filter = IMG_FILTER_GRAYSCALE;
				} else if ($_POST['effect'] == "negative") {
						$filter = IMG_FILTER_NEGATE;
				} 
				imagefilter($img, $filter);
		} else if ($_POST['effect-type'] == "overlay" && $_POST['effect'] != "no-effect") {
			$path = "design_elements/" . $_POST['effect'] . ".png";
			$tmp = imagecreatefrompng($path);
			$xoffset = 0;
			$yoffset = 0;
			switch ($_POST['effect']) {
				case "mario":
					$scale = 0.7;
					break;
				case "tiger":
					$scale = 0.5;
					$yoffset = 80;
					break;
				case "obama":
					$scale = 0.45;
					$yoffset = 55;
					break;
				case "balloons":
					$scale = 0.3;
					break;
				case "leaves":
					$yoffset = 0;
					$scale = 0.13;
					break;
				case "picture-frame":
					$scale = 0.308;
					break;
				case "skull":
					$scale = 0.9;
					$xoffset = 100;
					$yoffset = 10;
					break;
				default:
					$scale = 0.4;
					break;
			}
				$nw = imagesx($tmp) * $scale;
				$nh = imagesy($tmp) * $scale;
				$tmp = imagescale($tmp, $nw, $nh);
				imagecopyresampled($img, $tmp, $xoffset, $yoffset, 0, 0, $nw, $nh, $nw, $nh);
		}
		imagepng($img, $filepath);
		imagedestroy($img);
		$date = date("F j, Y, g:i a");
		$camUser->post_content($_SESSION['logged_in_user'], $date, $filepath, "current-session");
		echo "<div class=\"session-gallery-item\"><img src=\"$filepath\"></div>";
	} else {
		echo "photo not uploaded correctly";
} else {
	}
	$camUser->redirect("photobooth.php");
}

?>