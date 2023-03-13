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

function showMenu(e) {

    e.preventDefault();

    const caret = e.target;
    if (caret.classList.contains('fa-caret-down')) {
        caret.classList.add('hide');
        caret.nextElementSibling.classList.remove('hide');

        const divParent = caret.parentElement.parentElement.parentElement;
        divParent.classList.add('after');

        const buttonAffOffer = divParent.children[1].classList.remove('hide');

        const listOffer = divParent.nextElementSibling;
        listOffer.classList.remove('hide');
    } else {
        caret.classList.add('hide');
        caret.previousElementSibling.classList.remove('hide');

        const divParent = caret.parentElement.parentElement.parentElement;
        divParent.classList.remove('after');

        const buttonAffOffer = divParent.children[1].classList.add('hide')

        
        const listOffer = divParent.nextElementSibling;
        listOffer.classList.add('hide');
    }
}

function setMenu(e) {
    const form = e.target;
    const id = (form.id.split('_'))[1];

    const titleMenu = form[0];
    const titleMenuValue = titleMenu.value;

    if (titleMenuValue === '') {
        return;
    }
    e.preventDefault;
    const offers = form.querySelector('ul.modify_offers').children;
    let offerIndex = 0;
    const offersData = [];
    for (let offer of offers) {
        const offerTitle = offer.querySelector('input[name=offer_title_' + offerIndex);
        const titleValue = offerTitle.value;
        if (titleValue === '') {
            return;
        }
        offersData.push(titleValue);
        
        const conditions = offer.querySelector('input[name=offer_conditions_'+ offerIndex);
        const conditionsValue = conditions.value;
        offersData.push(conditionsValue);

        const compositions = offer.querySelector('ol.compositions').children;
        let compositionIndex = 0;
        const compositionsData = [];
        for (let composition of compositions) {
            const compositionTitle = composition.querySelector('input[name=offer_'+ offerIndex +'_composition_title_'+ compositionIndex);
    
            const compositionValue = compositionTitle.value;
            if (compositionValue === '') {
                return;
            }
            compositionIndex++;
            compositionsData.push(compositionValue);
        }
        offersData.push(compositionsData);
        
        const price = offer.querySelector('input[name=price_'+ offerIndex);
        const priceValue = price.value;
        if (priceValue === '') {
            return;
        }
        offersData.push(priceValue);
        offerIndex++;
    }   

    e.preventDefault();

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
    xhr.open('POST', '/admin/profil/{page_up}/{page_down}/{page_three}');
    xhr.setRequestHeader('Content-Type', 'application/json');

    const data = {
        id : id,
        menuTitle : titleMenuValue,
        offers : offersData
    }
    
    xhr.send(JSON.stringify(data));
}