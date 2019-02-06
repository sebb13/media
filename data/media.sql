-- phpMyAdmin SQL Dump

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `tilala`
--

-- --------------------------------------------------------

--
-- Structure de la table `tj_elmt_type_availables`
--

CREATE TABLE `tj_elmt_type_availables` (
  `elmt_type_availables_id` int(11) NOT NULL,
  `media_type_id` mediumint(9) NOT NULL,
  `media_elmt_type_id` mediumint(9) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `tj_elmt_type_availables`
--

INSERT INTO `tj_elmt_type_availables` (`elmt_type_availables_id`, `media_type_id`, `media_elmt_type_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 6),
(4, 1, 7),
(5, 2, 1),
(33, 1, 11),
(7, 2, 3),
(8, 2, 4),
(9, 3, 1),
(10, 3, 2),
(11, 3, 4),
(43, 2, 14),
(13, 3, 8),
(14, 3, 9),
(15, 4, 1),
(16, 4, 2),
(17, 4, 6),
(18, 4, 10),
(19, 5, 1),
(20, 5, 2),
(21, 5, 5),
(42, 1, 14),
(23, 5, 10),
(24, 6, 1),
(25, 6, 2),
(26, 6, 6),
(27, 6, 10),
(28, 7, 1),
(29, 7, 2),
(30, 7, 6),
(31, 7, 10),
(32, 1, 10),
(34, 2, 11),
(35, 3, 11),
(36, 4, 11),
(37, 5, 11),
(38, 6, 11),
(39, 7, 11),
(40, 2, 12),
(41, 3, 13),
(44, 3, 14),
(45, 4, 14),
(46, 5, 14),
(47, 6, 14),
(48, 7, 14);

-- --------------------------------------------------------

--
-- Structure de la table `tj_medias_types_extensions`
--

CREATE TABLE `tj_medias_types_extensions` (
  `medias_types_extensions_id` int(11) NOT NULL,
  `media_type_id` smallint(6) NOT NULL,
  `media_extension_id` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `tj_medias_types_extensions`
--

INSERT INTO `tj_medias_types_extensions` (`medias_types_extensions_id`, `media_type_id`, `media_extension_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 2, 5),
(6, 2, 6),
(7, 2, 7),
(8, 3, 6),
(9, 3, 8),
(10, 3, 9),
(11, 4, 5),
(12, 6, 10),
(13, 7, 11),
(14, 7, 12),
(15, 7, 13),
(16, 7, 14),
(17, 7, 15),
(18, 9, 1),
(19, 9, 2),
(20, 9, 3),
(21, 9, 4),
(22, 7, 16);

-- --------------------------------------------------------

--
-- Structure de la table `tr_medias_extensions`
--

CREATE TABLE `tr_medias_extensions` (
  `media_extension_id` mediumint(9) NOT NULL,
  `media_extension` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `tr_medias_extensions`
--

INSERT INTO `tr_medias_extensions` (`media_extension_id`, `media_extension`) VALUES
(1, 'jpg'),
(2, 'jpeg'),
(3, 'png'),
(4, 'gif'),
(5, 'mp3'),
(6, 'ogg'),
(7, 'wav'),
(8, 'mp4'),
(9, 'webm'),
(10, 'pdf'),
(11, 'zip'),
(12, 'rar'),
(13, 'tar.gz'),
(14, 'tgz'),
(15, 'tar'),
(16, 'gz');

-- --------------------------------------------------------

--
-- Structure de la table `tr_media_elmt_data_types`
--

CREATE TABLE `tr_media_elmt_data_types` (
  `media_elmt_data_type_id` int(11) NOT NULL,
  `media_elmt_data_type` varchar(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `tr_media_elmt_data_types`
--

INSERT INTO `tr_media_elmt_data_types` (`media_elmt_data_type_id`, `media_elmt_data_type`) VALUES
(1, 'text'),
(2, 'file'),
(3, 'locale_text'),
(4, 'system'),
(5, 'textarea');

-- --------------------------------------------------------

--
-- Structure de la table `tr_media_elmt_types`
--

CREATE TABLE `tr_media_elmt_types` (
  `media_elmt_type_id` smallint(6) NOT NULL,
  `media_elmt_type_name` varchar(64) NOT NULL,
  `media_elmt_data_type_id` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `tr_media_elmt_types`
--

INSERT INTO `tr_media_elmt_types` (`media_elmt_type_id`, `media_elmt_type_name`, `media_elmt_data_type_id`) VALUES
(1, 'title', 3),
(2, 'alt', 3),
(3, 'mp3', 2),
(4, 'ogg', 2),
(5, 'iframe', 5),
(6, 'src', 2),
(7, 'thumb', 4),
(8, 'mp4', 2),
(9, 'poster', 2),
(10, 'url', 1),
(11, 'media_description', 3),
(12, 'wav', 2),
(13, 'webm', 2),
(14, 'page', 4);

-- --------------------------------------------------------

--
-- Structure de la table `tr_media_types`
--

CREATE TABLE `tr_media_types` (
  `media_type_id` smallint(11) NOT NULL,
  `media_type_name` varchar(16) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `tr_media_types`
--

INSERT INTO `tr_media_types` (`media_type_id`, `media_type_name`) VALUES
(1, 'img'),
(2, 'audio'),
(3, 'video'),
(4, 'flux'),
(5, 'iframe'),
(6, 'pdf'),
(7, 'zip');

-- --------------------------------------------------------

--
-- Structure de la table `t_medias`
--

CREATE TABLE `t_medias` (
  `media_id` int(11) NOT NULL,
  `media_type_id` smallint(10) NOT NULL,
  `media_tags` varchar(256) NOT NULL,
  `media_download_allowed` tinyint(1) NOT NULL DEFAULT '0',
  `media_date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `media_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Structure de la table `t_media_elmts`
--

CREATE TABLE `t_media_elmts` (
  `media_elmts_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `media_elmt_type_id` smallint(6) NOT NULL,
  `media_elmt_add_data` varchar(3) CHARACTER SET latin1 NOT NULL,
  `media_elmt` varchar(260) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Index pour la table `tj_elmt_type_availables`
--
ALTER TABLE `tj_elmt_type_availables`
  ADD PRIMARY KEY (`elmt_type_availables_id`);

--
-- Index pour la table `tj_medias_types_extensions`
--
ALTER TABLE `tj_medias_types_extensions`
  ADD PRIMARY KEY (`medias_types_extensions_id`);

--
-- Index pour la table `tr_medias_extensions`
--
ALTER TABLE `tr_medias_extensions`
  ADD PRIMARY KEY (`media_extension_id`);

--
-- Index pour la table `tr_media_elmt_data_types`
--
ALTER TABLE `tr_media_elmt_data_types`
  ADD PRIMARY KEY (`media_elmt_data_type_id`);

--
-- Index pour la table `tr_media_elmt_types`
--
ALTER TABLE `tr_media_elmt_types`
  ADD PRIMARY KEY (`media_elmt_type_id`);

--
-- Index pour la table `tr_media_types`
--
ALTER TABLE `tr_media_types`
  ADD PRIMARY KEY (`media_type_id`);

--
-- Index pour la table `t_medias`
--
ALTER TABLE `t_medias`
  ADD PRIMARY KEY (`media_id`);

--
-- Index pour la table `t_media_elmts`
--
ALTER TABLE `t_media_elmts`
  ADD PRIMARY KEY (`media_elmts_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `tj_elmt_type_availables`
--
ALTER TABLE `tj_elmt_type_availables`
  MODIFY `elmt_type_availables_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT pour la table `tj_medias_types_extensions`
--
ALTER TABLE `tj_medias_types_extensions`
  MODIFY `medias_types_extensions_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT pour la table `tr_medias_extensions`
--
ALTER TABLE `tr_medias_extensions`
  MODIFY `media_extension_id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT pour la table `tr_media_elmt_data_types`
--
ALTER TABLE `tr_media_elmt_data_types`
  MODIFY `media_elmt_data_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `tr_media_elmt_types`
--
ALTER TABLE `tr_media_elmt_types`
  MODIFY `media_elmt_type_id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT pour la table `tr_media_types`
--
ALTER TABLE `tr_media_types`
  MODIFY `media_type_id` smallint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `t_medias`
--
ALTER TABLE `t_medias`
  MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT pour la table `t_media_elmts`
--
ALTER TABLE `t_media_elmts`
  MODIFY `media_elmts_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
