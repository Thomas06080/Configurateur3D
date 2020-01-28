<?php
include_once ('config.php');
$bdd = getbdd();

$produits = getAllProduit($bdd);
$textures = getAllTextures($bdd);

?>

<html lang="en">
    <head>
        <title>Affichage Bdd</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div id="produit">
            <table style="border-width:1px;
                 border-style:solid;
                 border-color:black;">
                <thead>
                    <tr>
                        <th width="80">ID</th>
                        <th width="150">Nom du Produit</th>
                        <th width="300">Model 3D</th>
                        <th width="30"></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($produits){
                    foreach ($produits as $produit){
                        ?>
                    <tr>
                        <td style="text-align: center"><?=$produit['id']?></td>
                        <td style="text-align: center"><?=$produit['nom']?></td>
                        <td style="text-align: center"><?=$produit['model']?></td>
                        <td style="text-align: center"><a href="#"><input type="button" name="Modifier"value="Modifier"/></a> </td>
                    </tr>
                <?php
                    }
                }else {
                    echo "<tr><td colspan='3'>Aucun résultat</td></tr>";
                }

                ?>
                </tbody>
            </table>
        </div>
        <br>
        <div id="textures">
            <table style="border-width:1px;
                 border-style:solid;
                 border-color:black;">
                <thead>
                <tr>
                    <th width="30">ID</th>
                    <th width="30">Num Produit</th>
                    <th width="150">Nom</th>
                    <th width="400">Liens texture</th>
                    <th width="400">Liens roughness</th>
                    <th width="400">Liens metal</th>
                    <th width="400">Liens normal</th>
                    <th width="30"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($textures){
                    foreach ($textures as $texture){
                        ?>
                        <tr>
                            <td style="text-align: center"><?=$texture['id_texture']?></td>
                            <td style="text-align: center"><?=$texture['produit']?></td>
                            <td style="text-align: center"><?=$texture['nom_texture']?></td>
                            <td style="text-align: center"><?=$texture['path_texture']?></td>
                            <td style="text-align: center"><?=$texture['path_rgh']?></td>
                            <td style="text-align: center"><?=$texture['path_metal']?></td>
                            <td style="text-align: center"><?=$texture['path_normal']?></td>
                            <td style="text-align: center"><a href="#"><input type="button" name="Modifier"value="Modifier"/></a> </td>
                        </tr>
                        <?php
                    }
                }else {
                    echo "<tr><td colspan='3'>Aucun résultat</td></tr>";
                }

                ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
