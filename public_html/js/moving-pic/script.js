const container = document.querySelector("#moving-pic");

const imagesClass = ['image_1', 'image_2', 'image_3','image_4', 'image_5'];

let page = location.pathname;
page = page.split("/");

switch(page[1]) {
    case 'reservation':
        changeImage('image_1', 0);
        break;
    case 'contact':
        changeImage('image_5', imagesClass.length - 1);
        break;
    case 'connexion': 
        changeImage('image_3',2);
        break;
    case 'inscription':
        changeImage('image_2', 1);
        break;
}

function changeImage(className, index) {
    container.classList.add(className);
    let currentClass = className;

    setTimeout(() => {
        setInterval(() => {

            container.classList.contains('image-coming') ? 
            container.classList.replace('image-coming', 'image-leaving') : 
            container.classList.add('image-leaving');

            setTimeout(() => {
                container.classList.remove(currentClass);
                if (index == imagesClass.length - 1) {
                    currentClass = imagesClass[0];
                    index = 0;
                } else {
                    currentClass = imagesClass[index += 1];
                }
                container.classList.add(currentClass);
                container.classList.replace('image-leaving', 'image-coming')
            }, 1000);
        }, 6000)
    }, 0);
}