const buttonEmail = document.querySelector('#change_email');
const passwordButton = document.querySelector('#change_password');
let count = 0;

buttonEmail.addEventListener('click', e => {
    e.preventDefault();

    const setPassword = document.querySelector('#need_password');
    const setEmail = document.querySelector('#change_email');

    setEmail.children[0].classList.replace('fa-pen', 'fa-check');
    passwordButton.classList.add('hide');
    setPassword.classList.remove('hide');

    let email = document.querySelector('#email').value;
    let passwordvalue = document.querySelector('#passwordvalue').value;

    if (passwordvalue === '') {
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

    let params = 'email='+ email +'&passwordvalue=' + passwordvalue;
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

    buttonEmail.classList.add('hide');

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
        error = removeError(errorOldPassword);
        removeMargin(oldPassword);
    }
    
    if (error) {
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.onload = function() {
        let url = window.location.origin + window.location.pathname;
        if (this.readyState == 4 && this.status == 200) {
            const response = JSON.parse(xhr.responseText);
            console.log('success');
            // if (response.result === "success") {
            //     window.location = url + "?result=success_email";
            // } else if (response.result === "error_email"){
            //     window.location = url + "?result=error_email_email";
            // } else if (response.result === "error_invalid") {   
            //     window.location = url + "?result=error_invalid";
            // }
        }
    }

    xhr.open('POST', "/admin/profil/{page}");
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let params = 'passwordvalue='+ passwordvalue +'&oldPasswordValue=' + oldPasswordValue;
    xhr.send(params);
})

function addError(element, error) {
    if (count > 1)
    {
        element.classList.contains('hide') ? element.classList.remove('hide') : null;
        element.innerHTML = error;
    }    
    return true;
}
function removeError(element) {
    !element.classList.contains('hide') ? element.classList.add('hide') : null;
    return true;
}
function addingMargin(element) {
    if (window.innerWidth >= 1440 && count > 1) {
        console.log('yo');
        element.parentElement.style.marginBottom = '.5rem';
    }
}
function removeMargin(element) {
    if (window.innerWidth >= 1440) {
        element.parentElement.style.marginBottom = '0';
    }
}