<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

require_once '../daos/fonctionsDAO.php';
require_once '../lib/connexion.php';

$connexion = seConnecter("../conf/infosServeur.ini");

$message = "";

// Récupération des saisies utilisateur
$mvtcourt = filter_input(INPUT_POST, "mvtcourt");
$idtiersemetteur = filter_input(INPUT_POST, "idtiersemetteur");
$idtiersreceveur = filter_input(INPUT_POST, "idtiersreceveur");
$mvtidlot = filter_input(INPUT_POST, "mvtidlot");
$mvtdate = date("Y-m-d");
$mvtecheance = filter_input(INPUT_POST, "mvtecheance");
$mvtstatut = "C";
$mvtmontant = filter_input(INPUT_POST, "mvtmontant");
$mvttexte = filter_input(INPUT_POST, "mvttexte");
$relationfacture = filter_input(INPUT_POST, "relationfacture");
//Les factures sont soldées si leur date d'échéance est dépassée
if ($mvtcourt == 'FAC' && $mvtecheance < date("Y-m-d")) {
    $mvtstatut = "E";
}
//les paiements, régularisations, devis et avoirs sont en statut "soldé"
if ($mvtcourt == "PAI" || $mvtcourt == "AVO" || $mvtcourt == "DEV" || $mvtcourt == "REG") {
    $mvtstatut = "S";
}
//Les appels de fonds du syndic sont soldés
if ($mvtcourt == 'APP' && $idtiersemetteur == '25' && $idtiersreceveur == '33') {
    $mvtstatut = "S";
}
/*
 * 1-création du mouvement
 */
