// const buttonAddImage = document.querySelector('#add_image');
// count = 2;

// buttonAddImage.addEventListener('click', e => addImage(e));

// function addImage(e) {

//     const fileInput = document.querySelector('#file');
//     const titleInput = document.querySelector('#title');

//     let file = fileInput.files;
//     let title = titleInput.value;

//     if (file.length == 0 || title == '' || title.length < 2 || title.length > 80) {
//         return;
//     } 
//     file = file[0];
//     e.preventDefault();

//     const errorFile = document.querySelector('#error_file');
//     const errorTitle = document.querySelector('#error_title');

//     if (file.length > 5000000) {
//         error = addError(errorFile, 'Fichier trop lourd.');
//         addingMargin(fileInput);
//     } else {
//         error = removeError(errorFile);
//         removeMargin(fileInput)
//     }

//     if (title.length < 2 || title.length > 80) {
//         error = addError(errorTitle, 'Titre non valide.');
//         addingMargin(titleInput);
//     } else {
//         error = removeError(errorTitle);
//         removeMargin(titleInput)
//     }
//     if (error) {
//         return;
//     }

//     buttonAddImage.disabled = true;

//     const xhr = new XMLHttpRequest();
//     xhr.onload = function() {
//         let url = window.location.origin + window.location.pathname;
//         console.log('hoy');
//         if (this.readyState == 4 && this.status == 200) {

//             // const response = JSON.parse(xhr.responseText);

//             console.log('success');
//         }
//     }
    
//     xhr.open('POST', '/admin/profil/{page_up}/{page_down}');
//     xhr.setRequestHeader('Content-type','multipart/form-data');
    
//     const formData = new FormData();
//     formData.append("image", file);
//     formData.append("title", title);

//     xhr.send(formData);
// }