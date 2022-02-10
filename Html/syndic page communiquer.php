<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>communiquer</title>
        <link href="../css/css.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="icon" href="../favicon.png" />
        <style>
            .tabledestinataires {
                border-style: solid; 
            }
            td {
                padding: 5px;
            }
            .checkboxcenter {
                text-align: center;
            }
        </style>
    </head>
    <header>
        <?php
        include("../controls/headerCTRL.php");
        ?>        
    </header>
    <body>
        <?php
        include ("navBarGestion.php");
        ?>  
        <br>
        <h1>Portail communication</h1>
        <br>    
        <div class="containermessageaccueil">
            <br><h2>1- Message d'accueil</h2>
            <label><b>Date du message actuel : </b></label><?php echo $datemodificationmessage; ?><br>
            <label><b>Texte du message : </b></label><div class="cadretexte"><?php echo $textemessage; ?></div><br>
            <button class="btn btn-secondary" onclick="window.location.href = '../controls/communiquerModifierCTRL.php';">Modifier</button>
        </div>
        <br>
        <div class="containermessageaccueil">
            <br>
            <h2>2- Liste des documents mis à disposition</h2>
            <br>
            <div class="listedocuments"><?php echo $contents ?></div><br>


            <h3>Ajouter un document</h3>
            <form action="../controls/communiquerInsertCTRL.php" enctype="multipart/form-data" method="post" class="mb-3">  

                <label><b>Type de media : </b></label>
                <select name="typemedia">
                    <option>Sélectionner un type de media</option>
                    <option value="CRE">Compte-rendu</option>
                    <option value="NOT">Note</option>
                </select><br>
                <label><b>Description du media : </b></label>
                <input type="text" placeholder="Saisir une description" size="50" name="descriptionmedia" />


                <input class="form-control" type="file" id="file" name="file" /><br>
                <input type="submit" class="btn btn-secondary"  name="submit" value="Upload" />
            </form>
        </div>
        <br>
        <div class="containermessageaccueil">
            <h2>3- Envoyer des messages</h2>
            <?php
            // gestion de l'apparition de l'insert de PJ ou de la suppression de pj
            if (isset($nompj)) {
                if ($nompj == "") {
                    //echo "la pj existe, elle est vide, l'insert doit apparaitre";
                    $formpj = "<form action = \"../controls/communiquerInsertPJCTRL.php\" enctype = \"multipart/form-data\" method = \"post\" class = \"mb-3\">
                        <label><b>Pièce jointe</b></label>
                        <input class = \"form-control\" type = \"file\" id = \"file\" name = \"filepj\" /><br>
                        <input type = \"submit\" class = \"btn btn-secondary\" name = \"submit\" value = \"Upload\" />
                        </form><br>
                        <br>";
                    echo $formpj;
                } else {
                    //echo "nompj existe : ne pas creer d'insert de pj";
                    echo $nompj . $supppj;
                }
            } else {
                //echo "nompj n'existe pas : faire apparaitre insert de pj";
                echo $formpj;
            }
            ?>
            <div class="containerenvoimail">
                <div class="gestionmail">
                    <form action="../controls/communiquerEnvoiMailCTRL.php" name="btModifier" method="POST">
                        <label><b>Donner un nom au mail : </b></label>
                        <input type="text" name="nomcourt" /><br>
                        <label><b>Objet du mail : </b></label>
                        <input type="text" name="objetmail" size="100%" value="Syndic de la Copropriété du Centre à Sainte Julie : " /><br>

                        <label><b>Texte du mail :</b></label><br>
                        <textarea id="textemail" name="textemail" rows="15" cols="150">
    
    
    
    
    Le Syndic de la Copropriété du Centre de Sainte Julie</textarea><br>

                        <label><b>Destinataires</b></label>
                        <?php echo $contentsmail ?>


                        <br>
                        <input type="submit" class="btn btn-secondary" value="Envoi du mail">
                    </form>
                </div>

                <button class="btn btn-secondary" onclick="window.location.href = '../controls/communiquerCTRL.php';">Annuler</button>
            </div></div>


        <br>

        <div class="containermessageaccueil">
            <br>
            <h2>4- Liste des mails envoyés</h2>
            <br>
            <h3>Mails envoyés par le formulaire d'envoi de mail</h3>     
            <?php echo $listemails ?>

        </div>
        <br>
        <div class="signethautdepage">
            <u><a href="#" >&uarr; Haut de page &uarr;</a></u>
        </div>
        <br>    
    </body>
    <footer>        
        <?php
        include("footer.php")
        ?>
    </footer>

</html>
