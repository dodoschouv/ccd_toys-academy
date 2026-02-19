USE `toys_academy`;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `article` (
  `id` VARCHAR(50) NOT NULL,
  `designation` VARCHAR(255) NOT NULL,
  `category` ENUM('SOC','FIG','CON','EXT','EVL','LIV') NOT NULL,
  `age_range` ENUM('BB','PE','EN','AD') NOT NULL,
  `state` ENUM('N','TB','B') NOT NULL,
  `price` INT UNSIGNED NOT NULL DEFAULT 0,
  `weight` INT UNSIGNED NOT NULL DEFAULT 0,
  `barcode` VARCHAR(100) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_article_category` (`category`),
  KEY `idx_article_age_range` (`age_range`),
  KEY `idx_article_state` (`state`),
  KEY `idx_article_barcode` (`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `subscriber` (
  `id` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `child_age_range` ENUM('BB','PE','EN','AD') NOT NULL,
  `preference_1` ENUM('SOC','FIG','CON','EXT','EVL','LIV') NOT NULL,
  `preference_2` ENUM('SOC','FIG','CON','EXT','EVL','LIV') NOT NULL,
  `preference_3` ENUM('SOC','FIG','CON','EXT','EVL','LIV') NOT NULL,
  `preference_4` ENUM('SOC','FIG','CON','EXT','EVL','LIV') NOT NULL,
  `preference_5` ENUM('SOC','FIG','CON','EXT','EVL','LIV') NOT NULL,
  `preference_6` ENUM('SOC','FIG','CON','EXT','EVL','LIV') NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_subscriber_email` (`email`),
  KEY `idx_subscriber_age` (`child_age_range`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('admin','subscriber') NOT NULL DEFAULT 'subscriber',
  `subscriber_id` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_email` (`email`),
  KEY `idx_user_role` (`role`),
  KEY `idx_user_subscriber` (`subscriber_id`),
  CONSTRAINT `fk_user_subscriber` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
