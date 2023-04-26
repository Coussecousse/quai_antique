function changeText() {
  const list = document.querySelector("#text-move");
  let active;
  for (let children of list.children) {
    if (children.classList.contains('active')){
      active = children;
      break;
    }
  }
  let next;
  if (active == list.children[2]) {
    next = list.children[0];
  } else {
    next = active.nextElementSibling;
  }

  active.classList.remove('active');
  
  active.classList.contains('down') ? active.classList.replace('up', 'down') : active.classList.add('down');
  setTimeout(() => {
    next.classList.add('active');
    next.classList.add("up");
  }, 200);
}

setInterval(() => {
  changeText();
}, 2500);

// ____ CAROUSEL ____
function giveSoustraction(activePic, last = false, previous = false) {
  const rect = activePic.getBoundingClientRect();

  const leftPosition = rect.left;

  const widthElement = activePic.clientWidth;

  const middleElement = leftPosition + widthElement / 2;

  const screenWidth = window.innerWidth;

  const middleScreen = screenWidth / 2;
  if (last) {
    return middleElement - leftPosition - middleScreen;
  } else if (previous) {
    return middleElement - middleScreen;
  } else {
    return middleScreen - middleElement;
  }
}
function transformCarousel(carousel, calcul) {
  carousel.style.transform = "translateX(" + calcul + "px)";
}

// ______________
const activePic = document.querySelector('div[data-active="true"]');

const soustraction = giveSoustraction(activePic);

const carousel = document.querySelector(".carousel_pics");
transformCarousel(carousel, soustraction);
// ________________

function giveTransform(carousel) {
  const style = window.getComputedStyle(carousel);
  return new WebKitCSSMatrix(style.transform);
}

window.addEventListener("resize", () => {
  setTimeout(() => {
    const carousel = document.querySelector(".carousel_pics");

    const transform = giveTransform(carousel);

    const activePic = document.querySelector('div[data-active="true"]');

    const soustraction = giveSoustraction(activePic);

    transformCarousel(carousel, transform.e + soustraction);
  }, 500);
});

function getElement(image) {
  const carousel = document.querySelector(".carousel_pics");

  const transform = giveTransform(carousel);

  let activePic;
  for (let pic of carousel.children) {
    if (pic.dataset.image == image) {
      activePic = pic;
      activePic.dataset.active = "true";
      activePic.classList.contains("invisible")
        ? activePic.classList.remove("invisible", "opacity-0")
        : null;
    } else {
      pic.dataset.active = "false";
      if (!pic.classList.contains("invisible")) {
        if (activePic) {
          if (
            pic == activePic.previousElementSibling ||
            pic == activePic.nextElementSibling
          ) {
            continue;
          }
        }
        pic.classList.add("invisible", "opacity-0");
      }
      !pic.classList.contains("invisible") &&
      (pic != activePic.previousElementSibling ||
        pic != activePic.nextElementSibling)
        ? pic.classList.add("invisible", "opacity-0")
        : null;
    }
  }

  activePic.previousElementSibling
    ? activePic.previousElementSibling.classList.remove(
        "invisible",
        "opacity-0"
      )
    : carousel.children[carousel.children.length - 1].classList.remove(
        "invisible",
        "opacity-0"
      );
  activePic.nextElementSibling
    ? activePic.nextElementSibling.classList.remove("invisible", "opacity-0")
    : carousel.children[0].classList.remove("invisible", "opacity-0");

  const soustraction = giveSoustraction(activePic);

  transformCarousel(carousel, transform.e + soustraction);
}
function alignButtonWithImage(image) {
  const buttons = document.querySelectorAll(".carousel_button");
  buttons.forEach((button) => {
    button.classList.contains("active") && button.dataset.button != image
      ? button.classList.remove("active")
      : null;
    button.dataset.button == image ? button.classList.add("active") : null;
  });
}

