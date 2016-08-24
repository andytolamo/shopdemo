CREATE DATABASE `shop` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE shop;

CREATE TABLE `sku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(90) NOT NULL,
  `created_at` date NOT NULL,
  `inserted_at` date NOT NULL,
  `country_of_origin` varchar(90) NOT NULL,
  `quantity` int(11) NOT NULL,
  `weight` float NOT NULL,
  `sku_name` varchar(45) NOT NULL,
  `tax_category_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `description` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) NOT NULL DEFAULT 'eng',
  `description` varchar(45) NOT NULL,
  `sku_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_description_1_idx` (`sku_id`),
  CONSTRAINT `fk_description_1` FOREIGN KEY (`sku_id`) REFERENCES `sku` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` TEXT DEFAULT NULL,
  `sku_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_images_1_idx` (`sku_id`),
  CONSTRAINT `fk_images_1` FOREIGN KEY (`sku_id`) REFERENCES `sku` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency` varchar(10) NOT NULL,
  `price` int(11) NOT NULL,
  `sku_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_price_1_idx` (`sku_id`),
  CONSTRAINT `fk_price_1` FOREIGN KEY (`sku_id`) REFERENCES `sku` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `tax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_category` int(11) NOT NULL,
  `percentage` float NOT NULL,
  `destination_location` varchar(45) NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
