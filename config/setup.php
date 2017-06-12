<?PHP

	$query = $DB_connect->exec("
	CREATE TABLE IF NOT EXISTS `UserAccounts` (
		`userID` INT(12) AUTO_INCREMENT,
		`username` VARCHAR(30) NOT NULL,
		`password` VARCHAR(128) NOT NULL,
		`email` VARCHAR(320) NOT NULL,
		`first_name` NVARCHAR(50),
		`last_name` NVARCHAR(50),
		`temphash` VARCHAR(32),
		`activated` VARCHAR(3) NOT NULL,
		PRIMARY KEY (`userID`)
		);");

	$query1 = $DB_connect->exec("
		CREATE TABLE IF NOT EXISTS `UserPosts` (
		`imageID` INT(12) AUTO_INCREMENT,
		`username` VARCHAR(30) NOT NULL,
		`post_date` VARCHAR(30) NOT NULL,
		`imagepath` VARCHAR(100) NOT NULL,
		`album` VARCHAR(100),
		`caption` VARCHAR(100),
		PRIMARY KEY (`imageID`)
		);");

	$query2 = $DB_connect->exec("
		CREATE TABLE IF NOT EXISTS `PostComments` (
		`commenter` VARCHAR(30) NOT NULL,
		`imageID` INT(12) NOT NULL,
		`post_date` VARCHAR(30) NOT NULL,
		`comment` VARCHAR(500) NOT NULL
		);");

	$query3 = $DB_connect->exec("
		CREATE TABLE IF NOT EXISTS `PostLikes` (
		`liker` VARCHAR(30) NOT NULL,
		`imageID` INT(12) NOT NULL,
		`post_date` VARCHAR(30) NOT NULL,
		`status` BOOLEAN NOT NULL
		);");
?>