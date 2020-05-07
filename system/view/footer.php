    <footer class="page-footer orange">
<?php
    if (!isset($_SESSION['user'])) {
?>
        <div class="container">
            <div class="row">
                <div class="col l8 s12">
                    <h5 class="white-text">Présentation expresse</h5>
                    <p class="grey-text text-lighten-4">
                        Ce site est principalement là pour montrer toute l'étendue des compétences des formidables étudiants de l'Université de Savoie. Comme ils sont doués et qu'ils sont allés au bout de ce TP, vous pouvez vous connecter et partager vos connaissances à travers des commentaires, gagner des points de confiance et construire la meilleure communauté SQL de l'univers...
                    </p>
                </div>
            </div>
        </div>
<?php
    }
?>
        <div class="footer-copyright">
            <div class="container">
                &copy David Wayntal
                & Merci <a class="orange-text text-lighten-3" href="http://materializecss.com" target="_blank">Materialize</a> !!!
            </div>
        </div>
    </footer>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/materialize.min.js"></script>
<?php
    require_once "tags.php";
    require_once "toast.php";
?>
    <script src="../assets/js/main.js"></script>
</body>
</html>
