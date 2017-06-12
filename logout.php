<?PHP

require_once(__DIR__."/config/database.php");

if ($camUser->loginStatus()) {
	$camUser->logout();
	$camUser->redirect("index.php");
} else {
	$camUser->redirect("login.php");
}

?>