<?php
    if (isset($_SESSION['user'])) {
        include_once("user_url.php");
        include_once("user_form.php");
    } else {
        include_once("visitor_url.php");
        include_once("visitor_form.php");
    }
