<?php
session_start();
require_once '../daos/fonctionsDAO.php';
require_once '../lib/connexion.php';


$message = "";
//25 = le syndic
$numerotiers = '25';

$mvtapp = 'APP';
$mvtfac = 'FAC';
$mvtpai = 'PAI';
$mvtreg = 'REG';
$mvtavo = 'AVO';
$mvtdev = 'DEV';

$annee = date('Y');

$yeartextformatn = strval($annee);
$yeartextformatn1 = strval($annee - 1);
$yeartextformatn2 = strval($annee - 2);

$connexion = seConnecter("../conf/infosServeur.ini");
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$connexion->exec("SET NAMES 'UTF8'");

// I - Tableau sur 3 mois
//liste des dates à selection
$listedatesdebut = "<option value=\"\" selected >Choisir la date de début</option>";
$listedatesfin = "<option value=\"\" >Choisir la date de fin</option>";
$dateplusun = date('Y') + 1;
$listedatesfin .= "<option value=\"" . $dateplusun . "-01-01\" selected>01-01-" . $dateplusun . "</option>";
for ($i = 0; $i < 5; $i++) {
    $datejour = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $datemoinsi = mktime(0, 0, 0, 9, 1, date('Y') - $i);
    if ($datejour > $datemoinsi) {
        $listedatesdebut .= "<option value=\"" . date('Y',$datemoinsi) . "-09-01\">01-09-" . date('Y',$datemoinsi) . "</option>";
        $listedatesfin .= "<option value=\"" . date('Y',$datemoinsi) . "-09-01\">01-09-" . date('Y',$datemoinsi) . "</option>";
    }
    if ($datejour > $datemoinsi) {
        $listedatesdebut .= "<option value=\"" . date('Y',$datemoinsi) . "-06-01\">01-06-" . date('Y',$datemoinsi) . "</option>";
        $listedatesfin .= "<option value=\"" . date('Y',$datemoinsi) . "-06-01\">01-06-" . date('Y',$datemoinsi) . "</option>";
    }
    if ($datejour > $datemoinsi) {
        $listedatesdebut .= "<option value=\"" . date('Y',$datemoinsi) . "-03-01\">01-03-" . date('Y',$datemoinsi) . "</option>";
        $listedatesfin .= "<option value=\"" . date('Y',$datemoinsi) . "-09-01\">01-03-" . date('Y',$datemoinsi) . "</option>";
    }
    $listedatesdebut .= "<option value=\"" . date('Y',$datemoinsi) . "-01-01\">01-01-" . date('Y',$datemoinsi) . "</option>";
    $listedatesfin .= "<option value=\"" . date('Y',$datemoinsi) . "-09-01\">01-01-" . date('Y',$datemoinsi) . "</option>";
}

//IV-1 Compte initial
$listecomptesinitial = "";

if (filter_input(INPUT_POST, "selectdatedebutconsultation") == null) {
    $dateliste = date('Y') . "-01-01";
    $datedebut = new DateTime($dateliste);
    $datedebut = $datedebut->format('Y-m-d');
} else {
    $datedebut = filter_input(INPUT_POST, "selectdatedebutconsultation");
}
if (filter_input(INPUT_POST, "selectdatefinconsultation") == null) {
    $dateliste = date('Y') + 1 . "-01-01";
    $datefin = new DateTime($dateliste);
    $datefin = $datefin->format('Y-m-d');
} else {
    $datefin = filter_input(INPUT_POST, "selectdatefinconsultation");
}

