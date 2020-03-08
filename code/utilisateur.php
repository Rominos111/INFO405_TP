<?php
    define("LOGIN_MIN_SIZE", 0);
    define("LOGIN_MAX_SIZE", 64);
    define("PASSWORD_MIN_SIZE", 0);
    define("PASSWORD_MAX_SIZE", 255);

    /*
        Crée toutes les tables en relation avec l'utilisateur.
    */
    function cree_table_utilisateur() {
        // la fonction bdd() renvoie l'instance de connexion à la base de données.
        $conn = bdd();

        $query = $conn->prepare("CREATE TABLE IF NOT EXISTS Utilisateur (
            id INT NOT NULL AUTO_INCREMENT,
            login VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(128) NOT NULL,
            salt VARCHAR(64) NOT NULL,
            dateNaissance DATE NOT NULL,
            niveauSql ENUM('DEBUTANT', 'INTERMEDIAIRE', 'AVANCE'),
            description TEXT,
            points INT DEFAULT 0,
            CONSTRAINT pk_User PRIMARY KEY (id),
            CONSTRAINT uc_User UNIQUE (login)
        ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
        $query->execute();
        $query->close();

        $query = $conn->prepare("CREATE TABLE IF NOT EXISTS Specialite (
            name VARCHAR(100) NOT NULL,
            CONSTRAINT pk_Specialite PRIMARY KEY (name)
        ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
        $query->execute();
        $query->close();

        $query = $conn->prepare("CREATE TABLE IF NOT EXISTS Competence (
            userId INT NOT NULL,
            specialiteName VARCHAR(100) NOT NULL,
            CONSTRAINT pk_Competence PRIMARY KEY (userId, specialiteName),
            CONSTRAINT fk_Competence_1 FOREIGN KEY (userId) REFERENCES Utilisateur(id),
            CONSTRAINT fk_Competence_2 FOREIGN KEY (specialiteName) REFERENCES Specialite(name)
        ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
        $query->execute();
        $query->close();

        $query = $conn->prepare("CREATE TABLE IF NOT EXISTS Action (
            name VARCHAR(100) NOT NULL,
            reward INT NOT NULL,
            CONSTRAINT pk_Action PRIMARY KEY (name)
        ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
        $query->execute();
        $query->close();

        $query = $conn->prepare("CREATE TABLE IF NOT EXISTS UtilisateurAction (
            userId INT NOT NULL,
            actionName VARCHAR(100) NOT NULL,
            CONSTRAINT pk_UserAction PRIMARY KEY (userId, actionName),
            CONSTRAINT fk_UserAction_1 FOREIGN KEY (userId) REFERENCES Utilisateur(id),
            CONSTRAINT fk_UserAction_2 FOREIGN KEY (actionName) REFERENCES Action(name)
        ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
        $query->execute();
        $query->close();
    }

    /*
        Ajoute un utilisateur.
        @param login : le login de l'utilisateur.
        @param mot_de_passe : le mot de passe de l'utilisateur.
        @param confirmation : la confirmation du mot de passe de l'utilisateur.
        @param date_de_naissance : la date de naissance de l'utilisateur.
        @param niveau : le niveau de l'utilisateur.
        @param competences : la liste des compétences de l'utilisateur.
        @param message : le message de l'utilisateur qui le décrit.
        @return si l'utilisateur a été ajouté ou non.
    */
    function inscrit_utilisateur($login, $mot_de_passe, $confirmation, $date_de_naissance, $niveau, $competences, $message) {
        $ok = false;

        if (isset($login, $mot_de_passe, $confirmation, $date_de_naissance, $niveau, $competences, $message)) {
            if (strlen($login) >= LOGIN_MIN_SIZE and strlen($login) <= LOGIN_MAX_SIZE) {
                if (strlen($mot_de_passe) >= PASSWORD_MIN_SIZE and strlen($mot_de_passe) <= PASSWORD_MAX_SIZE) {
                    if (dateValide($date_de_naissance)) {
                        if (strcmp($mot_de_passe, $confirmation) == 0) {
                            switch ($niveau) {
                                case "1":
                                    $niveau = "DEBUTANT";
                                    break;

                                case "2":
                                    $niveau = "INTERMEDIAIRE";
                                    break;

                                case "3":
                                    $niveau = "AVANCE";
                                    break;

                                default:
                                    $niveau = null;
                                    break;
                            }

                            if ($niveau != null) {
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
                                $query->close();
                            }
                        }
                    }
                }
            }
        }

        return $ok;
    }

    /*
        Sélectionne l'utilisateur selon son login et son mot de passe.
        @param login : le login de l'utilisateur.
        @param mot_de_passe : le mot de passe de l'utilisateur.
        @return l'objet utilisateur s'il est trouvé avec : id, login, point (son nombre de points); null sinon.
    */
    function connecte_utilisateur($login, $mot_de_passe) {
        $result = null;

        $conn = bdd();

        $query = $conn->prepare("SELECT id, salt, password, points
            FROM Utilisateur
            WHERE login = ?"
        );

        $query->bind_param("s", $login);
        $ok = $query->execute();

        if ($ok) {
            $query->bind_result($id, $salt, $password, $points);

            $query->fetch();

            if (hash_equals(chiffreMotDePasse($mot_de_passe, $salt), $password)) {
                $result = array($id, $login, $points);
            }
        }

        $query->close();

        return $result;
    }

    /*
        Sélectionne l'utilisateur selon son id.
        @param id : l'id de l'utilisateur.
        @return l'objet utilisateur s'il est trouvé avec : id, login, date_naissance, niveau, competences (liste avec pour clé l'id de la compétence et pour valeur si l'utilisateur l'a acquise ou non), message, point (son nombre de points); null sinon.
    */
    function recupere_utilisateur($id) {
        return null;
    }

    /*
        Modifie le niveau, la liste des compétences et le message de l'utilisateur.
        @param id : l'id de l'utilisateur.
        @param niveau : le niveau de l'utilisateur.
        @param competences : la liste des compétences de l'utilisateur.
        @param message : le message de l'utilisateur qui le décrit.
        @return si le niveau, les compétences et le message de l'utilisateur ont été modifiés ou non.
    */
    function modifie_information_utilisateur($id, $niveau, $competences, $message) {
        return false;
    }

    /*
        Modifie le mot de passe de l'utilisateur.
        @param id : l'id de l'utilisateur.
        @param ancien_mot_de_passe : l'ancien mot de passe de l'utilisateur.
        @param mot_de_passe : le mot de passe de l'utilisateur.
        @param confirmation : la confirmation du mot de passe de l'utilisateur.
        @return si le mot de passe de l'utilisateur a été modifié ou non.
    */
    function modifie_mot_de_passe_utilisateur($id, $ancien_mot_de_passe, $mot_de_passe, $confirmation) {
        return false;
    }

    /**
     * Modifie le nombre de points de l'utilisateur.
     * @param id : l'id de l'utilisateur.
     * @param point : le nombre de point de l'utilisateur.
     * @return si le nombre de points de l'utilisateur a été modifié ou non.
     */
    function modifie_point_utilisateur($id, $point) {
        return false;
    }


    ////////////////////////////////////////////////////////////////////////////

    /**
     * Fonction retournant si la date est valide ou non
     *
     * @param string Date format 2001-09-11
     *
     * @return bool Valide ou non
     */
    function dateValide($date){
        $d = DateTime::createFromFormat("Y-m-d", $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Fonction chiffrant un mot de passe
     *
     * @param string Mot de passe non chiffré
     * @param string Sel, si null alors créé
     *
     * @return array Si le sel est null, retourne le sel et le hash
     * @return string Si le sel n'est pas null, retourne seulement le hash
     */
    function chiffreMotDePasse($password, $salt = null) {
        if ($salt == null) {
            $salt = randomString(64);
            return array($salt, hash('sha512', $password . $salt));
        }
        else {
            return hash('sha512', $password . $salt);
        }
    }

    /**
     * Fonction qui retourne une chaîne de caractères aléatoire de longueur n
     *
     * @param int Longueur de la chaîne a generer
     *
     * @return string
     */
     function randomString($n) {
         $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
         $randomString = "";
         for ($i = 0; $i < $n; $i++) {
             $randomString .= $characters[rand(0, strlen($characters) - 1)];
         }
         return $randomString;
     }
