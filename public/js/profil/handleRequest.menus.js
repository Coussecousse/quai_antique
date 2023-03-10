function addDeleteButton(parent) {
    const element = parent.querySelector('.name');
    const removeButton = document.createElement('button');
    
    const i = document.createElement('i');
    i.classList.add('fa-solid', 'fa-trash', 'h-full', 'button-full', 'bg-primary/50', 'button_profil');
    
    removeButton.appendChild(i);
    
    if (parent.parentElement.classList.contains('compositions')) {
        removeButton.onclick = e => deleteComposition(e);
    } else {
        removeButton.onclick = e => deleteOffer(e);
    }
    element.appendChild(removeButton);
}
const addFormToCollection = (e) => {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
    
    const item = document.createElement('li');
    console.log('click');
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
document
    .querySelectorAll('ul.tags li')
    .forEach((tag) => {
        addTagFormDeleteLink(tag)
    })

function deleteComposition(e) {
    e.preventDefault();
    const element = e.target.parentElement.parentElement;
    // remove the li for the tag form
    element.remove();
}
function deleteOffer(e) {
    e.preventDefault();
    const element = e.target.parentElement.parentElement.parentElement;
    element.remove();
}