try {
    $sql = "SELECT sum(CASE c.mvtcourt WHEN 'FAC' THEN c.mvtmontant WHEN 'AVO' THEN c.mvtmontant * -1 ELSE 0 END) as 'mvtmontantdebit', "
            . "sum(CASE c.mvtcourt WHEN 'PAI' THEN c.mvtmontant WHEN 'REG' THEN c.mvtmontant ELSE 0 END) as 'mvtmontantcredit',"
            . "sum(CASE c.mvtcourt WHEN 'FAC' THEN c.mvtmontant * -1 WHEN 'AVO' THEN c.mvtmontant WHEN 'PAI' THEN c.mvtmontant WHEN 'REG' THEN c.mvtmontant ELSE 0 END) as 'mvtmontantsolde' "
            . "FROM comptabilite c "
            . "INNER JOIN tiers t ON t.idtiers = c.idtiersemetteur "
            . "INNER JOIN tiers t2 ON t2.idtiers = c.idtiersreceveur "
            . "INNER JOIN mouvementslibelles ml ON ml.idtypemvt = c.mvtcourt "
            . "WHERE c.mvtstatut <> ? AND (c.idtiersemetteur = ? OR c.idtiersreceveur = ?) AND c.mvtcourt IN (?,?,?,?,?) "
            . "AND t.typetiers IN (?,?) AND c.mvtecheance < ?";

    $cursor = $connexion->prepare($sql);
    $cursor->bindValue(1, "A");
    $cursor->bindValue(2, $numerotiers);
    $cursor->bindValue(3, $numerotiers);
    $cursor->bindValue(4, $mvtfac);
    $cursor->bindValue(5, $mvtdev);
    $cursor->bindValue(6, $mvtpai);
    $cursor->bindValue(7, $mvtavo);
    $cursor->bindValue(8, $mvtreg);
    $cursor->bindValue(9, "SYNDI");
    $cursor->bindValue(10, "FOURN");
    $cursor->bindValue(11, $datedebut);
    $cursor->execute();

    $statement = $cursor->fetch();
    $debitanterieur = $statement["mvtmontantdebit"];
    $creditanterieur = $statement["mvtmontantcredit"];
    $soldeanterieur = $statement["mvtmontantsolde"];

    //$listecomptesinitial .= "<tr><td></td><td><b>solde intial au " . date("d-m-Y", strtotime($datedebut)) . "</b></td><td></td><td></td><td></td><td></td><td></td><td><b>" . number_format($debitanterieur, 2, ',', ' ') . " €</b></td><td><b>" . number_format($creditanterieur, 2, ',', ' ') . " €</b></td><td><b>" . number_format($soldeanterieur, 2, ',', ' ') . " €</b></td><td></tr>";
    $listecomptesinitial .= "<tr><td></td><td><b>solde intial au " . date("d-m-Y", strtotime($datedebut)) . "</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><b>" . number_format($soldeanterieur, 2, ',', ' ') . " €</b></td><td></tr>";
} catch (Exception $ex) {
    $message .= "<br>Echec chiffrage compte initial " . $ex->getMessage();
}

//IV-2 détail des comptes de la période et solde

