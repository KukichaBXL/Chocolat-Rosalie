<?php
// path: view/inc/header.php
// $titre est défini en haut de chaque vue, $pg vient de public/index.php
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $titre ?? 'Maison Rosalie' ?> | Maison Rosalie</title>
    <link rel="icon" href="<?= RACINE_URL ?>assets/favicon.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Abhaya+Libre:wght@800&family=Amiri&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&family=Habibi&family=Instrument+Serif:ital@1&display=swap" rel="stylesheet" />
     <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
      <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
    <link rel="stylesheet" href="<?= RACINE_URL ?>css/style.css" />
    <script src="<?= RACINE_URL ?>js/navigation.js" defer></script>
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="<?= RACINE_URL ?>" class="brand">
                <img src="<?= RACINE_URL ?>assets/logo.png" alt="" width="110" height="110" />
                <span>
                    <span class="brand-name">Maison Rosalie</span>
                    <span class="brand-tagline">Depuis 1860</span>
                </span>
            </a>

            <div class="header-right">
                <button type="button" class="account-btn" aria-label="Se connecter ou créer un compte">
                    <a href="<?= RACINE_URL ?>connection" <?= $pg === 'connection' ? 'class="active"' : '' ?>><img src="<?= RACINE_URL ?>assets/icon-compte.svg" alt="" width="22" height="26" /></a>
                </button>
                <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="menu-principal">Menu</button>
            </div>

            <nav class="main-nav" id="menu-principal" aria-label="Navigation principale">
                <ul>
                    <li><a href="<?= RACINE_URL ?>" <?= $pg === 'accueil' ? 'class="active"' : '' ?>>Accueil</a></li>
                    <li><a href="<?= RACINE_URL ?>recettes" <?= $pg === 'recettes' || $pg === 'recette' ? 'class="active"' : '' ?>>Recettes</a></li>
                    <li><a href="<?= RACINE_URL ?>a-propos" <?= $pg === 'a-propos' ? 'class="active"' : '' ?>>À propos</a></li>
                    <li><a href="<?= RACINE_URL ?>contact" <?= $pg === 'contact' ? 'class="active"' : '' ?>>Contact</a></li>
                    <li><a href="<?= RACINE_URL ?>admin" <?= $pg === 'admin' ? 'class="active"' : '' ?>>Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
