<?php
    if (isset($_SESSION['user'])) {
?>
<ul id="dropdown" class="dropdown-content">
    <li><a href="./" class="blue-text">Mon tableau de bord</a></li>
    <li><a href="./?page=create" class="blue-text">Nouveau sujet</a></li>
    <li class="divider"></li>
    <li><a href="./?page=account" class="blue-text">Mon compte</a></li>
    <li><a href="./?page=group" class="blue-text">Mes groupes</a></li>
    <li class="divider"></li>
    <li><a href="./?page=subject" class="blue-text">Mes sujets</a></li>
    <li><a href="./?page=message" class="blue-text">Mes messages</a></li>
    <li><a href="./?page=bookmark" class="blue-text">Mes favoris</a></li>
    <li class="divider"></li>
    <li><a href="./?page=deconnection" class="blue-text">Déconnexion</a></li>
</ul>
<?php
    } else {
?>
<form method="post" id="modal-subscription" class="modal">
    <div class="modal-content">
        <div class="row">
            <h5>Inscription</h5>
        </div>
        <div class="row">
            <div class="input-field col s12">
                <input id="login_s" type="text" class="validate" name="login">
                <label for="login_s">Login*</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
                <input id="password_s" type="password" class="validate" name="mot_de_passe">
                <label for="password_s">Mot de passe*</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
                <input id="confirmation" type="password" class="validate" name="confirmation">
                <label for="confirmation">Confirmation du mot de passe*</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
                <input id="birthdate" type="date" class="validate" name="date_de_naissance">
                <label for="birthdate" class="active">Date de naissance*</label>
            </div>
        </div>
        <div class="row">
            <div class="col s6">
                <p>Niveau SQL</p>
                <p>
                    <input id="level_1" type="radio" class="with-gap" name="niveau" value="1"/>
                    <label for="level_1">Débutant</label>
                </p>
                <p>
                    <input id="level_2" type="radio" class="with-gap" name="niveau" value="2"/>
                    <label for="level_2">Intermédiaire</label>
                </p>
                <p>
                    <input id="level_3" type="radio" class="with-gap" name="niveau" value="3"/>
                    <label for="level_3">Expert</label>
                </p>
            </div>
            <div class="col s6">
                <p>Spécialité(s)</p>
                <p>
                    <input id="skill_1" type="checkbox" class="filled-in" name="competence_web"/>
                    <label for="skill_1">Web (front-end)</label>
                </p>
                <p>
                    <input id="skill_2" type="checkbox" class="filled-in" name="competence_mobile"/>
                    <label for="skill_2">Mobile (natif)</label>
                </p>
                <p>
                    <input id="skill_3" type="checkbox" class="filled-in" name="competence_serveur"/>
                    <label for="skill_3">Serveur</label>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
                <textarea id="message" class="materialize-textarea" name="message"></textarea>
                <label for="message">Message</label>
            </div>
        </div>
        <div class="row">
            <p class="center">
                <button class="modal-action modal-close btn waves-effect waves-light" type="submit" name="subscription">S'inscrire</button>
            </p>
            <p class="center">
                <a class="modal-trigger modal-close" data-target="modal-connection">Déjà inscrit ? Se connecter</a>
            </p>
            <p>
                *Les champs sont requis pour l'inscription.
            </p>
        </div>
    </div>
</form>
<form method="post" id="modal-connection" class="modal">
    <div class="modal-content">
        <div class="row">
            <h5>Connexion</h5>
        </div>
        <div class="row">
            <div class="input-field col s12">
                <input id="login_c" type="text" class="validate" name="login">
                <label for="login_c">Login</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
                <input id="password_c" type="password" class="validate" name="mot_de_passe">
                <label for="password_c">Mot de passe</label>
            </div>
        </div>
        <div class="row">
            <p class="center">
                <button class="modal-action modal-close btn waves-effect waves-light" type="submit" name="connection">Se connecter</button>
            </p>
            <p class="center">
                <a class="modal-trigger modal-close" data-target="modal-subscription">Pas encore de compte ? S'inscrire</a>
            </p>
        </div>
    </div>
</form>
<?php
    }
?>
<nav class="light-blue lighten-1" role="navigation">
    <div class="nav-wrapper container">
        <a id="logo-container" href="./" class="brand-logo">
            SQLers
        </a>
        <ul class="right hide-on-med-and-down">
<?php
    if (isset($_SESSION['user'])) {
        $point = $_SESSION['user']['point'] . " point" . ($_SESSION['user']['point'] > 1 ? "s" : "");
?>
            <li>
                <a class="dropdown-button" href="#!" data-activates="dropdown"
                        data-alignment="right" data-beloworigin="true" data-constrainwidth="false">
                    <?php echo $_SESSION['user']['login']; ?> - <?php echo $point; ?><i class="material-icons right">arrow_drop_down</i>
                </a>
            </li>
<?php
    } else {
?>
            <li><a data-target="modal-subscription" class="modal-trigger">Inscription</a></li>
            <li><a data-target="modal-connection" class="modal-trigger">Connexion</a></li>
<?php
    }
?>
        </ul>
        <ul id="nav-mobile" class="side-nav">
            <li>
                <div class="user-view">
                    <h2 class="header orange-text">SQLers</h2>
                </div>
            </li>
            <li><a data-target="modal-subscription" class="modal-trigger">Inscription</a></li>
            <li><a data-target="modal-connection" class="modal-trigger">Connexion</a></li>
        </ul>
        <a href="#" data-activates="nav-mobile" class="button-collapse">
            <i class="material-icons">menu</i>
        </a>
    </div>
</nav>
