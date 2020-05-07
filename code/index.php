<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once "utils.php";

    // C'est ici que le code PHP en lien avec le modèle du site doit être écrit.
    include_once "utilisateur.php";
    include_once "sujet.php";
    include_once "tag.php";
    include_once "message.php";
    include_once "groupe.php";

    // Précise les identifiants pour se connecter à la base de données.
    $utilisateur = "info_1_gr_1";
    $mot_de_passe = "mp si tu veux le mdp";

    // Inclut l'autre moitié du site (connexion à la base, vue et contrôleur).
    require_once "../system/index.php";
