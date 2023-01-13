aArray = document.querySelectorAll('.diamonds li a');
content = document.querySelector(".main-content");

aArray.forEach(a => {
    a.addEventListener("click", e => {
        e.preventDefault();
        console.log(a);
        for (let element of aArray) {
            if (element !== a ){
                element.parentElement.classList.add("black");
            } else {
                element.parentElement.classList.add('active');
            }
            setTimeout(() => {
                a.parentElement.classList.add("moving");
                console.log(a.parentElement.id);
                switch(a.parentElement.id) {
                    case "card-starter":
                        content.style.transform = "translateY(50%)";
                }
            }, 300);
            setTimeout(() => {
                window.location.href = a.href;
            }, 800);
        }
    })
});