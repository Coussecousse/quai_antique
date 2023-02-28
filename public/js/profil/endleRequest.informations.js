const emailButton = document.querySelector('#change_email');
const passwordButton = document.querySelector('#change_password');
let count = 0;

emailButton.addEventListener('click', e => {
    let email = document.querySelector('#email').value;
    
    if (email === '') {
        return;
    }
    e.preventDefault();
    count++;

    const setPassword = document.querySelector('#need_password');
    const setEmail = document.querySelector('#change_email');

    setEmail.children[0].classList.replace('fa-pen', 'fa-check');
    passwordButton.classList.add('hide');
    setPassword.classList.remove('hide');

    let password = document.querySelector('#password').value;

    if (password === '') {
        if (count > 1) {
            const a = setPassword.querySelector('a');
            setPassword.classList.add('form_error');
            a.style.color = '#F9F6F0';
        }
        return;
    }

    emailButton.disabled = true;

    const xhr = new XMLHttpRequest();
    xhr.onload = function() {
        let url = window.location.origin + window.location.pathname;

        if (this.readyState == 4 && this.status == 200) {
            const response = JSON.parse(xhr.responseText);

            switch(response.result) {
                case "success":
                    window.location = url + "?result=success_email";
                    break;
                case "error_email":
                    window.location = url + "?result=error_email_email";
                    break;
                case "error_invalid":
                    window.location = url + "?result=error_invalid";
                    break;
                case 'error_pattern' :
                    window.location = url + "?result=error_pattern";
                    break;
                default : 
                    window.location = url + "?result=error";
                    break;
            }
        }
    }

    xhr.open('POST', "/admin/profil/{page_up}/{page_down}");
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let params = 'email='+ email +'&password=' + password;
    xhr.send(params);
})


passwordButton.addEventListener('click', e => {
    let error = false;

    const password = document.querySelector('#password');
    const passwordValue = password.value;
    const errorPassword = document.querySelector('#error_password');

    e.preventDefault();

    if (passwordValue === ''){
        errorPassword.classList.contains('hide') ? errorPassword.classList.remove('hide') : null;
        errorPassword.innerHTML = "Veuillez remplir ce champ.";
        return;
    } else {
        !errorPassword.classList.contains('hide') ? errorPassword.classList.add('hide') : null;
    }

    count++;

    emailButton.classList.add('hide');

    passwordButton.children[0].classList.replace('fa-pen','fa-check');

    const changePassword = document.querySelectorAll('.change_password');

    changePassword.forEach(element => {
        element.classList.remove('hide');
    })

    const validPassword = document.querySelector('#valid_password');
    const oldPassword = document.querySelector('#old_password');

    const validPasswordValue = validPassword.value;
    const oldPasswordValue = oldPassword.value;

    const errorValidPassword = document.querySelector('#error_validPassword');
    const errorOldPassword = document.querySelector('#error_oldPassword');

    if (validPasswordValue === '') {
        error = addError(errorValidPassword, "Veuillez remplir ce champ.");
        addingMargin(validPassword);
    } else if (validPasswordValue !== passwordValue) {
        error = addError(errorValidPassword, "Les mots de passes ne sont pas identiques.");
        addingMargin(validPassword);
    } else {
        error = removeError(errorValidPassword);
        removeMargin(validPassword);
    }
    if (oldPasswordValue === '') {
        error = addError(errorOldPassword, "Veuillez remplir ce champ.");
        addingMargin(oldPassword);
    } else {
        if (!error) {
            error = removeError(errorOldPassword);
            removeMargin(oldPassword);
        }
    }

    if (error) {
        return;
    }

    passwordButton.disabled = true;

    const xhr = new XMLHttpRequest();
    xhr.onload = function() {
        let url = window.location.origin + window.location.pathname;
        if (this.readyState == 4 && this.status == 200) {
            const response = JSON.parse(xhr.responseText);

            switch(response.result){
                case 'success':
                    window.location = url + "?result=success_password";
                    break;
                case 'error_invalid':
                    window.location = url + "?result=error_invalid";
                    break;
                case 'error_pattern' :
                    window.location = url + "?result=error_pattern";
                    break;
                default : 
                    window.location = url + "?result=error";
                    break;
            }
        }
    }

    xhr.open('POST', "/admin/profil/{page}");
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let params = 'password='+ passwordValue +'&oldPassword=' + oldPasswordValue;
    xhr.send(params);
})


