function showTemplate(e) {
    e.preventDefault();

    const caret = e.target;
    const template = caret.offsetParent.nextElementSibling;

    if (caret.classList.contains('fa-caret-down')) {
        caret.classList.add('hide');
        caret.nextElementSibling.classList.remove('hide');

        template.classList.remove('hide');
    } else {
        caret.classList.add('hide');
        caret.previousElementSibling.classList.remove('hide');

        template.classList.add('hide');
    }
}

document.addEventListener('scroll', () => {
    let posY = window.pageYOffset;

    const menus = document.querySelector('.templates').children;
    const activeMenus = [];
    for (let menu of menus) {
        let activeButton = menu.querySelector('.fa-caret-up');
        if (!activeButton.classList.contains('hide'))  {
            const posYMenu = menu.getBoundingClientRect()
            activeMenus.push([menu, posYMenu]);
        }
    }
    if (activeMenus.length >= 1) {
        for (let menu of activeMenus) {
            const stickyElement = menu[0].querySelector('.get_sticky');
            if (menu[1].y < 1) {
                stickyElement.style.position = "sticky";
                stickyElement.style.zIndex = 10;
                stickyElement.style.padding = '.5rem 1rem';
                stickyElement.style.background = 'rgb(111 91 62)';
            } else {
                stickyElement.style.position = 'block';
                stickyElement.style.padding = '';
                stickyElement.style.background = '';
            }
        }
    }
})