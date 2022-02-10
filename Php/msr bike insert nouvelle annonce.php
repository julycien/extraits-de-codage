<?php
session_start();
//header('Location: ../controls/PhotosPrincipaleUpdateCTRL.php');
require_once '../entities/Annonces.php';
require_once '../daos/AnnoncesDAO.php';
require_once '../daos/Connexion.php';
require_once '../daos/Transaxion.php';

//1- insert de la nouvelle annonce
$idannonce = "";
$idvendeur = filter_input(INPUT_POST, "idvendeur");
$idacheteur = "";
$idcategorie = filter_input(INPUT_POST, "idcategorie");
$idmarque = filter_input(INPUT_POST, "idmarque");
$dateannonce = date("Y-m-d");
$datemel = filter_input(INPUT_POST, "datemel");
$datefinmel = "9999-12-31";
$datevente = "";
$prixvente = filter_input(INPUT_POST, "prixvente");
$immatriculation = filter_input(INPUT_POST, "immatriculation");
$datemec = filter_input(INPUT_POST, "datemec");
$couleur = filter_input(INPUT_POST, "couleur");
$textecourt = filter_input(INPUT_POST, "textecourt");
$textelong = filter_input(INPUT_POST, "textelong");
//$photoprincipale = filter_input(INPUT_POST, "photoprincipale"); sera dans update apres la creation;
$photoprincipale = "";
$nommodele = filter_input(INPUT_POST, "nommodele");
$cylindree = filter_input(INPUT_POST, "cylindree");
$annee = filter_input(INPUT_POST, "annee");
$permis = filter_input(INPUT_POST, "permis");
$kilometrage = filter_input(INPUT_POST, "kilometrage");

$cnx = new Connexion();
$tx = new Transaxion();
$pdo = $cnx->seConnecter("../conf/bd.ini");
//$annonces = new Annonces();
$dao = new AnnoncesDAO($pdo);

try {
    $tx->initialiser($pdo);
    $annonces = new Annonces($idannonce, $idvendeur, $idacheteur, $idcategorie, $idmarque, $dateannonce, $datemel, $datefinmel, $datevente, $prixvente, $immatriculation, $datemec, $couleur, $textecourt, $textelong, $photoprincipale, $nommodele, $cylindree, $annee, $permis, $kilometrage);
    $affected = $dao->insert($annonces);
    $lastid = $pdo->lastInsertId();
    //echo "lastid:$lastid";
    //echo $affected;
    if ($affected == 1) {
    $tx->valider($pdo);
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}


$_SESSION['lastid'] = $lastid;

include "../controls/PhotosPrincipaleUpdateCTRL.php";

//vers une nouvelle page pour charger la photo principale

?>

