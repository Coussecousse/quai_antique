
window.addEventListener("load", () => {
    time = 0;
    section = document.querySelector(".section_card");
    square = document.querySelector(".box");
    cardContainer = document.querySelector(".container-card");
    console.log(cardContainer);

    console.log("coucou");
    setTimeout(() => {
        switch(section.id) {
            case "section-starter":
                square.classList.add('growing');
                break;
        }
    }, time+=150);
    setTimeout(() => {
        cardContainer.classList.remove("blank");
    }, time += 650);

})