<!DOCTYPE html>

<html>
    <head>
        <title>menu administrateur</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../css/css.css"  rel = "stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="icon" href="../favicon.png" />
        <style>
            .iconeretour {
                width:100%;
                display:block;
                border-radius: 10px;
                margin: 3px;
            }

        </style>
    </head>
    <header>
        <?php
        include("../controls/headerCTRL.php");
        ?>        
    </header>
    <body class="bodybackmenu">
        <div class="cardmenu">
            <div class="card " style="width: 18rem;">
                <div class="card-body">
                    <a href="../controls/gestionComptesBackCTRL.php"><h5 class="card-title bluecard">Comptes</h5></a>
                    <p class="card-text">Visualisation synthétique des comptes : </p>
                    <ul>
                        <li>du syndic</li>
                        <li>des copropriétaires</li>
                        <li>des fournisseurs</li>
                    </ul>
                    <a href="../controls/gestionComptesBackCTRL.php" class="btn btn-secondary">Go</a>
                </div>
            </div>
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <a href="../controls/gestionPortailCTRL.php"><h5 class="card-title yellowcard">Comptabilité</h5></a>
                    <p class="card-text">Voir et gérer les mouvements comptables : </p>
                    <ul>
                        <li>appels de fonds</li>
                        <li>factures</li>
                        <li>paiements</li>
                        <li>avoirs</li>
                        <li>régularisations</li>
                        <li>devis</li>
                    </ul>
                    <a href="../controls/gestionPortailCTRL.php" class="btn btn-secondary">Go</a>
                </div>
            </div>
            <div class="card " style="width: 18rem;">
                <div class="card-body">
                    <a href="../controls/communiquerCTRL.php"><h5 class="card-title redcard">Communiquer</h5></a>
                    <p class="card-text">Communiquer avec les copropriétaires :</p>
                    <ul>
                        <li>modifier le message d'accueil</li>
                        <li>gérer les documents mis à disposition</li>
                        <li>envoi de messages</li>
                        <li>liste des mails envoyés</li>
                    </ul>
                    <a href="../controls/communiquerCTRL.php" class="btn btn-secondary">Go</a>
                </div>
            </div>
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <a href="../controls/tiersSelectAllCTRL.php"><h5 class="card-title greencard">Datas</h5></a>
                    <p class="card-text">Gérer les tables de données :</p>
                    <ul>
                        <li>les tiers</li>
                        <li>les lots</li>
                        <li>les liens entre les lots et les tiers</li>
                        <li>les compositions des lots entre leurs propriétaires</li>
                    </ul>
                    <a href="../controls/tiersSelectAllCTRL.php" class="btn btn-secondary">Go</a>
                </div>
            </div>

            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <a href="../../index.php"><img class="iconeretour" src="../boundaries/images/retourcopro.jpg" alt="icone lien retour site copro"></a>
                    <p class="card-text">Aller sur le site des copropriétaires</p>
                    <a href="../../index.php" class="btn btn-secondary">Go</a>
                </div>
            </div>


        </div>
        <br>
        <br>

    </body>
    <footer>        
        <?php
        include("footer.php")
        ?>
    </footer>
</html>
