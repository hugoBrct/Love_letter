-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2017 at 04:01 PM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 7.1.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `symfony`
--

-- --------------------------------------------------------

--
-- Table structure for table `cartes`
--

CREATE TABLE `cartes` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `img` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `point` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `cartes`
--

INSERT INTO `cartes` (`id`, `nom`, `description`, `img`, `point`) VALUES
(1, 'garde', 'Choisissez un joueur et essayez de\r\ndeviner la carte qu\'il a en main (excepté le Guard),\r\nsi vous tombez juste, le joueur est éliminé de la\r\nmanche.', 'garde.png', 1),
(2, 'garde', 'Choisissez un joueur et essayez de\r\ndeviner la carte qu\'il a en main (excepté le Guard),\r\nsi vous tombez juste, le joueur est éliminé de la\r\nmanche', 'garde.png', 1),
(3, 'garde', 'Choisissez un joueur et essayez de\r\ndeviner la carte qu\'il a en main (excepté le Guard),\r\nsi vous tombez juste, le joueur est éliminé de la\r\nmanche', 'garde.png', 1),
(4, 'garde', 'Choisissez un joueur et essayez de\r\ndeviner la carte qu\'il a en main (excepté le Guard),\r\nsi vous tombez juste, le joueur est éliminé de la\r\nmanche', 'garde.png', 1),
(5, 'garde', 'Choisissez un joueur et essayez de\r\ndeviner la carte qu\'il a en main (excepté le Guard),\r\nsi vous tombez juste, le joueur est éliminé de la\r\nmanche', 'garde.png', 1),
(6, 'pretre', 'Regardez la main d\'un autre joueur.', 'pretre.png', 2),
(7, 'pretre', 'Regardez la main d\'un autre joueur.', 'pretre.png', 2),
(8, 'baron', ' Comparez votre carte avec celle d\'un\r\nautre joueur, celui qui a la carte avec la plus faible\r\nvaleur est éliminé de la manche', 'baron.png', 3),
(9, 'baron', ' Comparez votre carte avec celle d\'un\r\nautre joueur, celui qui a la carte avec la plus faible\r\nvaleur est éliminé de la manche', 'baron.png', 3),
(10, 'servante', 'Jusqu\'au prochain tour, vous êtes\r\nprotégé des effets des cartes des autres joueurs', 'servante.png', 4),
(11, 'servante', 'Jusqu\'au prochain tour, vous êtes\r\nprotégé des effets des cartes des autres joueurs', 'servante.png', 4),
(12, 'prince', 'choisissez un joueur (y compris\r\nvous), celui-ci défausse la carte qu\'il a en main pour\r\nen piocher une nouvelle.', 'prince.png', 5),
(13, 'prince', 'choisissez un joueur (y compris\r\nvous), celui-ci défausse la carte qu\'il a en main pour\r\nen piocher une nouvelle.', 'prince.png', 5),
(14, 'roi', 'échangez votre main avec un autre\r\njoueur de votre choix.', 'roi.png', 6),
(15, 'comtesse', 'Si vous avez cette carte en main en\r\nmême temps que le King ou le Prince, alors vous\r\ndevez défausser la carte de la Countess', 'comtesse.png', 7),
(16, 'princesse', 'si vous défaussez cette carte, vous\r\nêtes éliminé de la manche', 'princesse.png', 8);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cartes`
--
ALTER TABLE `cartes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cartes`
--
ALTER TABLE `cartes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
