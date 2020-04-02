<?php
    include_once "utils.php";
    include_once "tag.php";

    define("TITLE_MIN_SIZE", 4);
    define("TITLE_MAX_SIZE", 255);

    /**
     * Crée toutes les tables en relation avec le sujet.
     */
    function cree_table_sujet() {
        basicSqlRequest("CREATE TABLE IF NOT EXISTS Sujet (
                id INT NOT NULL AUTO_INCREMENT,
                title VARCHAR(256) NOT NULL,
                description TEXT NOT NULL,
                picturePath TEXT,
                creationDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                creatorId INT NOT NULL,
                CONSTRAINT pk_Sujet PRIMARY KEY (id),
                CONSTRAINT fk_Sujet FOREIGN KEY (creatorId) REFERENCES Utilisateur(id)
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");

        basicSqlRequest("CREATE TABLE IF NOT EXISTS Favoris (
                userId INT NOT NULL,
                sujetId INT NOT NULL,
                CONSTRAINT pk_Favoris PRIMARY KEY (userId, sujetId),
                CONSTRAINT fk_Favoris_1 FOREIGN KEY (userId) REFERENCES Utilisateur(id),
                CONSTRAINT fk_Favoris_2 FOREIGN KEY (sujetId) REFERENCES Sujet(id)
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
    }

    /**
     * Ajoute un sujet.
     *
     * @param titre : le titre du sujet.
     * @param id_auteur : l'id de l'auteur du sujet.
     * @param description : la description du sujet.
     * @param image : l'image du sujet.
     * @param tags : la liste des tags.
     *
     * @return si le sujet a été ajouté ou non.
     */
    function ajoute_sujet($titre, $id_auteur, $description, $image, $tags) {
        if ($image == "") {
            $picturePathList = array(
                "http://getdrawings.com/vectors/troll-face-vector-17.jpg",
                "http://getdrawings.com/vectors/troll-face-vector-2.png",
                "http://getdrawings.com/vectors/troll-face-vector-4.png",
                "http://getdrawings.com/vectors/troll-face-vector-16.jpg",
                "http://getdrawings.com/vectors/troll-face-vector-13.jpg"
            );

            $image = $picturePathList[array_rand($picturePathList)];
        }

        $res = false;

        if (endsWith(str_replace("/", "", str_replace(" ", "", $image)), ".php") === false) {
            // Protection contre l'ajout de .php dans les images

            if (strpos($image, "deconnection") === false) {
                // Protection contre l'ajout de "http://os-vps418.infomaniak.ch/etu_info/info_1_gr_1/?page=deconnection" comme URL

                if (strlen($titre) >= TITLE_MIN_SIZE && strlen($titre) <= TITLE_MAX_SIZE) {
                    // Protection contre les titres trop cours ou trop longs

                    $conn = bdd();

                    $query = $conn->prepare("INSERT INTO Sujet
                        (title, description, picturePath, creatorId)
                        VALUES (?, ?, ?, ?)"
                    );

                    $query->bind_param("sssi", $titre, $description, $image, $id_auteur);
                    $ok = $query->execute();

                    if ($ok) {
                        $id_sujet = mysqli_insert_id($conn);

                        $query->close();
                        //ajout des relations tags sujets
                        ajoute_tag($id_sujet, $tags);
                        $res = true;
                    }
                    else {
                        logCustomMessage($query->error);
                        $query->close();
                    }


                }
            }
        }

        return $res;
    }

    /**
     * Compte les sujets selon l'id de son auteur.
     *
     * @param id_auteur : l'id de l'auteur du sujet.
     *
     * @return le nombre de sujets qui ont l'auteur donné.
     */
    function compte_sujet_par_auteur($id_auteur) {
        $nb = 0;

        $sql = "SELECT title
                FROM Sujet
                WHERE creatorId = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("i", $id_auteur);
        $ok = $query->execute();

        if ($ok) {
            while ($query->fetch()) {
                $nb ++;
            }
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();

        return $nb;
    }

    /**
     * Sélectionne le sujet selon son id
     *
     * @param id_sujet : l'id du sujet.
     * @param id_utilisateur : l'id de l'utilisateur connecté
     *
     * @return l'objet sujet s'il est trouvé avec : id, titre, login (le login de l'auteur), date_creation, description, image, favori (s'il est favori de l'utilisateur connecté); null sinon.
     */
    function recupere_sujet_par_id($id_sujet, $id_utilisateur) {
        $res = null;

        $sql = "SELECT title, creationDate, description, picturePath, creatorId
                FROM Sujet
                WHERE id = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("i", $id_sujet);

        $ok = $query->execute();

        if ($ok) {
            $query->bind_result($title, $creationDate, $description, $picturePath, $creatorId);
            $query->fetch();

            $res = array(
                "id" => $id_sujet,
                "titre" => htmlspecialchars($title),
                "login" => $creatorId,
                "date_creation" => $creationDate,
                "description" => htmlspecialchars($description),
                "image" => $picturePath,
                "favori" => false
            );
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();

        if ($res != null) {
            if ($res["login"] == 0) {
                $userId = $id_utilisateur;
            }
            else {
                $userId = $res["login"];
            }

            list($login, $ok) = getLoginFromId($userId);

            if ($ok) {
                $res["login"] = htmlspecialchars($login);
            }

            list($favori, $ok) = sujetFavori($res["id"], $userId);

            if ($ok) {
                $res["favori"] = $favori;
            }
        }

        return $res;
    }

    /**
     * Permet de savoir si un sujet est favori d'un utilisateur ou non
     *
     * @param sujetId Id du sujet
     * @param userId Id de l'utilisateur
     *
     * @return array Si le sujet est favori ou non, et s'il y a eu une erreur
     */
    function sujetFavori($sujetId, $userId) {
        $sql = "SELECT *
                FROM Favoris
                WHERE sujetId = ?
                AND userId = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("ii", $sujetId, $userId);
        $ok = $query->execute();

        $res = false;

        if ($ok) {
            $query->bind_result($sujetId, $userId);

            if ($query->fetch()) {
                $res = true;
            }
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();

        return array($res, $ok);
    }

    /**
     * Récupération générique d'un sujet
     *
     * @param query Query (pas encore exécutée !)
     * @param id_utilisateur Id de l'utilisateur connecté
     *
     * @return la liste des sujets avec : id, titre, login (le login de l'auteur), date_creation, description, image, favori (s'il est favori de l'utilisateur connecté).
     */
    function recuperationGenerale($query, $id_utilisateur) {
        $res = array();
        $ids = array();

        $ok = $query->execute();

        if ($ok) {
            $query->bind_result($id);

            while ($query->fetch()) {
                $ids[] = $id;
            }
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();

        for ($i=0; $i<count($ids); $i++) {
            $res[] = recupere_sujet_par_id($ids[$i], $id_utilisateur);
        }

        return $res;
    }

    /**
     * Sélectionne les sujets selon leur liste de tags.
     *
     * @param tags : la liste de tags.
     * @param id_utilisateur : l'id de l'utilisateur connecté.
     *
     * @return la liste des sujets avec : id, titre, login (le login de l'auteur), date_creation, description, image, favori (s'il est favori de l'utilisateur connecté).
     */
    function recupere_sujet_par_tag($tags, $id_utilisateur) {
        $res = array();

        foreach ($tags as $tag) {
            $sql = "SELECT sujetId
                    FROM SujetTag
                    WHERE tagName = ?";

            $query = bdd()->prepare($sql);
            $query->bind_param("s", $tag);

            $res = array_merge($res, recuperationGenerale($query, $id_utilisateur));
        }

        return $res;
    }

    /**
     * Sélectionne les sujets pour la pagination.
     *
     * @param limite : nombre de sujets par page.
     * @param decalage : nombre de sujets à passer.
     * @param id_utilisateur : l'id de l'utilisateur connecté.
     * @param id_auteur : l'id de l'auteur du sujet (pris en compte si supérieur à 0).
     *
     * @return la liste des sujets avec : id, titre, login (le login de l'auteur), date_creation, description, image, favori (s'il est favori de l'utilisateur connecté).
    */
    function recupere_sujet_par_date($limite, $decalage, $id_utilisateur, $id_auteur = 0) {
        $query = null;

        if ($id_auteur == 0) {
            $sql = "SELECT id
                    FROM Sujet
                    LIMIT ?
                    OFFSET ?";

            $query = bdd()->prepare($sql);
            $query->bind_param("ii", $limite, $decalage);
        }
        else {
            $sql = "SELECT id
                    FROM Sujet
                    WHERE creatorId = ?
                    LIMIT ?
                    OFFSET ?";

            $query = bdd()->prepare($sql);
            $query->bind_param("iii", $id_auteur, $limite, $decalage);
        }

        return recuperationGenerale($query, $id_utilisateur);
    }

    /**
     * Sélectionne les sujets liés aux messages postés par l'utilisateur donné.
     *
     * @param id_auteur : l'id de l'auteur des messages.
     *
     * @return la liste des sujets avec : id, titre, login (le login de l'auteur), date_creation, description, image, favori (s'il est favori de l'utilisateur connecté).
     */
    function recupere_sujet_par_message($id_auteur) {
        $sql = "SELECT sujetIdDestination
                FROM Message
                WHERE senderId = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("i", $id_auteur);

        return recuperationGenerale($query, $id_auteur);
    }

    /**
     * Ajoute/supprime un favori.
     *
     * @param id_utilisateur : l'id de l'utilisateur.
     * @param id_sujet : l'id du sujet.
     *
     * @return si le favori a été ajouté/supprimé ou non.
     */
    function ajoute_ou_supprime_favori($id_utilisateur, $id_sujet) {
        $sql = "INSERT INTO Favoris (userId, sujetId)
                VALUES (?, ?)";

        $query = bdd()->prepare($sql);
        $query->bind_param("ii", $id_utilisateur, $id_sujet);

        $ok = $query->execute();
        $query->close();

        $res = false;

        if ($ok) {
            $res = true;
        }
        else {
            $sql = "DELETE FROM Favoris
                    WHERE userId  = ?
                    AND   sujetId = ?";

            $query = bdd()->prepare($sql);
            $query->bind_param("ii", $id_utilisateur, $id_sujet);

            $ok = $query->execute();

            if ($ok) {
                $res = true;
            }
            else {
                logCustomMessage($query->error);
            }

            $query->close();
        }

        return $res;
    }

    /**
     * Sélectionne les sujets mis en favoris par l'utilisateur donné.
     *
     * @param id_utilisateur : l'id de l'utilisateur.
     *
     * @return la liste des sujets avec : id, titre, login (le login de l'auteur), date_creation, description, image, favori (s'il est favori de l'utilisateur connecté).
     */
    function recupere_favori($id_utilisateur) {
        $sql = "SELECT sujetId
                FROM Favoris
                WHERE userId = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("i", $id_utilisateur);

        return recuperationGenerale($query, $id_utilisateur);
    }
