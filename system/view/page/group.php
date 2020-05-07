<div class="container">
    <div class="section">
        <h5>Mes groupes</h5>
        <?php
            $groups = recupere_groupe_par_ids(recupere_id_groupe_par_proprietaire($_SESSION['user']['id']));
        ?>
        <ul class="collection">
            <?php
                foreach ($groups as $group) {
            ?>
            <li class="collection-item">
                <b class="title"><?php echo $group['nom']; ?></b>
            <?php
                if (!empty($group['membres'])) {
            ?>
                <p>Les membres :</p>

            <?php
                    foreach ($group['membres'] as $member) {
            ?>
                <p> - <?php echo $member['login']; ?><?php
                        if (!$member['valide']) { ?> &nbsp <a href="?page=group&id=<?php echo $group['id']; ?>&state=ok&id_membre=<?php echo $member['id']; ?>">Valider</a><?php } ?></p>
            <?php
                    }
            ?>
            <?php
                }
            ?>
                <p class="right-align"><a href="?page=group&id=<?php echo $group['id']; ?>&state=del">Supprimer</a></p>
            </li>
            <?php
                }
            ?>
            <li class="collection-item">
                <form method="post">
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="name" type="text" class="validate" name="nom">
                            <label for="name">Nom</label>
                        </div>
                    </div>
                    <div class="row">
                        <p class="right-align">
                            <button class="modal-action modal-close btn waves-effect waves-light light-blue" type="submit" name="group_add">
                                Créer
                            </button> &nbsp; &nbsp;
                        </p>
                    </div>
                </form>
            </li>
        </ul>
    </div>
    <div class="section">
        <h5>Je suis membre</h5>
        <?php
            $in_groups = recupere_groupe_par_ids(recupere_id_groupe_par_membre($_SESSION['user']['id']));
            if (empty($in_groups)) {
        ?>
        <p><em>Il n'y a aucun groupe dans lequel je suis membre.</em></p>
        <p>&nbsp;</p>
        <?php
            } else {
        ?>
        <ul class="collection">
            <?php
                foreach ($in_groups as $group) {
            ?>
            <li class="collection-item">
                <b class="title"><?php echo $group['nom']; ?></b>
                <?php
                    if (!empty($group['membres'])) {
                ?>
                    <p>Les membres :</p>

                <?php
                        foreach ($group['membres'] as $member) {
                ?>
                    <p> - <?php echo $member['login']; ?>
                    <?php if ($member['id'] == $_SESSION['user']['id'] && !$member['valide']) { ?> &nbsp <em>En attente</em></p><?php } ?>
                <?php
                    }
                ?>
                <?php
                    }
                ?>
                <p class="right-align"><a href="?page=group&id=<?php echo $group['id']; ?>&state=out">En sortir</a></p>
            </li>
<?php
                }
            }
?>
        </ul>
    </div>
    <div class="section">
        <h5>Je ne suis pas membre</h5>
        <?php
            $out_groups = recupere_groupe_par_ids(recupere_id_groupe_par_non_membre($_SESSION['user']['id']));
            if (empty($out_groups)) {
        ?>
        <p><em>Il n'y a aucun groupe dans lequel je ne suis pas membre.</em></p>
        <p>&nbsp;</p>
        <?php
            } else {
        ?>
        <ul class="collection">
            <?php
                foreach ($out_groups as $group) {
            ?>
            <li class="collection-item">
                <b class="title"><?php echo $group['nom']; ?></b>
                <?php
                    if (!empty($group['membres'])) {
                ?>
                    <p>Les membres :</p>

                <?php
                        foreach ($group['membres'] as $member) {
                ?>
                    <p> - <?php echo $member['login']; ?>
                    <?php if ($member['id'] == $_SESSION['user']['id'] && !$member['valide']) { ?> &nbsp <em>En attente</em></p><?php } ?>
                <?php
                    }
                ?>
                <?php
                    }
                ?>
                <p class="right-align"><a href="?page=group&id=<?php echo $group['id']; ?>&state=in">En faire parti</a></p>
            </li>
<?php
                }
            }
?>
        </ul>
    </div>
</div>
