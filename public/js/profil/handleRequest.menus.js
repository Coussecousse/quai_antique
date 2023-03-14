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

    const index = collectionHolder.dataset.index;
    
    const item = document.createElement('li');
    const offers = document.querySelector('.offers').children;
    let prototype;
    if (offers.length > 0 && !collectionHolder.classList.contains('offers') && collectionHolder.dataset.offerId != '0') {
        let offerId;
        if (collectionHolder.dataset.offerId === '') {
            let i = 0;
            for (let offer of offers) {
                if (offer == collectionHolder.parentElement.parentElement) {
                    collectionHolder.dataset.offerId = i;
                    offerId = i;
                }
                i++;
            }
        } else {
            offerId = collectionHolder.dataset.offerId;
        }
        
        prototype = `
        <li class="name flex gap-2" >
        <div class="flex-1">
        <label hidden for="menus_offers_${offerId}_description___name__"></label> 
        <input type="text" id="menus_offers_${offerId}_description___name__" name="menus[offers][${offerId}][description][__name__]" required="required" class="offer_model pr-2 bg-primary/20 input_whitePlaceholder input_withoutIcon" placeholder="Plat..." />
        <small class="form_error">
        
        </small>
        </div>
        </li>`
    } else {
        prototype = collectionHolder.dataset.prototype;
    }
    const newForm = prototype.replace(/__name__/g, index);

    item.innerHTML = newForm;
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
    let element;
    if (e.target.classList.contains('button-deleteOffer')) {
        element = e.target.parentElement.parentElement.parentElement;
    } else {
        element = e.target.parentElement.parentElement.parentElement.parentElement;
    }
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
    const form = e.target.form;
    const indexMenu = e.target.dataset.indexMenu;
    const indexOffer = e.target.dataset.indexOffer;

    const offerModel = document.querySelector('.offer_model');
    const newOffer = offerModel.cloneNode(true);
    
    newOffer.dataset.indexMenu = indexMenu;

    const formMenu = e.target.parentElement.parentElement;
    const ul = formMenu.querySelector('.modify_offers');
    const index = ul.children.length + 1;

    const title = newOffer.querySelector('.offer_title_model');
    modifyElement(title, index - 1, 'menu_'+ indexMenu +'_offer_title_');

    const conditions = newOffer.querySelector('.offer_conditions_model');
    modifyElement(conditions, index - 1, 'menu_'+ indexMenu +'_offer_conditions_');

    const buttons = newOffer.querySelectorAll('.add_item_link');
    buttons.forEach( button => {
        button.classList.remove('add_item_link');
        if (button.dataset.collectionHolderClass == "compositions") {
            button.onclick = e => addComposition(e);
            button.dataset.indexMenu = form[4].dataset.indexMenu;
            button.dataset.indexOffer = button.parentElement.nextElementSibling.children.length - 1;
        }
    })

    const olCompositions = newOffer.querySelector('.compositions_model');
    olCompositions.dataset.index = '';
    olCompositions.dataset.prototype = '';

    const compositions = olCompositions.querySelectorAll('.offer_model');
    compositions.forEach( (composition, i) => {
        composition.value = '';
        composition.name = 'menu_'+ indexMenu +'_offer_'+ indexOffer +'_composition_title_'+ i;
        composition.id = 'menu_'+ indexMenu +'_offer_'+ indexOffer +'_composition_title_'+ i;
        composition.previousElementSibling.setAttribute('for', 'menu_'+indexMenu+'_offer_'+ indexOffer +'composition_title_'+ i);
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

    const price = newOffer.querySelector('#menus_offers_0_price');
    modifyElement(price, index - 1, 'menu_'+ indexMenu +'_price_');
    
    ul.appendChild(newOffer);
}

function addComposition(e) {
    const indexMenu = e.target.dataset.indexMenu;
    const indexOffer = e.target.dataset.indexOffer;

    const parent = e.target.parentElement.parentElement.children[3];
    const offerModel = document.querySelector('#menu_0_offer_0_composition_title_0').parentElement.parentElement;
    const newOffer = offerModel.cloneNode(true);

    const input = newOffer.querySelector('input');
  
    input.value = '';
    input.name = "menu_"+ indexMenu + "_offer_" + indexOffer + "_composition_title_" + (parent.children.length);
    input.id = "menu_"+ indexMenu + "_offer_" + indexOffer + "_composition_title_" + (parent.children.length);
    input.previousElementSibling.setAttribute('for', "menu_"+ indexMenu + "_offer_" + indexOffer + "_composition_title_" + (parent.children.length));
    
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
    e.preventDefault();
    if (titleMenuValue === '') {
        return;
    }

    const offers = form.querySelector('ul.modify_offers').children;
    let offerIndex = 0;
    const offersData = [];
    for (let offer of offers) {
        let indexMenu = offer.dataset.indexMenu;

        const offerTitle = offer.querySelector('input[name=menu_'+ indexMenu +'_offer_title_' + offerIndex);
        const titleValue = offerTitle.value;
        if (titleValue === '') {
            return;
        }
        offersData.push(titleValue);
        
        const conditions = offer.querySelector('input[name=menu_'+ indexMenu +'_offer_conditions_'+ offerIndex);
        const conditionsValue = conditions.value;
        offersData.push(conditionsValue);

        const compositions = offer.querySelector('ol.compositions').children;
        let compositionIndex = 0;
        const compositionsData = [];
        for (let composition of compositions) {
            const compositionTitle = composition.querySelector('input[name=menu_'+ indexMenu +'_offer_'+ offerIndex +'_composition_title_'+ compositionIndex);
            const compositionValue = compositionTitle.value;
            if (compositionValue === '') {
                return;
            }
            compositionIndex++;
            compositionsData.push(compositionValue);
        }
        offersData.push(compositionsData);
        
        const price = offer.querySelector('input[name=menu_'+ indexMenu +'_price_'+ offerIndex);

        const priceValue = price.value;
        if (priceValue === '') {
            return;
        }
        offersData.push(priceValue);
        offerIndex++;
    }   

    e.preventDefault();
    
    const button = form.querySelector('button[type=submit]');
    button.disabled = true;

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