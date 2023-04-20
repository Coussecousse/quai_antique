function togglePassword(e) {
    const target = e.target.parentElement;
    const eye = target.querySelector('.fa-eye');
    const eyeSlash = target.querySelector('.fa-eye-slash');
    const password = target.previousElementSibling;

    if (!eye.classList.contains("hide")) {
        eye.classList.add("hide");
        eyeSlash.classList.remove("hide");
    } else {
        eye.classList.remove("hide");
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
    const errorPassword = document.querySelector('#error_password');

    const validPassword = document.querySelector('label[for=valid_password]');

    if (!errorPassword.classList.contains('hide')) {
        return;
    } else if (!validPassword.classList.contains('hide')) {
        return;
    }
}