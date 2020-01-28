<?php

function getbdd(){
    $host = "127.0.0.1";
    $dbName = "agarta";
    $login = "root";
    $password = "";

    $bdd = new mysqli($host, $login, $password, $dbName);
    mysqli_set_charset($bdd, "utf8");
    if ($bdd->connect_errno) {
        echo "Echec lors de la connexion à MySQL : (" . $bdd->connect_errno . ") " . $bdd->connect_error;
    }
    return $bdd;
}

function getAllProduit($bdd) {
    $res = $bdd->query("SELECT * FROM produit");
return $res;
}

function getAllEtiquette($bdd){
    $res = $bdd->query("SELECT * FROM textures WHERE produit = 1");
    return $res;
}

function getAllCartes($bdd){
    $res = $bdd->query('SELECT * FROM textures WHERE produit = 2');
    return $res;
}

function getAllTextures($bdd){
    $res = $bdd->query('SELECT * FROM textures ORDER BY id_texture ASC ');
    return $res;
}
