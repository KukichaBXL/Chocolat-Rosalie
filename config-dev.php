<?php
// path: config-dev.php

// paramètres de connexions
const DB_HOST = "localhost";
const DB_LOGIN = "root";
const DB_PWD = "";
const DB_NAME = "mydb";
const DB_PORT = 3307; // 3307 = MariaDB sous WAMP, 3306 = MySQL
const DB_CHARSET = "utf8mb4";

// paramètres supplémentaires pour PDO
const DB_TYPE = "mysql"; // valable pour MySQL et/ou MariaDB

// racine de notre site pour PHP
const RACINE_PATH = __DIR__;
// URL racine de notre site pour le navigateur (jusqu'au dossier public)
// évite les problèmes de chemins relatifs qui sont liés à la réécriture des URLs
const RACINE_URL = "http://rosalie/";
// const RACINE_URL = "http://chocolat-rosalie.local/"; // Virtual host pour le mac de TIRO 

