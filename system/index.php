<?php
    include_once "model/bdd.php";
    if (isset($bdd) && (bdd() !== null)) {
        session_start();
        include_once "controller/action.php";
        include_once "view/template.php";
    } else {
        echo "<h1>Vous ne passerez pas !</h1>";
    }
