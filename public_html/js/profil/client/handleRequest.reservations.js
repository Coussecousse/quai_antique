function deleteReservation(e) {
    const id = e.target.parentElement.dataset.id;

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

    xhr.open('POST', "{{ path('client_profil') }}");
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let params = 'delete_reservation=' + true + '&id=' + id;
    xhr.send(params);
}