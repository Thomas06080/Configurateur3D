<html lang="en">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    </head>
</html>

<?php
include_once ('config.php');
$bdd = getbdd();

//ENVOIE DES IMAGES DANS LE FICHIER TEXTURES_BDD//
if ( isset( $_FILES['image'] ) && isset( $_FILES['image-rgh'] )  ) {

    $dossier = 'textures_bdd/';
    $fichier_image = basename($_FILES['image']['name']);
    $fichier_rgh = basename($_FILES['image-rgh']['name']);
    $fichier_metal = basename($_FILES['image_metal']['name']);
    $fichier_normal = basename($_FILES['image_normal']['name']);
    $taille_maxi = 1900000;
    $taille_image = filesize($_FILES['image']['tmp_name']);
    $taille_rgh = filesize($_FILES['image-rgh']['tmp_name']);
    $taille_metal = filesize($_FILES['image_metal']['tmp_name']);
    $taille_normal = filesize($_FILES['image_normal']['tmp_name']);
    $extensions = array('.PNG','.jpg', '.jpeg', '.JPG', '.png', '.JPEG');
    $extension_image = strrchr($_FILES['image']['name'], '.');
    $extension_rgh = strrchr($_FILES['image-rgh']['name'], '.');
    $extension_metal = strrchr($_FILES['image_metal']['name'], '.');
    $extension_normal = strrchr($_FILES['image_normal']['name'], '.');

//Début des vérifications de sécurité...
    if (!in_array($extension_image, $extensions) && !in_array($extension_rgh, $extensions) && !in_array($extension_metal, $extensions) && !in_array($extension_normal, $extensions)) //Si l'extension n'est pas dans le tableau
    {
        $erreur = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg, txt ou doc...';
    }

    if ($taille_image > $taille_maxi && $taille_rgh>$taille_maxi && $taille_metal>$taille_maxi && $taille_normal>$taille_maxi) {
        $erreur = 'Le fichier est trop gros...';
    }

    if (!isset($erreur) ) //S'il n'y a pas d'erreur, on upload
    {
        //On formate le nom du fichier ici...
        $fichier = strtr($fichier_image,
            'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
            'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
        $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier_image);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $dossier . $fichier) && move_uploaded_file($_FILES['image-rgh']['tmp_name'], $dossier . $fichier_rgh) ) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
        {
            if (isset($_FILES['image_metal']['name'])){
                move_uploaded_file($_FILES['image_metal']['tmp_name'], $dossier . $fichier_metal);
            }
            if (isset($_FILES['image_normal']['name'])){
                move_uploaded_file($_FILES['image_normal']['tmp_name'], $dossier . $fichier_normal);
            }
            echo 'Upload effectué avec succès !';
        } else //Sinon (la fonction renvoie FALSE).
        {
            echo 'Echec de l\'upload !';
        }
    } else {
        echo $erreur;
    }
}
//ENVOIE DES IMAGES DANS LE FICHIER TEXTURES_BDD FIN //


//ENVOIE A LA BDD//

$nom_texture = $_POST['nom'];
$path_etiquette =$dossier.$fichier;
$path_etiquette_rgh = $dossier.$fichier_rgh;
$path_etiquette_metal= $dossier.$fichier_metal;
$path_etiquette_normal = $dossier.$fichier_normal;
if ($_POST['produit'] == "E-Liquide"){
    $path_etiquette_metal= null;
    $path_etiquette_normal = null;
    $num_produit = 1;
}else{
    if ($_FILES['image_metal']['error'] != "4"){
        $path_etiquette_metal = $dossier.$fichier_metal;
    }else{
        $path_etiquette_metal = null;
    }
    if ($_FILES['image_normal']['error'] != "4"){
        $path_etiquette_normal = $dossier.$fichier_normal;
    }else{
        $path_etiquette_normal = null;
    }
    $num_produit = 2;
}

/* Vérification de la connexion */
if (mysqli_connect_errno()) {
    printf("Échec de la connexion : %s\n", mysqli_connect_error());
    exit();
}
//requete
$query = 'INSERT INTO textures( produit, nom_texture, path_texture, path_rgh, path_metal, path_normal) VALUES ("'.$num_produit.'","'.$nom_texture.'","'.$path_etiquette.'","'.$path_etiquette_rgh.'","'.$path_etiquette_metal.'", "'.$path_etiquette_normal.'")';
if(mysqli_query($bdd, $query)){
    echo "Records inserted successfully.";
} else{
    echo "ERROR: Could not able to execute query. " . mysqli_error($bdd);
}

/* Fermeture de la connexion */
mysqli_close($bdd);
//ENVOIE A LA BDD FIN//


header('Location: CONFIG-BOTTLE.php');
?>


