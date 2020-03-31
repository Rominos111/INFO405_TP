<?php
    include_once "utils.php";

    /*
        Crée toutes les tables en relation avec le groupe.
    */
    function cree_table_groupe() {
        basicSqlRequest("CREATE TABLE IF NOT EXISTS Groupe (
                id INT NOT NULL,
                name VARCHAR(100) NOT NULL,
                creatorId INT NOT NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_creator FOREIGN KEY (creatorId) REFERENCES Utilisateur(Id)
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");

        basicSqlRequest("CREATE TABLE IF NOT EXISTS UtilisateurGroupe (
                userId INT NOT NULL,
                groupId INT NOT NULL,
                CONSTRAINT pk_UserGroupe PRIMARY KEY (userId, groupId),
                CONSTRAINT fk_UserGroupe_1 FOREIGN KEY (userId) REFERENCES Utilisateur(id),
                CONSTRAINT fk_UserGroupe_2 FOREIGN KEY (groupId) REFERENCES Groupe(id)
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");

        basicSqlRequest("CREATE TABLE IF NOT EXISTS Invitation (
                userId INT NOT NULL,
                groupeId INT NOT NULL,
                CONSTRAINT pk_Invite PRIMARY KEY (userId, groupeId),
                CONSTRAINT fk_Invite_1 FOREIGN KEY (userId) REFERENCES Utilisateur(id),
                CONSTRAINT fk_Invite_2 FOREIGN KEY (groupeId) REFERENCES Groupe(id)
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
    }

    /*
        Ajoute un groupe.
        @param nom : le nom du groupe.
        @param id_proprietaire : l'id du propriétaire du groupe.
        @return si le groupe a été ajouté ou non.
    */
    function ajoute_groupe($nom, $id_proprietaire) {
        $res = true;
        $bdd = bdd();
        //création du grp
        $sql = "INSERT INTO Groupe
                (name, creatorId)
                VALUES
                (?,?)";

        $query = $bdd->prepare($sql);
        $query->bind_param("si", $nom, $id_proprietaire);
        $ok = $query->execute();
        $query -> close();

        //recupération de l'id du grp
        $id_groupe = mysqli_insert_id($bdd);

        //ajout du proprio 
        $res = $ok && ajoute_membre($id_proprietaire, $id_groupe);

        return $res;
    }

    /*
        Sélectionne le groupe selon son id.
        @param id : l'id du groupe.
        @return l'objet groupe s'il est trouvé avec : id, nom, id_proprietaire; null sinon.
    */
    function recupere_groupe_par_id($id) {
        $grp = array();

        //recup infos
        $sql = "SELECT name, creatorId
                FROM Groupe
                WHERE
                id = ?";

        $query = $bdd->prepare($sql);
        $query->bind_param("i", $id);
        $ok = $query->execute();
        
        if ($ok) {
            $query->bind_result($name);
            $query->bind_result($creatorId);

            $grp = array(
                "id" => $id,
                "nom" => $name,
                "id_proprietaire" => $creatorId
            );
        }
        else {
            echo "ERR";
            var_dump($query->error);
        }

        $query -> close();

        return $grp;
    }

    /**
     * Fonction qui récupère les utilisateurs selon l'id d'un groupe
     * 
     * @param id : identifiant du groupe
     * @return : la liste de utilisateurs associés au groupe
     */
    function recupere_utilisateurs_par_id_grp($id){
        $users = array();
        //recupérations des ids
        $sql = "SELECT userId
                FROM UtilisateurGroupe
                WHERE
                groupeId = ?";

        $query = $bdd->prepare($sql);
        $query->bind_param("i", $id);
        $ok = $query->execute();

        if($ok){
            while($query->fetch()){
                $query->bind_result($id_user);
                $users[] = recupere_utilisateur($id_user);
                $i++;
            }
        }
    }

    /*
        Sélectionne la liste des groupes selon leur id.
        @param ids : la liste des ids du groupe.
        @return la liste des groupes avec : id, nom, membres (liste avec : id, login, valide).
    */
    function recupere_groupe_par_ids($ids) {
        $res = array();

        foreach($ids as $id){
            $res[]["grp"] = recupere_groupe_par_id($id);
            $res[]["users"] = recupere_utilisateurs_par_id_grp($id);
        }

        return $res;
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
        $res = false;
    
        $sql = "INSERT INTO UtilisateurGroupe
                (userId, groupId)
                VALUES
                (?,?)";

        $query = $bdd->prepare($sql);
        $query->bind_param("ii", $id_utilisateur, $id_groupe);
        $res = $query->execute();
        $query -> close();

        return $res;
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
