function addError(element, errorMessage) {
    const errorContainer = document.querySelector(element);
    errorContainer.innerHTML = errorMessage;
}
function removeError(element) {
    const errorContainer = document.querySelector(element);
    errorContainer.innerHTML = '';
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
    if (element === '') {
        addError(errorContainer, 'Ne peut pas être vide.');
        error = true;
    } else {
        removeError(errorContainer);
    }
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


    const xhr = new XMLHttpRequest();
    
    const errorContainer = document.querySelector('#error_service');
    const eveningContainer = document.querySelector('#service_evening');
    const noonContainer = document.querySelector('#service_noon');

    xhr.onload = function() {
        const url = window.location.origin + window.location.pathname;

        if (this.readyState == 4 && this.status == 200) {
            const response = JSON.parse(xhr.responseText);
            (response);
            const evening = response.evening;
            const noon = response.noon;
            (noon);
            (evening);

            if (evening.length == 0) {
                eveningContainer.classList.add('invisible', 'opacity-0');
                eveningContainer.querySelector('input').disabled = true;
                noonContainer.querySelector('input').checked = true;
            } else {
                eveningContainer.classList.remove('invisible', 'opacity-0');
                eveningContainer.querySelector('input').disabled = false;
                eveningContainer.querySelector('input').checked = true;
                noonContainer.querySelector('input').checked = false;
            }
            if (noon.length == 0) {
                noonContainer.classList.add('invisible', 'opacity-0');
                noonContainer.querySelector('input').disabled = true;
            } else {
                noonContainer.classList.remove('invisible', 'opacity-0');
                noonContainer.querySelector('input').disabled = false;
            }
            if (noon.length == 0 && evening.length == 0) {
                errorContainer.innerHTML = 'Nous sommes fermés à cette date.'
            } else {
                errorContainer.innerHTML = ''
            }

        } else {
        window.location = url + "?result=error";
        }
    }
    
    xhr.open('POST', '/reservation');
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    let params = 'date=' + date ;
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
    !eveningContainer.classList.contains('invisible') ?
        eveningContainer.classList.add('invisible', 'opacity-0')
        : null ;
    !noonContainer.classList.contains('invisible') ? 
        noonContainer.classList.add('invisible', 'opacity-0')
        : null ;
    service.classList.replace('slow-opacity-reverse','slow-opacity-in');

    const schedulesContainer = document.querySelector("#reservation_schedules") ;
    schedulesContainer.classList.contains('slow-opacity-in') ? 
        schedulesContainer.classList.replace('slow-opacity-in', 'slow-opacity-reverse') : null;

    sendPromise.then(response => successSend(response))
    function successSend(response) {
        errorContainer.classList.remove('flash');
        const evening = response.evening;
        const noon = response.noon;

        service.classList.replace('slow-opacity-reverse','slow-opacity-in');
        const eveningContainer = document.querySelector('#service_evening');
        const noonContainer = document.querySelector('#service_noon');

        if (evening.length == 0) {
            handleDisableInput(eveningContainer, true);
            noonContainer.querySelector('input').checked = true;
        } else {
            handleDisableInput(eveningContainer, false); 
            eveningContainer.querySelector('input').checked = true;
            noonContainer.querySelector('input').checked = false;
            seeSchedules(evening);
        }
        if (noon.length == 0) {
            handleDisableInput(noonContainer, true);
        } else {
            handleDisableInput(noonContainer, false);
        }
        
        if (noon.length == 0 && evening.length == 0) {
            errorContainer.innerHTML = 'Nous sommes fermés à cette date.'
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
    const widthForm = document.querySelector('form').offsetWidth + 'px';
    let activeCard;

    for (let card of cards) {
        if (card.classList.contains('active')) {
            activeCard = card;
        }
    }

    let index = activeCard.dataset.index;

    if (index == 0) {
        let error = false;
        const button = e.target.parentElement;
        const slide = button.dataset.card;
    
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
        
        gridContainer.style.transform = 'translateX(-'+ widthForm + ')';

        activeCard.classList.remove('active');
        const newActiveCard = activeCard.nextElementSibling;
        newActiveCard.classList.add('active');

        handleSecondCard(newActiveCard);
        return;
    }
    if (index == 1)
    {
    }
}