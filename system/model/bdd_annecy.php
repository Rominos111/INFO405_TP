<?php

  $mysqli = null;

  function connexion() {
    if (basename(getcwd()) != 'system') {
      if ($GLOBALS['mysqli'] === null)  {
        $GLOBALS['mysqli'] = new mysqli('localhost', $GLOBALS['utilisateur'], $GLOBALS['mot_de_passe']);
        $GLOBALS['mysqli']->query('CREATE DATABASE IF NOT EXISTS ' . $GLOBALS['utilisateur']);
              $GLOBALS['mysqli']->select_db($GLOBALS['utilisateur']);

              cree_table_utilisateur();
        cree_table_sujet();
        cree_table_tag();
        cree_table_message();
        cree_table_groupe();
      }
    } else {
      $GLOBALS['mysqli'] = null;
    }

    return $GLOBALS['mysqli'];
  }
