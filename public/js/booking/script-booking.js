// const buttonStepOne = document.querySelector("#step-one");
// const formInformations = document.querySelector("#informations");

// buttonStepOne.addEventListener("click", e => {
//     e.preventDefault();
//     const nameElement = document.querySelector("#informations_name");
//     const allergiesElement = document.querySelector("#informations_allergiesOther")

//     const name = nameElement.value;
//     const allergies_other = allergiesElement.value;
    
//     function checkIfNumber(chaîne) {
//         if (/[0-9]/.test(chaîne)) {
//             return "Caractères non autorisés.";
//         } else {
//             return true;
//         }
//     }
//     function checkTooLong(chaîne, max) {
//         if (chaîne.length > max) {
//             return "Chaîne de caractères trop longue.";
//         } else {
//             return true;
//         }
//     }
//     function checkTooSmall(chaîne, min) {
//         if (chaîne.length < min) {
//             return "Chaîne de caractères trop petite.";
//         } else {
//             return true;
//         }
//     }
    
    
//     function checkErrorFirstStep() {
//         const nameError = document.querySelector("#name_error");
//         const checkersName = [checkIfNumber(name), checkTooLong(name, 100), checkTooSmall(name, 5)];
        
//         for (let situation of checkersName) {
//             if (situation !== true){
//                 nameError.textContent = situation;
//                 nameError.classList.add("active");
//                 nameElement.classList.add('error');
//                 return false;
//             } else {
//                 nameElement.classList.contains("active") ? nameError.classList.remove('error') : null;
//                 nameError.classList.contains("active") ? nameError.classList.remove('active') : null;
//             }
//         }

//         const allergiesError = document.querySelector("#allergies_error");
//         const checkersAllergies = [checkIfNumber(allergies_other), checkTooLong(allergies_other, 255)];
//         for (let situation of checkersAllergies) {
//             if (situation !== true){
//                 allergiesError.textContent = situation;
//                 allergiesError.classList.add("active");
//                 allergiesElement.classList.add('error');
//                 return false;
//             } else {
//                 allergiesElement.classList.contains("error") ? allergiesError.classList.remove('error') : null;
//                 allergiesError.classList.contains("active") ? allergiesError.classList.remove('active') : null;
//             }
//         }
//         return true;
//     }
//     if (checkErrorFirstStep()) {
//         // GO STEP 2
//     };

// })   

// formInformations.addEventListener('submit', async (e) => {
//     e.preventDefault();
//     const formData = new FormData(e.target);
//     console.log(formData)
//     const response = await fetch("{{ path('reserver') }}", {
//         method: "POST", 
//         body : formData,
//     });
//     console.log(response);
//     const jsonData = await response.json();
// });
