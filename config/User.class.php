<?PHP

Class User {

	private $DB_connection;
	private $errMsg;

	function __construct($DB_conn) {
		$this->DB_connection = $DB_conn;
	}

	public function register($user, $pass, $email, $fname, $lname) {
		try {

			$query1 = $this->DB_connection->prepare("SELECT * FROM `UserAccounts` WHERE username=:usr");
			$query1->bindParam(":usr", $user, PDO::PARAM_STR);
			$query1->execute();
			if ($query1->rowCount() > 0) {
				$_SESSION['error'] = "Username already exists";
				return ;
			}
			$query2 = $this->DB_connection->prepare("SELECT * FROM `UserAccounts` WHERE email=:mail");
			$query2->bindParam(":mail", $user, PDO::PARAM_STR);
			$query2->execute();
			if ($query2->rowCount() > 0) {
				$_SESSION['error'] = "Email already exists";
				return ;
			}
			$hashlink = md5( rand(0, 9999));
			$subject= 'Account Activation';
			$message = "

			Hey $fname Thanks for signing up with Camvas. \n
			Your Account info\n
			------------------\n
			Username: $user\n
			Password: $pass\n

			Click the link below to activate your account.
			http://localhost/Camagru/activation.php?user=$user&hash=$hashlink
			";
			$headers = "";
			$headers .= "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-Type: text/html;charset=iso-8859-1" . "\r\n";
			$headers .= "From: noreply@localhost" . "\r\n";
			if (mail($email, $subject, $message, $headers)) {
				$_SESSION['register'] = "success";
			} else {
				$_SESSION['error'] = "Invalid Email";
				return false;
			}
			$hpass = hash('whirlpool', $pass);
			$query = $this->DB_connection->prepare("INSERT INTO `UserAccounts`(username, password, email, first_name, last_name, temphash, activated) VALUES(:user, :hpass, :email, :fname, :lname, :tmphash, :active);");
			$query->bindParam(":user", $user, PDO::PARAM_STR);
			$query->bindParam(":hpass", $hpass, PDO::PARAM_STR);
			$query->bindParam(":email", $email, PDO::PARAM_STR);
			$query->bindParam(":fname", $fname, PDO::PARAM_STR);
			$query->bindParam(":lname", $lname, PDO::PARAM_STR);
			$query->bindParam(":tmphash", $hashlink, PDO::PARAM_STR);
			$query->bindValue(":active", "no");
			$query->execute();
			return true;
		} catch (PDOException $err) {
			echo $err->getMessage();
			return false;
		}
	}

	public function verify($user, $hash) {
		try {
			$query = $this->DB_connection->prepare("SELECT * FROM `UserAccounts` WHERE username=:uname;");
			$query->bindParam(":uname", $user, PDO::PARAM_STR);
			$query->execute();
			$row = $query->fetch(PDO::FETCH_ASSOC);
			if ($query->rowCount() > 0) {
				if ($row['temphash'] != $hash) {
					$_SESSION['error'] = "The activation code was incorrect";
					return false;
				}
			} else {
				$_SESSION['error'] = "Account does not exist";
				return false;
			}
			$query2 = $this->DB_connection->prepare("UPDATE `UserAccounts`
				SET temphash='null', activated='yes' WHERE username=:uname");
			$query2->bindParam(":uname", $user, PDO::PARAM_STR);
			$query2->execute();
			return true;
		} catch (PDOException $err) {
			echo $err->getMessage();
		}
	}

	public function password_complexity($userPassword) {
		$i = -1;
		$upperCase = 0;
		$lowerCase = 0;
		$numbers = 0;

		while (++$i < strlen($userPassword)) {
			$c = $userPassword[$i];
			if ($lowerCase < 2 && ctype_lower($c)) {$lowerCase++;}
			else if ($upperCase < 2 && ctype_upper($c)) {$upperCase++;}
			else if ($numbers < 2 && ctype_digit($c)) {$numbers++;}
		}

		if ($upperCase == 2 && $lowerCase == 2 && $numbers == 2) {
			return true;
		} else {
			return false;
		}
	}

	public function resetPassword($uname, $pass) {
		$hpass = hash('whirlpool', $pass);
		try {
			$query = $this->DB_connection->prepare("UPDATE `UserAccounts` SET password=:hpass, temphash='null' WHERE username=:uname;");
			$query->bindParam(":hpass", $hpass, PDO::PARAM_STR);
			$query->bindParam(":uname", $uname, PDO::PARAM_STR);
			$query->execute();
			return true;
		} catch (PDOException $err) {
			echo $err->getMessage();
			return false;
		}
	}

	public function login($uname, $pass) {
		try {
			$query = $this->DB_connection->prepare("SELECT * FROM `UserAccounts` WHERE username=:uname;");
			$query->bindParam(":uname", $uname, PDO::PARAM_STR);
			$query->execute();
			$row = $query->fetch(PDO::FETCH_ASSOC);
			if ($query->rowCount() > 0) {
				if (hash('whirlpool', $pass) == $row['password']) {
					if ($row['activated'] == "yes") {
						$_SESSION['logged_in_user'] = $uname;
						return true;
					} else {
						$_SESSION['error'] = "Account not activated";
						return false;
					}
				} else {
					$_SESSION['error'] = "Invalid Password";
					return false;
				}
			} else {
				$_SESSION['error'] = "Invalid Username";
				return false;
			}
		} catch (PDOException $err) {
			echo $err->getMessage();
		}
	}

	public function logout() {
		unset($_SESSION['logged_in_user']);
		session_destroy();
		return true;
	}

	public function loginStatus() {
		if (isset($_SESSION['logged_in_user'])) {
			return true;
		} else {
			return false;
		}
	}

	public function post_content($username, $datestamp, $img, $album) {
		try {
			$query = $this->DB_connection->prepare("INSERT INTO `UserPosts`(
			username, post_date, imagepath, album) VALUES(:user, :datestamp, :img, :album);");
			$query->bindParam(":user", $username, PDO::PARAM_STR);
			$query->bindParam(":datestamp", $datestamp, PDO::PARAM_STR);
			$query->bindParam(":img", $img, PDO::PARAM_STR);
			$query->bindParam(":album", $album, PDO::PARAM_STR);
			$query->execute();
		} catch (PDOException $err) {
			echo $err->getMessage();
		}
	}

	public function post_comment($commenter, $postid, $datestamp, $comment) {
		try {
			$query = $this->DB_connection->prepare("INSERT INTO `PostComments`(
				commenter, imageID, post_date, comment) VALUES(:commenter, :postid, :stamp, :comment);");
			$query->bindParam(":commenter", $commenter, PDO::PARAM_STR);
			$query->bindParam(":postid", $postid, PDO::PARAM_INT);
			$query->bindParam(":stamp", $datestamp, PDO::PARAM_STR);
			$query->bindParam(":comment", $comment, PDO::PARAM_STR);
			$query->execute();

			$userSearch = $this->DB_connection->prepare("SELECT * FROM `UserPosts` WHERE imageID=:id LIMIT 1;");
			$userSearch->bindParam(":id", $postid, PDO::PARAM_INT);
			$userSearch->execute();
			$row = $userSearch->fetch(PDO::FETCH_ASSOC);
			$user = $row['username'];

			$emailSearch = $this->DB_connection->prepare("SELECT * FROM `UserAccounts` WHERE username=:user LIMIT 1;");
			$emailSearch->bindParam(":user", $user, PDO::PARAM_STR);
			$emailSearch->execute();
			$row = $emailSearch->fetch(PDO::FETCH_ASSOC);
			$email = $row['email'];

			$subject= 'New Comment';
			$message = "One of your photos on Camagru has recieved a new comment, log in to check it out!";
			$headers = "";
			$headers .= "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-Type: text/html;charset=iso-8859-1" . "\r\n";
			$headers .= "From: noreply@localhost" . "\r\n";
			if (mail($email, $subject, $message, $headers)) {
				return true;
			} else {
				$_SESSION['error'] = "Invalid Email";
				return false;
			}
			return true;
		} catch (PDOException $err) {
			echo $err->getMessage();
			return false;
		}
	}

	public function like_post($liker, $imageID, $date) {
		try {
			$status = 1;
			if (isset($liker) && $liker != null && isset($date)) {
				$likeStatus = $this->DB_connection->prepare("SELECT COUNT(*) FROM `PostLikes` WHERE imageID=:id AND liker=:liker;");
				$likeStatus->bindParam(":id", $imageID, PDO::PARAM_INT);
				$likeStatus->bindParam(":liker", $liker, PDO::PARAM_STR);
				$likeStatus->execute();
				if ($likeStatus->fetchColumn() >= 1) {
					$alreadyLike = true;
				} else {
					$alreadyLike = false;
				}
				if ($alreadyLike == false) {
					$query = $this->DB_connection->prepare("INSERT INTO `PostLikes`(liker, imageID, post_date, status) VALUES(:liker, :imageID, :post_date, :status)");
					$query->bindParam(":liker", $liker, PDO::PARAM_STR);
					$query->bindParam(":imageID", $imageID, PDO::PARAM_INT);
					$query->bindParam(":post_date", $date, PDO::PARAM_STR);
					$query->bindParam(":status", $status, PDO::PARAM_INT);
					$query->execute();
				}
			}
			$likeCount = $this->DB_connection->prepare("SELECT COUNT(*) FROM `PostLikes` WHERE imageID=:id;");
			$likeCount->bindParam(":id", $imageID, PDO::PARAM_INT);
			$likeCount->execute();
			$count = $likeCount->fetchColumn();
			return ($count);
		} catch (PDOException $err) {
			echo $err->getMessage();
			return (0);
		}
	}

	public function redirect($location) { 
		header("Location:" . $location);
	}
}

?>