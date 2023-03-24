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
function nextSlide(e) {
    e.preventDefault();
    let error = false;
    const button = e.target.parentElement;
    const slide = button.dataset.card;
    const nextSlide = slide + 1;

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
    console.log(allergies);

}
