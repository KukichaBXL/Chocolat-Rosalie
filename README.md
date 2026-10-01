# Maison Rosalie

Le livre de recettes au chocolat de la Maison Rosalie, en site web.
Projet MVC en PHP basé sur le modèle [choco-classe1](https://github.com/WebDevCF2m2026/choco-classe1).

## Stack

- HTML, CSS natif (nesting), JavaScript sans framework
- PHP 8 sans framework, PDO
- MariaDB
- Maquette : [Figma](https://www.figma.com/design/Tze6zvr5Fg6YOrGPhbfAeD/Maison-Rosalie)

## Structure

    config-dev.php     configuration locale
    controller/        contrôleurs
    data/              script SQL de la base
    model/             MyPDO, interface, abstract, mapping, manager
    public/            seul dossier visible depuis le navigateur (index.php, css, js, assets)
    view/              pages (inc/ = en-tête et pied de page)

## Installation

1. Cloner le dépôt.
2. Créer un virtual host qui pointe vers le dossier `public/`.
3. Importer `data/chocolaterie-rosalie.sql` dans phpMyAdmin (crée la base `mydb`).
4. Vérifier le port et `RACINE_URL` dans `config-dev.php`.
