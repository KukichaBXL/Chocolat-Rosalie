-- =====================================================
-- BASE PROPRE DE DÉPART (Maison Rosalie) — version LOCALE (WAMP / MAMP)
-- Contenu : 5 recettes complètes, catégories, ingrédients, 22 notes et 17 commentaires de démo,
--           5 comptes : admin, robin, maxim, tiromana, marie (tous confirmés).
-- Vide : messages de contact, abonnés newsletter, tentatives de connexion.
-- ATTENTION : efface puis recrée la base `mydb` (tout ce qu'elle contenait est perdu).
-- phpMyAdmin (connecté en root) > Importer > ce fichier.
-- Ensuite (une seule fois) : lancer data/compte-bdd.sql pour créer le compte rosalie_app.
-- Mots de passe de démo : admin@admin.com = Webdev2026 ; les autres comptes = Rosalie2026
-- =====================================================
DROP DATABASE IF EXISTS `mydb`;
CREATE DATABASE `mydb` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mydb`;
SET NAMES utf8mb4;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `description` varchar(400) DEFAULT NULL,
  `category_slug` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_slug_UNIQUE` (`category_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES
(1,'Gâteaux','Fondants, brownies et gâteaux au chocolat.','gateaux'),
(2,'Mousses','Mousses légères et aériennes.','mousses'),
(3,'Boissons','Chocolats chauds et boissons gourmandes.','boissons'),
(4,'Glacé','Glaces et desserts glacés.','glace');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(10) unsigned NOT NULL,
  `recipe_id` int(10) unsigned NOT NULL,
  `comment_title` varchar(120) DEFAULT NULL,
  `comment_text` varchar(500) NOT NULL,
  `comment_status` enum('banni','publié') NOT NULL DEFAULT 'publié',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`),
  KEY `fk_comments_author_idx` (`author_id`),
  KEY `idx_comments_recipe_date` (`recipe_id`,`created_at` DESC),
  CONSTRAINT `fk_comments_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_comments_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES
(1,2,1,'Un régal','Testé ce week-end, un vrai régal, merci pour la recette !','publié','2026-10-01 18:00:00'),
(2,3,1,NULL,'Le cœur coulant est parfait, j\'ai suivi les temps à la lettre.','publié','2026-10-01 19:30:00'),
(3,4,1,'Astuce','Avec un chocolat à 70 %, c\'est encore meilleur.','publié','2026-10-01 20:15:00'),
(4,5,1,NULL,'Mes enfants en redemandent tous les dimanches.','publié','2026-10-02 16:45:00'),
(5,2,1,NULL,'J\'ai remplacé le sucre blanc par du sucre de canne : top.','publié','2026-10-02 18:00:00'),
(6,3,1,'Cuisson','Dans mon four, 9 minutes suffisent.','publié','2026-10-02 21:00:00'),
(7,4,1,NULL,'Recette claire et rapide, parfaite pour un dessert improvisé.','publié','2026-10-03 10:10:00'),
(8,5,1,NULL,'Servi avec une boule de glace vanille, succès garanti.','publié','2026-10-03 19:00:00'),
(9,2,1,NULL,'Je l\'ai fait en version sans gluten, ça marche.','publié','2026-10-03 18:30:00'),
(10,3,1,'Merci','Merci à la famille Rosalie de partager ses recettes !','publié','2026-10-04 08:45:00'),
(11,4,1,NULL,'Déjà trois fois cette semaine…','publié','2026-10-04 09:30:00'),
(12,5,1,NULL,'Mes invités ont adoré.','publié','2026-10-04 12:00:00'),
(13,2,2,'Légère','Mousse bien aérienne, j\'ai laissé prendre toute la nuit.','publié','2026-10-03 20:00:00'),
(14,3,3,NULL,'Croustillant dessus, fondant dedans : exactement ce que j\'aime.','publié','2026-10-04 15:00:00'),
(15,4,3,'Noisettes','Avec des noisettes concassées, c\'est encore mieux.','publié','2026-10-05 11:20:00'),
(16,5,4,NULL,'Faciles à faire avec les enfants.','publié','2026-10-05 16:40:00'),
(17,2,5,NULL,'Très moelleux, ils se conservent bien deux jours.','publié','2026-10-05 17:10:00');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingredient` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ingredient_name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ingredient` WRITE;
/*!40000 ALTER TABLE `ingredient` DISABLE KEYS */;
INSERT INTO `ingredient` VALUES
(4,'beurre'),
(3,'chocolat noir'),
(2,'farine'),
(8,'lait'),
(11,'noisette'),
(9,'noisette en poudre'),
(6,'œuf'),
(12,'pépites de chocolat'),
(1,'sel'),
(5,'sucre en poudre'),
(7,'vanille');
/*!40000 ALTER TABLE `ingredient` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `login_attempt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_attempt` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `email` varchar(120) NOT NULL,
  `attempted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_login_attempt_ip_date` (`ip`,`attempted_at`),
  KEY `idx_login_attempt_email_date` (`email`,`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `login_attempt` WRITE;
/*!40000 ALTER TABLE `login_attempt` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempt` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `message_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `email` varchar(120) NOT NULL,
  `subject` varchar(120) DEFAULT NULL,
  `message` varchar(500) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `message_contact` WRITE;
/*!40000 ALTER TABLE `message_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_contact` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `newsletter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter` (
  `id` tinyint(3) unsigned NOT NULL,
  `subject` varchar(150) NOT NULL,
  `body` text NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `newsletter` WRITE;
/*!40000 ALTER TABLE `newsletter` DISABLE KEYS */;
INSERT INTO `newsletter` VALUES
(1,'Les nouveautés de la Maison Rosalie','Bonjour,\n\nCe mois-ci, découvrez notre nouvelle recette de brownie au chocolat noir et notre sélection de chocolats chauds pour l\'hiver.\n\nÀ très vite,\nLa Maison Rosalie','2026-10-14 00:00:00');
/*!40000 ALTER TABLE `newsletter` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `newsletter_subscriber`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter_subscriber` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(120) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_newsletter_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `newsletter_subscriber` WRITE;
/*!40000 ALTER TABLE `newsletter_subscriber` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsletter_subscriber` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rating` (
  `user_id` int(10) unsigned NOT NULL,
  `recipe_id` int(10) unsigned NOT NULL,
  `rate` tinyint(3) unsigned NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`recipe_id`),
  KEY `fk_rating_recipe_idx` (`recipe_id`),
  CONSTRAINT `fk_rating_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rating_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `rating` WRITE;
/*!40000 ALTER TABLE `rating` DISABLE KEYS */;
INSERT INTO `rating` VALUES
(1,1,4,'2026-10-12 11:26:22'),
(1,3,5,'2026-10-12 11:26:22'),
(1,5,4,'2026-10-12 11:26:22'),
(2,1,5,'2026-10-12 11:26:22'),
(2,2,4,'2026-10-12 11:26:22'),
(2,3,5,'2026-10-12 11:26:22'),
(2,4,3,'2026-10-12 11:26:22'),
(3,1,5,'2026-10-12 11:26:22'),
(3,2,4,'2026-10-12 11:26:22'),
(3,3,4,'2026-10-12 11:26:22'),
(3,4,4,'2026-10-12 11:26:22'),
(3,5,3,'2026-10-12 11:26:22'),
(4,1,4,'2026-10-12 11:26:22'),
(4,2,5,'2026-10-12 11:26:22'),
(4,3,5,'2026-10-12 11:26:22'),
(4,4,3,'2026-10-12 11:26:22'),
(4,5,4,'2026-10-12 11:26:22'),
(5,1,5,'2026-10-12 11:26:22'),
(5,2,3,'2026-10-12 11:26:22'),
(5,3,5,'2026-10-12 11:26:22'),
(5,4,4,'2026-10-12 11:26:22'),
(5,5,4,'2026-10-12 11:26:22');
/*!40000 ALTER TABLE `rating` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` varchar(400) DEFAULT NULL,
  `photo_main` varchar(500) DEFAULT NULL,
  `prepare_time` smallint(5) unsigned NOT NULL,
  `cook_time` smallint(5) unsigned NOT NULL DEFAULT 0,
  `portions` tinyint(3) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `difficulty` enum('facile','moyen','difficile') NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  `recipes_slug` varchar(110) NOT NULL,
  `recipe_status` enum('publiée','masquée') NOT NULL DEFAULT 'publiée',
  PRIMARY KEY (`id`),
  UNIQUE KEY `recipes_slug_UNIQUE` (`recipes_slug`),
  KEY `fk_recipes_users_idx` (`users_id`),
  CONSTRAINT `fk_recipes_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
INSERT INTO `recipes` VALUES
(1,'moelleux au chocolat','Le moelleux au chocolat est un gâteau individuel ou familial caractérisé par une texture à la fois aérienne à l\'extérieur et incroyablement fondante, voire légèrement coulante en son cœur','moelleux.webp',5,25,4,'2026-10-05 09:36:47','moyen',1,'moelleux-au-chocolat','publiée'),
(2,'mousse au chocolat','Pour réaliser une mousse au chocolat traditionnelle, aérienne et ferme pour 6 personnes, il vous faut 200 g de chocolat noir, 6 œufs, 1 pincée de sel et éventuellement 30 g de sucre selon vos goûts.','mousse.webp',15,0,6,'2026-10-05 11:57:08','facile',1,'mousse-au-chocolat','publiée'),
(3,'brownie au chocolat','C\'est un gâteau originaire des États-Unis, caractérisé par sa texture double. Il offre un contraste parfait entre une surface fine, brillante et craquelée, et un cœur extrêmement dense, lourd et fondant. L\'ajout traditionnel de morceaux de noix apporte une touche croquante irrésistible qui casse la richesse du chocolat noir.','brownie.webp',15,25,8,'2026-10-05 12:15:40','difficile',1,'brownie-au-chocolat','publiée'),
(4,'cookie au chocolat','Le biscuit réconfortant par excellence. Un cookie parfait doit être croustillant sur les bords et intensément moelleux, presque mi-cuit (chewy) au centre. Parsemé de pépites de chocolat qui fondent à la dégustation, il combine la douceur du beurre et du sucre roux avec le caractère des morceaux de chocolat.','cookie.webp',15,12,12,'2026-10-05 12:15:40','facile',1,'cookie-au-chocolat','publiée'),
(5,'muffin au chocolat','Ce petit gâteau individuel se distingue par sa texture très aérée, spongieuse et incroyablement gonflée. Contrairement au brownie qui est dense, le muffin est léger en bouche comme un gâteau traditionnel, tout en restant très gourmand grâce à sa pâte riche en cacao et ses pépites de chocolat dissimulées à l\'intérieur.','muffin.webp',20,20,6,'2026-10-05 12:15:40','moyen',1,'muffin-au-chocolat','publiée');
/*!40000 ALTER TABLE `recipes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `recipes_has_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipes_has_category` (
  `recipes_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`recipes_id`,`category_id`),
  KEY `fk_recipes_has_category_category_idx` (`category_id`),
  CONSTRAINT `fk_recipes_has_category_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_recipes_has_category_recipes` FOREIGN KEY (`recipes_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `recipes_has_category` WRITE;
/*!40000 ALTER TABLE `recipes_has_category` DISABLE KEYS */;
INSERT INTO `recipes_has_category` VALUES
(1,1),
(2,2),
(3,1),
(4,1),
(5,1);
/*!40000 ALTER TABLE `recipes_has_category` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `recipes_ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipes_ingredient` (
  `recipe_id` int(10) unsigned NOT NULL,
  `ingredient_id` int(10) unsigned NOT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`recipe_id`,`ingredient_id`),
  KEY `fk_recipes_ingredient_ingredient_idx` (`ingredient_id`),
  CONSTRAINT `fk_recipes_ingredient_ingredient` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredient` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_recipes_ingredient_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `recipes_ingredient` WRITE;
/*!40000 ALTER TABLE `recipes_ingredient` DISABLE KEYS */;
INSERT INTO `recipes_ingredient` VALUES
(1,1,1.00,'pincée'),
(1,2,80.00,'g'),
(1,3,200.00,'g'),
(1,4,120.00,'g'),
(1,5,150.00,'g'),
(1,6,4.00,NULL),
(2,1,1.00,'pincée'),
(2,2,80.00,'g'),
(2,3,200.00,'g'),
(2,4,150.00,'g'),
(2,5,150.00,'g'),
(2,6,3.00,NULL),
(2,7,1.00,'gousse'),
(3,1,1.00,NULL),
(3,2,80.00,'gr'),
(3,3,200.00,'gr'),
(3,4,150.00,'gr'),
(3,5,150.00,'gr'),
(3,6,3.00,NULL),
(3,7,1.00,NULL),
(3,9,20.00,'gr'),
(3,11,100.00,'gr'),
(4,1,1.00,NULL),
(4,2,200.00,'gr'),
(4,4,125.00,'gr'),
(4,5,50.00,'gr'),
(4,6,1.00,NULL),
(4,7,1.00,NULL),
(4,12,150.00,'gr'),
(5,1,1.00,NULL),
(5,2,250.00,'gr'),
(5,4,100.00,'gr'),
(5,5,150.00,'gr'),
(5,6,2.00,NULL),
(5,8,20.00,'cl'),
(5,9,30.00,'gr'),
(5,12,100.00,'gr');
/*!40000 ALTER TABLE `recipes_ingredient` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `step`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `step` (
  `idstep` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `recipe_id` int(10) unsigned NOT NULL,
  `step_number` tinyint(3) unsigned NOT NULL,
  `step_title` varchar(80) NOT NULL,
  `description` text NOT NULL,
  `step_photo` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`idstep`),
  UNIQUE KEY `step_recipe_number_UNIQUE` (`recipe_id`,`step_number`),
  CONSTRAINT `fk_step_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `step` WRITE;
/*!40000 ALTER TABLE `step` DISABLE KEYS */;
INSERT INTO `step` VALUES
(1,1,1,'Le préchauffage du four','Préchauffez le four à 180°C (th. 6), en chaleur tournante si possible.','stepprepare.webp'),
(2,1,2,'La préparation du moule','Beurrez et farinez un moule d\'environ 20 cm pour faciliter le démoulage.','stepmoule.webp'),
(3,1,3,'La fonte du chocolat et du beurre','Faites fondre {chocolat noir} et {beurre} en morceaux à feu doux (bain-marie ou micro-ondes par 30 s), mélangez jusqu\'à obtenir une texture lisse, puis laissez tiédir.','stepmelange3.webp'),
(4,1,4,'Le blanchiment des œufs et du sucre','Fouettez {œuf} et {sucre en poudre} 2 à 3 minutes jusqu\'à ce que le mélange blanchisse et double de volume.','stepoeuf.webp'),
(5,1,5,'L\'ajout des ingrédients secs','Incorporez délicatement {farine} et {sel} sans casser la mousse.','stepajoutfruitsec.webp'),
(6,1,6,'Le mariage des deux préparations','Ajoutez le chocolat fondu tiédi et mélangez doucement à la maryse jusqu\'à obtenir une pâte homogène.','stepmelange2.webp'),
(7,1,7,'Le remplissage du moule','Versez la pâte dans le moule et tapotez-le légèrement pour chasser les bulles d\'air.','stepmoulage.webp'),
(8,1,8,'La cuisson précise','Enfournez 20 à 25 minutes à mi-hauteur : les bords doivent être gonflés et le centre encore légèrement tremblotant.','stepcuit.webp'),
(9,1,9,'Le repos et le service','Laissez tiédir une dizaine de minutes sur une grille avant de démouler.','moelleux.webp'),
(10,3,1,'Le préchauffage et le moule','Préchauffez le four à 180°C (th. 6) et beurrez-farinez un moule carré, ou tapissez-le de papier sulfurisé.','stepmoule.webp'),
(11,3,2,'La fonte du chocolat','Faites fondre le chocolat et le beurre en morceaux à feu doux, mélangez jusqu\'à obtenir une texture lisse, puis laissez tiédir.','stepfondu.webp'),
(12,3,3,'Le blanchiment des œufs','Fouettez les œufs avec le sucre et le sucre vanillé jusqu\'à ce que le mélange blanchisse et devienne mousseux.','stepoeuf.webp'),
(13,3,4,'L\'ajout des ingrédients secs','Incorporez délicatement la farine tamisée et une pincée de sel sans trop travailler la pâte.','stepajoutfruitsec.webp'),
(14,3,5,'L\'assemblage et les gourmandises','Ajoutez le chocolat fondu tiède, mélangez jusqu\'à obtenir une pâte homogène, puis incorporez les noix ou les pépites (facultatif).','stepnoisette.webp'),
(15,3,6,'Le moulage','Versez la pâte dans le moule et lissez la surface pour une épaisseur uniforme.','stepmoulage.webp'),
(16,3,7,'La cuisson maîtrisée','Enfournez et surveillez le cœur. Faites cuire au four pendant 15 à 20 minutes. Le brownie doit former une fine croûte craquelée sur le dessus tout en restant bien fondant et humide à l\'intérieur.','stepbiencuit.webp'),
(17,3,8,'Le repos avant découpe','Laissez refroidir complètement dans le moule, puis découpez en carrés réguliers.','stepfin.webp'),
(18,2,1,'La fonte du chocolat','Faites fondre {chocolat noir} (et {beurre} si vous le souhaitez) à feu doux, puis laissez tiédir.','stepfondu.webp'),
(19,2,2,'La clarification des oeufs','Séparez les blancs des jaunes, en plaçant les blancs dans un saladier propre et sec.','stepsepare.webp'),
(20,2,3,'L\'intégration des jaunes','Ajoutez les jaunes un à un au chocolat tiédi, en mélangeant énergiquement après chaque ajout.','stepoeuf2.webp'),
(21,2,4,'La monte des blancs','Montez les blancs en neige bien ferme avec {sel}.','stepoeuf.webp'),
(22,2,5,'Le mélange délicat','Mélangez d\'abord un tiers des blancs au chocolat, puis incorporez le reste très délicatement à la maryse, de bas en haut.','stepmelange3.webp'),
(23,2,6,'Le repos au frais','Répartissez la mousse dans des ramequins et placez-la au réfrigérateur au moins 3 heures, idéalement une nuit.','stepfinal2.webp'),
(24,4,1,'Le préchauffage et la plaque','Préchauffez le four à 180°C (th. 6) et tapissez une plaque de papier sulfurisé.','stepchauffe.webp'),
(25,4,2,'Le crémage du beurre et des sucres','Fouettez le beurre mou avec le sucre roux et le sucre blanc jusqu\'à obtenir un mélange crémeux.','stepcreme.webp'),
(26,4,3,'L\'ajout de l\'œuf','Incorporez l\'œuf et l\'extrait de vanille, puis mélangez jusqu\'à obtenir une texture homogène.','stepoeuf2.webp'),
(27,4,4,'L\'ajout des ingrédients secs','Ajoutez la farine, la levure et une pincée de sel, puis mélangez sans trop travailler la pâte.','stepajoutfruitsec.webp'),
(28,4,5,'Les pépites de chocolat','Incorporez les pépites ou le chocolat noir concassé à l\'aide d\'une spatule.','stepnoisette.webp'),
(29,4,6,'Le façonnage','Formez des boules de pâte et disposez-les sur la plaque en les espaçant bien, car elles s\'étalent à la cuisson.','stepcreation.webp'),
(30,4,7,'La cuisson','Enfournez 10 à 12 minutes : les bords doivent être dorés et le centre encore mou.','stepcuisson2.webp'),
(31,4,8,'Le refroidissement','Laissez reposer 5 minutes sur la plaque, puis transférez sur une grille pour qu\'ils durcissent en refroidissant.','stepfroid.webp'),
(32,5,1,'Le préchauffage et les moules','Préchauffez le four à 180°C (th. 6) et placez des caissettes en papier dans un moule à muffins.','stepmoule.webp'),
(33,5,2,'La fonte du chocolat et du beurre','Faites fondre le chocolat et le beurre à feu doux, puis laissez tiédir.','stepmelange3.webp'),
(34,5,3,'Le mélange des ingrédients secs','Dans un saladier, mélangez la farine, le cacao, la levure, le sucre et une pincée de sel.','stepajoutfruitsec.webp'),
(35,5,4,'Le mélange des ingrédients liquides','Dans un autre saladier, fouettez les œufs avec le lait, puis ajoutez le chocolat fondu tiède.','stepoeuf3.webp'),
(36,5,5,'L\'assemblage','Versez les liquides sur les ingrédients secs et mélangez brièvement : quelques grumeaux rendent les muffins plus moelleux.','stepmelange.webp'),
(37,5,6,'Les pépites','Incorporez les pépites de chocolat en en gardant quelques-unes pour décorer le dessus.','stepnoisette.webp'),
(38,5,7,'Le remplissage des moules','Remplissez les caissettes aux deux tiers, puis parsemez le dessus des pépites restantes.','stepfilling.webp'),
(39,5,8,'La cuisson','Enfournez 20 à 22 minutes : la lame d\'un couteau plantée au centre doit ressortir presque sèche.','stepprepare.webp'),
(40,5,9,'Le refroidissement','Laissez tiédir 5 minutes dans le moule, puis démoulez sur une grille.','stepfroid2.webp');
/*!40000 ALTER TABLE `step` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','inscrit') NOT NULL DEFAULT 'inscrit',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'admin','admin@admin.com','$2y$10$.chXqsloONRa0.R7TOGk.eDAS1MKUmZIFebLm.P3W6Q3dtEWyec/m','admin','2026-10-01 09:00:00',1),
(2,'robin','robin@exemple.be','$2y$10$Bfd9a45RHAqDPNhYmhMBB.0es6TnZpR2eJtkHgMVINZZ37v1Arevy','inscrit','2026-10-01 14:12:00',1),
(3,'maxim','maxim@exemple.be','$2y$10$Bfd9a45RHAqDPNhYmhMBB.0es6TnZpR2eJtkHgMVINZZ37v1Arevy','inscrit','2026-10-02 18:40:00',1),
(4,'tiromana','tiromana@exemple.be','$2y$10$Bfd9a45RHAqDPNhYmhMBB.0es6TnZpR2eJtkHgMVINZZ37v1Arevy','inscrit','2026-10-02 11:05:00',1),
(5,'marie','marie@exemple.be','$2y$10$Bfd9a45RHAqDPNhYmhMBB.0es6TnZpR2eJtkHgMVINZZ37v1Arevy','inscrit','2026-10-03 20:30:00',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

