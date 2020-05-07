<?php
    $user = recupere_utilisateur($_SESSION['user']['id']);
?>
<div class="container">
    <div class="section">
        <p>Retrouvez et modifiez toutes les informations qui vous concernent.</p>
        <h5>Information pour la communauté</h5>
        <form method="post">
            <div class="row">
                <div class="col s6">
                    <p>Niveau SQL</p>
<?php
    $levels = ["Débutant", "Intermédiaire", "Expert"];
    for ($i = 1; $i <= count($levels); $i++) {
?>
                    <p>
                        <input id="level_<?php echo $i; ?>" type="radio" class="with-gap" name="niveau" value="<?php echo $i; ?>"<?php if ($user['niveau'] == $i) { echo ' checked="checked"'; } ?>/>
                        <label for="level_<?php echo $i; ?>"><?php echo $levels[$i - 1]; ?></label>
                    </p>
<?php
    }
?>
                </div>
                <div class="col s6">
                    <p>Spécialité(s)</p>
<?php
    $skill_names = ["web", "mobile", "serveur"];
    $skills = ["Web (front-end)", "Mobile (natif)", "Serveur"];
    for ($i = 1; $i <= count($skills); $i++) {
?>
                    <p>
                        <input id="skill_<?php echo $i; ?>" type="checkbox" class="filled-in" name="competence_<?php echo $skill_names[$i - 1]; ?>"<?php if ($user['competences'][$i]) { echo ' checked="checked"'; } ?>/>
                        <label for="skill_<?php echo $i; ?>"><?php echo $skills[$i - 1]; ?></label>
                    </p>
<?php
    }
?>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <textarea id="message" class="materialize-textarea" name="message"><?php echo $user['message']; ?></textarea>
                    <label for="message">Message</label>
                </div>
            </div>
            <div class="row">
                <p class="center">
                    <button class="modal-action modal-close btn waves-effect waves-light" type="submit" name="information_update">Modifier les informations</button>
                </p>
            </div>
        </form>
        <p>&nbsp;</p>
        <h5>Modification du mot de passe</h5>
        <form method="post">
            <div class="row">
                <div class="input-field col s12">
                    <input id="password" type="password" class="validate" name="ancien_mot_de_passe">
                    <label for="password">Ancien mot de passe</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="password" type="password" class="validate" name="mot_de_passe">
                    <label for="password">Nouveau mot de passe</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="confirmation" type="password" class="validate" name="confirmation">
                    <label for="confirmation">Confirmation du nouveau mot de passe</label>
                </div>
            </div>
            <div class="row">
                <p class="center">
                    <button class="modal-action modal-close btn waves-effect waves-light" type="submit" name="password_update">
                        Modifier le mot de passe
                    </button>
                </p>
            </div>
        </form>
    </div>
</div>
