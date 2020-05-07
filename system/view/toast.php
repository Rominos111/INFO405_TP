<?php
    if (isset($_SESSION['toast'])) {
?>
<script type="text/javascript">
    Materialize.toast("<?php echo $_SESSION['toast']; ?>", 3000);
</script>
<?php
        unset($_SESSION['toast']);
    }
