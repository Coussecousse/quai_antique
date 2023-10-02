function addError(element, error) {
    console.log('yop')
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
document.addEventListener('scroll', () => {
    let posY = window.pageYOffset;

    const button = document.querySelector('#button-up');
    if (posY > 1100) {
        if (button.classList.contains('hide')){
            button.classList.remove('hide');
            button.classList.replace('slow-opacity-reverse', 'slow-opacity-in');
        }
        button.addEventListener('click', () => {
            window.scrollTo({top: 0, behavior: 'smooth'})
        })
    }
    if (posY < 1100) {
        if (!button.classList.contains('hide')) {
            button.classList.replace('slow-opacity-in', 'slow-opacity-reverse');
            setTimeout(() => {
                button.classList.add('hide');
            }, 150);
        }
    }
})