try {

    $sql = "SELECT c.mvtecheance, c.numeromouvement, t.nom as 'nom', ml.mvtlibelle, "
            . "CASE c.mvtcourt WHEN 'FAC' THEN c.mvtmontant WHEN 'AVO' THEN -1 * c.mvtmontant ELSE 0 END as 'mvtmontantdebit', "
            . "CASE c.mvtcourt WHEN 'PAI' THEN c.mvtmontant WHEN 'DEV' THEN c.mvtmontant WHEN 'REG' THEN c.mvtmontant ELSE 0 END as 'mvtmontantcredit', "
            . "c.relationfacture, c.media, "
            . "t2.nom as 'crediteur', "
            . "(select sum(c1.mvtmontant) FROM comptabilite c1 WHERE c1.relationfacture = c.numeromouvement) 'montant_paye' "
            . "FROM comptabilite c "
            . "INNER JOIN tiers t ON t.idtiers = c.idtiersemetteur "
            . "INNER JOIN tiers t2 ON t2.idtiers = c.idtiersreceveur "
            . "INNER JOIN mouvementslibelles ml ON ml.idtypemvt = c.mvtcourt "
            . "WHERE c.mvtstatut <> ? AND (c.idtiersemetteur = ? OR c.idtiersreceveur = ?) AND c.mvtcourt IN (?,?,?,?,?) AND t.typetiers IN (?,?) "
            . "AND c.mvtecheance >= ? AND c.mvtecheance < ? "
            . "ORDER BY c.mvtecheance, c.numeromouvement";

    $cursor = $connexion->prepare($sql);
    $cursor->bindValue(1, "A");
    $cursor->bindValue(2, $numerotiers);
    $cursor->bindValue(3, $numerotiers);
    $cursor->bindValue(4, $mvtfac);
    $cursor->bindValue(5, $mvtdev);
    $cursor->bindValue(6, $mvtpai);
    $cursor->bindValue(7, $mvtavo);
    $cursor->bindValue(8, $mvtreg);
    $cursor->bindValue(9, "SYNDI");
    $cursor->bindValue(10, "FOURN");
    $cursor->bindValue(11, $datedebut);
    $cursor->bindValue(12, $datefin);

    $cursor->execute();

    $detailsyndicdates = $listecomptesinitial;
    $solde = 0;
    $debits = 0;
    $credits = 0;

    while ($enregistrement = $cursor->fetch()) {

        $detailsyndicdates .= "<tr>";
        $detailsyndicdates .= "<td>" . date('j-m-Y', strtotime($enregistrement["mvtecheance"])) . "</td>";
        $detailsyndicdates .= "<td>" . $enregistrement["nom"] . "</td>";

        $detailsyndicdates .= "<td>" . $enregistrement["numeromouvement"] . "</td>";
        $detailsyndicdates .= "<td>" . $enregistrement["mvtlibelle"] . "</td>";
        $detailsyndicdates .= "<td>" . $enregistrement["crediteur"] . "</td>";
        if ($enregistrement["montant_paye"] <> 0) {
            $detailsyndicdates .= "<td>" . number_format($enregistrement["montant_paye"], 2, ',', ' ') . " €</td>";
        } else {
            $detailsyndicdates .= "<td></td>";
        }
        if ($enregistrement["media"] == null) {
            $vuemedia = "";
        } else {
            $vuemedia = "<a href=\"../medias/" . $enregistrement["media"] . "\" target=\"_blank\"><img src=\"../boundaries/images/voir.jpg\" alt=\"logo voir\" height=\"30\" /></a>";
        }

        $detailsyndicdates .= "<td>" . $vuemedia . "</td>";
        $debits += $enregistrement["mvtmontantdebit"];

        if ($enregistrement["mvtmontantdebit"] != 0) {
            $detailsyndicdates .= "<td>" . number_format($enregistrement["mvtmontantdebit"], 2, ',', ' ') . " €</td>";
        } else {
            $detailsyndicdates .= "<td></td>";
        }

        if ($enregistrement["mvtmontantcredit"] != 0) {
            $detailsyndicdates .= "<td>" . number_format($enregistrement["mvtmontantcredit"], 2, ',', ' ') . " €</td>";
        } else {
            $detailsyndicdates .= "<td></td>";
        }

        if ($enregistrement["mvtlibelle"] == "devis") {
            $enregistrement["mvtmontantcredit"] = 0;
        }
        $credits += $enregistrement["mvtmontantcredit"];
        $solde += $enregistrement["mvtmontantcredit"] - $enregistrement["mvtmontantdebit"];
        $detailsyndicdates .= "<td>" . number_format($solde, 2, ',', ' ') . " €</td>";
        $detailsyndicdates .= "</tr>";
    }
    $detailsyndicdates .= "<tr><td></td><td><b>Total des mouvements du " . date("d-m-Y", strtotime($datedebut)) . " au " . date("d-m-Y", strtotime($datefin)) . "</b></td><td></td><td></td><td></td><td></td><td></td><td><b>" . number_format($debits, 2, ',', ' ') . " €</b></td><td><b>" . number_format($credits, 2, ',', ' ') . " €</b></td><td><b>" . number_format($credits - $debits, 2, ',', ' ') . " €</b></td><td></tr>";
    $detailsyndicdates .= "<tr><td></td><td><b>Solde au " . date("d-m-Y", strtotime($datefin)) . "</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><b>" . number_format($soldeanterieur + $solde, 2, ',', ' ') . " €</b></td><td></tr>";
} catch (Exception $ex) {
    $message .= "<br>Echec de l'exécution du compte fournisseur du syndic par dates : " . $ex->getMessage();
}



