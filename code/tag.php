<?php
    /*
        Crée toutes les tables en relation avec le tag.
    */
    function cree_table_tag() {
        // la fonction bdd() renvoie l'instance de connexion à la base de données.
        $conn = bdd();

        $query = $conn->prepare("CREATE TABLE IF NOT EXISTS Tag (
            nameTag VARCHAR(100) NOT NULL,
            CONSTRAINT pk_Tag PRIMARY KEY (nameTag)
        ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
        $query->execute();

        $query = $conn->prepare("CREATE TABLE IF NOT EXISTS SujetTag (
            sujetId INT NOT NULL,
            tagName VARCHAR(100) NOT NULL,
            CONSTRAINT pk_SujetTag PRIMARY KEY (sujetId, tagName),
            CONSTRAINT fk_SujetTag_1 FOREIGN KEY (sujetId) REFERENCES Sujet(idSujet),
            CONSTRAINT fk_SujetTag_2 FOREIGN KEY (tagName) REFERENCES Tag(nameTag)
        ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
        $query->execute();
    }

    /*
        Ajoute la liste des tags.
        @param id_sujet : l'id du sujet de la liste des tags.
        @param tags : la liste des tags.
        @return si les tags ont été ajoutés ou non.
    */
    function ajoute_tag($id_sujet, $tags) {
        return false;
    }

    /*
        Sélectionne la liste de tous les tags.
        @return la liste de tous les tags (liste avec pour clé le nom des tags et pour valeur null).
    */
    function recupere_tag() {
        return array();
    }

    /*
        Sélectionne la liste des tags selon un sujet.
        @return la liste des tags selon un sujet.
    */
    function recupere_tag_par_sujet($id_sujet) {
        return array();
    }
