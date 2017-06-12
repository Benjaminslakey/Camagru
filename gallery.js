	var slideIndex = 1;

	function plusSlides(n) {showSlides(slideIndex += n);}
	function currentSlide(n) {showSlides(slidesIndex = n);}

	function showSlides(n) {
		var i;
		var slides = document.getElementsByClassName('index-slide');
		var imageID = null;

		if (n > slides.length) {slideIndex = 1}
		if (n < 1) {slideIndex = slides.length}

		for (i = 0; i < slides.length; i++) {
			slides[i].style.display = "none";
		}
		if (slides[slideIndex-1] != null) {
			slides[slideIndex-1].style.display = "flex";
			fillComments();
			fillLikes();
		}
	}

	function fillComments() {
		var userComments = document.getElementById('post-comments');
		var slides = document.getElementsByClassName('index-slide');
		var httpRequest = new XMLHttpRequest();
		var data = new FormData();
		var imageID;

		for (var i = 0; i < slides.length; i++) {
			if (slides[i].style.display == "flex") {
				imageID = slides[i].getAttribute('id');
				break ;
			}
		}
		data.append("comments", "fill");
		data.append("imageID", imageID);
		httpRequest.open("POST", "social.php", true);
		httpRequest.onload = function () {
			userComments.innerHTML = httpRequest.responseText;
		}
		httpRequest.send(data);
	}

	function fillLikes() {
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
		data.append("like", "fill");
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

	showSlides(slideIndex);