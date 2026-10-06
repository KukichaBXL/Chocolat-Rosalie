-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : mar. 06 oct. 2026 à 09:39
-- Version du serveur : 11.4.9-MariaDB
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `mydb`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `description` varchar(400) DEFAULT NULL,
  `category_slug` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_slug_UNIQUE` (`category_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `author_id` int(10) UNSIGNED NOT NULL,
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `comment_title` varchar(120) DEFAULT NULL,
  `comment_text` varchar(500) NOT NULL,
  `comment_status` enum('banni','publié') NOT NULL DEFAULT 'publié',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`comment_id`),
  KEY `fk_comments_author_idx` (`author_id`),
  KEY `idx_comments_recipe_date` (`recipe_id`,`created_at` DESC)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ingredient`
--

DROP TABLE IF EXISTS `ingredient`;
CREATE TABLE IF NOT EXISTS `ingredient` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ingredient_name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ingredient`
--

INSERT INTO `ingredient` (`id`, `name`) VALUES
(4, 'beurre'),
(3, 'chocolat noir'),
(2, 'farine'),
(8, 'lait'),
(11, 'noisette'),
(9, 'noisette en poudre'),
(6, 'oeuf'),
(1, 'sel'),
(5, 'sucre en poudre'),
(7, 'vanille');

-- --------------------------------------------------------

--
-- Structure de la table `login_attempt`
--

