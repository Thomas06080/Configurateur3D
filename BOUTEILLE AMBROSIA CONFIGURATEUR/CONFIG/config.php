<?php
//function getbdd(){
//    $host = "localhost";
//    $dbName = "agarta";
//    $login = "root";
//    $password = "";
//
//    try
//    {
//        $bdd = new PDO('mysql:host='.$host.';dbname='.$dbName.';charset=utf8', $login, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
//    }
//    catch (Exception $e)
//    {
//        $bdd = null;
//        die('Erreur : ' . $e->getMessage());
//    }
//
//    return $bdd;
//}
//
//function getProduit($bdd){
//    // La requete de base
//    $query = "SELECT nom, model FROM produit";
//
//    // Etape 1
//    $resultat = $bdd->prepare($query);
//    // Etape 2
//    $resultat->execute();
//
//
//    return $resultat->fetchAll(PDO::FETCH_OBJ);
//}