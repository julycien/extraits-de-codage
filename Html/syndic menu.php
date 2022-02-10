<!DOCTYPE html>

<html>
    <head>
        <title>menu copropriétaires</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../css/css.css"  rel = "stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="icon" href="../faviconfront.png" />
    </head>
    <header>
        <?php
        include("../controls/headerCTRL.php");
        ?>        
    </header>
    <body class="fondbodymenu">
        <?php echo $textemessage ?>
        <div class="cardmenu">
            <div class="card " style="width: 18rem;">
                <div class="card-body">
                    <a href="../controls/gestionComptesCTRL.php"><h5 class="card-title bluecard">Consulter les comptes</h5></a>
                    <p class="card-text">Interroger la situation : </p>
                    <ul>
                        <li>des comptes du syndic</li>
                        <li>de son compte personnel</li>
                    </ul>
                    <a href="../controls/gestionComptesCTRL.php" class="btn btn-secondary">Go</a>
                </div>
            </div>
            <div class="card " style="width: 18rem;">
                <div class="card-body">
                    <a href="../controls/mInformerCTRL.php"><h5 class="card-title yellowcard">M'informer</h5></a>
                    <p class="card-text">consulter les documents du syndic</p>
                    <ul>
                        <li>le syndic et ses copropriétaires</li>
                        <li>compte rendus</li>
                        <li>notifications</li>
                    </ul>
                    <a href="../controls/mInformerCTRL.php" class="btn btn-secondary">Go</a>
                </div>
            </div>
            <div class="card " style="width: 18rem;">
                <div class="card-body">
                    <a href="../controls/mesInformationsCTRL.php"><h5 class="card-title browncard">Mes informations</h5></a>
                    <p class="card-text">Visualiser :</p>
                    <ul>
                        <li>mes informations personnelles</li>
                        <li>mon lot copropriétaire</li>
                    </ul>


                    <a href="../controls/mesInformationsCTRL.php" class="btn btn-secondary">Go</a>
                </div>
            </div>
            <div class="card " style="width: 18rem;">
                <div class="card-body">
                    <a href="../controls/communiquerCTRL.php"><h5 class="card-title pinkcard">Communiquer</h5></a>
                    <p class="card-text">Visualiser :</p>
                    <ul>
                        <p class="card-text">Envoyer un mail au syndic</p>
                    </ul>

                    <a href="../controls/communiquerCTRL.php" class="btn btn-secondary">Go</a>
                </div>
            </div>
        </div>
        <br>

    </body>
    <footer>        
        <?php
        include("footer.php")
        ?>
    </footer>
</html>
