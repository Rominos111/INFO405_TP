<?php
	/*
		Crée toutes les tables en relation avec l'utilisateur.
	*/
	function cree_table_utilisateur() {
        $conn = bdd();

        $conn->query("CREATE TABLE User (
            login VARCHAR(45) NOT NULL,
            password VARCHAR(45) NOT NULL,
            dateNaissance DATE NOT NULL,
            niveauSql ENUM('DEBUTANT', 'INTERMEDIAIRE', 'AVANCE'),
            description VARCHAR(45),
            points INT DEFAULT 0,
            CONSTRAINT pk_User PRIMARY KEY (login)
        )");

        $conn->query("CREATE TABLE Specialite (
            name VARCHAR(45) NOT NULL,
            CONSTRAINT pk_Specialite PRIMARY KEY (name)
        )");

        $conn->query("CREATE TABLE Competence (
            userLogin VARCHAR(45) NOT NULL,
            specialiteName VARCHAR(45) NOT NULL,
            CONSTRAINT pk_Competence PRIMARY KEY (userLogin, specialiteName),
            CONSTRAINT fk_Competence_1 FOREIGN KEY (userLogin) REFERENCES User(login),
            CONSTRAINT fk_Competence_2 FOREIGN KEY (specialiteName) REFERENCES Specialite(name)
        )");

        $conn->query("CREATE TABLE Action (
            name VARCHAR(45) NOT NULL,
            reward INT NOT NULL,
            CONSTRAINT pk_Action PRIMARY KEY (name)
        )");

        $conn->query("CREATE TABLE UserAction (
            userLogin VARCHAR(45) NOT NULL,
            actionName VARCHAR(45) NOT NULL,
            CONSTRAINT pk_UserAction PRIMARY KEY (userLogin, actionName),
            CONSTRAINT fk_UserAction_1 FOREIGN KEY (userLogin) REFERENCES User(login),
            CONSTRAINT fk_UserAction_2 FOREIGN KEY (actionName) REFERENCES Action(name)
        )");

		// la fonction bdd() renvoie l'instance de connexion à la base de données.
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
		return false;
	}

	/*
		Sélectionne l'utilisateur selon son login et son mot de passe.
		@param login : le login de l'utilisateur.
		@param mot_de_passe : le mot de passe de l'utilisateur.
		@return l'objet utilisateur s'il est trouvé avec : id, login, point (son nombre de points); null sinon.
	*/
	function connecte_utilisateur($login, $mot_de_passe) {
		return null;
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

	/*
		Modifie le nombre de points de l'utilisateur.
		@param id : l'id de l'utilisateur.
		@param point : le nombre de point de l'utilisateur.
		@return si le nombre de points de l'utilisateur a été modifié ou non.
	*/
	function modifie_point_utilisateur($id, $point) {
		return false;
	}
