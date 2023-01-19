cardContainer = document.querySelector(".container-card");
let section = document.querySelector(".section_card");
let square = document.querySelector(".box");

window.addEventListener("load", () => {
    let time = 0;

    setTimeout(() => {
        square.classList.add('growing');
    }, time+=150);
    
    setTimeout(() => {
        cardContainer.classList.remove("blank");
    }, time += 650);

})
let button = document.querySelector("#back-button");

button.addEventListener("click", e => {
    e.preventDefault();
    let time = 0

    cardContainer.classList.add("blank");
    setTimeout(() => {
        square.style.height = 'clamp(6.25rem, 0.134rem + 16.31vw, 14.813rem)';
    }, time+=400);

    setTimeout(() => {
        window.location.href = button.children[0].href;
    }, time+=500);
})