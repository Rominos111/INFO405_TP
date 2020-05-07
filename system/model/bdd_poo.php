<?php

    $_bdd = null;
    function bdd() {
        if (basename(getcwd()) != 'system') {
            if ($GLOBALS['_bdd'] === null)  {
                $GLOBALS['_bdd'] = new mysqli('localhost', $GLOBALS['utilisateur'], $GLOBALS['mot_de_passe']);
                $GLOBALS['_bdd']->query('CREATE DATABASE IF NOT EXISTS ' . $GLOBALS['utilisateur']);
                $GLOBALS['_bdd']->select_db($GLOBALS['utilisateur']);

                cree_table_utilisateur();
                cree_table_sujet();
                cree_table_tag();
                cree_table_message();
                cree_table_groupe();
            }
        } else {
            $GLOBALS['_bdd'] = null;
        }

        return $GLOBALS['_bdd'];
    }
