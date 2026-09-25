<?php
// crée ou continu un session existante
session_start();

// chargement du fichier config.php
require_once "../config.php";

// connexion de type PDO
try {
    $connectPDO = new PDO(
        DB_TYPE
        .':host='.DB_HOST
        .';port='.DB_PORT
        .';dbname='.DB_NAME
        .';charset='.DB_CHARSET,
        DB_LOGIN,
        DB_PWD
    );
        $connectPDO->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $connectPDO->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);

    

}catch(Exception $e){
    die($e->getMessage());
}
$connectPDO = null;
?>