<?php
    $page = "dashboard";
    if (isset($_GET['bookmark'])) {
        ajoute_ou_supprime_favori($_SESSION['user']['id'], $_GET['bookmark']);
        header("location: ./" . (isset($_GET['page']) ? "?page=bookmark" : ""));
    }
    if (isset($_GET['page'])) {
        switch ($_GET['page']) {
        case "search":
            unset($_GET['page']);
            $page = "search";
            break;
        case "subject":
            $page = "subject";
            if ((!isset($_GET['id']) || intval($_GET['id']) == 0) && (!isset($_GET['p']) || intval($_GET['p']) == 0)) {
                header("location: ./?page=subject&p=1");
            }
            break;
        case "group":
            $page = "group";
            if (isset($_GET['id']) && isset($_GET['state'])) {
                switch($_GET['state']) {
                case "ok":
                    if (isset($_GET['id_membre'])) {
                        valide_membre($_GET['id_membre'], $_SESSION['user']['id'], $_GET['id']);
                    }
                    break;
                case "in":
                    ajoute_membre($_SESSION['user']['id'], $_GET['id']);
                    break;
                case "out":
                    supprime_membre($_SESSION['user']['id'], $_GET['id']);
                    break;
                case "del":
                    supprime_groupe($_SESSION['user']['id'], $_GET['id']);
                    break;
                }
                header("location: ./?page=group");
            }
            break;
        case "message":
            $page = 'list';
            break;
        case "create":
        case "account":
        case "bookmark":
            $page = $_GET['page'];
            break;
        case "deconnection":
            unset($_SESSION['user']);
            header("location: ./");
            break;
        }
    }
