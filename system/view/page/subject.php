<div class="container">
    <div class="section">
    <?php
        if (isset($_GET['id'])) {
            $subject = recupere_sujet_par_id($_GET['id'], $_SESSION['user']['id']);
    ?>
        <div class="row">
            <div class="col s12 m6">
                <div class="card">
                    <div class="card-image">
                        <img src="<?php echo $subject['image'] ? $subject['image'] : '../assets/image.png'; ?>">
                    </div>
                    <div class="card-content">
                        <a href="?bookmark=<?php echo $subject['id']; ?>"
                                class="secondary-content <?php echo $subject['favori'] ? "amber" : "grey"; ?>-text">
                            <i class="material-icons">grade</i>
                        </a>
                        <span class="card-title"><?php echo $subject['titre']; ?></span>
                        <p><?php echo $subject['description']; ?></p>
                    </div>
                    <div class="card-action right-align">
                        De <b><?php echo $subject['login']; ?></b> -
                        <?php echo date_format(date_create($subject['date_creation']), 'd/m/Y - H:i'); ?>
                    </div>
                </div>
            </div>
            <div class="col s12 m6">
    <?php
        foreach (recupere_message_par_sujet($_GET['id']) as $message) {
    ?>
                <div class="card light-blue lighten-5">
                    <div class="card-content">
                        <p><?php echo $message['texte']; ?></p>
                    </div>
                    <div class="card-action right-align">
                        De <b><?php echo $message['login']; ?></b> -
                        <?php echo date_format(date_create($message['date_creation']), 'd/m/Y - H:i'); ?>
                    </div>
                </div>
    <?php
        }
    ?>
                <form method="post" class="card light-blue lighten-5">
                    <div class="card-content">
                        <textarea name="message" class="materialize-textarea no-border" placeholder="Votre message"></textarea>
                    </div>
                    <div class="card-action right-align">
                        <button class="btn light-blue" type="submit" name="message_add">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    <?php
        } else {
    ?>
        <h5>Mes sujets</h5>
        <p class="right-align"><a href="?page=create">Ajouter un nouveau sujet.</a></p>
        <?php
            $nb_subject = 3;
            $nb_subject_total = compte_sujet_par_auteur($_SESSION['user']['id']);
            $min = 1;
            $max = ceil($nb_subject_total / $nb_subject);
            $last_subjects = recupere_sujet_par_date(
                    $nb_subject, ($_GET['p'] - 1) * $nb_subject, $_SESSION['user']['id'], $_SESSION['user']['id']);
            if (empty($last_subjects)) {
        ?>
        <p class="center-align"><em>Il n'y a aucun sujet à afficher.</em></p>
        <p>&nbsp;</p>
        <?php
            } else {
        ?>
        <ul class="collection">
            <?php
                foreach ($last_subjects as $subject) {
            ?>
            <li class="collection-item avatar">
                <img src="<?php echo $subject['image'] ? $subject['image'] : '../assets/image.png'; ?>" class="circle">
                <span class="title"><?php echo $subject['titre']; ?></span>
                <p><?php echo $subject['description']; ?></p>
                <a href="?bookmark=<?php echo $subject['id']; ?>"
                        class="secondary-content <?php echo $subject['favori'] ? "amber" : "grey"; ?>-text">
                    <i class="material-icons">grade</i>
                </a>
                <p class="right-align"><a href="?page=subject&id=<?php echo $subject['id']; ?>">Voir le sujet</a></p>
            </li>
            <?php
                }
            ?>
        </ul>
        <ul class="pagination center-align">
            <li<?php if ($_GET['p'] == $min) { echo ' class="disabled"'; } ?>>
                <a<?php if ($_GET['p'] > $min) { echo ' href="?page=subject&p=' . ($_GET['p'] - 1) . '"'; } ?>>
                    <i class="material-icons">chevron_left</i>
                </a>
            </li>
        <?php
            for ($i = $min; $i <= $max; $i++) {
        ?>
            <li<?php if ($i == $_GET['p']) { echo ' class="active light-blue lighten-1"'; } ?>>
                <a href="?page=subject&p=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php
            }
        ?>
            <li<?php if ($_GET['p'] == $max) { echo ' class="disabled"'; } ?>>
                <a<?php if ($_GET['p'] < $max) { echo ' href="?page=subject&p=' . ($_GET['p'] + 1) . '"'; } ?>>
                    <i class="material-icons">chevron_right</i>
                </a>
            </li>
        </ul>
        <?php
            }
        ?>
    <?php
        }
    ?>
    </div>
</div>
