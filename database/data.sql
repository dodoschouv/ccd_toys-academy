USE `toys_academy`;
SET NAMES utf8mb4;

-- Articles d'exemple (selon le format de l'exemple fourni)
INSERT INTO `article` (`id`, `designation`, `category`, `age_range`, `state`, `price`, `weight`, `barcode`) VALUES
('a1', 'Monopoly Junior', 'SOC', 'PE', 'N', 8, 400, NULL),
('a2', 'Barbie Aventurière', 'FIG', 'PE', 'TB', 5, 300, NULL),
('a3', 'Puzzle éducatif', 'EVL', 'PE', 'TB', 7, 350, NULL),
('a4', 'Cubes alphabet', 'CON', 'PE', 'N', 4, 300, NULL),
('a5', 'Livre cache-cache', 'LIV', 'PE', 'N', 3, 200, NULL),
('a6', 'Kapla 200 pièces', 'CON', 'EN', 'B', 10, 600, NULL),
('a7', 'Cerf-volant Pirate', 'EXT', 'EN', 'N', 6, 400, NULL),
('a8', 'Le Petit Nicolas', 'LIV', 'EN', 'TB', 5, 200, NULL),
('a9', 'Lego Classic Boîte Créative', 'CON', 'EN', 'N', 12, 800, NULL),
('a10', 'Ballon de Football', 'EXT', 'EN', 'TB', 8, 300, NULL),
('a11', 'Peluche Ours en Peluche', 'FIG', 'BB', 'N', 6, 200, NULL),
('a12', 'Tricycle Rouge', 'EXT', 'PE', 'TB', 25, 3500, NULL),
('a13', 'Livre "Le Petit Prince"', 'LIV', 'EN', 'N', 5, 150, NULL),
('a14', 'Kit de Peinture à l''Eau', 'CON', 'PE', 'N', 5, 300, NULL),
('a15', 'Voiture Télécommandée', 'EXT', 'EN', 'B', 15, 600, NULL),
('a16', 'Poupée Bébé qui Pleure', 'FIG', 'PE', 'N', 9, 250, NULL),
('a17', 'Jeu de Cartes Uno', 'SOC', 'EN', 'TB', 3, 100, NULL),
('a18', 'Trottinette Bleue', 'EXT', 'EN', 'N', 18, 2500, NULL),
('a19', 'Livre "Harry Potter Tome 1"', 'LIV', 'AD', 'N', 7, 300, NULL),
('a20', 'Puzzle 500 pièces Paysage', 'CON', 'AD', 'N', 8, 600, NULL);

-- Abonnés d'exemple (selon le format de l'exemple fourni)
INSERT INTO `subscriber` (`id`, `last_name`, `first_name`, `email`, `child_age_range`, `preference_1`, `preference_2`, `preference_3`, `preference_4`, `preference_5`, `preference_6`) VALUES
('s1', 'Dupont', 'Alice', 'alice.dupont@example.com', 'PE', 'SOC', 'FIG', 'EVL', 'CON', 'LIV', 'EXT'),
('s2', 'Martin', 'Bob', 'bob.martin@example.com', 'EN', 'EXT', 'CON', 'SOC', 'EVL', 'FIG', 'LIV'),
('s3', 'Bernard', 'Clara', 'clara.bernard@example.com', 'PE', 'EVL', 'LIV', 'FIG', 'SOC', 'CON', 'EXT'),
('s4', 'Petit', 'David', 'david.petit@example.com', 'EN', 'CON', 'EXT', 'SOC', 'FIG', 'LIV', 'EVL'),
('s5', 'Moreau', 'Emma', 'emma.moreau@example.com', 'PE', 'SOC', 'CON', 'FIG', 'EXT', 'LIV', 'EVL');

-- Utilisateurs d'exemple
INSERT INTO `user` (`email`, `first_name`, `last_name`, `password_hash`, `role`, `subscriber_id`) VALUES
('admin@toysacademy.com', 'Admin', 'System', NULL, 'admin', NULL),
('alice.dupont@example.com', 'Alice', 'Dupont', NULL, 'subscriber', 's1'),
('bob.martin@example.com', 'Bob', 'Martin', NULL, 'subscriber', 's2');

-- Campagnes d'exemple
INSERT INTO `campaign` (`max_weight_per_box`) VALUES
(1200),
(5000),
(3000);
