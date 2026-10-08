<?php

declare(strict_types=1);

// Le routeur aiguille la requête vers le bon contrôleur
// Variables disponibles : $db (connexion MyPDO) et $pg (la page demandée), créés dans public/index.php.

require_once RACINE_PATH.'/controller/PublicController.php';
