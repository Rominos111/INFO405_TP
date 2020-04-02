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

    /**
     * Exécute une requête sql basique
     */
    function basicSqlRequest($request) {
        // la fonction bdd() renvoie l'instance de connexion à la base de données.

        $conn = bdd();
        $query = $conn->prepare($request);
        $ok = $query->execute();

        if (!$ok) {
            logCustomMessage($query->error);
        }

        $query->close();

        return $ok;
    }

    /**
     * Log dans la console JS les erreurs
     *
     * @param msg Message à afficher
     */
    function logCustomMessage($msg) {
        $msg = str_replace("\"", "``", htmlspecialchars($msg));

        echo "<script>console.log(\"$msg\");alert(\"ERREUR PHP : $msg\");</script>";
    }

    /**
     * Si une chaine se termine par une autre chaine ou non
     *
     * @param baseString Chaine 1
     * @param endString Chaine 2
     *
     * @return Si chaine 1 se termine par chaine 2 ou non
     */
    function endsWith($baseString, $endString) {
        $len = strlen($endString);

        if ($len == 0) {
            return true;
        }

        return (substr($baseString, -$len) === $endString);
    }
