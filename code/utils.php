<?php

    /**
     * Fonction retournant si la date est valide ou non
     *
     * @param string Date format 2001-09-11
     *
     * @return bool Valide ou non
     */
    function dateValide($date){
        $d = DateTime::createFromFormat("Y-m-d", $date);
        return $d && $d->format("Y-m-d") === $date;
    }

    /**
     * Drop toutes les tables
     */
    function dropAll() {
        basicSqlRequest("DROP TABLE Invitation");
        basicSqlRequest("DROP TABLE UtilisateurGroupe");
        basicSqlRequest("DROP TABLE Groupe");

        basicSqlRequest("DROP TABLE Message");

        basicSqlRequest("DROP TABLE SujetTag");
        basicSqlRequest("DROP TABLE Tag");

        basicSqlRequest("DROP TABLE Favoris");
        basicSqlRequest("DROP TABLE Sujet");

        basicSqlRequest("DROP TABLE UtilisateurAction");
        basicSqlRequest("DROP TABLE Action");
        basicSqlRequest("DROP TABLE Competence");
        basicSqlRequest("DROP TABLE Specialite");
        basicSqlRequest("DROP TABLE Utilisateur");
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

    function basicSqlRequest($request) {
        // la fonction bdd() renvoie l'instance de connexion à la base de données.

        $conn = bdd();
        $query = $conn->prepare($request);

        if ($query) {
            $ok = $query->execute();
            $query->close();

            return $ok;
        }

        echo "DEBUG: Requete échouée : ";
        echo $request;
        echo "\n\n";

        return false;
    }