DROP TABLE IF EXISTS `login_attempt`;
CREATE TABLE IF NOT EXISTS `login_attempt` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `email` varchar(120) NOT NULL,
  `attempted_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_login_attempt_ip_date` (`ip`,`attempted_at`),
  KEY `idx_login_attempt_email_date` (`email`,`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message_contact`
--

DROP TABLE IF EXISTS `message_contact`;
CREATE TABLE IF NOT EXISTS `message_contact` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `email` varchar(120) NOT NULL,
  `subject` varchar(120) DEFAULT NULL,
  `message` varchar(500) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rating`
--

DROP TABLE IF EXISTS `rating`;
CREATE TABLE IF NOT EXISTS `rating` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `rate` tinyint(3) UNSIGNED NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`recipe_id`),
  KEY `fk_rating_recipe_idx` (`recipe_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
CREATE TABLE IF NOT EXISTS `recipes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` varchar(400) DEFAULT NULL,
  `photo_main` varchar(500) DEFAULT NULL,
  `prepare_time` smallint(5) UNSIGNED NOT NULL,
  `cook_time` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `portions` tinyint(3) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `difficulty` enum('facile','moyen','difficile') NOT NULL,
  `users_id` int(10) UNSIGNED NOT NULL,
  `recipes_slug` varchar(110) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recipes_slug_UNIQUE` (`recipes_slug`),
  KEY `fk_recipes_users_idx` (`users_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `recipes`
--

INSERT INTO `recipes` (`id`, `title`, `description`, `photo_main`, `prepare_time`, `cook_time`, `portions`, `created_at`, `difficulty`, `users_id`, `recipes_slug`) VALUES
(1, 'moelleux au chocolat', 'Le moelleux au chocolat est un gâteau individuel ou familial caractérisé par une texture à la fois aérienne à l\'extérieur et incroyablement fondante, voire légèrement coulante en son cœur', 'moelleux.jpg', 5, 25, 1, '2026-10-05 09:36:47', 'moyen', 1, 'moelleux-au-chocolat'),
(2, 'mousse au chocolat', 'Pour réaliser une mousse au chocolat traditionnelle, aérienne et ferme pour 6 personnes, il vous faut 200 g de chocolat noir, 6 œufs, 1 pincée de sel et éventuellement 30 g de sucre selon vos goûts.', 'mousse.jpg', 15, 0, 6, '2026-10-05 11:57:08', 'moyen', 1, 'mousse-au-chocolat'),
(3, 'brownie au chocolat', 'C\'est un gâteau originaire des États-Unis, caractérisé par sa texture double. Il offre un contraste parfait entre une surface fine, brillante et craquelée, et un cœur extrêmement dense, lourd et fondant. L\'ajout traditionnel de morceaux de noix apporte une touche croquante irrésistible qui casse la richesse du chocolat noir.', 'brownie.jpg', 15, 25, 1, '2026-10-05 12:15:40', 'difficile', 1, 'brownie-au-chocolat'),
(4, 'cookie au chocolat', 'Le biscuit réconfortant par excellence. Un cookie parfait doit être croustillant sur les bords et intensément moelleux, presque mi-cuit (chewy) au centre. Parsemé de pépites de chocolat qui fondent à la dégustation, il combine la douceur du beurre et du sucre roux avec le caractère des morceaux de chocolat.', 'cookie.jpg', 15, 12, 1, '2026-10-05 12:15:40', 'moyen', 1, 'cookie-au-chocolat'),
(5, 'muffin au chocolat', 'Ce petit gâteau individuel se distingue par sa texture très aérée, spongieuse et incroyablement gonflée. Contrairement au brownie qui est dense, le muffin est léger en bouche comme un gâteau traditionnel, tout en restant très gourmand grâce à sa pâte riche en cacao et ses pépites de chocolat dissimulées à l\'intérieur.', 'muffin.jpg', 20, 20, 1, '2026-10-05 12:15:40', 'moyen', 1, 'muffin-au-chocolat');

-- --------------------------------------------------------

--
-- Structure de la table `recipes_has_category`
--

DROP TABLE IF EXISTS `recipes_has_category`;
CREATE TABLE IF NOT EXISTS `recipes_has_category` (
  `recipes_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`recipes_id`,`category_id`),
  KEY `fk_recipes_has_category_category_idx` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `recipes_ingredient`
--

DROP TABLE IF EXISTS `recipes_ingredient`;
CREATE TABLE IF NOT EXISTS `recipes_ingredient` (
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `ingredient_id` int(10) UNSIGNED NOT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`recipe_id`,`ingredient_id`),
  KEY `fk_recipes_ingredient_ingredient_idx` (`ingredient_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `recipes_ingredient`
--

INSERT INTO `recipes_ingredient` (`recipe_id`, `ingredient_id`, `quantity`, `unit`) VALUES
(1, 3, 200.00, 'gr'),
(1, 4, 120.00, 'gr'),
(1, 6, 4.00, NULL),
(1, 5, 150.00, 'gr'),
(1, 2, 80.00, 'gr'),
(1, 1, 1.00, NULL),
(2, 3, 200.00, 'gr'),
(2, 4, 150.00, 'gr'),
(2, 5, 150.00, 'gr'),
(2, 6, 3.00, NULL),
(2, 2, 80.00, 'gr'),
(2, 7, 1.00, NULL),
(2, 1, 1.00, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `step`
--

DROP TABLE IF EXISTS `step`;
CREATE TABLE IF NOT EXISTS `step` (
  `idstep` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `step_number` tinyint(3) UNSIGNED NOT NULL,
  `step_title` varchar(80) NOT NULL,
  `description` text NOT NULL,
  `step_photo` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`idstep`),
  UNIQUE KEY `step_recipe_number_UNIQUE` (`recipe_id`,`step_number`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `step`
--

INSERT INTO `step` (`idstep`, `recipe_id`, `step_number`, `step_title`, `description`, `step_photo`) VALUES
(1, 1, 1, 'Le préchauffage du four', 'Préchauffez votre four à 180°C (thermostat 6) en mode chaleur tournante si possible. Cette étape permet au four d\'atteindre la température idéale afin de saisir le gâteau dès son entrée et de garantir une cuisson uniforme.', ''),
(2, 1, 2, 'La préparation du moule', 'Beurrez généreusement l\'intérieur de votre moule à gâteau (d\'environ 20 cm de diamètre) avec un morceau de beurre mou, puis saupoudrez-le d\'une fine couche de farine en tapotant pour enlever l\'excédent. Cela évitera au moelleux de coller et facilitera un démoulage parfait.', ''),
(3, 1, 3, 'La fonte du chocolat et du beurre', 'Découpez le chocolat noir et le beurre en morceaux. Faites-les fondre ensemble à feu très doux au bain-marie ou au micro-ondes par sessions de 30 secondes. Mélangez délicatement à l\'aide d\'une spatule jusqu\'à l\'obtention d\'une texture parfaitement lisse, brillante et homogène. Laissez tiédir quelques minutes.', NULL),
(4, 1, 4, 'Le blanchiment des œufs et du sucre', 'vigoureusement à l\'aide d\'un fouet électrique ou manuel pendant 2 à 3 minutes. Le mélange doit blanchir, devenir mousseux et doubler de volume pour apporter de la légèreté au gâteau.', NULL),
(5, 1, 5, 'L\'ajout des ingrédients secs', 'Ajoutez la farine et la pincée de sel directement dans le saladier contenant les œufs blanchis. Mélangez doucement sans trop insister, juste assez pour incorporer la farine à la masse sans casser le côté aérien des œufs.', NULL),
(6, 1, 6, 'Le mariage des deux préparations', 'Versez le chocolat et le beurre fondus tiédis dans la pâte. Remuez délicatement avec une maryse ou une cuillère en bois en effectuant un mouvement circulaire du centre vers les bords jusqu’à ce que la pâte prenne une jolie couleur chocolatée uniforme.', NULL),
(7, 1, 7, 'Le remplissage du moule', 'Versez la pâte de manière homogène dans le moule que vous avez beurré et fariné à l\'étape 2. Tapotez légèrement le dessous du moule sur votre plan de travail pour répartir la pâte et éliminer les grosses bulles d\'air.', NULL),
(8, 1, 8, 'La cuisson précise', 'Enfournez le gâteau à mi-hauteur et laissez cuire pendant 20 à 25 minutes. Surveillez bien la fin de la cuisson : le gâteau doit être gonflé sur les bords et le centre doit rester légèrement tremblotant au toucher pour conserver toute sa texture moelleuse.', NULL),
(9, 1, 9, 'Le repos et le service', 'Sortez le gâteau du four et laissez-le tiédir pendant une dizaine de minutes sur une grille. Cela permet à la structure du moelleux de se stabiliser avant de le démouler délicatement sur votre plat de service.', NULL),
(10, 3, 1, 'Le préchauffage et le moule', 'Préparez votre four et votre matériel. Préchauffez votre four à 180°C (thermostat 6). Beurrez et farinez un moule carré, ou tapissez-le de papier sulfurisé pour faciliter le démoulage après la cuisson.', NULL),
(11, 3, 2, 'La fonte du chocolat', 'Faites fondre la base chocolatée. Coupez le chocolat noir et le beurre en morceaux. Faites-les fondre ensemble au bain-marie ou au micro-ondes à faible puissance, puis mélangez jusqu\'à obtenir une texture lisse et laissez tiédir.', NULL),
(12, 3, 3, 'Le blanchiment des œufs', 'Fouettez les œufs et le sucre. Dans un grand saladier, battez énergiquement les œufs entiers avec le sucre en poudre et le sucre vanillé. Le mélange doit blanchir et devenir légèrement mousseux pour donner du volume au brownie.', NULL),
(13, 3, 4, 'L\'ajout des ingrédients secs', 'Incorporez la farine et le sel. Ajoutez une pincée de sel et la farine tamisée à la préparation. Mélangez délicatement avec une spatule ou un fouet pour l\'intégrer complètement sans trop travailler la pâte.', NULL),
(14, 3, 5, 'L\'assemblage et les gourmandises', 'Mélangez le chocolat et les garnitures. Versez le chocolat et le beurre fondus tièdes sur le mélange d\'œufs et de farine. Remuez doucement jusqu\'à l\'obtention d\'une pâte homogène, puis incorporez les noix ou les pépites si vous avez choisi d\'en mettre.', NULL),
(15, 3, 6, 'Le moulage', 'Versez la pâte dans le moule. Transférez la préparation finale dans votre moule en veillant à bien racler les bords. Utilisez une spatule pour lisser la surface afin que l\'épaisseur soit bien uniforme partout.', NULL),
(16, 3, 7, 'La cuisson maîtrisée', 'Enfournez et surveillez le cœur. Faites cuire au four pendant 15 à 20 minutes. Le brownie doit former une fine croûte craquelée sur le dessus tout en restant bien fondant et humide à l\'intérieur.', NULL),
(17, 3, 8, 'Le repos avant découpe', 'Laissez refroidir et découpez. Sortez le brownie du four et laissez-le refroidir complètement dans son moule avant de le manipuler. Découpez-le ensuite en carrés réguliers pour obtenir une présentation nette.', NULL),
(18, 2, 1, 'La fonte du chocolat', 'Faites fondre la base de la mousse. Cassez le chocolat en morceaux et faites-le fondre au bain-marie ou au micro-ondes à faible puissance. Si vous le souhaitez, ajoutez le beurre à cette étape. Mélangez jusqu\'à obtenir une texture lisse, puis laissez tiédir à température ambiante.', NULL),
(19, 2, 2, 'La clarification des oeufs', 'Séparez les blancs des jaunes. Cassez les œufs en séparant soigneusement les blancs des jaunes. Placez les blancs dans un grand saladier propre et sec (indispensable pour qu\'ils montent bien) et mettez les jaunes de côté.', NULL),
(20, 2, 3, 'L\'intégration des jaunes', 'Incorporez les jaunes au chocolat. Versez les jaunes d\'œufs un à un dans le chocolat fondu et légèrement refroidi. Mélangez énergiquement à l\'aide d\'un fouet ou d\'une spatule après chaque ajout pour obtenir un mélange homogène.', NULL),
(21, 2, 4, 'La monte des blancs', 'Montez les blancs en neige ferme. Ajoutez une pincée de sel dans vos blancs d\'œufs. À l\'aide d\'un batteur électrique, fouettez-les jusqu\'à ce qu\'ils deviennent bien fermes (ils doivent former un \"bec d\'oiseau\" au bout du batteur et ne pas tomber si vous retournez le saladier).', NULL),
(22, 2, 5, 'Le mélange délicat', 'Incorporez les blancs sans les casser. Ajoutez un tiers des blancs en neige au chocolat et mélangez vivement pour détendre la pâte. Incorporez ensuite le reste des blancs très délicatement, en effectuant un mouvement de bas en haut avec une maryse (spatule souple) pour emprisonner l\'air.', NULL),
(23, 2, 6, 'Le repos au frais', 'Laissez la mousse figer et prendre corps. Répartissez la mousse dans des ramequins individuels ou un grand saladier. Couvrez et placez au réfrigérateur pendant au moins 3 heures (idéalement toute une nuit) avant de déguster pour qu\'elle soit parfaitement ferme.', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','inscrit') NOT NULL DEFAULT 'inscrit',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'test', 'test@gmail.com', '123', 'admin', '2026-10-05 09:34:36');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `recipes_has_category`
--
ALTER TABLE `recipes_has_category`
  ADD CONSTRAINT `fk_recipes_has_category_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recipes_has_category_recipes` FOREIGN KEY (`recipes_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
