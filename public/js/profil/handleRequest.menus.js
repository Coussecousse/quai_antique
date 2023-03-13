function addDeleteButton(parent) {
    const element = parent.querySelector('.name');
    const removeButton = document.createElement('button');
    
    const i = document.createElement('i');
    i.classList.add('fa-solid', 'fa-trash', 'button-full', 'bg-primary/50', 'button_profil');
    
    removeButton.appendChild(i);

    if (parent.parentElement.classList.contains('compositions')) {
        removeButton.onclick = e => deleteComposition(e);
    } else {
        removeButton.onclick = e => deleteOffer(e);
    }
    element.appendChild(removeButton);

    document
    .querySelectorAll('.add_item_link')
    .forEach(btn => {
        btn.addEventListener("click", addFormToCollection)
    });
}
function addFormToCollection(e) {
    let collectionHolder;

    if (e.target.dataset.collectionHolderClass == "compositions") {
        collectionHolder = e.target.parentElement.parentElement.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
    } else {
        collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
    }
    const item = document.createElement('li');

    item.innerHTML = collectionHolder
    .dataset
    .prototype
    .replace(
        /__name__/g,
        collectionHolder.dataset.index
        );
        
        collectionHolder.appendChild(item);
        
        collectionHolder.dataset.index++;

    addDeleteButton(item);
};
document
    .querySelectorAll('.add_item_link')
    .forEach(btn => {
        btn.addEventListener("click", addFormToCollection)
    });

function deleteComposition(e) {
    e.preventDefault();
    const element = e.target.parentElement.parentElement;
    // remove the li for the tag form
    element.remove();
}
function deleteOffer(e) {
    e.preventDefault();
    const element = e.target.parentElement.parentElement.parentElement.parentElement;
    element.remove();
}
function setElementAttribute(element, attributs) {
    for (let key in attributs) {
        element.setAttribute(key, attributs[key]);
    };
}

function modifyElement(element, index, changement) {
    element.value = '';
    element.name = changement + index;
    element.id = changement + index;

    if (element.previousElementSibling.children.length == 0) {
        element.previousElementSibling.setAttribute('for', changement + index);
    } else {
        element.previousElementSibling.children[0].setAttribute('for', changement + index);
    }
}
function addOffer(e) {
    const offerModel = document.querySelector('.offer_model');
    const newOffer = offerModel.cloneNode(true);

    const ul = document.querySelector('.modify_offers');
    const index = ul.children.length + 1;

    const title = newOffer.querySelector('.offer_title_model');
    modifyElement(title, index, 'offer_title_');
    const conditions = newOffer.querySelector('.offer_conditions_model');
    modifyElement(conditions, index, 'offer_conditions_');

    const buttons = newOffer.querySelectorAll('.add_item_link');
    buttons.forEach( button => {
        button.classList.remove('add_item_link');
        button.dataset.collectionHolderClass == "compositions" ?
            button.onclick = e => addComposition(e) :
            null;
    })

    const olCompositions = newOffer.querySelector('.compositions_model');
    olCompositions.dataset.index = '';
    olCompositions.dataset.prototype = '';

    const compositions = olCompositions.querySelectorAll('.offer_model');
    compositions.forEach( (composition, i) => {
        composition.value = '';
        composition.name = 'offer_'+ offerModel.parentElement.children.length +'_composition_title_'+ i;
        composition.id =  'offer_'+ offerModel.parentElement.children.length +'_composition_title_'+ i;
        composition.previousElementSibling.children[0].setAttribute('for', 'offer_composition_title_'+ i);
    })

    // Add delete button
    const elementAddDeleteButon = newOffer.querySelectorAll('.name');
    elementAddDeleteButon.forEach( (element, index) => {
        if (index == 1) {
            return;
        }
        const removeButton = document.createElement('button');
    
        const i = document.createElement('i');
        i.classList.add('fa-solid', 'fa-trash', 'button-full', 'bg-primary/50', 'button_profil');
        
        removeButton.appendChild(i);
        if (element.parentElement.classList.contains('compositions_model')) {
            removeButton.onclick = e => deleteComposition(e);
        } else {
            removeButton.onclick = e => deleteOffer(e);
        }

        element.appendChild(removeButton);
    })

    ul.appendChild(newOffer);
}

function addComposition(e) {
    const parent = e.target.parentElement.parentElement.children[3];
    const offerModel = document.querySelector('#offer_0_composition_title_0').parentElement.parentElement;
    const newOffer = offerModel.cloneNode(true);

    const input = newOffer.querySelector('input');
    let inputName = input.name.split('_');
    inputName.pop();
    
    inputName = inputName.join('_');    
    modifyElement(input, parent.length, inputName + '_');
    if (parent.children.length !== 0) {
        const removeButton = document.createElement('button');
        
        const i = document.createElement('i');
        i.classList.add('fa-solid', 'fa-trash', 'button-full', 'bg-primary/50', 'button_profil');
        
        removeButton.appendChild(i);
        removeButton.onclick = e => deleteComposition(e);
        newOffer.appendChild(removeButton);
    }

    parent.appendChild(newOffer);
}