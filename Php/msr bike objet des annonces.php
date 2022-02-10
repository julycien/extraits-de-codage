<?php

declare(strict_types = 1);

class Annonces {

    private $idAnnonce;
    private $idVendeur;
    private $idAcheteur;
    private $idCategorie;
    private $idMarque;
    private $dateAnnonce;
    private $dateMel;
    private $dateFinMel;
    private $dateVente;
    private $prixVente;
    private $immatriculation;
    private $dateMec;
    private $couleur;
    private $texteCourt;
    private $texteLong;
    private $photoPrincipale;
    private $nomModele;
    private $cylindree;
    private $annee;
    private $permis;
    private $kilometrage;

    public function __construct($idAnnonce = "", $idVendeur = "", $idAcheteur = "", $idCategorie = "", $idMarque = "", $dateAnnonce = "", $dateMel = "", $dateFinMel = "", $dateVente = "", $prixVente = "", $immatriculation = "", $dateMec = "", $couleur = "", $texteCourt = "", $texteLong = "", $photoPrincipale = "", $nomModele = "", $cylindree = "", $annee = "", $permis = "", $kilometrage = "") {
        $this->idAnnonce = $idAnnonce;
        $this->idVendeur = $idVendeur;
        $this->idAcheteur = $idAcheteur;
        $this->idCategorie = $idCategorie;
        $this->idMarque = $idMarque;
        $this->dateAnnonce = $dateAnnonce;
        $this->dateMel = $dateMel;
        $this->dateFinMel = $dateFinMel;
        $this->dateVente = $dateVente;
        $this->prixVente = $prixVente;
        $this->immatriculation = $immatriculation;
        $this->dateMec = $dateMec;
        $this->couleur = $couleur;
        $this->texteCourt = $texteCourt;
        $this->texteLong = $texteLong;
        $this->photoPrincipale = $photoPrincipale;
        $this->nomModele = $nomModele;
        $this->cylindree = $cylindree;
        $this->annee = $annee;
        $this->permis = $permis;
        $this->kilometrage = $kilometrage;
    }

    function getNomModele() {
        return $this->nomModele;
    }

    function getCylindree() {
        return $this->cylindree;
    }

    function getAnnee() {
        return $this->annee;
    }

    function getPermis() {
        return $this->permis;
    }

    function getIdAnnonce() {
        return $this->idAnnonce;
    }

    function getIdVendeur() {
        return $this->idVendeur;
    }

    function getIdAcheteur() {
        return $this->idAcheteur;
    }

    function getIdCategorie() {
        return $this->idCategorie;
    }

    function getIdMarque() {
        return $this->idMarque;
    }

    function getDateAnnonce() {
        return $this->dateAnnonce;
    }

    function getDateMel() {
        return $this->dateMel;
    }

    function getDateFinMel() {
        return $this->dateFinMel;
    }

    function getDateVente() {
        return $this->dateVente;
    }

    function getPrixVente() {
        return $this->prixVente;
    }

    function getImmatriculation() {
        return $this->immatriculation;
    }

    function getDateMec() {
        return $this->dateMec;
    }

    function getCouleur() {
        return $this->couleur;
    }

    function getTexteCourt() {
        return $this->texteCourt;
    }

    function getTexteLong() {
        return $this->texteLong;
    }

    function getPhotoPrincipale() {
        return $this->photoPrincipale;
    }
    
    function getKilometrage() {
        return $this->kilometrage;
    }

    function setIdAnnonce($idAnnonce) {
        $this->idAnnonce = $idAnnonce;
    }

    function setIdVendeur($idVendeur) {
        $this->idVendeur = $idVendeur;
    }

    function setIdAcheteur($idAcheteur) {
        $this->idAcheteur = $idAcheteur;
    }

    function setIdCategorie($idCategorie) {
        $this->idCategorie = $idCategorie;
    }

    function setIdMarque($idMarque) {
        $this->idMarque = $idMarque;
    }
    function setDateAnnonce($dateAnnonce) {
        $this->dateAnnonce = $dateAnnonce;
    }

    function setDateMel($dateMel) {
        $this->dateMel = $dateMel;
    }

    function setDateFinMel($dateFinMel) {
        $this->dateFinMel = $dateFinMel;
    }

    function setDateVente($dateVente) {
        $this->dateVente = $dateVente;
    }

    function setPrixVente($prixVente) {
        $this->prixVente = $prixVente;
    }

    function setImmatriculation($immatriculation) {
        $this->immatriculation = $immatriculation;
    }

    function setDateMec($dateMec) {
        $this->dateMec = $dateMec;
    }

    function setCouleur($couleur) {
        $this->couleur = $couleur;
    }

    function setTexteCourt($texteCourt) {
        $this->texteCourt = $texteCourt;
    }

    function setTexteLong($texteLong) {
        $this->texteLong = $texteLong;
    }

    function setPhotoPrincipale($photoPrincipale) {
        $this->photoPrincipale = $photoPrincipale;
    }

    function setNomModele($nomModele) {
        $this->nomModele = $nomModele;
    }

    function setCylindree($cylindree) {
        $this->cylindree = $cylindree;
    }

    function setAnnee($annee) {
        $this->annee = $annee;
    }

    function setPermis($permis) {
        $this->permis = $permis;
    }

    function setKilometrage($kilometrage) {
        $this->kilometrage = $kilometrage;
    }

}
