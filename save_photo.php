<?PHP

require_once(__DIR__."/config/database.php");

if (isset($_POST)) {
	$filepath = 'images/' . uniqid() . '.png';

	if (isset($_POST['img'])) {
		$imgurl = $_POST['img'];
		$imgurl = str_replace('data:image/png;base64,', '', $imgurl);
		$imgurl = str_replace(' ', '+', $imgurl);
		$imgdata = base64_decode($imgurl);
		$result = file_put_contents($filepath, $imgdata);
		$img = imagecreatefrompng($filepath);
		unset($_POST['img']);
	} else if (isset($_POST['upload-path'])) {
		$up_path = $_POST['upload-path'];
		$img = imagecreatefrompng($up_path);
		unlink($up_path);
		unset($_POST['upload-path']);
	}
	
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
	$camUser->redirect("photobooth.php");
}

?>