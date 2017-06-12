(function() {
    var width = 400;
    var height = 0;

    var streaming = false;
    var video = null;
    var photo = null;
    var canvas = null;
    var allowCapture = null;
    var capture_button = null;
    var save_button = null;
    var upload_button = null;
    var uploaded_photo = null;

    var effect_none = null;
    var effect_neg = null;
    var effect_baw = null;
    var effect_picframe = null;
    var effect_mario = null;
    var effect_tiger = null;
    var effect_balloons = null;
    var effect_skull = null;
    var effect_obama = null;
    var effect_leaves = null;

    var effect_type = null;
    var chosen_effect = null;

    function start_camera() {
        video = document.getElementById('video');
        canvas = document.getElementById('photo-canvas');
        photo = document.getElementById('photo');
        capture_button = document.getElementById('take-photo');
        upload_button = document.getElementById('upload-button');
        uploaded_photo = document.getElementById('uploaded-photo');

        effect_none = document.getElementById('no-effect');
        effect_neg = document.getElementById('negative');
        effect_baw = document.getElementById('black-and-white');
        effect_picframe = document.getElementById('picture-frame');
        effect_mario = document.getElementById('mario');
        effect_tiger = document.getElementById('tiger');
        effect_balloons = document.getElementById('balloons');
        effect_skull = document.getElementById('skull');
        effect_obama = document.getElementById('obama');
        effect_leaves = document.getElementById('leaves');

        navigator.getMedia = ( navigator.getUserMedia ||
                navigator.webkitGetUserMedia ||
                navigator.mozGetUserMedia ||
                navigator.msGetUserMedia);

        navigator.getMedia(
            {
               video: true,
               audio: false
            },
                function(stream) {
                    if (navigator.mozGetUserMedia) {
                        video.mozSrcObject = stream;
                    } else {
                        var vendorURL = window.URL || window.webkitURL;
                        video.src = vendorURL.createObjectURL(stream);
                    }
                    video.play();
                },
                function(err) {
                    console.log("Error with video stream\n" + err);
                }
            );

        video.addEventListener('canplay', function(ev) {
            if (!streaming) {
                height = video.videoHeight / (video.videoWidth/width);
                if (isNaN(height)) {
                    height = width / (4/3);
                }

                video.setAttribute('width', width);
                video.setAttribute('height', height);
                canvas.setAttribute('width', width);
                canvas.setAttribute('height', height);
                streaming = true;
            }
        }, false);

        effect_none.addEventListener('click', choose_effect, false);
        effect_neg.addEventListener('click', choose_effect, false);
        effect_baw.addEventListener('click', choose_effect, false);
        effect_picframe.addEventListener('click', choose_effect, false);
        effect_mario.addEventListener('click', choose_effect, false);
        effect_tiger.addEventListener('click', choose_effect, false);
        effect_balloons.addEventListener('click', choose_effect, false);
        effect_skull.addEventListener('click', choose_effect, false);
        effect_obama.addEventListener('click', choose_effect, false);
        effect_leaves.addEventListener('click', choose_effect, false);

        capture_button.addEventListener('click', function(ev) {
            if (allowCapture) {
                take_photo();
                ev.preventDefault();
            }
        }, false);

        upload_button.addEventListener('click', upload, false);
        clear_canvas();
    }


    function choose_effect(ev) {
        button = ev.target;
        chosen_effect = button.id;
        if (chosen_effect == "negative" || chosen_effect == "black-and-white") {
            effect_type = "filter";
        } else {
            effect_type = "overlay";
        }
        allowCapture = true;
    }

    function clear_canvas() {
        var context = canvas.getContext('2d');
        context.fillStyle = "#AAA";
        context.fillRect(0, 0, canvas.width, canvas.height);
        var data = canvas.toDataURL('image/png');
        photo.setAttribute('src', data);
    }

    function save(ev) {
        var popup = document.getElementById('savepic-popup');
        save_photo();
        ev.preventDefault();
        popup.style.display = "none";
    }

    function upload() {
        var upload_popup = document.getElementById('upload-popup');
        var submitButton = document.getElementById('submit-upload');

        if (allowCapture) {
            upload_popup.style.display = "flex";
            submitButton.addEventListener('click', save_upload, true);
            window.onclick = function (event) {
                if (event.target == upload_popup) {
                    submitButton.removeEventListener('click', save_upload);
                    upload_popup.style.display = "none";
                }
            }
        } else
            alert("Select an effect to upload a photo!");           
    }

    function save_upload() {
        var submitButton = document.getElementById('submit-upload');
        var uploaded = document.getElementById('userphoto').files[0];
        var httpRequest = new XMLHttpRequest();
        var data = new FormData();

        httpRequest.open('POST', "upload_photo.php", true);
        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState === XMLHttpRequest.DONE) {
                if (httpRequest.status === 200) {
                    console.log("uploaded image sent using POST");
                } else {
                    alert('There was a problem saving the photo');
                }
            }
        }
        httpRequest.onload = function() {
            if (httpRequest.responseText) {
                document.getElementById('session-gallery').innerHTML = httpRequest.responseText + document.getElementById('session-gallery').innerHTML;
            }
        }
        data.append("userphoto", uploaded);
        data.append("effect-type", effect_type);
        data.append("effect", chosen_effect);
        httpRequest.send(data);
        // submitButton.removeEventListener('click', save_upload);
    }

    function take_photo() {
        var canvas = document.getElementById('photo-canvas');
        var popup = document.getElementById('savepic-popup');
        var save_button = document.getElementById('save-button');
        var context = canvas.getContext('2d');
        if (width && height) {
            canvas.width = width;
            canvas.height = height;
            context.drawImage(video, 0, 0, width, height);
            var data = canvas.toDataURL('image/png');
            photo.setAttribute('src', data);
            canvas.style.display = "block";
            popup.style.display = "flex";
            save_button.addEventListener('click', save, false);
            window.onclick = function(event) {
                if (event.target == popup) {
                    popup.style.display = "none";
                    save_button.removeEventListener('click', save);
                }
            }
        } else {
            clear_canvas();
        }
    }

    function save_photo() {
        var pic = document.getElementById('photo').getAttribute('src');
        var httpRequest = new XMLHttpRequest();
        var data = new FormData();
        httpRequest.open('POST', "save_photo.php", true);
        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState === XMLHttpRequest.DONE) {
                if (httpRequest.status === 200) {
                    console.log("image sent using POST");
                } else {
                    alert('There was a problem saving the photo');
                }
            }
        }
        httpRequest.onload = function() {
            document.getElementById('session-gallery').innerHTML = httpRequest.responseText + document.getElementById('session-gallery').innerHTML;
        }
        data.append("img", pic);
        data.append("effect-type", effect_type);
        data.append("effect", chosen_effect);
        httpRequest.send(data);
    }

    window.addEventListener('load', start_camera, false);
})();