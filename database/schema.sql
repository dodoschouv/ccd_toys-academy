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
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
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

CREATE TABLE IF NOT EXISTS `campaign` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `max_weight_per_box` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `box` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `campaign_id` INT UNSIGNED NOT NULL,
  `subscriber_id` VARCHAR(50) NOT NULL,
  `status` ENUM('draft','validated') NOT NULL DEFAULT 'draft',
  `score` INT NOT NULL DEFAULT 0,
  `total_weight` INT UNSIGNED NOT NULL DEFAULT 0,
  `total_price` INT UNSIGNED NOT NULL DEFAULT 0,
  `validated_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_box_campaign` (`campaign_id`),
  KEY `idx_box_subscriber` (`subscriber_id`),
  KEY `idx_box_status` (`status`),
  CONSTRAINT `fk_box_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_box_subscriber` FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `box_article` (
  `box_id` INT UNSIGNED NOT NULL,
  `article_id` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`box_id`, `article_id`),
  CONSTRAINT `fk_box_article_box` FOREIGN KEY (`box_id`) REFERENCES `box` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_box_article_article` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
