<?php
    include_once "utils.php";

    /**
     * Crée toutes les tables en relation avec le message.
     */
    function cree_table_message() {
        basicSqlRequest("CREATE TABLE IF NOT EXISTS Message (
                id INT NOT NULL AUTO_INCREMENT,
                content TEXT NOT NULL,
                sujetIdDestination INT NOT NULL,
                senderId INT NOT NULL,
                creationDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                deleted BOOLEAN NOT NULL DEFAULT 0,
                CONSTRAINT pk_Message PRIMARY KEY (id),
                CONSTRAINT fk_Message_1 FOREIGN KEY (senderId) REFERENCES Utilisateur(id),
                CONSTRAINT fk_Message_2 FOREIGN KEY (sujetIdDestination) REFERENCES Sujet(id)
            ) CHARACTER SET utf8 COLLATE utf8_unicode_ci
        ");
    }

    /**
     * Ajoute un message.
     *
     * @param texte : le texte du message.
     * @param id_sujet : l'id du sujet du message.
     * @param id_auteur : l'id de l'auteur du message.
     *
     * @return si le message a été ajouté ou non.
    */
    function ajoute_message($texte, $id_sujet, $id_auteur) {
        $sql = "INSERT INTO Message (content, sujetIdDestination, senderId)
                VALUES (?, ?, ?)";

        $query = bdd()->prepare($sql);
        $query->bind_param("sii", $texte, $id_sujet, $id_auteur);
        $ok = $query->execute();

        if (!$ok) {
            logCustomMessage($query->error);
        }

        return $ok;
    }

    /**
     * Sélectionne les messages selon le sujet.
     *
     * @param id_sujet : l'id du sujet du message
     *
     * @return la liste des messages avec : id, texte, login (le login de l'auteur), date_creation.
    */
    function recupere_message_par_sujet($id_sujet) {
        $res = array();

        $sql = "SELECT id, content, senderId, creationDate
                FROM Message
                WHERE deleted = 0
                AND sujetIdDestination = ?";

        $query = bdd()->prepare($sql);
        $query->bind_param("i", $id_sujet);
        $ok = $query->execute();

        if ($ok) {
            $query->bind_result($idMessage, $content, $senderId, $creationDate);

            while ($query->fetch()) {
                if ($ok) {
                    $res[] = array(
                        "id" => $idMessage,
                        "texte" => htmlspecialchars($content),
                        "login" => $senderId,
                        "date_creation" => $creationDate
                    );
                }
            }
        }
        else {
            logCustomMessage($query->error);
        }

        $query->close();

        for ($i=0; $i<count($res); $i++) {
            list($login, $ok) = getLoginFromId($res[$i]["login"]);

            if ($ok) {
                $res[$i]["login"] = htmlspecialchars($login);
            }
        }

        return $res;
    }

    /**
     * Supprime un message.
     *
     * @param id : l'id du message.
     *
     * @return si le message a été supprimé ou non.
     */
    function supprime_message($id) {
        $sql = "UPDATE Message
                SET deleted = 1
                WHERE id = ?";

        $query = bdd()->prepare($sql);
        $query->bind_result("i", $id);
        $ok = $query->execute();

        if (!$ok) {
            logCustomMessage($query->error);
        }

        return $ok;
    }
