<?php
    /*
        Crée toutes les tables en relation avec le groupe.
    */
    function cree_table_groupe() {
        // la fonction bdd() renvoie l'instance de connexion à la base de données.
        $conn = bdd();

        $query = $conn->prepare("CREATE TABLE IF NOT EXISTS Groupe (
            id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            PRIMARY KEY (id)
        ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
        $query->execute();
        $query->close();

        $query = $conn->prepare("CREATE TABLE IF NOT EXISTS UtilisateurGroupe (
            userId INT NOT NULL,
            groupId INT NOT NULL,
            CONSTRAINT pk_UserGroupe PRIMARY KEY (userId, groupId),
            CONSTRAINT fk_UserGroupe_1 FOREIGN KEY (userId) REFERENCES Utilisateur(id),
            CONSTRAINT fk_UserGroupe_2 FOREIGN KEY (groupId) REFERENCES Groupe(id)
        ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
        $query->execute();
        $query->close();

        $query = $conn->prepare("CREATE TABLE IF NOT EXISTS Invitation (
            userId INT NOT NULL,
            groupeId INT NOT NULL,
            CONSTRAINT pk_Invite PRIMARY KEY (userId, groupeId),
            CONSTRAINT fk_Invite_1 FOREIGN KEY (userId) REFERENCES Utilisateur(id),
            CONSTRAINT fk_Invite_2 FOREIGN KEY (groupeId) REFERENCES Groupe(id)
        ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
        $query->execute();
        $query->close();
    }

    /*
        Ajoute un groupe.
        @param nom : le nom du groupe.
        @param id_proprietaire : l'id du propriétaire du groupe.
        @return si le groupe a été ajouté ou non.
    */
    function ajoute_groupe($nom, $id_proprietaire) {
        return false;
    }

    /*
        Sélectionne le groupe selon son id.
        @param id : l'id du groupe.
        @return l'objet groupe s'il est trouvé avec : id, nom, id_proprietaire; null sinon.
    */
    function recupere_groupe_par_id($id) {
        return null;
    }

    /*
        Sélectionne la liste des groupes selon leur id.
        @param ids : la liste des ids du groupe.
        @return la liste des groupes avec : id, nom, membres (liste avec : id, login, valide).
    */
    function recupere_groupe_par_ids($ids) {
        return array();
    }

    /*
        Sélectionne la liste des ids des groupes selon l'id de leur propriétaire.
        @param id_proprietaire : l'id du propriétaire.
        @return la liste des ids.
    */
    function recupere_id_groupe_par_proprietaire($id_proprietaire) {
        return array();
    }

    /*
        Sélectionne la liste des ids des groupes où se trouve l'utilisateur connecté.
        @param id_utilisateur : l'id de l'utilisateur connecté.
        @return la liste des ids.
    */
    function recupere_id_groupe_par_membre($id_utilisateur) {
        return array();
    }

    /*
        Sélectionne la liste des ids des groupes où ne se trouve pas l'utilisateur connecté.
        @param id_utilisateur : l'id de l'utilisateur connecté.
        @return la liste des ids.
    */
    function recupere_id_groupe_par_non_membre($id_utilisateur) {
        return array();
    }

    /*
        Supprime un groupe.
        @param id_proprietaire : l'id du propriétaire.
        @param id_groupe : l'id du groupe.
        @return si le groupe a été supprimé ou non.
    */
    function supprime_groupe($id_proprietaire, $id_groupe) {
        return false;
    }

    /*
        Ajoute un membre dans un groupe.
        @param id_utilisateur : l'id du membre.
        @param id_groupe : l'id du groupe.
        @return si le membre a été ajouté ou non.
    */
    function ajoute_membre($id_utilisateur, $id_groupe) {
        return false;
    }

    /*
        Valide un membre dans un groupe.
        @param id_utilisateur : l'id du membre.
        @param id_proprietaire : l'id du propriétaire.
        @param id_groupe : l'id du groupe.
        @return si le membre a été validé ou non.
    */
    function valide_membre($id_utilisateur, $id_proprietaire, $id_groupe) {
        return false;
    }

    /*
        Supprime un membre dans un groupe.
        @param id_utilisateur : l'id du membre.
        @param id_groupe : l'id du groupe.
        @return si le membre a été supprimé ou non.
    */
    function supprime_membre($id_utilisateur, $id_groupe) {
        return false;
    }
