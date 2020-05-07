<div class="container">
    <div class="section">
        <h5>Votre recherche :<?php foreach ($_GET as $key) { echo " " . $key; } ?></h5>
        <div class="row">
<?php
    $subjects = recupere_sujet_par_tag($_GET, $_SESSION['user']['id']);
    if (count($subjects) == 0) {
?>
            <div class="col s12">
                <p><em>Aucun résultat.</em></p>
            </div>
<?php
    } else {
        foreach ($subjects as $subject) {
?>
            <div class="col s12">
                <div class="card-panel grey lighten-5 z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s3">
                            <img src="<?php echo $subject['image'] ? $subject['image'] : '../assets/image.png'; ?>" class="circle responsive-img"/>
                        </div>
                        <div class="col s9">
                            <a href="?page=bookmark&bookmark=<?php echo $subject['id']; ?>" class="secondary-content <?php echo $subject['favori'] ? "amber" : "grey"; ?>-text">
                                <i class="material-icons">grade</i>
                            </a>
                            <span><?php echo $subject['titre']; ?></span> de <strong><?php echo $subject['login']; ?></strong> -
                            <span><?php echo date_format(date_create($subject['date_creation']), 'd/m/Y'); ?></span>
                            <p><?php echo $subject['description']; ?></p>
<?php
            foreach (recupere_tag_par_sujet($subject['id']) as $tag) {
?>
                            <div class="chip"><?php echo $tag; ?></div>
<?php
            }
?>
                            <div class="right-align"><a href="?page=subject&id=<?php echo $subject['id']; ?>">Voir le sujet</a></div>
                        </div>
                    </div>
                </div>
            </div>
<?php
        }
    }
?>
        </div>
    </div>
</div>
