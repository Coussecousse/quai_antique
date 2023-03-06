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
    return false;
}
function addingMargin(element) {
    if (count > 1) {
        element.parentElement.style.marginBottom = '.8rem';
    }
}
function removeMargin(element) {
    element.parentElement.style.marginBottom = '0';
}

