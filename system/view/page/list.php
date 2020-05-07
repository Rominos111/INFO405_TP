<div class="container">
    <div class="section">
        <h5>Mes messages</h5>
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
                <p class="right-align"><a href="?page=subject&id=<?php echo $subject['id']; ?>">Voir les messages</a></p>
            </li>
            <?php
                }
            ?>
        </ul>
        <?php
            }
        ?>
    </div>
</div>
