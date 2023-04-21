function addError(element, errorMessage) {
    const errorContainer = document.querySelector(element);
    errorContainer.innerHTML = errorMessage;
}
function removeError(element) {
    const errorContainer = document.querySelector(element);
    errorContainer.innerHTML = '';
}
function checkEmpty(element, errorContainer, message) {
    if (element === '') {
        addError(errorContainer, message);
        return true;
    } else {
        removeError(errorContainer);
    }
}

function checkIfError(element, min, max, errorContainer) {
    let error=false;
    
    if ( element < min ) {
        addError(errorContainer, 'Entrée trop petite.');
        error = true;
    } else {
        removeError(errorContainer);
    }
    if (element > max) {
        addError(errorContainer, 'Entrée trop longue.');
        error = true;
    }  else {
        removeError(errorContainer);
    } 
    error = checkEmpty(element, errorContainer, "Ne peut pas être vide");

    return error;
}

function seeSchedules(schedule) {
    const container = document.querySelector("#reservation_schedules");
    container.classList.replace('slow-opacity-reverse', 'slow-opacity-in');

    const list = container.querySelector('select');
    list.disabled = false;
    if (list.children.length > 1) {
        for (let i = 1; i != list.children.length; i) {
            list.children[i].remove();
        }
    }
    
    for (let time of schedule) {
        const option = document.createElement("option");
        time = new Date(time * 1000);
        let hour = time.getHours();
        let minutes = '0' + time.getMinutes();
        time = hour + ':' + minutes.slice(-2);
        option.value = time;
        option.textContent = time;
        list.appendChild(option);
    }
}
function sendDate(date, service) {
    function handleDisableInput(element, statue) {
        if (statue) {
            element.classList.add('invisible', 'opacity-0');
            element.querySelector('input').disabled = statue;
        } else {
            element.classList.remove("invisible", "opacity-0");
            element.querySelector('input').disabled = statue;
        }
    }

    const places = document.querySelector('input[name=places]').value;

    
    const errorContainer = document.querySelector('#error_service');
    const eveningContainer = document.querySelector('#service_evening');
    const noonContainer = document.querySelector('#service_noon');
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/reservation');
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    let params = 'date=' + date + '&places=' + places ;
    const sendPromise = new Promise((resolve,reject) => {
        xhr.send(params);
        xhr.onload = function() {
            if (this.readyState == 4 && this.status == 200) {
                const response = JSON.parse(xhr.responseText);
                resolve(response);
            }
        }
    })
    
    errorContainer.classList.add('flash');
    errorContainer.innerHTML ="Chargement...";

    // Reset
    !eveningContainer.classList.contains('invisible') ?
        eveningContainer.classList.add('invisible', 'opacity-0')
        : null ;
    !noonContainer.classList.contains('invisible') ? 
        noonContainer.classList.add('invisible', 'opacity-0')
        : null ;
    service.classList.replace('slow-opacity-reverse','slow-opacity-in');

    const schedulesContainer = document.querySelector("#reservation_schedules") ;
    schedulesContainer.classList.contains('slow-opacity-in') ? 
        schedulesContainer.classList.replace('slow-opacity-in', 'slow-opacity-reverse') 
        : null;

    sendPromise.then(response => successSend(response))
    function successSend(response) {
        errorContainer.classList.remove('flash');
        const evening = response.evening;
        const noon = response.noon;

        service.classList.replace('slow-opacity-reverse','slow-opacity-in');
        const eveningContainer = document.querySelector('#service_evening');
        const noonContainer = document.querySelector('#service_noon');
        console.log(evening);
        if (evening.length == 0 || !evening) {
            handleDisableInput(eveningContainer, true);
            noonContainer.querySelector('input').checked = true;
        } else {
            handleDisableInput(eveningContainer, false); 
            eveningContainer.querySelector('input').checked = true;
            noonContainer.querySelector('input').checked = false;
            seeSchedules(evening);
        }
        if (noon.length == 0 || !noon) {
            handleDisableInput(noonContainer, true);
        } else {
            handleDisableInput(noonContainer, false);
            if (evening.length == 0)
            {
                seeSchedules(noon); 
            }
        }
        
        if (!noon && !evening) {
            errorContainer.innerHTML = 'Nous sommes complets pour cette date.';
        } else if (noon.length == 0 && evening.length == 0) {
            errorContainer.innerHTML = 'Nous sommes fermés à cette date.';
        } else {
            errorContainer.innerHTML = '';
        }
        
        const boxs = [eveningContainer.querySelector('input'), noonContainer.querySelector('input')];
        for (let box of boxs) {
            box.addEventListener('change', () => {
                if (box.checked) {
                    if (box.id == 'evening') {
                        seeSchedules(evening);
                    } else {
                        seeSchedules(noon);
                    }
                }
            })
        }

    }
}

