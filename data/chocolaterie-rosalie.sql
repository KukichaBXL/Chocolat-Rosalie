-- MySQL Script corrigé
-- Base de données de recettes
 
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE,
SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
 
-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
 
CREATE SCHEMA IF NOT EXISTS `mydb`
DEFAULT CHARACTER SET utf8mb4;
 
USE `mydb`;
 
-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
 
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(30) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'inscrit') NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC)
)
ENGINE = InnoDB;
 
-- -----------------------------------------------------
-- Table `category`
-- -----------------------------------------------------
 
CREATE TABLE IF NOT EXISTS `category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  `description` VARCHAR(400) NULL,
  `category_slug` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `category_slug_UNIQUE` (`category_slug` ASC)
)
ENGINE = InnoDB;
 
-- -----------------------------------------------------
-- Table `recipes`
-- -----------------------------------------------------
 
CREATE TABLE IF NOT EXISTS `recipes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  `description` VARCHAR(400) NULL,
  `photo_main` VARCHAR(500) NULL,
  `created_at` DATETIME NOT NULL,
  `difficulty` ENUM('facile', 'moyen', 'difficile') NULL,
  `users_id` INT UNSIGNED NOT NULL,
  `recipes_slug` VARCHAR(45) NULL,
 
  PRIMARY KEY (`id`),
 
  INDEX `fk_recipes_users_idx` (`users_id` ASC),
 
  UNIQUE INDEX `recipes_slug_UNIQUE` (`recipes_slug` ASC),
 
  CONSTRAINT `fk_recipes_users`
    FOREIGN KEY (`users_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
ENGINE = InnoDB;
 
-- -----------------------------------------------------
-- Table `ingredient`
-- -----------------------------------------------------
 
CREATE TABLE IF NOT EXISTS `ingredient` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
 
  PRIMARY KEY (`id`),
 
  UNIQUE INDEX `ingredient_name_UNIQUE` (`name` ASC)
)
ENGINE = InnoDB;
 
-- -----------------------------------------------------
-- Table `recipes_ingredient`
-- -----------------------------------------------------
 
CREATE TABLE IF NOT EXISTS `recipes_ingredient` (
  `recipe_id` INT UNSIGNED NOT NULL,
  `ingredient_id` INT UNSIGNED NOT NULL,
  `quantity` DECIMAL(10,2) NULL,
  `unit` VARCHAR(50) NULL,
 
  PRIMARY KEY (`recipe_id`, `ingredient_id`),
 
  INDEX `fk_recipes_ingredient_ingredient_idx`
    (`ingredient_id` ASC),
 
  CONSTRAINT `fk_recipes_ingredient_recipe`
    FOREIGN KEY (`recipe_id`)
    REFERENCES `recipes` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
 
  CONSTRAINT `fk_recipes_ingredient_ingredient`
    FOREIGN KEY (`ingredient_id`)
    REFERENCES `ingredient` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
ENGINE = InnoDB;
 
-- -----------------------------------------------------
-- Table `step`
-- -----------------------------------------------------
 
CREATE TABLE IF NOT EXISTS `step` (
  `idstep` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipe_id` INT UNSIGNED NOT NULL,
  `step_number` INT UNSIGNED NOT NULL,
  `description` TEXT NOT NULL,
  `prepare_time` INT NOT NULL,
  `cook_time` INT NOT NULL,
  `portions` INT NOT NULL,
  `step_slug` VARCHAR(45) NULL,
 
  PRIMARY KEY (`idstep`),
 
  INDEX `fk_step_recipe_idx`
    (`recipe_id` ASC),
 
  CONSTRAINT `fk_step_recipe`
    FOREIGN KEY (`recipe_id`)
    REFERENCES `recipes` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
ENGINE = InnoDB;
 
-- -----------------------------------------------------
-- Table `comments`
-- -----------------------------------------------------
 
CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `author_id` INT UNSIGNED NOT NULL,
  `recipe_id` INT UNSIGNED NOT NULL,
  `comment_title` VARCHAR(120) NULL,
  `comment_text` VARCHAR(500) NOT NULL,
  `comment_status` ENUM('banni', 'publié') NOT NULL DEFAULT 'publié',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 
  PRIMARY KEY (`comment_id`),
 
  INDEX `fk_comments_author_idx`
    (`author_id` ASC),
 
  INDEX `fk_comments_recipe_idx`
    (`recipe_id` ASC),
 
  CONSTRAINT `fk_comments_author`
    FOREIGN KEY (`author_id`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
 
  CONSTRAINT `fk_comments_recipe`
    FOREIGN KEY (`recipe_id`)
    REFERENCES `recipes` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
ENGINE = InnoDB;
 
-- -----------------------------------------------------
-- Table `rating`
-- -----------------------------------------------------
 
CREATE TABLE IF NOT EXISTS `rating` (
  `user_id` INT UNSIGNED NOT NULL,
  `recipe_id` INT UNSIGNED NOT NULL,
  `rate` ENUM('1', '2', '3', '4', '5') NOT NULL,
  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 
  PRIMARY KEY (`user_id`, `recipe_id`),
 
  INDEX `fk_rating_recipe_idx`
    (`recipe_id` ASC),
 
  CONSTRAINT `fk_rating_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
 
  CONSTRAINT `fk_rating_recipe`
    FOREIGN KEY (`recipe_id`)
    REFERENCES `recipes` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
ENGINE = InnoDB;
 
-- -----------------------------------------------------
-- Table `message_contact`
-- -----------------------------------------------------
 
CREATE TABLE IF NOT EXISTS `message_contact` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `email` VARCHAR(120) NULL,
  `subject` VARCHAR(45) NULL,
  `message` VARCHAR(400) NULL,
  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB;
 
-- -----------------------------------------------------
-- Table `recipes_has_category`
-- -----------------------------------------------------
 
CREATE TABLE IF NOT EXISTS `recipes_has_category` (
  `recipes_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
 
  PRIMARY KEY (`recipes_id`, `category_id`),
 
  INDEX `fk_recipes_has_category_category_idx`
    (`category_id` ASC),
 
  INDEX `fk_recipes_has_category_recipes_idx`
    (`recipes_id` ASC),
 
  CONSTRAINT `fk_recipes_has_category_recipes`
    FOREIGN KEY (`recipes_id`)
    REFERENCES `recipes` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
 
  CONSTRAINT `fk_recipes_has_category_category`
    FOREIGN KEY (`category_id`)
    REFERENCES `category` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
ENGINE = InnoDB;
 
-- -----------------------------------------------------
-- Restore settings
-- -----------------------------------------------------
 
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;