// II - Le syndic : par mouvements annuels par statuts
try {


    $sql = "SELECT t.idtiers, t.nom, "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'appels_crees_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'appels_echus_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'appels_soldes_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'factures_recues_creees_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'factures_recues_echues_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'factures_recues_soldees_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_emis_crees_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_emis_echus_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_emis_soldes_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_recus_crees_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_recus_echus_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_recus_soldes_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_emis_crees_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_emis_echus_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_emis_soldes_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_recus_crees_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_recus_echus_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_recus_soldes_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'regul_emises_creees_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'regul_emises_echues_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'regul_emises_soldees_n', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'appels_crees_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'appels_echus_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'appels_soldes_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'factures_recues_creees_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'factures_recues_echues_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'factures_recues_soldees_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_emis_crees_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_emis_echus_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_emis_soldes_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_recus_crees_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_recus_echus_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_recus_soldes_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_emis_crees_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_emis_echus_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_emis_soldes_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_recus_crees_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_recus_echus_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_recus_soldes_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'regul_emises_creees_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'regul_emises_echues_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'regul_emises_soldees_n1', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'appels_crees_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'appels_echus_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'appels_soldes_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'factures_recues_creees_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'factures_recues_echues_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'factures_recues_soldees_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_emis_crees_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_emis_echus_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_emis_soldes_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_recus_crees_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_recus_echus_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'paiements_recus_soldes_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_emis_crees_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_emis_echus_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_emis_soldes_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_recus_crees_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_recus_echus_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersreceveur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'avoirs_recus_soldes_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'regul_emises_creees_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'regul_emises_echues_n2', "
            . "(select sum(c.mvtmontant) FROM comptabilite c WHERE c.idtiersemetteur = t.idtiers AND mvtstatut = ? AND mvtcourt = ? AND left(mvtecheance,4) = ?) as 'regul_emises_soldees_n2' "
            . "FROM tiers t "
            . "WHERE t.idtiers = ? ";

    $cursor = $connexion->prepare($sql);

    $cursor->bindValue(1, "C");
    $cursor->bindValue(2, $mvtapp);
    $cursor->bindValue(3, $annee);
    $cursor->bindValue(4, "E");
    $cursor->bindValue(5, $mvtapp);
    $cursor->bindValue(6, $annee);
    $cursor->bindValue(7, "S");
    $cursor->bindValue(8, $mvtapp);
    $cursor->bindValue(9, $annee);
    $cursor->bindValue(10, "C");
    $cursor->bindValue(11, $mvtfac);
    $cursor->bindValue(12, $annee);
    $cursor->bindValue(13, "E");
    $cursor->bindValue(14, $mvtfac);
    $cursor->bindValue(15, $annee);
    $cursor->bindValue(16, "S");
    $cursor->bindValue(17, $mvtfac);
    $cursor->bindValue(18, $annee);
    $cursor->bindValue(19, "C");
    $cursor->bindValue(20, $mvtpai);
    $cursor->bindValue(21, $annee);
    $cursor->bindValue(22, "E");
    $cursor->bindValue(23, $mvtpai);
    $cursor->bindValue(24, $annee);
    $cursor->bindValue(25, "S");
    $cursor->bindValue(26, $mvtpai);
    $cursor->bindValue(27, $annee);
    $cursor->bindValue(28, "C");
    $cursor->bindValue(29, $mvtpai);
    $cursor->bindValue(30, $annee);
    $cursor->bindValue(31, "E");
    $cursor->bindValue(32, $mvtpai);
    $cursor->bindValue(33, $annee);
    $cursor->bindValue(34, "S");
    $cursor->bindValue(35, $mvtpai);
    $cursor->bindValue(36, $annee);
    $cursor->bindValue(37, "C");
    $cursor->bindValue(38, $mvtavo);
    $cursor->bindValue(39, $annee);
    $cursor->bindValue(40, "E");
    $cursor->bindValue(41, $mvtavo);
    $cursor->bindValue(42, $annee);
    $cursor->bindValue(43, "S");
    $cursor->bindValue(44, $mvtavo);
    $cursor->bindValue(45, $annee);
    $cursor->bindValue(46, "C");
    $cursor->bindValue(47, $mvtavo);
    $cursor->bindValue(48, $annee);
    $cursor->bindValue(49, "E");
    $cursor->bindValue(50, $mvtavo);
    $cursor->bindValue(51, $annee);
    $cursor->bindValue(52, "S");
    $cursor->bindValue(53, $mvtavo);
    $cursor->bindValue(54, $annee);
    $cursor->bindValue(55, "C");
    $cursor->bindValue(56, $mvtreg);
    $cursor->bindValue(57, $annee);
    $cursor->bindValue(58, "E");
    $cursor->bindValue(59, $mvtreg);
    $cursor->bindValue(60, $annee);
    $cursor->bindValue(61, "S");
    $cursor->bindValue(62, $mvtreg);
    $cursor->bindValue(63, $annee);

    $cursor->bindValue(64, "C");
    $cursor->bindValue(65, $mvtapp);
    $cursor->bindValue(66, $yeartextformatn1);
    $cursor->bindValue(67, "E");
    $cursor->bindValue(68, $mvtapp);
    $cursor->bindValue(69, $yeartextformatn1);
    $cursor->bindValue(70, "S");
    $cursor->bindValue(71, $mvtapp);
    $cursor->bindValue(72, $yeartextformatn1);
    $cursor->bindValue(73, "C");
    $cursor->bindValue(74, $mvtfac);
    $cursor->bindValue(75, $yeartextformatn1);
    $cursor->bindValue(76, "E");
    $cursor->bindValue(77, $mvtfac);
    $cursor->bindValue(78, $yeartextformatn1);
    $cursor->bindValue(79, "S");
    $cursor->bindValue(80, $mvtfac);
    $cursor->bindValue(81, $yeartextformatn1);
    $cursor->bindValue(82, "C");
    $cursor->bindValue(83, $mvtpai);
    $cursor->bindValue(84, $yeartextformatn1);
    $cursor->bindValue(85, "E");
    $cursor->bindValue(86, $mvtpai);
    $cursor->bindValue(87, $yeartextformatn1);
    $cursor->bindValue(88, "S");
    $cursor->bindValue(89, $mvtpai);
    $cursor->bindValue(90, $yeartextformatn1);
    $cursor->bindValue(91, "C");
    $cursor->bindValue(92, $mvtpai);
    $cursor->bindValue(93, $yeartextformatn1);
    $cursor->bindValue(94, "E");
    $cursor->bindValue(95, $mvtpai);
    $cursor->bindValue(96, $yeartextformatn1);
    $cursor->bindValue(97, "S");
    $cursor->bindValue(98, $mvtpai);
    $cursor->bindValue(99, $yeartextformatn1);
    $cursor->bindValue(100, "C");
    $cursor->bindValue(101, $mvtavo);
    $cursor->bindValue(102, $yeartextformatn1);
    $cursor->bindValue(103, "E");
    $cursor->bindValue(104, $mvtavo);
    $cursor->bindValue(105, $yeartextformatn1);
    $cursor->bindValue(106, "S");
    $cursor->bindValue(107, $mvtavo);
    $cursor->bindValue(108, $yeartextformatn1);
    $cursor->bindValue(109, "C");
    $cursor->bindValue(110, $mvtavo);
    $cursor->bindValue(111, $yeartextformatn1);
    $cursor->bindValue(112, "E");
    $cursor->bindValue(113, $mvtavo);
    $cursor->bindValue(114, $yeartextformatn1);
    $cursor->bindValue(115, "S");
    $cursor->bindValue(116, $mvtavo);
    $cursor->bindValue(117, $yeartextformatn1);
    $cursor->bindValue(118, "C");
    $cursor->bindValue(119, $mvtreg);
    $cursor->bindValue(120, $yeartextformatn1);
    $cursor->bindValue(121, "E");
    $cursor->bindValue(122, $mvtreg);
    $cursor->bindValue(123, $yeartextformatn1);
    $cursor->bindValue(124, "S");
    $cursor->bindValue(125, $mvtreg);
    $cursor->bindValue(126, $yeartextformatn1);

    $cursor->bindValue(127, "C");
    $cursor->bindValue(128, $mvtapp);
    $cursor->bindValue(129, $yeartextformatn2);
    $cursor->bindValue(130, "E");
    $cursor->bindValue(131, $mvtapp);
    $cursor->bindValue(132, $yeartextformatn2);
    $cursor->bindValue(133, "S");
    $cursor->bindValue(134, $mvtapp);
    $cursor->bindValue(135, $yeartextformatn2);
    $cursor->bindValue(136, "C");
    $cursor->bindValue(137, $mvtfac);
    $cursor->bindValue(138, $yeartextformatn2);
    $cursor->bindValue(139, "E");
    $cursor->bindValue(140, $mvtfac);
    $cursor->bindValue(141, $yeartextformatn2);
    $cursor->bindValue(142, "S");
    $cursor->bindValue(143, $mvtfac);
    $cursor->bindValue(144, $yeartextformatn2);
    $cursor->bindValue(145, "C");
    $cursor->bindValue(146, $mvtpai);
    $cursor->bindValue(147, $yeartextformatn2);
    $cursor->bindValue(148, "E");
    $cursor->bindValue(149, $mvtpai);
    $cursor->bindValue(150, $yeartextformatn2);
    $cursor->bindValue(151, "S");
    $cursor->bindValue(152, $mvtpai);
    $cursor->bindValue(153, $yeartextformatn2);
    $cursor->bindValue(154, "C");
    $cursor->bindValue(155, $mvtpai);
    $cursor->bindValue(156, $yeartextformatn2);
    $cursor->bindValue(157, "E");
    $cursor->bindValue(158, $mvtpai);
    $cursor->bindValue(159, $yeartextformatn2);
    $cursor->bindValue(160, "S");
    $cursor->bindValue(161, $mvtpai);
    $cursor->bindValue(162, $yeartextformatn2);
    $cursor->bindValue(163, "C");
    $cursor->bindValue(164, $mvtavo);
    $cursor->bindValue(165, $yeartextformatn2);
    $cursor->bindValue(166, "E");
    $cursor->bindValue(167, $mvtavo);
    $cursor->bindValue(168, $yeartextformatn2);
    $cursor->bindValue(169, "S");
    $cursor->bindValue(170, $mvtavo);
    $cursor->bindValue(171, $yeartextformatn2);
    $cursor->bindValue(172, "C");
    $cursor->bindValue(173, $mvtavo);
    $cursor->bindValue(174, $yeartextformatn2);
    $cursor->bindValue(175, "E");
    $cursor->bindValue(176, $mvtavo);
    $cursor->bindValue(177, $yeartextformatn2);
    $cursor->bindValue(178, "S");
    $cursor->bindValue(179, $mvtavo);
    $cursor->bindValue(180, $yeartextformatn2);
    $cursor->bindValue(181, "C");
    $cursor->bindValue(182, $mvtreg);
    $cursor->bindValue(183, $yeartextformatn2);
    $cursor->bindValue(184, "E");
    $cursor->bindValue(185, $mvtreg);
    $cursor->bindValue(186, $yeartextformatn2);
    $cursor->bindValue(187, "S");
    $cursor->bindValue(188, $mvtreg);
    $cursor->bindValue(189, $yeartextformatn2);

    $cursor->bindValue(190, $numerotiers);
    $cursor->execute();

    $contentsyndic = "";

    while ($enregistrement = $cursor->fetch()) {
        //Année n
        if ($enregistrement["appels_crees_n"] + $enregistrement["appels_echus_n"] + $enregistrement["appels_soldes_n"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Appels de fonds émis</b></td>";
            $contentsyndic .= "<td>" . $annee . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["appels_crees_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["appels_echus_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["appels_soldes_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["factures_recues_creees_n"] + $enregistrement["factures_recues_echues_n"] + $enregistrement["factures_recues_soldees_n"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Factures reçues</b></td>";
            $contentsyndic .= "<td>" . $annee . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["factures_recues_creees_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["factures_recues_echues_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["factures_recues_soldees_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["paiements_emis_crees_n"] + $enregistrement["paiements_emis_echus_n"] + $enregistrement["paiements_emis_soldes_n"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Paiements effectués</b></td>";
            $contentsyndic .= "<td>" . $annee . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_emis_crees_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_emis_echus_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_emis_soldes_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["paiements_recus_crees_n"] + $enregistrement["paiements_recus_echus_n"] + $enregistrement["paiements_recus_soldes_n"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Paiements reçus</b></td>";
            $contentsyndic .= "<td>" . $annee . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_recus_crees_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_recus_echus_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_recus_soldes_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["avoirs_emis_crees_n"] + $enregistrement["avoirs_emis_echus_n"] + $enregistrement["avoirs_emis_soldes_n"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Avoirs émis</b></td>";
            $contentsyndic .= "<td>" . $annee . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_emis_crees_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_emis_echus_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_emis_soldes_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["avoirs_recus_crees_n"] + $enregistrement["avoirs_recus_echus_n"] + $enregistrement["avoirs_recus_soldes_n"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Avoirs reçus</b></td>";
            $contentsyndic .= "<td>" . $annee . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_recus_crees_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_recus_echus_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_recus_soldes_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["regul_emises_creees_n"] + $enregistrement["regul_emises_echues_n"] + $enregistrement["regul_emises_soldees_n"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Régularisations émises</b></td>";
            $contentsyndic .= "<td>" . $annee . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["regul_emises_creees_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["regul_emises_echues_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["regul_emises_soldees_n"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }

//Année n-1
        if ($enregistrement["appels_crees_n1"] + $enregistrement["appels_echus_n1"] + $enregistrement["appels_soldes_n1"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Appels de fonds émis</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn1 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["appels_crees_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["appels_echus_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["appels_soldes_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["factures_recues_creees_n1"] + $enregistrement["factures_recues_echues_n1"] + $enregistrement["factures_recues_soldees_n1"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Factures reçues</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn1 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["factures_recues_creees_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["factures_recues_echues_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["factures_recues_soldees_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["paiements_emis_crees_n1"] + $enregistrement["paiements_emis_echus_n1"] + $enregistrement["paiements_emis_soldes_n1"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Paiements effectués</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn1 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_emis_crees_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_emis_echus_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_emis_soldes_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["paiements_recus_crees_n1"] + $enregistrement["paiements_recus_echus_n1"] + $enregistrement["paiements_recus_soldes_n1"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Paiements reçus</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn1 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_recus_crees_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_recus_echus_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_recus_soldes_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["avoirs_emis_crees_n1"] + $enregistrement["avoirs_emis_echus_n1"] + $enregistrement["avoirs_emis_soldes_n1"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Avoirs émis</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn1 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_emis_crees_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_emis_echus_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_emis_soldes_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["avoirs_recus_crees_n1"] + $enregistrement["avoirs_recus_echus_n1"] + $enregistrement["avoirs_recus_soldes_n1"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Avoirs reçus</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn1 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_recus_crees_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_recus_echus_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_recus_soldes_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["regul_emises_creees_n1"] + $enregistrement["regul_emises_echues_n1"] + $enregistrement["regul_emises_soldees_n1"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Régularisations émises</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn1 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["regul_emises_creees_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["regul_emises_echues_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["regul_emises_soldees_n1"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }

        //Année n-2
        if ($enregistrement["appels_crees_n2"] + $enregistrement["appels_echus_n2"] + $enregistrement["appels_soldes_n2"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Appels de fonds émis</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn2 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["appels_crees_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["appels_echus_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["appels_soldes_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["factures_recues_creees_n2"] + $enregistrement["factures_recues_echues_n2"] + $enregistrement["factures_recues_soldees_n2"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>factures reçues</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn2 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["factures_recues_creees_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["factures_recues_echues_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["factures_recues_soldees_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["paiements_emis_crees_n2"] + $enregistrement["paiements_emis_echus_n2"] + $enregistrement["paiements_emis_soldes_n2"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Paiements effectués</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn2 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_emis_crees_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_emis_echus_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_emis_soldes_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["paiements_recus_crees_n2"] + $enregistrement["paiements_recus_echus_n2"] + $enregistrement["paiements_recus_soldes_n2"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Paiements reçus</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn2 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_recus_crees_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_recus_echus_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["paiements_recus_soldes_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["avoirs_emis_crees_n2"] + $enregistrement["avoirs_emis_echus_n2"] + $enregistrement["avoirs_emis_soldes_n2"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Avoirs émis</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn2 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_emis_crees_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_emis_echus_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_emis_soldes_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["avoirs_recus_crees_n2"] + $enregistrement["avoirs_recus_echus_n2"] + $enregistrement["avoirs_recus_soldes_n2"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Avoirs reçus</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn2 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_recus_crees_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_recus_echus_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["avoirs_recus_soldes_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
        if ($enregistrement["regul_emises_creees_n2"] + $enregistrement["regul_emises_echues_n2"] + $enregistrement["regul_emises_soldees_n2"] != 0) {
            $contentsyndic .= "<tr>";
            $contentsyndic .= "<td>" . $enregistrement["idtiers"] . "</td>";
            $contentsyndic .= "<td><b>" . $enregistrement["nom"] . "</b></td>";
            $contentsyndic .= "<td><b>Régularisations émises</b></td>";
            $contentsyndic .= "<td>" . $yeartextformatn2 . "</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["regul_emises_creees_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["regul_emises_echues_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "<td>" . number_format($enregistrement["regul_emises_soldees_n2"], 2, ',', ' ') . " €</td>";
            $contentsyndic .= "</tr>";
        }
    }
} catch (Exception $ex) {
    $message = "Echec de l'exécution visualisation syndic par année : " . $ex->getMessage();
}

//II- Comptes des copropriétaires
//II-1 Liste des copropriétaires actifs global et par copro

try {
    $select = "SELECT t.idtiers, ci.libellecivilite, t.nom, t.prenom "
            . "FROM tiers t "
            . "INNER JOIN civilite ci ON ci.idcivilite = t.civilite "
            . "INNER JOIN lienlot ll ON ll.iduserprincipal = t.idtiers "
            . "WHERE t.typetiers = 'PROPR' AND t.idtiers <> '33' ORDER BY t.nom, t.prenom";

    $cursor = $connexion->query($select);
    //pour chaque coproprietaire, écrire le compte dans une table
    $detailcopro = "";

    while ($enregistrementtiers = $cursor->fetch()) {
        $idtiers = $enregistrementtiers["idtiers"];

        try {
            $sql = "SELECT c.mvtecheance, c.numeromouvement, ci.libellecivilite, t.prenom, t.nom as 'nom', ml.mvtlibelle, "
                    . "CASE c.mvtcourt WHEN 'APP' THEN c.mvtmontant WHEN 'AVO' THEN -1 * c.mvtmontant WHEN 'REG' THEN -1 * c.mvtmontant ELSE 0 END as 'mvtmontantdebit', "
                    . "CASE c.mvtcourt WHEN 'PAI' THEN c.mvtmontant ELSE 0 END as 'mvtmontantcredit', "
                    . "c.relationfacture "
                    . "FROM comptabilite c "
                    . "INNER JOIN tiers t ON t.idtiers = c.idtiersemetteur "
                    . "INNER JOIN mouvementslibelles ml ON ml.idtypemvt = c.mvtcourt "
                    . "INNER JOIN civilite ci ON idcivilite = t.civilite "
                    . "WHERE c.mvtstatut <> ? AND (c.idtiersemetteur = ? OR c.idtiersreceveur = ?)"
                    . "ORDER BY t.nom, t.prenom";

            $statement = $connexion->prepare($sql);
            $statement->bindValue(1, "A");
            $statement->bindValue(2, $idtiers);
            $statement->bindValue(3, $idtiers);
            $statement->execute();

            $solde = 0;
            $debits = 0;
            $credits = 0;

            $detailcopro .= "<b>" . $enregistrementtiers["libellecivilite"] . " " . $enregistrementtiers["prenom"] . " " . $enregistrementtiers["nom"] . "</b><br>";
            $detailcopro .= "<table class=\"tablesituation\"><thead><tr><th>Date</th><th>Num. mouvement</th><th>Type mouvement</th><th>Débit</th><th>Crédit</th><th>Solde</th></tr></thead>";
            $detailcopro .= "<tbody>";

            while ($enregistrement = $statement->fetch()) {

                $detailcopro .= "<tr>";
                $detailcopro .= "<td>" . date('j-m-Y', strtotime($enregistrement["mvtecheance"])) . "</td>";

                $detailcopro .= "<td>" . $enregistrement["numeromouvement"] . "</td>";

                $detailcopro .= "<td>" . $enregistrement["mvtlibelle"] . "</td>";
                $debits += $enregistrement["mvtmontantdebit"];
                $detailcopro .= "<td>" . number_format($enregistrement["mvtmontantdebit"], 2, ',', ' ') . " €</td>";
                $credits += $enregistrement["mvtmontantcredit"];
                $detailcopro .= "<td>" . number_format($enregistrement["mvtmontantcredit"], 2, ',', ' ') . " €</td>";

                $solde += $enregistrement["mvtmontantcredit"] - $enregistrement["mvtmontantdebit"];
                $detailcopro .= "<td>" . number_format($solde, 2, ',', ' ') . " €</td>";
                $detailcopro .= "</tr>";
            }
            $detailcopro .= "<tr><td></td><td>Total des mouvements</td><td></td><td><b>" . number_format($debits, 2, ',', ' ') . " €</b></td><td><b>" . number_format($credits, 2, ',', ' ') . " €</b></td><td><b>" . number_format($credits - $debits, 2, ',', ' ') . " €</b></td><td></tr>";
            $detailcopro .= "<tr><td></td><td><b>Solde</b></td><td></td><td></td><td></td><td><b>" . number_format($solde, 2, ',', ' ') . "€</b></td><td></tr>";
            $detailcopro .= "</tbody></table><br>";
        } catch (Exception $ex) {
            $message .= "Echec de l'exécution requete comptes individuels copropriétaires : " . $ex->getMessage();
        }
    }
} catch (Exception $ex) {
    $message .= "Echec de l'exécution requete lister les copropriétaires principaux : " . $ex->getMessage();
}

echo $message;
include '../boundaries/gestionComptesBackIHM.php';
?>