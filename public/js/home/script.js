const activePic = document.querySelector('div[data-active="true"]');

const rect = activePic.getBoundingClientRect();

const leftPosition = rect.left;

const widthElement = activePic.clientWidth;
const middleElement = leftPosition + widthElement/2;

const screenWidth = window.innerWidth;

const middleScreen = screenWidth/2;
const soustraction = middleElement - middleScreen;
const carousel = document.querySelector(".carousel_pics");
carousel.style.transform = soustraction > 0 ? "translateX(calc(-" + (soustraction) + 'px))': "translateX(calc(" + (soustraction * -1)  + 'px))';
for (let child of carousel.children) {
    child.classList.add('duration-150');
}

function centerElement() {
    const activePic = document.querySelector('div[data-active="true"]');

    const rect = activePic.getBoundingClientRect();

    const leftPosition = rect.left;

    const widthElement = activePic.clientWidth;
    const middleElement = leftPosition + widthElement/2;

    const screenWidth = window.innerWidth;

    const middleScreen = screenWidth/2;
    const soustraction = middleElement - middleScreen;

    const carousel = document.querySelector(".carousel_pics");
    carousel.style.transform = soustraction > 0 ? "translateX(-" + (soustraction) + 'px)': "translateX(" + (soustraction * -1)  + 'px)';
}

function centerNextElement() {
    const carousel = document.querySelector(".carousel_pics");
    
    const style = window.getComputedStyle(carousel);
    const transform = new WebKitCSSMatrix(style.transform);
    
    const active = document.querySelector("div[data-active='true']");
    let next;
    if (active.dataset.image == carousel.children.length - 1) {
        next = document.querySelector("div[data-image='0']");
    } else {
        next = active.nextElementSibling;
    }
    
    active.dataset.active = false;
    next.dataset.active = true;
    
    const rect = next.getBoundingClientRect();
    const leftPosition = rect.left;
    
    const widthElement = next.clientWidth;
    const middleElement = leftPosition + widthElement/2;
    
    const screenWidth = window.innerWidth;
    
    const middleScreen = screenWidth/2;
    
    if (next.dataset.image == carousel.children.length - 1) {
        const addition = middleElement - leftPosition - middleScreen + 16;
        carousel.style.transform = addition > 0 ? "translateX(" + addition + "px)" : "translateX(" + ((addition * -1)) + "px)";
    } else {
        const soustraction = middleElement - middleScreen;
        carousel.style.transform = soustraction > 0 ? "translateX(" + (transform.e - soustraction) + 'px)': "translateX(" + (transform.e - (soustraction * -1))  + 'px)';
    }
    
    if (active.dataset.image == 0 ) {
        const previousToLastActive = carousel.children[carousel.children.length - 1];
        previousToLastActive.classList.add('invisible', 'opacity-0');
    } else {
        const previousToLastActive = active.previousElementSibling;
        previousToLastActive.classList.add("invisible", 'opacity-0');   
    }

    if (next.dataset.image == carousel.children.length - 1 ) {
        const nextToNewActive = document.querySelector("div[data-image='0']");
        nextToNewActive.classList.remove("invisible", "opacity-0");
    } else {
        const nextToNewActive = next.nextElementSibling;
        nextToNewActive.classList.remove("invisible", "opacity-0");
    }

}

window.addEventListener("resize", () => {
    const carousel = document.querySelector(".carousel_pics");

    const style = window.getComputedStyle(carousel);
    const transform = new WebKitCSSMatrix(style.transform);

    const activePic = document.querySelector('div[data-active="true"]');

    const rect = activePic.getBoundingClientRect();

    const leftPosition = rect.left;

    const widthElement = activePic.clientWidth;

    const middleElement = leftPosition + widthElement/2;

    const screenWidth = window.innerWidth;

    const middleScreen = screenWidth/2;

    const soustraction = middleScreen - middleElement ;

    carousel.style.transform = soustraction > 0 ? "translateX(" + (transform.e + soustraction) + 'px)': "translateX(" + (transform.e - (soustraction * -1))  + 'px)';
});

const buttons = document.querySelector("#home_buttons");

buttons.addEventListener("click", e => {

    if (e.target.parentElement.ariaLabel == "gauche") {
        centerNextElement();
    } else if (e.target.parentElement.ariaLabel == "droite") {
        
    } else  {

    }
});