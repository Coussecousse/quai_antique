const menuButton = document.querySelector("#menu-hamburger");
const header = document.querySelector("header");

menuButton.addEventListener("click", () => {

    menuButton.disabled = true;
    if (menuButton.children[0].classList.contains('close')) {
        menuButton.children[0].classList.replace('close', 'open');
        header.style.position = "fixed";
    } else {
        menuButton.children[0].classList.replace('open', 'close');
        header.style.position = "";
    }
    
    const menu = document.querySelector("#header-menu");
        if (!menu.classList.contains('open')) {
        menu.classList.remove("hidden");
        setTimeout(() => {
            menu.classList.add("open");
        }, 100);
    } else {
        menu.classList.remove("open");
        setTimeout(() => {
            menu.classList.add('hidden');
        }, 1000);
    }
    setTimeout(() => {
        menuButton.disabled = false;
    }, 600);
})

// Sticky menu
const sticky = header.offsetTop;

window.onscroll = () => {
    if (window.pageYOffset >= sticky + 1) {
        header.classList.add("header-fixed")
    } else {
        header.classList.remove("header-fixed");
    }
}

