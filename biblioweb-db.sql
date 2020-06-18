-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 18 Juin 2020 à 18:15
-- Version du serveur :  5.7.11
-- Version de PHP :  7.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `biblioweb-db`
--

-- --------------------------------------------------------

--
-- Structure de la table `authors`
--

CREATE TABLE `authors` (
  `id` int(10) UNSIGNED NOT NULL,
  `lastname` varchar(30) NOT NULL DEFAULT '',
  `firstname` varchar(30) NOT NULL DEFAULT '',
  `nationality` varchar(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `authors`
--

INSERT INTO `authors` (`id`, `lastname`, `firstname`, `nationality`) VALUES
(1, 'Simenon', 'Georges', 'Belgique'),
(2, 'Zola', 'Émile', 'France'),
(3, 'Clark', 'Marie-Higgins', 'Grande-Bretagne'),
(4, 'Dick', 'Philip K.', 'États-Unis'),
(5, 'Balzac', 'Honoré de', 'France'),
(7, 'Maupassant', 'Guy', 'France');

-- --------------------------------------------------------

--
-- Structure de la table `books`
--

CREATE TABLE `books` (
  `ref` int(10) UNSIGNED NOT NULL,
  `title` varchar(50) NOT NULL DEFAULT '',
  `author_id` int(10) UNSIGNED NOT NULL,
  `description` text,
  `cover_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `books`
--

INSERT INTO `books` (`ref`, `title`, `author_id`, `description`, `cover_url`) VALUES
(1, 'Ubik', 4, '\\\' OR true -- Un <strong>excellent </strong> thriller de science-fiction!alert(\'attack!!\')', NULL),
(2, 'Une vie', 7, NULL, NULL),
(3, 'Germinal', 2, NULL, NULL),
(4, 'Maigret et le voleur paresseux', 1, NULL, NULL),
(5, 'Un crime en Hollande', 1, NULL, NULL),
(6, 'Le Chien Jaune', 1, NULL, NULL),
(7, 'Maigret à la mer', 1, NULL, NULL),
(8, 'La Danseuse du Gai-Moulin', 1, NULL, NULL),
(9, 'Le Chat', 1, NULL, NULL),
(10, 'Blade Runner', 4, NULL, NULL),
(11, 'Minority Report', 4, NULL, NULL),
(12, 'Paycheck', 4, NULL, NULL),
(13, 'En attendant l\'année dernière', 4, NULL, NULL),
(14, 'Le Crâne', 4, NULL, NULL),
(15, 'LeHorla', 7, NULL, NULL),
(16, 'Boule de Suif', 7, NULL, NULL),
(17, 'Bel Ami', 7, NULL, NULL),
(18, 'L\'Assommoir', 2, NULL, NULL),
(19, 'La Bête humaine', 2, NULL, NULL),
(20, 'Nana', 2, NULL, NULL),
(21, 'Thérèse Raquin', 2, NULL, NULL),
(22, 'Germinal', 2, NULL, NULL),
(23, 'La Croisière de Noël', 3, NULL, NULL),
(24, 'Cette chanson que je n\'oublierai jamais', 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `loans`
--

CREATE TABLE `loans` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `book_id` int(10) UNSIGNED NOT NULL,
  `return_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `loans`
--

INSERT INTO `loans` (`id`, `user_id`, `book_id`, `return_date`) VALUES
(53, 33, 18, '2020-06-12'),
(57, 33, 8, '2020-06-12'),
(65, 51, 15, '2020-06-15'),
(66, 52, 2, '2020-06-15'),
(106, 34, 5, '2020-06-18'),
(118, 37, 12, '2020-06-18'),
(119, 37, 13, '2020-06-18'),
(124, 34, 3, '2020-07-02');

-- --------------------------------------------------------

--
-- Structure de la table `status`
--

CREATE TABLE `status` (
  `status` varchar(60) NOT NULL,
  `min_loans` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `status`
--

INSERT INTO `status` (`status`, `min_loans`) VALUES
('admin', 0),
('expert', 2),
('habitué', 1),
('novice', 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `login` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL,
  `status` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(100) DEFAULT NULL,
  `nb_loans` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Nombre total des emprunts',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `login`, `email`, `status`, `password`, `nb_loans`, `created_at`, `image`, `updated_at`) VALUES
(23, 'jim', 'jim@sull.com', 'admin', '$2y$10$bITcyKAea.UpRMVwx1zfSOZ4TLdI6Y4dbva/sH4b2WD2Zb58s5klC', 0, '2020-04-24 13:39:03', NULL, NULL),
(28, 'mike', 'mike@sull.com', 'novice', '$2y$10$.12N3QsO8LjofYBxPBgcUe0AUUtHuMSJOo1m5kMzqDyb.yqnPaOpS', 0, '2020-04-24 16:51:18', NULL, '2020-05-29 14:23:42'),
(33, 'clarck', 'cl@sull.com', 'expert', '$2y$10$bITcyKAea.UpRMVwx1zfSOZ4TLdI6Y4dbva/sH4b2WD2Zb58s5klC', 7, '2020-05-15 15:43:26', 'Test', NULL),
(34, 'ced', 'ceruth@epfc.eu', 'admin', '$2y$10$bITcyKAea.UpRMVwx1zfSOZ4TLdI6Y4dbva/sH4b2WD2Zb58s5klC', 7, '2020-05-29 13:40:21', 'upload/1591239676AdobeStock_31686680.jpeg', NULL),
(37, 'root', 'cv@hotmail.com', 'expert', '$2y$10$1mAmSbiDdAVdq8jhsRVZm.XOH.gJogzG.UoCD4CN8OEsWXCAqwh/6', 88, '2020-06-01 17:59:14', 'upload/1591292963singe.jpg', NULL),
(39, 'lucien', 'lucien@hotmail.com', 'novice', '$2y$10$83UTUboAqA4bMnItTNd1huvJthUbSIfqw3AKo69WJKRb0LZU7c/Qi', 0, '2020-06-01 22:14:32', NULL, NULL),
(40, 'rob', 'rob@ffp.com', 'novice', '$2y$10$.s/4JH6icQDr8hgl9S2ZnOMl9S8V2eeXkxjralI/Ee904S5jT.Twm', 0, '2020-06-01 22:23:06', NULL, NULL),
(41, 'sylvia', 'sylvia2@hotmail.com', 'novice', '$2y$10$WEzFayLY5R3960HkS0pDbOuLdmO43vOdFxiqT5Ct/GrOst4fMBeza', 0, '2020-06-01 22:28:02', NULL, NULL),
(44, 'roxane', 'roxane@h.com', 'novice', '$2y$10$wQwJivNL9MS1ew3CaMQWueOhcbepwRZXDeU7zfXhwm340.vui25e6', 0, '2020-06-01 22:33:29', NULL, NULL),
(45, 'albert', 'albert@g.com', 'novice', '$2y$10$3VWrTB/w9XIiV1HCfVRxwurw..ly7.FfOETY2UC/PRRtIFgx6d4OW', 0, '2020-06-01 22:34:15', NULL, NULL),
(46, 'paul', 'paul@ht.com', 'novice', '$2y$10$KD6wCznmRvij.efhwXXab.OCZRATcRFFMlIlU32x8iNHNq8EvRv4y', 0, '2020-06-01 22:35:06', NULL, NULL),
(49, 'dylan', 'dylan@gmail.com', 'expert', '$2y$10$HL8eC7duAtZYuc//lRJb4OXCRw495Gt.7eq8FL36QGz3oGUTtC3IG', 3, '2020-06-01 22:58:14', NULL, NULL),
(53, 'loana', 'lolo@trr.com', 'expert', '$2y$10$/r8wqNXX6q9ImdEqam.RQO7fvCLuZNUZnuPwaph2AhpAW7NZbn4Hu', 3, '2020-06-01 23:58:07', NULL, NULL),
(55, 'sarah', 'sarah@g.com', 'novice', '$2y$10$Cnq0bKpxqwYBZZU19XXqFu0E3zdO6xTmiAoZHAavVbEyv4BRj1n/S', 0, '2020-06-04 04:54:06', NULL, NULL),
(57, 'filip', 'filip@gg.com', 'expert', '$2y$10$d0MWhxXAR25gngo8oEAuI.SDbABZfGxYO0QF4yMuvjoFCWQeVZsTa', 6, '2020-06-04 18:58:09', 'upload/1591289958kangourou.jpg', NULL),
(58, 'rooter', 'rooter@rooter.com', 'expert', '$2y$10$ag7k7IlJc1eVui9bOpVzCu2WvYYzZ0POC/No5S9eSilKhg8Qo5SMm', 4, '2020-06-18 19:34:12', NULL, NULL);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`ref`),
  ADD KEY `titre` (`title`),
  ADD KEY `auteur_id` (`author_id`);

--
-- Index pour la table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `nom` (`login`),
  ADD KEY `statut` (`status`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `books`
--
ALTER TABLE `books`
  MODIFY `ref` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT pour la table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status` (`status`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
