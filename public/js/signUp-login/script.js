function togglePassword(e) {
    const target = e.target.parentElement;
    
    const eye = target.querySelector('.fa-eye');
    const eyeSlash = target.querySelector('.fa-eye-slash');
    const password = target.previousElementSibling;

    console.log(eye, eyeSlash, password);
    console.log(eye.classList.contains('hide'));

    eye.classList.remove("lala");

    // if (eye.classList.contains('hide')){
    //     console.log('hey');
    //     eye.classList.remove("hide");
    // } else {
    //     console.log('yo')
    //     eye.classList.add("hide");
    // }

    if (!eye.classList.contains("hide")) {
        eye.classList.add("hide");
        eyeSlash.classList.remove("hide");
    } else {
        eye.classList.remove("hide");
        console.log(eyeSlash);
        eyeSlash.classList.add("hide");
    }
    // eye.classList.contains('hide') ? eye.classList.remove('hide') : eye.classList.add("hide");
    // eyeSlash.classList.contains("hide") ? eyeSlash.classList.remove("hide") : eye.classList.add("hide");
    password.type = password.type === "text" ? "password" : "text";

}

function closeResetPassword(e) {
    let target = e.target.parentElement;
    if (target.id === "reset_success") {
        target = target.parentElement;
    }
    target.style.opacity = "0";
    setTimeout(() => {
        target.remove();
    }, 75);
}