function togglePassword(e) {
    console.log('test');
    const target = e.target.parentElement;
    const eye = target.querySelector('.fa-eye');
    const eyeSlash = target.querySelector('.fa-eye-slash');
    const password = target.previousElementSibling;

    if (!eye.classList.contains("hide")) {
        eye.classList.add("hide");
        eyeSlash.classList.remove("hide");
    } else {
        eye.classList.remove("hide");
        console.log(eyeSlash);
        eyeSlash.classList.add("hide");
    }
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

function displayRules() {
    const small = document.querySelector('#password_rule');
    const needPassword = document.querySelector('#need_password');

    needPassword.classList.contains('hide') ? small.classList.remove('hide') : null;
}
function removeRules() {
    const small = document.querySelector('#password_rule');
    
    small.classList.add('hide');
}