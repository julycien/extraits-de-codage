   let clicForm = document.querySelector("#contactenter");
            //lancer la fonction de controle
            clicForm.addEventListener("click", clique, false);
            //attribuer les valeurs du fomulaire pour controle
            let contactnom = document.querySelector("#contactnom");
            let contactprenom = document.querySelector("#contactprenom");
            let contactnumtel = document.querySelector("#contactnumtel");
            let contactemail = document.querySelector("#contactemail");
            let contactsujet = document.querySelector("#contactsujet");
            let contacttexte = document.querySelector("#contacttexte");
            let confidentialite = document.querySelector("#confidentialite");
            //le modal
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

            let error = document.querySelector("#msgError");
            let msg = "";
            //fonction de controle
            function clique() {
                //controle du nom
                document.querySelector("#contactnom").style.border = "1px solid grey";
                if (contactnom.value == 0) {
                    msg += "Merci d'indiquer votre nom" + "<br>";
                    document.querySelector("#contactnom").style.border = "4px solid red";
                }
                //controle du prenom
                document.querySelector("#contactprenom").style.border = "1px solid grey";
                if (contactprenom.value == 0) {
                    msg += "Merci d'indiquer votre prénom" + "<br>";
                    document.querySelector("#contactprenom").style.border = "4px solid red";
                }
                //controle du numero de telephone expression reguliere 10 chiffres
                document.querySelector("#contactnumtel").style.border = "1px solid grey";
                //let expressionReguliereTel = /^[0-9]{10}$/;
                //let expressionReguliereTel = new RegExp('^[0-9a-z._-]+@{1}[0-9a-z.-]{2,}[.]{1}[a-z]{2,5}$','i');
                let expressionReguliereTel = new RegExp('^[0-9]{10}$', 'i');
                if (!expressionReguliereTel.test(contactnumtel.value)) {
                    msg += "Merci de saisir les 10 chiffres de votre numéro de téléphone" + "<br>";
                    document.querySelector("#contactnumtel").style.border = "4px solid red";
                }
                //controle email

                document.querySelector("#contactemail").style.border = "1px solid grey";
                let expressionReguliereMail = new RegExp('^[0-9a-z._-]+@{1}[0-9a-z.-]{2,}[.]{1}[a-z]{2,5}$', 'i');
                if (!expressionReguliereMail.test(contactemail.value)) {
                    msg += "Merci d'indiquer votre email" + "<br>";
                    document.querySelector("#contactemail").style.border = "4px solid red";
                }

                //controle confidentialite
                if (clicForm.value == "Envoyer") {
                    //faire les controles et le message d'erreur
                    console.log(confidentialite.value);
                    if (confidentialite.checked == false) {
                        console.log(confidentialite.checked);
                        msg += "Merci de valider la clause de confidentialité de la case à cocher" + "<br>";
                    }
                    //fin  d            es controles et affichage du message
                    if (msg != "") {
                        error.innerHTML = msg;
                        //Apparition du modal
                        modal.style.display = "block";
                        console.log("blocage du script");
                        event.preventDefault();
                    }

                    msg = "";
                }
            }
