<?php
    include_once "utils.php";

    define("LOGIN_MIN_SIZE", 0);
    define("LOGIN_MAX_SIZE", 63);
    define("PASSWORD_MIN_SIZE", 0);
    define("PASSWORD_MAX_SIZE", 255);

    /**
     * Crée toutes les tables en relation avec l'utilisateur.
     */
    function cree_table_utilisateur() {
        basicSqlRequest("CREATE TABLE IF NOT EXISTS Utilisateur (
                id INT NOT NULL AUTO_INCREMENT,
                login VARCHAR(64) NOT NULL UNIQUE,
                password VARCHAR(128) NOT NULL,
                salt VARCHAR(64) NOT NULL,
                dateNaissance DATE NOT NULL,
                niveauSql ENUM('0', '1', '2'),
                description TEXT,
                points INT DEFAULT 0,
                CONSTRAINT pk_User PRIMARY KEY (id),
                CONSTRAINT uc_User UNIQUE (login)
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");

        basicSqlRequest("CREATE TABLE IF NOT EXISTS Specialite (
                id INT AUTO_INCREMENT,
                name VARCHAR(100) UNIQUE NOT NULL,
                CONSTRAINT pk_Specialite PRIMARY KEY (id)
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");

        basicSqlRequest("ALTER TABLE Specialite AUTO_INCREMENT = 1");

        $names = array("Web (front-end)", "Mobile (natif)", "Serveur");

        foreach ($names as $name) {
            $sql = "SELECT id
                    FROM Specialite
                    WHERE name = ?";

            $query = bdd()->prepare($sql);
            $query->bind_param("s", $name);
            $ok = $query->execute();

            if ($ok) {
                $query->bind_result($id);

                if (!$query->fetch()) {
                    basicSqlRequest("INSERT INTO Specialite (name) VALUES ('$name')");
                }
            }

            $query->close();
        }

        basicSqlRequest("CREATE TABLE IF NOT EXISTS Competence (
                userId INT NOT NULL,
                specialiteId INT NOT NULL,
                CONSTRAINT pk_Competence PRIMARY KEY (userId, specialiteId),
                CONSTRAINT fk_Competence_1 FOREIGN KEY (userId) REFERENCES Utilisateur(id),
                CONSTRAINT fk_Competence_2 FOREIGN KEY (specialiteid) REFERENCES Specialite(id)
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");

        basicSqlRequest("CREATE TABLE IF NOT EXISTS Action (
                name VARCHAR(100) NOT NULL,
                reward INT NOT NULL,
                CONSTRAINT pk_Action PRIMARY KEY (name)
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");

        $names = array("CONNEXION" => 10, "MESSAGE" => 15);

        foreach ($names as $name => $value) {
            $sql = "SELECT reward
                    FROM Action
                    WHERE name = ?";

            $query = bdd()->prepare($sql);
            $query->bind_param("s", $name);
            $ok = $query->execute();

            if ($ok) {
                $query->bind_result($reward);

                if (!$query->fetch()) {
                    basicSqlRequest("INSERT INTO Action (name, reward) VALUES ('$name', '$value')");
                }
            }

            $query->close();
        }

        basicSqlRequest("CREATE TABLE IF NOT EXISTS UtilisateurAction (
                userId INT NOT NULL,
                actionName VARCHAR(100) NOT NULL,
                CONSTRAINT pk_UserAction PRIMARY KEY (userId, actionName),
                CONSTRAINT fk_UserAction_1 FOREIGN KEY (userId) REFERENCES Utilisateur(id),
                CONSTRAINT fk_UserAction_2 FOREIGN KEY (actionName) REFERENCES Action(name)
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
    }

    /**
     * Ajoute un utilisateur
     *
     * @param login : le login de l'utilisateur.
     * @param mot_de_passe : le mot de passe de l'utilisateur.
     * @param confirmation : la confirmation du mot de passe de l'utilisateur.
     * @param date_de_naissance : la date de naissance de l'utilisateur.
     * @param niveau : le niveau de l'utilisateur.
     * @param competences : la liste des compétences de l'utilisateur.
     * @param message : le message de l'utilisateur qui le décrit.
     *
     * @return si l'utilisateur a été ajouté ou non.
     */
    function inscrit_utilisateur($login, $mot_de_passe, $confirmation, $date_de_naissance, $niveau, $competences, $message) {
        $ok = false;

        if (isset($login, $mot_de_passe, $confirmation, $date_de_naissance, $niveau, $competences, $message)) {
            if (strlen($login) >= LOGIN_MIN_SIZE and strlen($login) <= LOGIN_MAX_SIZE) {
                if (strlen($mot_de_passe) >= PASSWORD_MIN_SIZE and strlen($mot_de_passe) <= PASSWORD_MAX_SIZE) {
                    if (dateValide($date_de_naissance)) {
                        if (strcmp($mot_de_passe, $confirmation) == 0) {
                            $conn = bdd();

                            $query = $conn->prepare("INSERT INTO Utilisateur (
                                login,
                                password,
                                salt,
                                dateNaissance,
                                niveauSql,
                                description)
                                VALUES (?,?,?,?,?,?)"
                            );

                            list($salt, $hash) = chiffreMotDePasse($mot_de_passe);

                            $query->bind_param("ssssss", $login, $hash, $salt, $date_de_naissance, $niveau, $message);
                            $ok = $query->execute();

                            if ($ok) {
                                $query->close();
                                $userId = mysqli_insert_id($conn);

                                foreach ($competences as $specialiteId => $here) {
                                    if ($here && $ok) {
                                        $sql = "INSERT INTO Competence (userId, specialiteId)
                                                VALUES (?, ?)";

                                        $query = $conn->prepare($sql);
                                        $query->bind_param("ii", $userId, $specialiteId);

                                        $ok = $query->execute();

                                        if (!$ok) {
                                            logCustomMessage($query->error);
                                        }
                                    }
                                }
                            }
                            else {
                                logCustomMessage($query->error);
                                $query->close();
                            }
                        }
                    }
                }
            }
        }

        return $ok;
    }

    /**
     * Sélectionne l'utilisateur selon son login et son mot de passe
     *
     * @param login : le login de l'utilisateur
     * @param mot_de_passe : le mot de passe de l'utilisateur
     *
     * @return l'objet utilisateur s'il est trouvé avec : id, login, point (son nombre de points); null sinon
     */
    function connecte_utilisateur($login, $mot_de_passe) {
        $result = null;

        $sql = "SELECT id, salt, password, points
                FROM Utilisateur
                WHERE login = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("s", $login);
        $ok = $query->execute();

        if ($ok) {
            $query->bind_result($id, $salt, $password, $points);

            $query->fetch();

            if (hash_equals(chiffreMotDePasse($mot_de_passe, $salt), $password)) {
                $result = array(
                    "id" => $id,
                    "login" => htmlspecialchars($login),
                    "point" => $points
                );

                $query->close();

                ajoute_points($id, get_points("CONNEXION"));
            }
            else {
                $query->close();

                // Mdp changé ?
            }
        }
        else {
            logCustomMessage($query->error);
            $query->close();
        }

        return $result;
    }

    /**
      * Sélectionne l'utilisateur selon son id.
      *
      * @param id : l'id de l'utilisateur.
      *
      * @return l'objet utilisateur s'il est trouvé avec : id, login, date_naissance, niveau, competences (liste avec pour clé l'id de la compétence et pour valeur si l'utilisateur l'a acquise ou non), message, point (son nombre de points); null sinon.
      */
    function recupere_utilisateur($id) {
        $competences = array();

        $sql = "SELECT id
                FROM Specialite";

        $query = bdd()->prepare($sql);
        $ok = $query->execute();

        if ($ok) {
            $query->bind_result($specialiteId);

            while ($query->fetch()) {
                $competences[$specialiteId] = false;
            }
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();



        $sql = "SELECT specialiteId
                FROM Competence
                WHERE userId = ?";

        $query = bdd()->prepare($sql);

        $query->bind_param("i", $id);
        $ok = $query->execute();

        if ($ok) {
            $query->bind_result($specialiteId);

            while ($query->fetch()) {
                $competences[$specialiteId] = true;
            }
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();



        $result = null;

        $query = bdd()->prepare("SELECT login, dateNaissance, niveauSql, description, points
            FROM Utilisateur
            WHERE id = ?"
        );

        $query->bind_param("i", $id);
        $ok = $query->execute();

        if ($ok) {
            $query->bind_result($login, $dateNaissance, $niveau, $description, $points);
            $query->fetch();

            $result = array(
                "id" => $id,
                "login" => htmlspecialchars($login),
                "date_naissance" => $dateNaissance,
                "niveau" => htmlspecialchars($niveau),
                "competences" => $competences,
                "message" => htmlspecialchars($description),
                "point" => $points
            );
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();

        return $result;
    }

    /**
     * Modifie le niveau, la liste des compétences et le message de l'utilisateur.
     *
     * @param id : l'id de l'utilisateur.
     * @param niveau : le niveau de l'utilisateur.
     * @param competences : la liste des compétences de l'utilisateur.
     * @param message : le message de l'utilisateur qui le décrit.
     *
     * @return si le niveau, les compétences et le message de l'utilisateur ont été modifiés ou non.
     */
    function modifie_information_utilisateur($id, $niveau, $competences, $message) {
        $query = bdd()->prepare("UPDATE Utilisateur
            SET niveauSql = ?, description = ?
            WHERE id = ?"
        );

        $query->bind_param("ssi", $niveau, $message, $id);
        $ok = $query->execute();
        $query->close();

        return $ok;
    }

    /**
     * Modifie le mot de passe de l'utilisateur.
     *
     * @param id : l'id de l'utilisateur.
     * @param ancien_mot_de_passe : l'ancien mot de passe de l'utilisateur.
     * @param mot_de_passe : le mot de passe de l'utilisateur.
     * @param confirmation : la confirmation du mot de passe de l'utilisateur.
     *
     * @return si le mot de passe de l'utilisateur a été modifié ou non.
     */
    function modifie_mot_de_passe_utilisateur($id, $ancien_mot_de_passe, $mot_de_passe, $confirmation) {
        $res = false;

        if (strcmp($mot_de_passe, $confirmation) == 0) {
            // Si mdp == confirmation

            if (strlen($mot_de_passe) >= PASSWORD_MIN_SIZE and strlen($mot_de_passe) <= PASSWORD_MAX_SIZE) {
                // Si taille valide

                $query = bdd()->prepare("SELECT password, salt
                    FROM Utilisateur
                    WHERE id = ?"
                );

                $query->bind_param("i", $id);
                $ok = $query->execute();

                if ($ok) {
                    $query->bind_result($password, $salt);
                    $query->fetch();
                    $query->close();

                    if (hash_equals(chiffreMotDePasse($ancien_mot_de_passe, $salt), $password)) {
                        // Si hash(ancien_mdp, sel) == mdp_bdd

                        $query = bdd()->prepare("UPDATE Utilisateur
                            SET password = ?, salt = ?
                            WHERE id = ?"
                        );

                        list($newSalt, $newPassword) = chiffreMotDePasse($mot_de_passe);

                        $query->bind_param("ssi", $newPassword, $newSalt, $id);
                        $ok = $query->execute();
                        $query->close();

                        $res = $ok;
                    }
                }
            }
        }

        return $res;
    }

    /**
     * Modifie le nombre de points de l'utilisateur.
     *
     * @param id : l'id de l'utilisateur.
     * @param point : le nombre de point de l'utilisateur.
     *
     * @return si le nombre de points de l'utilisateur a été modifié ou non.
     */
    function modifie_point_utilisateur($id, $point) {
        $query = bdd()->prepare("UPDATE Utilisateur
            SET points = ?
            WHERE id = ?"
        );

        $query->bind_param("ii", $point, $id);
        $ok = $query->execute();
        $query->close();

        return $ok;
    }

    /**
     * Ajoute un nombre de points à l'utilisateur.
     *
     * @param id : l'id de l'utilisateur.
     * @param point : le nombre de point à ajouter l'utilisateur.
     *
     * @return si le nombre de points de l'utilisateur a été modifié ou non.
     */
    function ajoute_points($id, $points) {
        $ok = false;

        if ($points == NULL) {
            logCustomMessage("Points null");
        }
        else {
            $query = bdd()->prepare("SELECT points
                FROM Utilisateur
                WHERE id = ?"
            );

            $query->bind_param("i", $id);
            $ok = $query->execute();

            if ($ok) {
                $query->bind_result($pointsOld);
                $query->fetch();

                $query->close();

                $ok = modifie_point_utilisateur($id, $pointsOld + $points);
            }
            else {
                logCustomMessage($query->error);
                $query->close();
            }
        }

        return $ok;
    }

    function get_points($name) {
        $sql = "SELECT reward
                FROM Action
                WHERE name = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("s", $name);
        $ok = $query->execute();

        $value = NULL;

        if ($ok) {
            $query->bind_result($value);
            $query->fetch();
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();

        return $value;
    }

    /**
     * Récupère le login d'un utilisateur selon son id
     *
     * @param id Id
     *
     * @return res Liste contenant le login et si l'id a été trouvé
     */
    function getLoginFromId($id) {
        $loginRes = null;
        $ok = false;

        $sql = "SELECT login
                FROM Utilisateur
                WHERE id = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("i", $id);
        $ok = $query->execute();

        if ($ok) {
            $query->bind_result($login);
            $query->fetch();

            $loginRes = $login;
            $ok = true;
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();

        return array($loginRes, $ok);
    }
