aArray = document.querySelectorAll('.diamonds li a');
content = document.querySelector(".main-content");
button = document.querySelector("#card-booking");

aArray.forEach(a => {
    a.addEventListener("click", e => {
        e.preventDefault();
        console.log(a);
        for (let element of aArray) {
            if (element !== a ){
                switch (a.parentElement.id) {
                    case "card-starter":
                        element.parentElement.classList.add("bottom");
                        break;
                    case "card-main": 
                        element.parentElement.classList.add("right");
                        break;
                    case "card-dessert": 
                        element.parentElement.classList.add("left");
                        break;
                }
            } else {
                element.parentElement.classList.add('active');
            }
            button.style.opacity = "0";
            setTimeout(() => {
                a.parentElement.classList.add("moving");
            }, 300);
            setTimeout(() => {
                window.location.href = a.href;
            }, 800);
        }
    })
});