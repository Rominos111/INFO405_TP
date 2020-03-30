<?php
    include_once "utils.php";
    include_once "tag.php";

    /**
     * Crée toutes les tables en relation avec le sujet.
     */
    function cree_table_sujet() {
        basicSqlRequest("CREATE TABLE IF NOT EXISTS Sujet (
                id INT NOT NULL,
                title VARCHAR(100) NOT NULL,
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
        /*
        $picturePathList = array(
            "http://getdrawings.com/vectors/troll-face-vector-17.jpg",
            "http://getdrawings.com/vectors/troll-face-vector-2.png",
            "http://getdrawings.com/vectors/troll-face-vector-4.png",
            "http://getdrawings.com/vectors/troll-face-vector-16.jpg",
            "http://getdrawings.com/vectors/troll-face-vector-13.jpg"
        );

        $picturePath = $picturePathList[array_rand($picturePathList)];
        */

        $res = false;

        $query = bdd()->prepare("INSERT INTO Sujet
            (title, description, picturePath, creatorId)
            VALUES (?, ?, ?, ?)"
        );

        $query->bind_param("sssi", $titre, $description, $image, $id_auteur);
        $ok = $query->execute();

        if ($ok) {
            // Recupération de l'id
            $get_id_query = bdd()->prepare("SELECT id
                FROM Sujet
                WHERE title = ?
                AND description = ?
                AND picturePath = ?
                AND creatorId = ?"
            );

            $get_id_query->bind_param("sssi", $titre, $description, $image, $id_auteur);
            $ok = $get_id_query->execute();

            if ($ok) {
                $get_id_query->bind_result($id_sujet);
                $get_id_query->fetch();

                //ajout des relations tags sujets
                ajoute_tag($id_sujet, $tags);

                $res = true;
            }
            else {
                echo "ERR";
                var_dump($query->error);
            }

            $get_id_query->close();
        }
        else {
            echo "ERR";
            var_dump($query->error);
        }

        $query->close();

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
            echo "ERR";
            var_dump($query->error);
        }

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


        return $res;
    }

    /*
        Sélectionne les sujets selon leur liste de tags.
        @param tags : la liste de tags.
        @param id_utilisateur : l'id de l'utilisateur connecté.
        @return la liste des sujets avec : id, titre, login (le login de l'auteur), date_creation, description, image, favori (s'il est favori de l'utilisateur connecté).
    */
    function recupere_sujet_par_tag($tags, $id_utilisateur) {
        return array();
    }

    /*
        Sélectionne les sujets pour la pagination.
        @param limite : nombre de sujets par page.
        @param decalage : nombre de sujets à passer.
        @param id_utilisateur : l'id de l'utilisateur connecté.
        @param id_auteur : l'id de l'auteur du sujet (pris en compte si supérieur à 0).
        @return la liste des sujets avec : id, titre, login (le login de l'auteur), date_creation, description, image, favori (s'il est favori de l'utilisateur connecté).
    */
    function recupere_sujet_par_date($limite, $decalage, $id_utilisateur, $id_auteur = 0) {
        $res = array();

        $query = null;

        if ($id_auteur == 0) {
            $sql = "SELECT id, title, creationDate, description, picturePath
                    FROM Sujet
                    LIMIT ?
                    OFFSET ?";

            $query = bdd()->prepare($sql);
            $query->bind_param("ii", $limite, $decalage);
        }
        else {
            $sql = "SELECT id, title, creationDate, description, picturePath
                    FROM Sujet
                    WHERE creatorId = ?
                    LIMIT ?
                    OFFSET ?";

            $query = bdd()->prepare($sql);
            $query->bind_param("iii", $id_auteur, $limite, $decalage);
        }

        $ok = $query->execute();

        if ($ok) {
            $query->bind_result($id, $title, $creationDate, $description, $picturePath);

            while ($query->fetch()) {
                list($login, $ok) = getLoginFromId($senderId);

                if ($ok) {
                    $res[] = array(
                        "id" => $id,
                        "titre" => $title,
                        "login" => $login,
                        "date_creation" => $creationDate,
                        "description" => $description,
                        "image" => $picturePath,
                        "favori" => false
                    );
                }
            }
        }
        else {
            echo "ERR";
            var_dump($query->error);
        }

        return $res;
    }

    /*
        Sélectionne les sujets liés aux messages postés par l'utilisateur donné.
        @param id_auteur : l'id de l'auteur des messages.
        @return la liste des sujets avec : id, titre, login (le login de l'auteur), date_creation, description, image, favori (s'il est favori de l'utilisateur connecté).
    */
    function recupere_sujet_par_message($id_auteur) {
        return array();
    }

    /*
        Ajoute/supprime un favori.
        @param id_utilisateur : l'id de l'utilisateur.
        @param id_sujet : l'id du sujet.
        @return si le favori a été ajouté/supprimé ou non.
    */
    function ajoute_ou_supprime_favori($id_utilisateur, $id_sujet) {
        return false;
    }

    /*
        Sélectionne les sujets mis en favoris par l'utilisateur donné.
        @param id_utilisateur : l'id de l'utilisateur.
        @return la liste des sujets avec : id, titre, login (le login de l'auteur), date_creation, description, image, favori (s'il est favori de l'utilisateur connecté).
    */
    function recupere_favori($id_utilisateur) {
        return array();
    }