function changeDate(input, card) { 
    const service = card.querySelector('#reservation_service');
    const dateRegex = new RegExp("^\\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$");

    const schedulesContainer = document.querySelector("#reservation_schedules");

    // If date is not valid
    if (!dateRegex.test(input.value)) {
        const errorContainer = card.querySelector('#error_date');
        errorContainer.innerHTML = "Le format de date n'est pas bon.";
        if (!service.classList.contains('hidden')) {
            service.classList.replace('slow-opacity-in', 'slow-opacity-reverse');
            service.querySelectorAll('input[name=service]').forEach(input => {
                input.disabled = true;
            });

            schedulesContainer.classList.contains('slow-opacity-in') ?
            schedulesContainer.classList.replace('slow-opacity-in', 'slow-opacity-reverse') : null;
            schedulesContainer.querySelector('select').disabled = true;
        }
        return;
    }

    // Today
    let today = new Date();
    const dd = String(today.getDate()).padStart(2, '0');
    const mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    const yyyy = today.getFullYear();
    const hh = String(today.getHours()).padStart(2, '0');
    const ii = String(today.getMinutes()).padStart(2, '0');
    
    today = yyyy + '-' + mm + '-' + dd;
    let date = input.value;
    if (date == today) {
        date += ' '+ hh + ':' + ii + ':00';
        today +=' '+ hh + ':' + ii + ':00' ;
    }
    date = new Date(date);
    
    today = new Date(today);

    let todayInAYear = yyyy+1 + '-' + mm + '-' + dd + ' '+ hh + ':' + ii + ':00';
    
    todayInAYear = new Date(todayInAYear);


    // Error with date
    const errorContainer = card.querySelector('#error_date');
    if (date.getTime() < today.getTime() || date.getTime() > todayInAYear.getTime()) {
        errorContainer.innerHTML = "Nous n'acceptons pas les dates passées ou de plus d'un an.";
        if (!service.classList.contains('hidden')) {
            service.classList.replace('slow-opacity-in', 'slow-opacity-reverse');
            service.querySelectorAll('input[name=service]').forEach(input => {
                input.disabled = true;
            });

            schedulesContainer.classList.contains('slow-opacity-in') ?
            schedulesContainer.classList.replace('slow-opacity-in', 'slow-opacity-reverse') : null;
            schedulesContainer.querySelector('select').disabled = true;
        }
        return;
    } 
    errorContainer.innerHTML = "";

    service.querySelectorAll('input[name=service]').forEach(input => {
        input.disabled = false;
    });

    sendDate(date, service);
}
// Second cards
function handleSecondCard(card) {
    const buttons = card.querySelector('.buttons').children;
    for (let button of buttons) {
        button.disabled = false;
    }

    const inputDate = card.querySelector('input[name=date]');
    inputDate.addEventListener( 'change' , () => changeDate(inputDate, card))
}

