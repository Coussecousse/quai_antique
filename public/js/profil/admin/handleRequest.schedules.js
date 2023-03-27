function submitAllTheDays(event) {
    const days = document.querySelector('#days').children;
    const datas = {schedules : []};
    
    for (let day of days) {
        const dataDay = {id : '', service : []};
        const id = day.dataset.day;
        dataDay.id = id;
        const containers = day.querySelectorAll('.schedule_container');
        
        for (let container of containers) {
            const service = [];
            const checkBox = container.querySelector('.input_check');

            const inputs = container.querySelectorAll('.input_time');
            const regex = new RegExp('^(?:2[0-3]|[01][0-9]):[0-5][0-9]$');
            
            for (let input of inputs) {
                if (!regex.test(input.value) && !checkBox.checked) {
                    const errorElement = document.querySelector('.error_submitAll');
                    errorElement.classList.remove('hide');
                    errorElement.textContent = "Des données rentrées sont vides et/ou invalides.";
                    return;
                }
                if (checkBox.checked) {
                    service.push('close');
                    break;
                } else {
                    service.push(input.value);
                }
            }
            
            dataDay.service.push(service);
        }
        datas.schedules.push(dataDay);
    }

    event.target.disabled = true;

    const xhr = new XMLHttpRequest();

    xhr.onload = function() {
        const url = window.location.origin + window.location.pathname;

        if (this.readyState == 4 && this.status == 200) {
            const response = JSON.parse(xhr.responseText);

            switch(response.result) {
                case 'success' :
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
        window.location = url + "?result=error";
        }
    }

    xhr.open('POST', '/admin/profil/{page_up}/{page_down}');
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.send(JSON.stringify(datas));
}


