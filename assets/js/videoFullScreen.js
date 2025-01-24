btnFullscreenVideo = document.getElementById('fs-mode');
videoContainer = document.getElementById('video-container');

btnFullscreenVideo.addEventListener('click', function(){
    videoContainer.classList.toggle("video-container-toggle-class");
    }
);