(function () {
	var delete_buttons = document.getElementsByClassName('delete');
	var reset_pass = document.getElementById('reset-pass');
	var account_button = document.getElementById('delete-account');

	function delete_post(event) {
		var httpRequest = new XMLHttpRequest();
		var imageID = event.currentTarget.getAttribute('id');
		var data = new FormData();
		httpRequest.open("POST", "edit_account.php", true);
		data.append("delete", imageID);
		httpRequest.onreadystatechange = function () {
			if (httpRequest.readyState === XMLHttpRequest.DONE) {
				if (httpRequest.status !== 200) {
					console.log("Error sending delete request");
				}
			}
		}
		httpRequest.onload = function() {
			var post = document.getElementById(imageID).parentNode;
			while (post.firstChild) {
				post.removeChild(post.firstChild);
			}
			console.log("Post " + imageID + " deleted");
		}
		httpRequest.send(data);
	}

	function reset_password() {
		var httpRequest = new XMLHttpRequest();
		var data = new FormData();
		httpRequest.open("POST", "reset_password.php", true);
		data.append("reset", "password");
		httpRequest.onreadystatechange = function () {
			if (httpRequest.readyState === XMLHttpRequest.DONE) {
				if (httpRequest.status != 200)
					console.log("There was an error sending the ResetPassword request");
				}
		}
		httpRequest.onload = function () {
			alert("Password reset email sent");
		}
		httpRequest.send(data);
	}

	function delete_account() {
		var httpRequest = new XMLHttpRequest();
		var data = new FormData();
		httpRequest.open("POST", "delete_account.php", true);
		data.append("delete", "account");
		httpRequest.onreadystatechange = function () {
			if (httpRequest.readyState === XMLHttpRequest.DONE) {
				if (httpRequest.status != 200) {
					console.log("There was an error sending the DeleteAccount request");
				}
			}
		}
		httpRequest.onload = function () {
			if (httpRequest.responseText == "success") {
				alert("successfully deleted account");
				window.location.replace("logout.php");
			} else {
				alert("failure to delete account");
			}
		}
		httpRequest.send(data);
	}

	for (var i = 0; i < delete_buttons.length; i++) {
		delete_buttons[i].addEventListener('click', delete_post, false);
	}
	if (reset_pass != null) {reset_pass.addEventListener('click', reset_password, false);}
	if (account_button != null) {account_button.addEventListener('click', delete_account, false);}
})();