function setTitle(e) {

    const input = e.target[0];
    const value = input.value;
    const button = e.target[1];
    
    if (value == '') {
        return;
    }
    e.preventDefault();

    const id = (input.name.split('_'))[1];

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
    xhr.open('POST', '/admin/profil/{page_up}/{page_down}');
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let params = 'imageTitle='+ value + '&id='+ id;
    xhr.send(params);
}

function deleteImage(e) {
    const button = e.target.parentElement;
    const id  = (button.id.split('_'))[1];
    
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
    xhr.open('POST', '/admin/profil/{page_up}/{page_down}');
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let params = 'delete=true&id_carousel='+ id;

    xhr.send(params);
}

function setDescription(e) {
    const textarea = e.target[0];
    const value = textarea.value;
    const button = e.target[1];

    if (value == '') {
        return;
    }
    e.preventDefault();

    const id = (textarea.name.split('_'))[1];

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
    xhr.open('POST', '/admin/profil/{page_up}/{page_down}');
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let params = 'imageDescription='+ value + '&id='+ id;
    xhr.send(params);
}