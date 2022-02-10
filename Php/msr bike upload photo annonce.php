<?php
session_start();

//1- upload d'une photo supplémentaire 
$messageupload = "";

if ($_FILES["file"]["name"] != null) {
    if ($_FILES["file"]["error"] > 0) {
        $messageupload .= "Error: " . $_FILES["file"]["error"] . "<br>";
    } else {
        $messageupload .= "Fichier à télécharger : " . $_FILES["file"]["name"] . "<br>";
        $messageupload .= "Type : " . $_FILES["file"]["type"] . "<br>";
        $messageupload .= "Taille : " . ($_FILES["file"]["size"] / 1024) . "Kb<br />";
        $messageupload .= "Stocké dans : " . $_FILES["file"]["tmp_name"];
    }

//Enregistrer le fichier envoyé
    if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] < 50000000)) {
        if ($_FILES["file"]["error"] > 0) {
            $messageupload .= "Return Code: " . $_FILES["file"]["error"] . "<br>";
        } else {
            $messageupload .= "Fichier à télécharger : " . $_FILES["file"]["name"] . "<br>";
            $messageupload .= "Type: " . $_FILES["file"]["type"] . "<br>";
            $messageupload .= "Taille: " . ($_FILES["file"]["size"] / 1024) . "  Kb<br>";
            $messageupload .= "Fichier temporaire : " . $_FILES["file"]["tmp_name"] . "<br>";

            move_uploaded_file($_FILES["file"]["tmp_name"], "../boundaries/images/" . $_FILES["file"]["name"]);
            $messageupload .= "Enregistré dans : " . "../boundaries/images/" .
                    $_FILES["file"]["name"];
        }
    } else {
        $messageupload .= "Chemin invalide !!";
    }

//echo $messageupload;
// 2- insert de la photo supplementaire

    $idannonce = $_SESSION['lastid'];
    $photoprincipale = $_FILES["file"]["name"];
//echo "<br> last id=" . $idannonce . " photo principale = " . $_FILES["file"]["name"];

    require_once '../entities/Photos.php';
    require_once '../daos/PhotosDAO.php';
    require_once '../daos/Connexion.php';
    require_once '../daos/Transaxion.php';

    $cnx = new Connexion();
    $tx = new Transaxion();
    $pdo = $cnx->seConnecter("../conf/bd.ini");
    $dao = new PhotosDAO($pdo);
    $photos = new Photos();

    $tx->initialiser($pdo);

//incrémenter l'ordre en faisant un count dans la table photos
    $n = $dao->photoCount($idannonce);

//echo "<br> count photos: " . $n;
//Insert de la nouvelle photo

    $cnx = new Connexion();
    $tx = new Transaxion();
    $pdo = $cnx->seConnecter("../conf/bd.ini");
    $dao = new PhotosDAO($pdo);
    $photos = new Photos();
    $tx->initialiser($pdo);
    $idphoto = "";
    $nomfichier = $_FILES["file"]["name"];

    $photos->setIdPhoto($idphoto);
    $photos->setIdAnnonce($idannonce);
    $photos->setNomFichier($nomfichier);

    $affected = $dao->insert($photos);

    $tx->valider($pdo);   
}
include '../controls/PhotosPrincipaleUpdateCTRL.php'
?>

