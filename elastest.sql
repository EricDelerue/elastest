-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Set 24, 2018 alle 01:16
-- Versione del server: 10.1.34-MariaDB
-- Versione PHP: 7.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elastest`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='authors table';

--
-- Dump dei dati per la tabella `authors`
--

INSERT INTO `authors` (`id`, `first_name`, `last_name`) VALUES
(1, 'Ingeborg', 'Pos'),
(2, 'Patrick', 'Welling'),
(3, 'Eric', 'Delerue');

-- --------------------------------------------------------

--
-- Struttura della tabella `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `cover_url` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `isbn` int(13) NOT NULL,
  `author_id` int(11) NOT NULL,
  `publisher_id` int(11) NOT NULL,
  `highlighted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='books table';

--
-- Dump dei dati per la tabella `books`
--

INSERT INTO `books` (`id`, `title`, `description`, `cover_url`, `isbn`, `author_id`, `publisher_id`, `highlighted`) VALUES
(1, 'Dead or Scrum', 'Dead or Scrum', '', 123456789, 1, 1, -1),
(2, 'Metallica applied to PHP', 'Metallica applied to PHP', '', 123456789, 2, 1, -1),
(3, 'Once upon a time in the south', 'Once upon a time in the south', '', 123456789, 3, 2, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `publishers`
--

CREATE TABLE `publishers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Publishers table';

--
-- Dump dei dati per la tabella `publishers`
--

INSERT INTO `publishers` (`id`, `name`) VALUES
(1, 'Elastique'),
(2, 'Ink salad');

-- --------------------------------------------------------

--
-- Struttura della tabella `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `application_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `application_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `application_secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `application_origin` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `application_is_active` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Applications / Clients Public / Secret keys table';

--
-- Dump dei dati per la tabella `applications`
--

INSERT INTO `applications` (`application_id`, `application_name`, `application_key`, `application_secret`, `application_origin`, `application_is_active`) VALUES
(1, 'elastique', '64b62cf8af12ef490b37323027220cfbe7825f7f86bac7470afb131c5af22819', 'da254b2fa2eb38ce155b326792e5bc6df750110d8dcc389a007a58404af0c372', '127.0.0.1', -1);

-- --------------------------------------------------------


--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `publishers`
--
ALTER TABLE `publishers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

--
-- AUTO_INCREMENT per la tabella `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
