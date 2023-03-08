console.log('test');

function setFood(e) {

    const titleInput = e.target[0];
    const title = titleInput.value;

    const description = e.target[1].value;
    console.log(description);
    const price = e.target[2].value;
    const button = e.target[3];

    e.preventDefault();

    const id = (titleInput.name.split('_'))[1];

    button.disabled = true;

    const xhr = new XMLHttpRequest();
    xhr.onload = function() {
        let url = window.location.origin + window.location.pathname;

        if (this.readyState == 4 && this.status == 200) {
            const response = JSON.parse(xhr.responseText);

            switch(response.result){
                case 'success':
                    window.location = url + "?result=success";
                    break;
                case 'error_pattern' :
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
    xhr.open('POST', '/admin/profil/{page_up}/{page_down}/{page_three}');
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let params = 'id='+ id + '&title='+ title + '&description=' + description + '&price=' + price;
    xhr.send(params);
}

function deleteFood(e) {
    const button = e.target.parentElement;
    const id = (button.id.split('_'))[1];

    e.preventDefault();

    button.disabled = true;

    const xhr = new XMLHttpRequest();
    xhr.onload = function() {
        let url = window.location.origin + window.location.pathname;

        if (this.readyState == 4 && this.status == 200) {
            const response = JSON.parse(xhr.responseText);

            switch(response.result){
                case 'success':
                    window.location = url + "?result=success";
                    break;
                default : 
                    window.location = url + "?result=error";
                    break;
            }
        } else {
            window.location = url + "?result=error";
        }
    }
    xhr.open('POST', '/admin/profil/{page_up}/{page_down}/{page_three}');
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let params = 'delete=true&id='+ id;

    xhr.send(params);
}