const buttonEmail = document.querySelector('#change_email');

buttonEmail.addEventListener('click', e => {
    e.preventDefault();

    const setPassword = document.querySelector('#need_password');
    const buttonChangePassword = document.querySelector('#change_password');
    const linkPasswordForgotten = document.querySelector('#forgot_password');
    const setEmail = document.querySelector('#change_email');

    setEmail.children[0].classList.replace('fa-pen', 'fa-check');
    buttonChangePassword.classList.add('hide');
    setPassword.classList.remove('hide');
    // linkPasswordForgotten.parentElement.classList.remove('hide');

    let email = document.querySelector('#email').value;
    let password = document.querySelector('#password').value;

    if (password === '') {
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.onload = function() {
        let url = window.location.origin + window.location.pathname;

        if (this.readyState == 4 && this.status == 200) {
            const response = JSON.parse(xhr.responseText);

            if (response.result === "success") {
                window.location = url + "?result=success_email";
            } else if (response.result === "error_email"){
                window.location = url + "?result=error_email_email";
            } else if (response.result === "error_invalid") {   
                window.location = url + "?result=error_invalid";
            }
        }
    }

    xhr.open('POST', "/admin/profil/{page}");
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let params = 'email='+ email +'&password=' + password;
    xhr.send(params);
})

const passwordButton = document.querySelector('#change_password');

buttonPassword.addEventListener('click', e => {
    e.preventDefault();

    
})