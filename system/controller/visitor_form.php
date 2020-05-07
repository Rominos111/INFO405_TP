<?php
    if (isset($_POST['subscription'])) {
        inscrit_utilisateur_form();
    } else if (isset($_POST['connection'])) {
        connecte_utilisateur_form();
    }

    function inscrit_utilisateur_form() {
        $result = inscrit_utilisateur($_POST['login'], $_POST['mot_de_passe'], $_POST['confirmation'],
                $_POST['date_de_naissance'], isset($_POST['niveau']) ? $_POST['niveau'] : 'NULL', array(
                    1 => isset($_POST['competence_web']),
                    2 => isset($_POST['competence_mobile']),
                    3 => isset($_POST['competence_serveur'])
                ), $_POST['message']);
        if ($result) {
            connecte_utilisateur_form();
        } else {
            $_SESSION['toast'] = "Les champs requis n'ont pas été saisis.";
        }
    }

    function connecte_utilisateur_form() {
        $user = connecte_utilisateur($_POST['login'], $_POST['mot_de_passe']);
        if ($user) {
            $_SESSION['user'] = $user;
            header("location: ./");
        } else {
            $_SESSION['toast'] = "Il n'y a aucun compte qui ne correspond.";
        }
    }
