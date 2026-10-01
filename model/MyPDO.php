<?php
// path: model/MyPDO.php
// typage strict
declare(strict_types=1);

namespace model;

use PDO;

// on va modifier la classe PDO pour utiliser un SINGLETON pour la connexion à la base de données (une seule instance de la classe PDO sera créée et utilisée dans toute l'application)
class MyPDO extends PDO
{
    // Stocke l'instance unique
    private static ?MyPDO $instance = null;

    // Le constructeur est protégé pour empêcher l'utilisation de 'new' depuis l'extérieur
    protected function __construct()
    {
        // Appel du constructeur parent avec les paramètres de connexion à la base de données
        parent::__construct(DB_TYPE.':host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_LOGIN, DB_PWD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    // Méthode pour obtenir l'instance unique
    public static function getInstance(): MyPDO
    {
        // si pas de connexion, on crée une nouvelle instance
        if (self::$instance === null) {
            self::$instance = new self();
        }
        // sinon on retourne l'instance existante
        return self::$instance;
    }

    // Empêche la duplication de l'instance via clonage
    private function __clone() {}
}