if ($mvtcourt != null && $idtiersemetteur != null && $idtiersreceveur != null) {
    try {
        $message .= "<br>insert du mouvement comptable";
        $tAttributesValues = array();

        $tAttributesValues['mvtcourt'] = $mvtcourt;
        $tAttributesValues['idtiersemetteur'] = $idtiersemetteur;
        $tAttributesValues['idtiersreceveur'] = $idtiersreceveur;
        $tAttributesValues['mvtidlot'] = $mvtidlot;
        $tAttributesValues['mvtdate'] = $mvtdate;
        $tAttributesValues['mvtecheance'] = $mvtecheance;
        $tAttributesValues['mvtstatut'] = $mvtstatut;
        $tAttributesValues['mvtmontant'] = $mvtmontant;
        $tAttributesValues['mvttexte'] = $mvttexte;
        $tAttributesValues['relationfacture'] = $relationfacture;
        
        $lastid = insertGestion($connexion, $tAttributesValues);
        $message .= "<br>Nouveau mouvement " . $lastid . " ajouté avec succès - 60";
        $lastidparent = $lastid;

        /*
         * 2-solder la facture ou l'appel si les montants des paiements sont complets
         * 2-1 Somme des montants réglés
         * Rechercher la somme des montants déjà payés sur la facture
         */
        if ($mvtcourt == 'PAI') {
            try {
                //calcul des montants des PAI sur la FAC ou l'APP
                $select = "SELECT sum(mvtmontant)"
                        . "FROM comptabilite "
                        . "WHERE relationfacture = " . $relationfacture;
                $curseur = $connexion->query($select);
                $curseur->setFetchMode(PDO::FETCH_NUM);

                foreach ($curseur as $enregistrement) {
                    $montantpaye = $enregistrement[0];
                }
                $message .= "<br>montant total payé sur mouvement " . $relationfacture . " = " . $montantpaye . "<br>";
                $curseur->closeCursor();
            } catch (Exception $ex) {
                $message .= "<br>Echec de l'extraction des factures : " . $ex->getMessage();
            }

//2-2 extraire le montant total de la facture ou de l'appel de fonds
            try {
//calcul des montants des PAI sur la FAC ou l'APP
                $select = "SELECT mvtmontant "
                        . "FROM comptabilite "
                        . "WHERE numeromouvement = " . $relationfacture;
                $curseur = $connexion->query($select);
                $curseur->setFetchMode(PDO::FETCH_NUM);

                foreach ($curseur as $enregistrement) {
                    $montantdu = $enregistrement[0];
                }
                $message .= "<br>Montant dû = " . $montantdu . "<br>";
                $montantrestant = $montantdu - $montantpaye;
                $message .= "<br>Montant restant à payer sur la facture = " . $montantrestant . " <br>";
            } catch (Exception $ex) {
                $message .= "<br>Echec de l'extraction du montant de la facture relative : " . $ex->getMessage();
            }
//2-3 si le montant payé >= montantdu, la facture est soldée.
            $message .= "<br>Solder la facture si " . $montantrestant . "<= 0 <br>";

            if ($montantrestant <= 0) {
                $message .= "<br>Solder la facture";

                try {
                    $tAttributesValues = array();
                    $tAttributesValues['mvtstatut'] = "S";
                    $tAttributesValues['numeromouvement'] = $relationfacture;
                    $affected = updateGestionStatutSolde($connexion, $tAttributesValues);
                    if ($affected === 1) {
                        $message .= "<br>Facture soldée <br>";
                    } else {
                        $message .= "<br>Echec de l'action de solder la facture";
                    }
                } catch (PDOException $e) {
                    $message = "<br>Echec facture non soldée " . $e->getMessage();
                }
            } else {
                $message .= "<br>Ne pas solder la facture";
            }
        } else {
            $message .= "<br>n'est pas un paiement - 140";
        }
        //Appel de fonds du syndic vers tous les copros
        if ($mvtcourt == 'APP' && $idtiersemetteur == '25' && $idtiersreceveur == '33') {
            $message .= "<br>identification d'un appel de fonds de la copro : Générer les app - 143";

            // si l'appel de fonds est destiné à l'ensemble des copros, 
            // alors selection des tiers et des tantiemes pour caluler et créer les appels de fonds APP
            // envoyer les App par mail
            // conserver les mails sur le serveur
            // lister les emails envoyés dans la table medias
            // Select all des copros actifs
            try {
                //selection des PROPR actuels
                $select = "SELECT t.idtiers, t.nom, t.typetiers, l.numerolot, l.tantieme, t.prenom, c.libellecivilite, t.email "
                        . "FROM lienlot ll INNER JOIN tiers t  ON ll.iduserprincipal = t.idtiers "
                        . "INNER JOIN civilite c ON c.idcivilite = t.civilite "
                        . "INNER JOIN lots l ON l.idlot = ll.idlotlien "
                        . "WHERE t.idtiers != '33' AND t.typetiers = 'PROPR' AND l.idlot != '7' "
                        . "AND ll.datedebut <= NOW() "
                        . "AND ll.datefin >= NOW()";
                $curseur = $connexion->query($select);
                $curseur->setFetchMode(PDO::FETCH_NUM);

                // boucle création des appels de fonds pour chaque Copro

                foreach ($curseur as $enregistrement) {
                    $message .= "<br>debut boucle insert app $enregistrement[7] - 174";

                    //creation de chaque APP dans la table comptabilite
                    //initialisation des données
                    $mvtcourt = 'APP';
                    $idtiersemetteur = '25';
                    $idtiersreceveur = $enregistrement[0];
                    $mvtidlot = $enregistrement[3];
                    $prenom = $enregistrement[5];
                    $civilite = $enregistrement[6];
                    $email = $enregistrement[7];
                    $mvtstatut = 'C';
                    $mvtmontantenfant = filter_input(INPUT_POST, "mvtmontant") * $enregistrement[4] * 0.01;
                    $relationfacture = "";
                    //Insert de chaque appel de fonds dans comptabilite
                    try {
                        $tAttributesValues = array();
                        $tAttributesValues['mvtcourt'] = $mvtcourt;
                        $tAttributesValues['idtiersemetteur'] = $idtiersemetteur;
                        $tAttributesValues['idtiersreceveur'] = $idtiersreceveur;
                        $tAttributesValues['mvtidlot'] = $mvtidlot;
                        $tAttributesValues['mvtdate'] = $mvtdate;
                        $tAttributesValues['mvtecheance'] = $mvtecheance;
                        $tAttributesValues['mvtstatut'] = $mvtstatut;
                        $tAttributesValues['mvtmontant'] = $mvtmontantenfant;
                        $tAttributesValues['mvttexte'] = $mvttexte;
                        $tAttributesValues['relationfacture'] = $relationfacture;
                        $lastid = insertGestion($connexion, $tAttributesValues);
                        $message .= "<br>création app enfant " . $lastid;
                        $messagesuivimail = "";
                        //création du message du mail au destinataire de l'appel de fonds
                        if ($lastid != null) {
                            $message .= "<br>début mail" . $lastid;
                            //création du message du mail
                            $newFile = "appeldefonds_" . $lastid . "_" . $mvtdate . ".html";
                            $objetmail = "Appel de fonds de votre Syndic du Centre à Sainte Julie";
                            $textemail = "<!DOCTYPE html>
                                    <html lang='en'>
                                    <head>
                                    <meta charset='UTF-8'>
                                    </head>
                                    <body>
                                    <div>a l'attention de " . $civilite . " " . $prenom . " " . $enregistrement[1] . "</div>
                                    <div>mail " . $newFile . "</div>
                                    <br>
                                    <br>
                                    <h1><i>Syndic de la Copropriété du Centre à Sainte Julie</i></h1>
                                    <h1>Appels de fonds " . $lastid . " </h1>
                                    <div>Bonjour " . $civilite . " " . $prenom . " " . $enregistrement[1] . "<br>
                                    Le montant de votre quote-part de votre appel de fonds numéro " . $lastid . " du " . $mvtdate . " est de " . $mvtmontantenfant . " Euros; <br>
                                    avec une date d'échéance au  " . date('d-m-Y', strtotime($mvtecheance)) . ". <br>
                                    Son paiement serait de préférence à faire par virement, ou à défaut par chèque à l'attention de la Copropriété du Centre à Sainte Julie.<br>
                                    <br>
                                    Votre Syndic reste disponible pour tout complément d'information,<br>
                                    Meilleures salutations;<br></div>
                                    </body>
                                    </html>
                                    ";
                            //enregistrement de chaque mail en html sur le serveur

                            $open = fopen("../medias/" . $newFile, "w");
                            fwrite($open, $textemail);
                            fclose($open);
                            $message = "<br> fin enregistrement mail " . $newFile;

                            //Insert du mail dans la table medias
                            try {
                                $message .= "<br> insert media " . $newFile;
                                $descriptionmedia = "mail appel de fonds " . $lastid . "du " . $mvtdate . " " . $enregistrement[1];
                                $typemedia = "ADF";
                                $nommedia = $newFile;
                                $datemedia = date('Y-m-d');

                                $select = "INSERT INTO medias (idmedia, descriptionmedia, typemedia, nommedia, datemedia) VALUES (?, ?, ?, ?, ?) ";
                                $statement = $connexion->prepare($select);
                                $statement->bindValue(1, "");
                                $statement->bindValue(2, $descriptionmedia);
                                $statement->bindValue(3, $typemedia);
                                $statement->bindValue(4, $nommedia);
                                $statement->bindValue(5, $datemedia);
                                $statement->execute();

                                $affected = $statement->rowcount();
                                if ($affected === 1) {
                                    $message .= "<br>mail $newFile inséré dans medias avec succès";
                                } else {
                                    $message .= "<br>/!\ anomalie de l'insert du mail $newFile dans la table media";
                                }
                            } catch (Exception $ex) {
                                $message .= "<br>Echec de l'insert du mail d'appel de fonds " . $lastid . " dans la table medias : " . $ex->getMessage();
                            }

                            //Fin insert du mail dans la table media
                            //envoi du mail
                            $expediteur = "no-reply@syndicsaintejulie.fr";
                            // centre69.copro@gmail.com = mail de la copro
                            $copie = "gudel@free.fr";
                            $mail = new PHPMailer(true);
                            $message .= "<br>envoi du mail " . $newFile;
                            try {
                                //Server settings
                                $mail->CharSet = "UTF-8";
                                $mail->SMTPDebug = 0;                             //mettre 2 pour avoir tout le message, sinon 0 = pas de message
                                $mail->isSMTP();                                  //Send using SMTP
                                $mail->Host = 'xxx';                    //Set the SMTP server to send through
                                $mail->SMTPAuth = true;                           //Enable SMTP authentication
                                $mail->Username = 'xxx';    //SMTP username
                                $mail->Password = 'xxx';                  //SMTP password
                                $mail->SMTPSecure = 'xxx';                        //Enable implicit TLS encryption
                                $mail->Port = xxx;                                //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                                //Recipients
                                $mail->setFrom($expediteur);
                                $mail->addAddress($email);
                                $mail->addCC($copie);
                                $mail->isHTML(true);
                                $mail->Subject = $objetmail;
                                $mail->Body = $textemail;
                                $mail->sensitivity = 'Private';
                                $mail->send();

                                $message .= "<br>Message pour " . $email . " envoyé avec succès<br>";
                            } catch (Exception $e) {
                                $message .= "<br>Message to " . $email . " could not be sent. Mailer Error: {$mail->ErrorInfo}";
                            }//Fin d'envoi du mail
                        } else {
                            $message .= "<br>Pas de création d'appel de fonds : " . $affected;
                        }
                    } catch (PDOException $e) {
                        $message .= "<br>Echec de la création d'appel de fonds : " . $e->getMessage();
                    }
                } //fin de la boucle de création des appels de fonds
                //1-extraire les données de l'appel parent avant de le modifier pour annuler son statut
                try {
                    $connexion = seConnecter("../conf/infosServeur.ini");
                    $sql = "SELECT idtiersemetteur, idtiersreceveur, mvtidlot, mvtcourt, mvtdate, mvtecheance, "
                            . "mvtstatut, mvtmontant, mvttexte, relationfacture, media  FROM comptabilite WHERE numeromouvement = ? ";
                    $cursor = $connexion->prepare($sql);
                    $cursor->bindValue(1, $lastidparent);
                    $cursor->execute();
                    $statement = $cursor->fetch(PDO::FETCH_ASSOC);
                    $affected = $cursor->rowcount();
                    $idtiersemetteur = $statement["idtiersemetteur"];
                    $idtiersreceveur = $statement["idtiersreceveur"];
                    $mvtidlot = $statement["mvtidlot"];
                    $mvtcourt = $statement["mvtcourt"];
                    $mvtecheance = $statement["mvtecheance"];
                    $mvtstatut = $statement["mvtstatut"];
                    $mvttexte = "mouvement parent " . $statement["mvttexte"];
                    $relationfacture = $statement["relationfacture"];
                    $media = $statement["media"];
                    if ($affected === 1) {
                        $message .= "<br>appel de fonds parent $lastidparent sélectionné pour modifier son statut à Annulé";
                    } else {
                        $message .= "<br>appel de fonds parent à annuler non trouvé";
                    }
                } catch (Exception $ex) {
                    $message .= "<br>Echec du select de l'appel de fonds à annuler" . $ex->getMessage();
                }

                //2-update statut "annulé" sur l'appel de fonds parent sélectionné
                try {
                    $connexion = seConnecter("../conf/infosServeur.ini");
                    $sql = "UPDATE comptabilite SET idtiersemetteur= ?, idtiersreceveur = ?, mvtidlot = ?, mvtcourt = ?, mvtdate = ?, mvtecheance = ?, "
                            . "mvtstatut = ?, mvtmontant = ?, mvttexte = ?, relationfacture = ?, media = ? "
                            . "WHERE numeromouvement = ?";

                    $cursor = $connexion->prepare($sql);
                    $cursor->bindValue(1, $idtiersemetteur);
                    $cursor->bindValue(2, $idtiersreceveur);
                    $cursor->bindValue(3, $mvtidlot);
                    $cursor->bindValue(4, $mvtcourt);
                    $cursor->bindValue(5, $mvtdate);
                    $cursor->bindValue(6, $mvtecheance);
                    $cursor->bindValue(7, "A");
                    $cursor->bindValue(8, $mvtmontant);
                    $cursor->bindValue(9, $mvttexte);
                    $cursor->bindValue(10, $relationfacture);
                    $cursor->bindValue(11, $media);

                    $cursor->bindValue(12, $lastidparent);
                    $cursor->execute();
                    $affected = $cursor->rowcount();
                    if ($affected === 1) {
                        $message .= "<br>mouvement parent $lastidparent statut annulé";
                    } else {
                        $message .= "<br>Echec : mouvement parent $lastidparent NON annulé";
                    }
                } catch (Exception $ex) {
                    $affected = -1;
                    $message .= "<br>Echec : statut de l'appel de fonds parent non mis à jour " . $eX->getMessage();
                }
            } catch (Exception $e) {  //fin try -152
                $message = "<br>Echec de l'exécution : " . $e->getMessage();
            }
        } else {
            $message .= "<br>Il ne s'agit pas d'un appel de fonds du syndic vers tous les Copros";
        }
    } catch (PDOException $e) {
        $message .= "<br>Echec de la création du mouvement" . $e->getMessage();
    }
} else {
    $message .= "<br>Données manquantes : Veuillez saisir les informations nécessaires";
}
$connexion = null;
echo $message;
include "../controls/gestionPortailCTRL.php";
?>
