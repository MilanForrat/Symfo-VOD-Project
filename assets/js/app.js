btnFullscreenVideo = document.getElementById('fs-mode');
videoContainer = document.getElementById('video-container')


// console.log(btnFullscreenVideo)
// console.log(videoContainer)

btnFullscreenVideo.addEventListener('click', function(){
    // videoContainer.style.width = "100%"
    videoContainer.classList.toggle("video-container-toggle-class");
    }
);
