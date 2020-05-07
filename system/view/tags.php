<?php
    if (isset($_SESSION['user'])) {
?>
<script type="text/javascript">
    var TAGS = <?php echo json_encode(recupere_tag()); ?>;
</script>
<?php
    } else {
?>
<script type="text/javascript">
    var TAGS = {};
</script>
<?php
    }
