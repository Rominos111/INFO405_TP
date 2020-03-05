<?php
    /*
        Crée toutes les tables en relation avec le message.
    */
    function cree_table_message() {
        // la fonction bdd() renvoie l'instance de connexion à la base de données.
        $conn = bdd();

        $conn->query("CREATE TABLE Message (
            idMessage INT NOT NULL,
            content VARCHAR(45) NOT NULL,
            sujetIdDestination INT NOT NULL,
            senderLogin VARCHAR(45) NOT NULL,
            PRIMARY KEY (idMessage),
            CONSTRAINT expediteur
              FOREIGN KEY (senderLogin)
              REFERENCES User (login),
            CONSTRAINT sujetDestination
              FOREIGN KEY (sujetIdDestination)
              REFERENCES Sujet (idSujet)
              )");

        $conn->close();
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
