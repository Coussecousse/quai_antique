function deleteDate(e) {
    const button = e.target.parentElement;

    const id = button.dataset.id;

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
            window.location = url + "?result=error"
        }
    }
    xhr.open('POST', '/admin/profil/{page_up}/{page_down}');
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let params = 'id_date='+ id + "&delete_date=" + true;

    xhr.send(params);
}