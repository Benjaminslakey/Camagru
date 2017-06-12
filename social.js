
	var	socialSection = document.getElementById('social');
	var commentSubmitButton = document.getElementById('comment-submit');
	var likeButton = document.getElementById('like-button');
	var posts = document.getElementsByClassName('post-links');
	var loginStatus = getLoginStatus();

	function postAComment() {
		var httpRequest = new XMLHttpRequest();
		var userComments = document.getElementById('post-comments');
		var comment = document.getElementById('comment-input').value;
		var slides = document.getElementsByClassName('index-slide');
		var data = new FormData();
		var imageID = null;

		data.append("comment", comment);
		data.append("submit", "submit");
		for (var i = 0; i < slides.length; i++) {
			if (slides[i].style.display == "flex") {
				imageID = slides[i].getAttribute('id');
				break ;
			}
		}
		data.append("imageID", imageID);
		httpRequest.open("POST", "social.php", true);
		httpRequest.onload = function () {
			if (httpRequest.readyState === XMLHttpRequest.DONE) {
                if (httpRequest.status === 200) {
                    console.log("Comment posted.");
                } else {
                    alert('Error posting comment.');
                }
            }
			userComments.innerHTML = httpRequest.responseText + userComments.innerHTML;
		}
		httpRequest.send(data);
		if (socialSection != null) {socialSection.style.display = "none";}
	}

	function likeAPost() {
		var httpRequest = new XMLHttpRequest();
		var data = new FormData();
		var slides = document.getElementsByClassName('index-slide');
		var likeDisplay = document.getElementById('like-counter');
		var imageID = null;

		for (var i = 0; i < slides.length; i++) {
			if (slides[i].style.display == "flex") {
				imageID = slides[i].getAttribute('id');
				break ;
			}
		}
		data.append("submit", "submit");
		data.append("imageID", imageID);
		data.append("like", "yes");
		httpRequest.open("POST", "social.php", true);
		httpRequest.onload = function () {
			if (httpRequest.readyState === XMLHttpRequest.DONE) {
				if (httpRequest.status != 200) {
					alert("Error posting like.");
				}
			}
			likeDisplay.innerHTML = httpRequest.responseText;
		}
		httpRequest.send(data);
		if (socialSection != null) {socialSection.style.display = "none";}
	}

	function displaySocial() {
		socialSection.style.display = "flex";
		window.onclick = function(event) {
            if (event.target == socialSection) {
                socialSection.style.display = "none";
            }
        }
	}

	function getLoginStatus() {
		var loginStatus = "<?php echo $_SESSION['logged_in_user']; ?>";

			if (loginStatus != null)
				return true;
			else
				return false;
	}

	if (likeButton != null) {likeButton.addEventListener('click', likeAPost, false);}
	if (commentSubmitButton != null) {commentSubmitButton.addEventListener('click', postAComment, false);}
	if (posts != null) {
		for (var i = 0; i < posts.length; i++) {
			posts[i].addEventListener('click', displaySocial, false);
		}
	}
