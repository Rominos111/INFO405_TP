<?php
    include_once "utils.php";

    define("GROUP_NAME_MIN_SIZE", 3);
    define("GROUP_NAME_MAX_SIZE", 127);

    /**
     * Crée toutes les tables en relation avec le groupe.
     */
    function cree_table_groupe() {
        basicSqlRequest("CREATE TABLE IF NOT EXISTS Groupe (
                id INT NOT NULL AUTO_INCREMENT,
                name VARCHAR(128) NOT NULL,
                creatorId INT NOT NULL,
                CONSTRAINT pk_Groupe PRIMARY KEY (id),
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
                groupId INT NOT NULL,
                CONSTRAINT pk_Invite PRIMARY KEY (userId, groupId),
                CONSTRAINT fk_Invite_1 FOREIGN KEY (userId) REFERENCES Utilisateur(id),
                CONSTRAINT fk_Invite_2 FOREIGN KEY (groupId) REFERENCES Groupe(id)
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
    }

    /**
     * Ajoute un groupe.
     *
     * @param nom : le nom du groupe.
     * @param id_proprietaire : l'id du propriétaire du groupe.
     *
     * @return si le groupe a été ajouté ou non.
     */
    function ajoute_groupe($nom, $id_proprietaire) {
        $ok = false;

        if (strlen($nom) >= GROUP_NAME_MIN_SIZE && strlen($nom) <= GROUP_NAME_MAX_SIZE) {
            $conn = bdd();
            $sql = "INSERT INTO Groupe (name, creatorId)
                    VALUES (?,?)";

            $query = $conn->prepare($sql);
            $query->bind_param("si", $nom, $id_proprietaire);
            $ok = $query->execute();

            if ($ok) {
                $id_groupe = mysqli_insert_id($conn);
                $query->close();

                $ok = ajoute_membre($id_proprietaire, $id_groupe);
            }
            else {
                logCustomMessage($query->error);
                $query->close();
            }
        }

        return $ok;
    }

    /**
     * Sélectionne le groupe selon son id.
     *
     * @param id : l'id du groupe.
     *
     * @return l'objet groupe s'il est trouvé avec : id, nom, id_proprietaire; null sinon.
     */
    function recupere_groupe_par_id($id) {
        $grp = null;

        //recup infos
        $sql = "SELECT name, creatorId
                FROM Groupe
                WHERE id = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("i", $id);
        $ok = $query->execute();

        if ($ok) {
            $query->bind_result($name, $creatorId);
            $query->fetch();

            $grp = array(
                "id" => $id,
                "nom" => $name,
                "id_proprietaire" => $creatorId
            );
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();

        return $grp;
    }

    /**
     * Fonction qui récupère les utilisateurs selon l'id d'un groupe
     *
     * @param id : identifiant du groupe
     *
     * @return : la liste de utilisateurs associés au groupe
     */
    function recupere_utilisateurs_par_id_grp($id) {
        $users = array();
        $ids = array();

        $sql = "SELECT userId
                FROM UtilisateurGroupe
                WHERE groupId = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("i", $id);
        $ok = $query->execute();

        if ($ok) {
            $query->bind_result($userId);

            while ($query->fetch()) {
                $ids[] = $userId;
            }
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();

        foreach ($ids as $id) {
            list($login, $ok) = getLoginFromId($id);

            if ($ok) {
                $users[] = array (
                    "id" => $id,
                    "login" => $login,
                    "valide" => true
                );
            }
        }

        return $users;
    }

    /**
     * Sélectionne la liste des groupes selon leur id.
     *
     * @param ids : la liste des ids du groupe.
     *
     * @return la liste des groupes avec : id, nom, membres (liste avec : id, login, valide).
     */
    function recupere_groupe_par_ids($ids) {
        $res = array();

        foreach($ids as $id){
            $groupe = recupere_groupe_par_id($id);

            $res[] = array (
                "id" => $id,
                "nom" => $groupe["nom"],
                "membres" => recupere_utilisateurs_par_id_grp($id)
            );
        }

        return $res;
    }

    /**
     * Sélectionne la liste des ids des groupes selon l'id de leur propriétaire.
     *
     * @param id_proprietaire : l'id du propriétaire.
     *
     * @return la liste des ids.
    */
    function recupere_id_groupe_par_proprietaire($id_proprietaire) {
        $sql = "SELECT id
                FROM Groupe
                WHERE creatorId = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("i", $id_proprietaire);
        $ok = $query->execute();

        $res = array();

        if ($ok) {
            $query->bind_result($id);

            while ($query->fetch()) {
                $res[] = $id;
            }
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();

        return $res;
    }

    /**
     * Sélectionne la liste des ids des groupes où se trouve l'utilisateur connecté.
     *
     * @param id_utilisateur : l'id de l'utilisateur connecté.
     *
     * @return la liste des ids.
     */
    function recupere_id_groupe_par_membre($id_utilisateur) {
        $sql = "SELECT groupId
                FROM UtilisateurGroupe
                WHERE userId = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("i", $id_utilisateur);
        $ok = $query->execute();

        $res = array();

        if ($ok) {
            $query->bind_result($id);

            while ($query->fetch()) {
                $res[] = $id;
            }
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();

        return $res;
    }

    /**
     * Sélectionne la liste des ids des groupes où ne se trouve pas l'utilisateur connecté.
     *
     * @param id_utilisateur : l'id de l'utilisateur connecté.
     *
     * @return la liste des ids.
     */
    function recupere_id_groupe_par_non_membre($id_utilisateur) {
        $sql = "SELECT id
                FROM Groupe";

        $query = bdd()->prepare($sql);
        $ok = $query->execute();

        $res = array();
        $allGroupId = array();

        if ($ok) {
            $query->bind_result($id);

            while ($query->fetch()) {
                $allGroupId[$id] = false;
            }

            $query->close();



            $myGroupsId = recupere_id_groupe_par_membre($id_utilisateur);

            foreach ($myGroupsId as $id) {
                $allGroupId[$id] = true;
            }
        }
        else {
            logCustomMessage($query->error);
            $query->close();
        }

        foreach ($allGroupId as $id => $value) {
            if (!$value) {
                $res[] = $id;
            }
        }

        return $res;
    }

    /**
     * Supprime un groupe.
     *
     * @param id_proprietaire : l'id du propriétaire.
     * @param id_groupe : l'id du groupe.
     *
     * @return si le groupe a été supprimé ou non.
     */
    function supprime_groupe($id_proprietaire, $id_groupe) {
        $ok = supprime_membre($id_proprietaire, $id_groupe);
    }

    /**
     * Ajoute un membre dans un groupe.
     *
     * @param id_utilisateur : l'id du membre.
     * @param id_groupe : l'id du groupe.
     *
     * @return si le membre a été ajouté ou non.
     */
    function ajoute_membre($id_utilisateur, $id_groupe) {
        $sql = "INSERT INTO UtilisateurGroupe (userId, groupId)
                VALUES (?, ?)";

        $query = bdd()->prepare($sql);
        $query->bind_param("ii", $id_utilisateur, $id_groupe);
        $ok = $query->execute();

        if (!$ok) {
            logCustomMessage($query->error);
        }

        $query->close();

        return $ok;
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
    FAIT MAISON
        Valide un membre dans un groupe.
        @param id_utilisateur : l'id du membre.
        @param id_proprietaire : l'id du propriétaire.
        @param id_groupe : l'id du groupe.
        @return si le membre a été validé ou non.
    */
    function membre_en_attente($id_utilisateur, $id_groupe) {
        $sql = "SELECT userId
                FROM Invitation
                WHERE userId = ?
                AND groupId = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("ii", $id_utilisateur, $id_groupe);
        $ok = $query->execute();

        if ($ok) {
            $query->bind_result($userId);

            $ok = $query->fetch();
            // Si l'utilisateur est dans la table ou non
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();

        return $ok;
    }

    /**
     * Supprime un membre dans un groupe.
     *
     * @param id_utilisateur : l'id du membre.
     * @param id_groupe : l'id du groupe.
     *
     * @return si le membre a été supprimé ou non.
     */
    function supprime_membre($id_utilisateur, $id_groupe) {
        $sql = "DELETE
                FROM UtilisateurGroupe
                WHERE userId = ?
                AND groupId = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("ii", $id_utilisateur, $id_groupe);
        $ok = $query->execute();

        if ($ok) {
            $query->close();

            $sql = "SELECT creatorId
                    FROM Groupe
                    WHERE id = ?";

            $query = bdd()->prepare($sql);
            $query->bind_param("i", $id_groupe);
            $ok = $query->execute();

            if ($ok) {
                $query->bind_result($creatorId);
                $query->fetch();
                $query->close();

                if ($creatorId == $id_utilisateur) {
                    $sql = "DELETE
                            FROM Groupe
                            WHERE id = ?";

                    $query = bdd()->prepare($sql);
                    $query->bind_param("i", $id_groupe);
                    $ok = $query->execute();

                    if (!$ok) {
                        logCustomMessage($query->error);
                    }

                    $query->close();
                }
            }
            else {
                logCustomMessage($query->error);
                $query->close();
            }


        }
        else {
            logCustomMessage($query->error);
            $query->close();
        }

        return $ok;
    }