function nextSlide(e) {
    e.preventDefault();
    const gridContainer = document.querySelector('#grid_reservation');
    const cards = gridContainer.children;
    const widthForm = document.querySelector('form').offsetWidth;
    let activeCard;

    for (let card of cards) {
        if (card.classList.contains('active')) {
            activeCard = card;
        }
    }

    let index = activeCard.dataset.index;
    
    if (index == 0) {
    
        const name = document.querySelector('input[name=name]').value;
        const places = document.querySelector("input[name=places]").value;
    
        if (checkIfError(name, 2, 100, '#error_name') || checkIfError(places, 1, 20, '#error_places')){
            return;
        }
    
        const allergies = [];
        const allergiesContainer = document.querySelector('#allergies').children;
    
        for (let allergie of allergiesContainer) {
            if (allergie.checked) {
                allergies.push(allergie.value);
            }
        }
        
        gridContainer.style.transform = 'translateX(-'+ widthForm + 'px)';

        activeCard.classList.remove('active');
        const newActiveCard = activeCard.nextElementSibling;
        newActiveCard.classList.add('active');

        handleSecondCard(newActiveCard);
        return;
    }
    if (index == 1)
    {
        // Date
        let date = document.querySelector("#date").value;
        
        // Hour
        const hour = document.querySelector("#schedules").value;
        
        if (checkEmpty(date, "#error_date", "Veuillez selectionner une date.") ||
            checkEmpty(hour, "#error_schedule", "Veuillez selectionner une heure.")){
            return;
        }
        
        activeCard.classList.remove('active');
        const newActiveCard = activeCard.nextElementSibling;
        newActiveCard.classList.add('active');
        const buttons = newActiveCard.querySelector('.buttons').children;
        for (let button of buttons) {
            button.disabled = false;
        }

        const summaryName = document.querySelector("#summary_name");
        const summaryPlaces = document.querySelector("#summary_places");
        const summaryDate = document.querySelector("#summary_date");
        const summarySchedule = document.querySelector("#summary_schedule");
        
        const elements = [summaryName, summaryPlaces, summaryDate, summarySchedule];
        for (let element of elements) {
            let value;
            if (element.id === "summary_name") {
                value = document.querySelector("#name").value;
            } else if (element.id === "summary_places") {
                value = document.querySelector("#places").value;
            } else if (element.id === "summary_date") {
                date = date.split('-');
                date = date[2]+ '/' + date[1] + '/' + date[0];
                value = date;
            } else {
                value = hour;
            }
            element.textContent = value;
        }
        
        const summaryAllergies = document.querySelector("#summary_allergies");
        const allergies = document.querySelector('#allergies').querySelectorAll('input');
        const allergiesList = [];
        allergies.forEach(allergie => {
            if (allergie.checked) {
                allergiesList.push(allergie);
            }
        })

        for (let child of summaryAllergies.children) {
            child.remove();
        }
        for (let allergie of allergiesList) {
            const li = document.createElement("li");
            li.innerHTML = "<span>"+allergie.dataset.text+"</span>";
            li.dataset.value = allergie.value;
            summaryAllergies.appendChild(li);
        }

        const summaryService = document.querySelector("#summary_service");
        const services = document.querySelectorAll('input[name=service]');

        services.forEach(service => {
            if (service.checked) {
                if (service.value === 'evening') {
                    summaryService.innerHTML=`
                    <i class='fa-solid fa-sun form_icon'></i>
                    <p>Midi</p>`
                } else {
                    summaryService.innerHTML=`
                    <i class='fa-solid fa-moon form_icon'></i>
                    <p>Soir</p>`
                }
            }
        })

        gridContainer.style.transform = 'translateX(-'+ widthForm * 2 + 'px)';
    }
}

