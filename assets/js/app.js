btnFullscreenVideo = document.getElementById('fs-mode');
videoContainer = document.getElementById('video-container')

btnSubmitSignIn = document.getElementById('sign_in_submit');
console.log(btnSubmitSignIn);
btnSubmitSignIn.className = 'btn-dark btn btn-lg w-100 mt-2';

// console.log(btnFullscreenVideo)
// console.log(videoContainer)

btnFullscreenVideo.addEventListener('click', function(){
    // videoContainer.style.width = "100%"
    videoContainer.classList.toggle("video-container-toggle-class");
    }
);