function centerNextElement() {
  const carousel = document.querySelector(".carousel_pics");

  const transform = giveTransform(carousel);

  const active = document.querySelector("div[data-active='true']");

  let next;
  if (active.dataset.image == carousel.children.length - 1) {
    next = document.querySelector("div[data-image='0']");
  } else {
    next = active.nextElementSibling;
  }

  active.dataset.active = false;
  next.dataset.active = true;

  if (next.dataset.image == carousel.children.length - 1) {
    const soustraction = giveSoustraction(next, true);
    carousel.style.transform =
      soustraction > 0
        ? "translateX(" + soustraction + "px)"
        : "translateX(" + soustraction * -1 + "px)";

  } else {
    const soustraction = giveSoustraction(next);
    transformCarousel(carousel, transform.e + soustraction);
  }
  next.classList.contains('invisible') ? 
  next.classList.remove('invisible', 'opacity-0') : 
  null;

  if (active.dataset.image == 0) {
    const previousToLastActive =
    carousel.children[carousel.children.length - 1];
    previousToLastActive.classList.add("invisible", "opacity-0");
  } else {
    const previousToLastActive = active.previousElementSibling;
    previousToLastActive.classList.add("invisible", "opacity-0");
  }
  if (next.dataset.image == carousel.children.length - 1) {
    const nextToNewActive = carousel.children[0];
    nextToNewActive.classList.remove('invisible', 'opacity-0');

    const previous = next.previousElementSibling;
    previous.classList.add('invisible', 'opacity-0');
  } else if(next.dataset.image != carousel.children.length - 2 ) {
    const nextToNewActive = next.nextElementSibling;
    nextToNewActive.classList.remove('invisible', 'opacity-0');
  }

  alignButtonWithImage(next.dataset.image);
}

function centerPreviousElement() {
  const carousel = document.querySelector(".carousel_pics");

  const transform = giveTransform(carousel);

  const active = document.querySelector("div[data-active='true']");

  let previous;
  if (active.dataset.image == 0) {
    previous = carousel.children[carousel.children.length - 1];
  } else {
    previous = active.previousElementSibling;
  }

  active.dataset.active = false;
  previous.dataset.active = true;

  if (previous.dataset.image == carousel.children.length - 1) {
    const soustraction = giveSoustraction(previous, true) + 16;
    carousel.style.transform =
      soustraction > 0
        ? "translateX(" + soustraction + "px)"
        : "translateX(" + soustraction * -1 + "px)";
  } else {
    const soustraction = giveSoustraction(previous, false, true);
    transformCarousel(carousel, transform.e - soustraction);
  }

  previous.classList.contains('invisible') ? 
  previous.classList.remove('invisible', 'opacity-0') : 
  null;

  if (active.dataset.image == carousel.children.length - 1) {
    const nextToLastActive = document.querySelector("div[data-image='0']");
    nextToLastActive.classList.add("invisible", "opacity-0");
  } else if (previous.dataset.image == carousel.children.length - 2) {
    const nextToNewActive = previous.nextElementSibling;
    next.classList.add("invisible", "opacity-0");
  } else {
    const nextToLastActive = active.nextElementSibling;
    nextToLastActive.classList.add("invisible", "opacity-0");
  }

  if (previous.dataset.image == 0) {
    const previousToNewActive = carousel.children[carousel.children.length - 1];
    previousToNewActive.classList.remove("invisible", "opacity-0");
  } else if (previous.dataset.image != carousel.children.length - 1) {
    const previousToNewActive = previous.previousElementSibling;
    previousToNewActive.classList.remove("invisible", "opacity-0");
  } 
  alignButtonWithImage(previous.dataset.image);
}

const buttons = document.querySelector("#home_buttons");

buttons.addEventListener("click", (e) => {
  if (e.target.parentElement.ariaLabel == "Gauche") {
    centerPreviousElement();
  } else if (e.target.parentElement.ariaLabel == "Droite") {
    centerNextElement();
  } else if (e.target.classList.contains("carousel_button")) {
    for (let button of buttons.children) {
      button.classList.contains("active")
        ? button.classList.remove("active")
        : null;
    }
    e.target.classList.add("active");
    const image = e.target.dataset.button;
    getElement(image);
  }
});
