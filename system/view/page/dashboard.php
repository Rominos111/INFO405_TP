<div class="container">
    <div class="section">
        <div class="row">
            <form method="post">
                <div class="row">
                    <div class="input-field col s11">
                        <div type="text" class="chips"></div>
                        <input type="hidden" name="tags"/>
                    </div>
                    <div class="input-field col s1">
                        <button type="submit" name="search" class="btn-floating waves-effect waves-light light-blue">
                            <i class="material-icons">search</i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="section">
        <h5>Ça vient d'arriver...</h5>
        <div class="row">
            <?php
                foreach (recupere_sujet_par_date(3, 0, $_SESSION['user']['id']) as $subject) {
            ?>
            <div class="col s12 l4">
                <div class="card-panel grey lighten-5 z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s3">
                            <img src="<?php echo $subject['image'] ? $subject['image'] : '../assets/image.png'; ?>" class="circle responsive-img"/>
                        </div>
                        <div class="col s9">
                            <a href="?bookmark=<?php echo $subject['id']; ?>"
                                    class="secondary-content <?php echo $subject['favori'] ? "amber" : "grey"; ?>-text">
                                <i class="material-icons">grade</i>
                            </a>
                            <span><?php echo $subject['titre']; ?></span>
                            <p><?php echo $subject['description']; ?></p>
                            <div class="right-align"><a href="?page=subject&id=<?php echo $subject['id']; ?>">Voir le sujet</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
    </div>
    <div class="section">
        <h5>Mes sujets</h5>
        <?php
            $last_subjects = recupere_sujet_par_date(5, 0, $_SESSION['user']['id'], $_SESSION['user']['id']);
            if (empty($last_subjects)) {
        ?>
        <p class="right-align"><a href="?page=create">Ajouter votre premier sujet.</a></p>
        <?php
            } else {
        ?>
        <p class="right-align"><a href="?page=create">Ajouter un nouveau sujet.</a></p>
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
        <p class="right-align"><a href="?page=subject">Les voir tous</a></p>
        <?php
            }
        ?>
        <h5>J'ai participé !</h5>
        <?php
            $message_subjects = recupere_sujet_par_message($_SESSION['user']['id']);
            if (empty($message_subjects)) {
        ?>
        <p class="center-align"><em>Il n'y a aucun message de posté sur un sujet.</em></p>
        <p>&nbsp;</p>
        <?php
            } else {
        ?>
        <ul class="collection">
            <?php
                foreach ($message_subjects as $subject) {
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
            }
?>
        </ul>
    </div>
</div>
