<?php
    if (isset($_POST['information_update'])) {
        modifie_information_utilisateur_form();
    } else if (isset($_POST['password_update'])) {
        modifie_mot_de_passe_utilisateur_form();
    } else if (isset($_POST['search'])) {
        header("location: ./?page=search&" . http_build_query(json_decode($_POST['tags'])));
    } else if (isset($_POST['subject_add'])) {
        ajoute_sujet_form();
    } else if (isset($_POST['message_add'])) {
        ajoute_message_form();
    } else if (isset($_POST['group_add'])) {
        ajoute_groupe_form();
    }

    function modifie_information_utilisateur_form() {
        $result = modifie_information_utilisateur($_SESSION['user']['id'],
                isset($_POST['niveau']) ? $_POST['niveau'] : 'NULL', array(
                    1 => isset($_POST['competence_web']),
                    2 => isset($_POST['competence_mobile']),
                    3 => isset($_POST['competence_serveur'])
                ), $_POST['message']);
        if ($result) {
            $_SESSION['toast'] = "Les informations ont été mises à jour avec succès.";
            header("location: ./");
        } else {
            $_SESSION['toast'] = "Une erreur s'est produite lors de la mise à jour des données.";
        }
    }

    function modifie_mot_de_passe_utilisateur_form() {
        $result = modifie_mot_de_passe_utilisateur($_SESSION['user']['id'],
                $_POST['ancien_mot_de_passe'], $_POST['mot_de_passe'], $_POST['confirmation']);
        if ($result) {
            $_SESSION['toast'] = "Le mot de passe été mis à jour avec succès.";
            header("location: ./");
        } else {
            $_SESSION['toast'] = "Une erreur s'est produite lors de la mise à jour du mot de passe.";
        }
    }

    function ajoute_sujet_form() {
        $result = ajoute_sujet($_POST['titre'], $_SESSION['user']['id'],
                $_POST['description'], $_POST['image'], json_decode($_POST['tags']));
        if ($result) {
            $_SESSION['toast'] = "Le sujet a bien été créé.";
            header("location: ./");
        } else {
            $_SESSION['toast'] = "Une erreur s'est produite lors de la création du sujet.";
        }
    }

    function ajoute_message_form() {
        $result = ajoute_message($_POST['message'], $_GET['id'], $_SESSION['user']['id']);
        if ($result) {
            $_SESSION['toast'] = "Le message a bien été créé.";
        } else {
            $_SESSION['toast'] = "Une erreur s'est produite lors de la création du message.";
        }
    }

    function ajoute_groupe_form() {
        $result = ajoute_groupe($_POST['nom'], $_SESSION['user']['id']);
        if ($result) {
            $_SESSION['toast'] = "Le groupe a bien été créé.";
        } else {
            $_SESSION['toast'] = "Une erreur s'est produite lors de la création du groupe.";
        }
    }
