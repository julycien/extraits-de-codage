//identification des champs du formulaire
let clicForm = document.getElementById("btnSubmit");
//appel de la fonction de contrôle 'clique()' au clic de validation du formulaire
clicForm.addEventListener("click", clique);
let vendeur = document.getElementById("idvendeur");
let categorie = document.getElementById("idcategorie");
let marque = document.getElementById("idmarque");
let prix = document.getElementById("prixvente");
let couleur = document.getElementById("couleur");
let modal = document.getElementById("myModal");
// Get the <span> element that closes the modal         
let span = document.getElementsByClassName("close")[0];
// When the user clicks on <span> (x), close the modal
span.onclick = function () {
    modal.style.display = "none";
}
// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
//contrôle et génération et apparition du message d'erreur 
let error = document.getElementById("msgError");
let msg = "";
function clique() {
    if (clicForm.value == "creation") {
        //détection des erreurs
        if (vendeur.value == 0 || idcategorie.value == 0 || idmarque.value == 0 || prix.value == 0 || couleur.value == 0) {
            if (vendeur.value == 0) {
                //incrémenter le message d'erreur
                msg += "Sélectionner le vendeur dans la liste des clients<br>";
            }
            if (idcategorie.value == 0) {
                msg += "Sélectionner une categorie<br>";
            }
            if (idmarque.value == 0) {
                msg += "Sélectionner la marque<br>";
            }
            if (prix.value == 0) {
                msg += "Saisir le prix de vente<br>";
            }
            if (couleur.value == 0) {
                msg += "Sélectionner la couleur<br>";
            }
            //ecriture du message d'erreur
            error.innerHTML = msg;
            //Apparition du modal
            modal.style.display = "block";
            //reinitialisation du lessage d'erreur
            msg = "";
            //blocage du lancement du formulaire
            event.preventDefault();
        }
    }
}
