
<html lang="en">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <link href="style.css" rel="stylesheet">
    <title>Formulaire</title>
    <link rel="icon" type="image/jpg" href="textures/agarta.jpg"/>
</head>
<body>
<a href="CONFIG-BOTTLE.php"> <input type="button" value="Retour"> </a>


<?php
include_once ('config.php');

$bdd = getbdd();

$produits = getAllProduit($bdd);
$etiquettes = getAllEtiquette($bdd);
?>
<div id="formulaire">
    <h3 id="titre_form">Formulaire d'envoie des textures</h3>

    <form method="POST" action="upload.php" enctype="multipart/form-data">
        <!--Select produit-->
        <div id="div_form">
            <span>Selectionner le produit:</span>
            <select name="produit" id="select_produit">
                <?php
                foreach ($produits as $produit){
                    ?>
                    <option ><?= $produit['nom'] ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <!--Select produit FIN-->

        <!--Nom produit-->
        <div id="div_form">
            <label>
                <span>Entrez le nom de la texture</span>
                <br>
                <input type="text" name="nom" placeholder="Ex: Belle Prune" required>
            </label>
        </div>
        <!--Nom produit FIN-->

        <!--Etiquette-->
        <div id="div_form">
            <br>
            <span>Entrer UNIQUEMENT l'étiquette en .jpg ou .png (Obligatoire)</span>
            <br>
            <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
            Fichier : <input type="file" name="image" required>
        </div>
        <!--    Etiquette FIN-->

        <!--    RGH-->
        <div id="div_form">
            <br>
            <span>Entrer UNIQUEMENT l'étiquette RGH en .jpg ou .png (Obligatoire)</span>
            <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
            <br>
            Fichier : <input type="file" name="image-rgh" required>
        </div>
        <!--    RGH FIN-->

        <!--    METAL-->
        <div id="div_form">
            <br>
            <span>Entrer UNIQUEMENT l'étiquette Metal en .jpg ou .png (Facultatif)</span>
            <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
            <br>
            Fichier : <input type="file" name="image_metal">
        </div>
        <!--    METAL FIN-->

        <!--    NORMAL-->
        <div id="div_form">
            <br>
            <span>Entrer UNIQUEMENT l'étiquette Normal en .jpg ou .png (Facultatif)</span>
            <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
            <br>
            Fichier : <input type="file" name="image_normal">
        </div>
        <!--    NORMAL FIN-->
        <br>
        <input type="submit" name="envoyer" value="Envoyer le formulaire">
    </form>
</div>

</body>
</html>









