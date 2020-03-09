<?php
    include_once "utils.php";

    /*
        Crée toutes les tables en relation avec le message.
    */
    function cree_table_message() {
        basicSqlRequest("CREATE TABLE IF NOT EXISTS Message (
                id INT NOT NULL,
                content VARCHAR(100) NOT NULL,
                sujetIdDestination INT NOT NULL,
                senderId INT NOT NULL,
                CONSTRAINT pk_Message PRIMARY KEY (id),
                CONSTRAINT fk_Message_1 FOREIGN KEY (senderId) REFERENCES Utilisateur(id),
                CONSTRAINT fk_Message_2 FOREIGN KEY (sujetIdDestination) REFERENCES Sujet(id)
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
    }

    /*
        Ajoute un message.
        @param texte : le texte du message.
        @param id_sujet : l'id du sujet du message.
        @param id_auteur : l'id de l'auteur du message.
        @return si le message a été ajouté ou non.
    */
    function ajoute_message($texte, $id_sujet, $id_auteur) {
        return false;
    }

    /*
        Sélectionne les messages selon le sujet.
        @param id_sujet : l'id du sujet du message
        @return la liste des messages avec : id, texte, login (le login de l'auteur), date_creation.
    */
    function recupere_message_par_sujet($id_sujet) {
        return array();
    }

    /*
        Supprime un message.
        @param id : l'id du message.
        @return si le message a été supprimé ou non.
    */
    function supprime_message($id) {
        return false;
    }
