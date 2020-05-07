<div class="container">
    <div class="section">
        <h5>Ajouter un nouveau sujet</h5>
        <p>&nbsp;</p>
        <form method="post">
            <div class="row">
                <div class="input-field col s6">
                    <input id="title" type="text" class="validate" name="titre">
                    <label for="title">Titre</label>
                </div>
                <div class="input-field col s6">
                    <input id="image" type="text" class="validate" name="image">
                    <label for="image">Url de l'image</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <div type="text" class="chips"></div>
                    <input type="hidden" name="tags"/>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <textarea id="description" class="materialize-textarea" name="description"></textarea>
                    <label for="description">Description</label>
                </div>
            </div>
            <div class="row">
                <p class="center">
                    <button class="modal-action modal-close btn waves-effect waves-light light-blue" type="submit" name="subject_add">
                        Ajouter
                    </button>
                </p>
            </div>
        </form>
        <p>&nbsp;</p>
    </div>
</div>
