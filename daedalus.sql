-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 14 juin 2024 à 15:46
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `daedalus`
--

-- --------------------------------------------------------

--
-- Structure de la table `achievement`
--

CREATE TABLE `achievement` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `achievement`
--

INSERT INTO `achievement` (`id`, `name`, `description`) VALUES
(0, 'Gamebreaker', 'You\'ve beaten our incredible solver '),
(1, 'Challenger', 'You\'ve managed to escape from the challenge mode '),
(2, 'Hell Climber', 'Level 25 of infinite mode achieved'),
(3, 'Why ?', 'Level 50 of infinite mode achieved'),
(4, 'Still here ?', 'Level 75 of infinite mode achieved'),
(5, 'Hell returner', 'Level 100 of infinite mode achieved'),
(6, 'Map maker', 'Create your own level '),
(7, 'Adventurer', 'Your adventure begins ');

-- --------------------------------------------------------

--
-- Structure de la table `adventure`
--

CREATE TABLE `adventure` (
  `id` int(11) NOT NULL,
  `difficulty` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `adventure`
--

INSERT INTO `adventure` (`id`, `difficulty`, `name`) VALUES
(1, 1, 'Tutoriel'),
(2, 1, 'Niveau 2'),
(3, 1, 'Niveau 3'),
(4, 2, 'Niveau 4'),
(5, 5, 'Niveau 5');

-- --------------------------------------------------------

--
-- Structure de la table `adventure_leaderboard`
--

CREATE TABLE `adventure_leaderboard` (
  `level_id` int(11) NOT NULL,
  `player_id` char(22) NOT NULL,
  `moves` int(11) NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `adventure_leaderboard`
--

INSERT INTO `adventure_leaderboard` (`level_id`, `player_id`, `moves`, `time`) VALUES
(1, '666b8c1fddcfc387256589', 700, '00:01:17'),
(4, '666b8c1fddcfc387256589', 480, '00:01:33');

-- --------------------------------------------------------

--
-- Structure de la table `leaderboard`
--

CREATE TABLE `leaderboard` (
  `player_id` varchar(22) NOT NULL,
  `level_id` varchar(22) NOT NULL,
  `moves` int(11) NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `leaderboard`
--

INSERT INTO `leaderboard` (`player_id`, `level_id`, `moves`, `time`) VALUES
('6645c6093c644139042401', 'presentation', 88, '00:00:21');

-- --------------------------------------------------------

--
-- Structure de la table `level`
--

CREATE TABLE `level` (
  `id` char(22) NOT NULL,
  `creator_id` char(22) NOT NULL,
  `difficulty` int(11) NOT NULL,
  `objects` set('KEY_RED','KEY_YELLOW','KEY_GREEN','KEY_BLUE','LIGHTSABER','PLANK','JETPACK') DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name` text NOT NULL,
  `validated` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `level`
--

INSERT INTO `level` (`id`, `creator_id`, `difficulty`, `objects`, `timestamp`, `name`, `validated`) VALUES
('666b1660bed12398539845', '6645c6093c644139042401', 1, 'JETPACK', '2024-06-13 16:11:49', 'New level', 0),
('666b2cbeccafc790321447', '6645c6093c644139042401', 1, 'LIGHTSABER', '2024-06-13 23:23:40', 'New level', 0),
('presentation', '6645c6093c644139042401', 0, 'KEY_RED,KEY_BLUE,LIGHTSABER', '2024-06-13 23:28:52', 'Présentation du jeu', 1);

-- --------------------------------------------------------

--
-- Structure de la table `player`
--

CREATE TABLE `player` (
  `id` char(22) NOT NULL,
  `tag` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` longtext NOT NULL,
  `pfp_extension` varchar(4) NOT NULL,
  `last_lvl` int(11) NOT NULL DEFAULT 0,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  `tuto_finished` tinyint(1) NOT NULL DEFAULT 0,
  `achievements` set('0','1','2','3','4','5','6','7','8','9','10') DEFAULT NULL,
  `banned` tinyint(1) DEFAULT 0,
  `current_inf` int(11) DEFAULT 0,
  `max_inf` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `player`
--

INSERT INTO `player` (`id`, `tag`, `email`, `password`, `pfp_extension`, `last_lvl`, `admin`, `tuto_finished`, `achievements`, `banned`, `current_inf`, `max_inf`) VALUES
('6645c6093c644139042401', 'veli', 'admin@junia.com', '$argon2i$v=19$m=65536,t=4,p=1$bDVvRmlCV2k2SERvazhZYQ$UhNw++KSQEB2SMLlF2VJmTAIR5sufoKkVvXbA1dfAck', 'png', 1, 1, 1, '6', 0, 0, 2),
('66475dfdd7de2101479375', 'SuperGamer59', 'test@junia.com', '$argon2i$v=19$m=65536,t=4,p=1$cmY2QTB0eTdKbGQ1U0tIWg$QwkEgZIC38BG6lW84jl/zVWwnAhk00lTq8K5SgI+eYs', 'png', 1, 0, 0, NULL, 0, 0, 0),
('666b8c1fddcfc387256589', 'test2', 'test2@junia.com', '$argon2i$v=19$m=65536,t=4,p=1$NFZzSEhsSzhOTFEuajE3eg$e+jEf4tw5or6NP1TGG+7M71cyQPG6xZVJXN1laiArak', 'png', 0, 0, 0, NULL, 0, 0, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `achievement`
--
ALTER TABLE `achievement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`(768));

--
-- Index pour la table `adventure`
--
ALTER TABLE `adventure`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `adventure_leaderboard`
--
ALTER TABLE `adventure_leaderboard`
  ADD KEY `level_id` (`level_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Index pour la table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD KEY `level_id_FK` (`level_id`),
  ADD KEY `player_id_FK` (`player_id`);

--
-- Index pour la table `level`
--
ALTER TABLE `level`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_id` (`creator_id`);

--
-- Index pour la table `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tag` (`tag`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `adventure_leaderboard`
--
ALTER TABLE `adventure_leaderboard`
  ADD CONSTRAINT `adventure_leaderboard_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `adventure` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adventure_leaderboard_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD CONSTRAINT `level_id_FK` FOREIGN KEY (`level_id`) REFERENCES `level` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `player_id_FK` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `level`
--
ALTER TABLE `level`
  ADD CONSTRAINT `creator_FK` FOREIGN KEY (`creator_id`) REFERENCES `player` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
