const buttons = document.querySelector('#restaurant').querySelectorAll('button[type="submit"]');
console.log(buttons);

function changeRestaurant(e, button) {

    let element = button.previousElementSibling;
    let valueElement = element.value;
    let nameElement = element.name;

    if (valueElement === '') {
        return;
    }

    e.preventDefault();

    button.disabled = true;

    const xhr = new XMLHttpRequest();
    xhr.onload = function() {
        let url = window.location.origin + window.location.pathname;

        if (this.readyState == 4 && this.status == 200) {
            const response = JSON.parse(xhr.responseText);
            //  Gestion des réponses
            switch(response.result) {
                case "success" : 
                    window.location = url + "?result=success";
                    break;
                case "error_pattern" : 
                    window.location = url + "?result=error_pattern";
                    break;
                default : 
                    window.location = url + "?result=error";
                    break;
            }
        } else {
            window.location = url + "?result=error"
        }
    }
    xhr.open('POST', '/admin/profil/{page_up}/{page_down}');
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let params = nameElement+'='+valueElement;
    console.log(params);
    xhr.send(params);

}
buttons.forEach( button => {
    button.addEventListener('click', e => changeRestaurant(e, button))
})