function previousSlide(e) {
    e.preventDefault();
    const gridContainer = document.querySelector("#grid_reservation");
    const cards = gridContainer.children;
    const widthForm = document.querySelector("form").offsetWidth;
    let activeCard;

    for (let card of cards) {
        if (card.classList.contains("active")) {
            activeCard = card;
        }
    }

    let index = activeCard.dataset.index;

    if (index == 1) {
        gridContainer.style.transform = "translateX(0)";
        activeCard.classList.remove('active');
        const newActiveCard = activeCard.previousElementSibling;
        newActiveCard.classList.add('active');   
        return;
    }
    if (index == 2) {
        gridContainer.style.transform = 'translateX(-'+ widthForm + 'px)';
        activeCard.classList.remove('active');
        const newActiveCard = activeCard.previousElementSibling;
        newActiveCard.classList.add('active');  
    }
}

function sendReservation(e) {
    e.preventDefault();

    const name = document.querySelector("#summary_name").textContent;
    const places = document.querySelector("input[name=places").value;
    const schedule = document.querySelector("#summary_schedule").textContent;
    
    let date = document.querySelector("#summary_date").textContent;
    date = date.split('/');
    date = date[2] + '-' + date[1] + '-' + date[0];
    
    let allergiesElement = document.querySelector("#summary_allergies").children;
    const allergies = [];
    for (let allergie of allergiesElement) {
        allergies.push(allergie.dataset.value);
    }

    let service = document.querySelector("#summary_service");
    if (service.querySelector("p").textContent === "Midi") {
        service = "evening";
    } else {
        service = "noon";
    }

    const token = document.querySelector('input[name=token]').value;

    const xhr = new XMLHttpRequest();
    xhr.onload = function() {
        let url = window.location.origin + window.location.pathname;

        if (this.readyState == 4 && this.status == 200) {
            const response = JSON.parse(xhr.responseText);
            console.log(response.result)
            switch(response.result){
                case 'success':
                    window.location = url + "?result=success";
                    break;
                case 'error_pattern' :
                    window.location = url + "?result=error_pattern";
                    break;
                case 'error_date_exist':
                    window.location = url + "?result=error_date_exist";
                    break;
                default : 
                    window.location = url + "?result=error";
                    break;
            }
        } else {
            window.location = url + "?result=error";
        }
    }
    xhr.open('POST', '/reservation');
    xhr.setRequestHeader('Content-Type', 'application/json');

    const data = {
        name : name,
        places : places,
        allergies : allergies,
        date : date,
        schedule : schedule,
        token : token
    }
    xhr.send(JSON.stringify(data));
}

function handleTemplate(e) {
    const templateId = e.target.value;
    if (templateId == "") {
        return;
    }
    const xhr = new XMLHttpRequest();
    xhr.open('POST', "/reservation");
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded'); 
    
    let params = 'template='+ templateId;
    const sendTemplatePromise = new Promise((resolve, reject) => {
        xhr.send(params);
        xhr.onload = function() {
            if (this.readyState == 4 && this.status == 200) {
                const response = JSON.parse(xhr.response);
                resolve(response)
            } else {
                reject();
            }
        }
    })
    const templateSpinner = document.querySelector("#spinner_template");
    templateSpinner.classList.add('lds-ring');


    sendTemplatePromise.then( response => {
        templateSpinner.classList.remove('lds-ring');
            if (response.result === 'error') {
                const errorTemplate = document.querySelector('#error_template');
                errorTemplate.textContent = "Une erreur est survenue.";
            } else {
                const templateName = response.name;
                const formName = document.querySelector("#name");
                formName.value = templateName;
                
                const templacePlaces = response.places;
                const formPlaces = document.querySelector("#places");
                formPlaces.value = templacePlaces;
                
                const templateAllergies = response.allergies;
                const formAllergies = document.querySelector("#allergies").querySelectorAll('input');
                for (let templateAllergie of templateAllergies) {
                    formAllergies.forEach(formAllergie => {
                        if (templateAllergie === formAllergie.value) {
                            formAllergie.checked = true;
                            return;
                        }
                    });
                }
            }
        })
        .catch( () => {
            let url = window.location.origin + window.location.pathname;
            window.location = url + "?result=error";
        })
}