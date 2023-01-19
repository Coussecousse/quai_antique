aArray = document.querySelectorAll('.diamonds li a');
content = document.querySelector(".main-content");

aArray.forEach(a => {
    a.addEventListener("click", e => {
        e.preventDefault();
        console.log(a);
        for (let element of aArray) {
            if (element !== a ){
                if (element.parentElement.id === "card-dessert") {
                    element.parentElement.classList.add("bottom0");
                } else {
                    element.parentElement.classList.add("bottom100");
                }
            } else {
                element.parentElement.classList.add('active');
            }
            setTimeout(() => {
                a.parentElement.classList.add("moving");
                console.log(a.parentElement.id);
                switch(a.parentElement.id) {
                    case "card-starter":
                        content.style.transform = "translateY(25%)";
                }
            }, 300);
            // setTimeout(() => {
            //     window.location.href = a.href;
            // }, 800);
        }
    })
});