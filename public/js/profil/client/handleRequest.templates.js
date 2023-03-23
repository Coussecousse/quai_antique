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

function setTemplate(e) {
    console.log('click')
    
    const form = e.target;
    const id = form.dataset.id;
    
    const title = form[0].value;
    const name = form[4].value;
    const places = form[5].value;
    if (title === '' || name === '' || places === '' || places > 20 ||
        name.length < 2 || name.length > 100 || title.length < 2 || title.length > 150) {
        return;
    }
    
    e.preventDefault();

    const allergies = [];

    for (let i = 6; i < 20; i++) {
        if (form[i].checked) {
            allergies.push(form[i].value);
        }
    }
    const xhr = new XMLHttpRequest();
    xhr.onload = function() {
        let url = window.location.origin + window.location.pathname;

        if (this.readyState == 4 && this.status == 200) {
            const response = JSON.parse(xhr.responseText);

            switch(response.result){
                case 'success':
                    window.location = url + "?result=success";
                    break;
                case 'error_pattern' :
                    window.location = url + "?result=error_pattern";
                    break;
                default : 
                    window.location = url + "?result=error";
                    break;
            }
        } else {
            window.location = url + "?result=error";
        }
    }
    xhr.open('POST', '/client/profil/{page_down}');
    xhr.setRequestHeader('Content-Type', 'application/json');

    const data = {
        template_id : id,
        template_title : title,
        template_name : name,
        template_places : places,
        template_allergies : allergies
    }
    console.log(xhr);
    console.log('send')
    xhr.send(JSON.stringify(data));
}