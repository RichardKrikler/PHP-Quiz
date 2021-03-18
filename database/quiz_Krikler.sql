-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 19. Feb 2021 um 17:37
-- Server-Version: 10.4.14-MariaDB
-- PHP-Version: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `quiz`
--

-- --------------------------------------------------------

# falls die DB schon vorhanden ist --> löschen
DROP DATABASE IF EXISTS quiz;

# Datenbank erstellen:
CREATE DATABASE quiz CHARACTER SET utf8 COLLATE utf8_general_ci;
# Diese Datenbank verwenden
USE quiz;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `answer`
--

CREATE TABLE `answer` (
  `pk_answer_id` int(11) NOT NULL,
  `fk_pk_question_id` int(11) DEFAULT NULL,
  `is_true` tinyint(1) DEFAULT NULL,
  `answer_text` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `answer`
--

INSERT INTO `answer` (`pk_answer_id`, `fk_pk_question_id`, `is_true`, `answer_text`) VALUES
(1, 1, 1, 'Der Projektmanagementprozess hat ein Anfangs- und ein Endereignis.'),
(2, 1, 0, 'Der Projektmanagementprozess stellt einen Teilprozess der Projektkoordination dar.'),
(3, 1, 0, 'Der Projektmanagementprozess stellt einen Teilprozess der Projektadministration dar.'),
(4, 1, 1, 'Der Projektmanagementprozess stellt einen Geschäftsprozess des Projektorientierten Unternehmens dar.'),
(5, 2, 1, 'Ein Projekt kostet Geld und Ressourcen.'),
(6, 2, 1, 'Ein Projekt setzt einen Projektauftrag voraus.'),
(7, 2, 0, 'Ein Projekt dauert nie länger als ein Jahr.'),
(8, 2, 1, 'Ein Projekt ist zieldeterminiert.'),
(9, 3, 0, 'Festlegen des äußeren Auftretens des Teams (Logo, Form der Dokumente etc.)'),
(10, 3, 0, 'Festlegen des Produktdesigns / Produktformen'),
(11, 3, 1, 'Vorsichtiges gegenseitiges Kennenlernen und Einschätzen'),
(12, 3, 0, 'Klare Festlegung der inneren Strukturen und Formen eines Teams '),
(13, 4, 0, 'Regelmäßige Prüfung der internen Spielregeln eines Teams'),
(14, 4, 0, 'Projektergebnisse über Patente, Standards und Normen zu schützen'),
(15, 4, 1, 'Festlegen der internen Spielregeln eines Teams'),
(16, 4, 0, 'Prüfung welche Normen und Standards für das Projekt wichtig sind'),
(17, 5, 1, 'RIP hat ein langsameres Konvergenzverhalten als OSPF.'),
(18, 5, 0, 'In OSPF kann keine Authentifizierung verwendet werden.'),
(19, 5, 0, 'Hello Nachrichten werden nicht regelmäßig gesendet.'),
(20, 5, 1, 'OSPF ist gut skalierbar.'),
(21, 5, 1, 'Hello Nachrichten werden regelmäßig gesendet.'),
(22, 6, 0, 'IPv4'),
(23, 6, 0, 'classfull Netze'),
(24, 6, 1, 'IPv6'),
(25, 6, 0, 'classless Netze'),
(26, 7, 0, 'Sie werden im Interface Configuration Mode konfiguriert.'),
(27, 7, 1, 'Sie filtern nur auf Basis von Quell-IPv4 Adressen.'),
(28, 7, 1, 'Sie können nur Nummerierung und nicht mit einem Namen erstellt werden.'),
(29, 7, 0, 'Sie filtern auf Basis von Quell-Adressen und Quell-Ports'),
(30, 8, 0, 'Access List Nummer zwischen 1 und 99'),
(31, 8, 1, 'Access List Nummer zwischen 100 und 199'),
(32, 8, 0, 'Default Gateway Address und Wildcard Mask'),
(33, 8, 1, 'Destination Address und Wildcard Mask'),
(34, 8, 1, 'Source Address und Wildcard Mask'),
(35, 9, 1, 'Umsetzung'),
(36, 9, 0, 'Vorprojekt'),
(37, 9, 0, 'Planung'),
(38, 9, 0, 'Nachprojekt'),
(39, 10, 0, 'Umsetzung'),
(40, 10, 0, 'Vorprojekt'),
(41, 10, 0, 'Planung'),
(42, 10, 1, 'Nachprojekt'),
(43, 11, 1, 'Wahr'),
(44, 11, 0, 'Falsch'),
(45, 12, 1, 'Fachliches Know-How'),
(46, 12, 0, 'Kenntnisse der Betriebsstruktur'),
(47, 12, 1, 'Berufserfahrung'),
(48, 12, 1, 'Experten für verschiedene fachliche Problemstellungen'),
(49, 12, 0, 'Fähigkeit zum Führen und Folgen');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `category`
--

CREATE TABLE `category` (
  `pk_category_id` int(11) NOT NULL,
  `category_text` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `category`
--

INSERT INTO `category` (`pk_category_id`, `category_text`) VALUES
(1, 'ITP'),
(2, 'NWT');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `question`
--

CREATE TABLE `question` (
  `pk_question_id` int(11) NOT NULL,
  `fk_pk_subcategory_id` int(11) DEFAULT NULL,
  `question_text` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `question`
--

INSERT INTO `question` (`pk_question_id`, `fk_pk_subcategory_id`, `question_text`) VALUES
(1, 1, 'Welche der folgenden Aussagen passt zum Projektmanagementprozess?'),
(2, 1, 'Welche der folgenden Eigenschaften passen zum Projekt?'),
(3, 2, 'Wie verhalten sich Teammitglieder in der Phase \"Forming\"?'),
(4, 2, 'Was passiert in der Teamphase \"Norming\"'),
(5, 3, 'Folgende Aussagen zu OSPF treffen zu...'),
(6, 3, 'OSPFv3 wurde hauptsächlich entwickelt für ...'),
(7, 4, 'Welche Aussage beschreibt eine Charakteristik von Standard IPv4 ACLs?'),
(8, 4, 'Welche 3 Parameter können in einer Extended Access Control List vorkommen?'),
(9, 1, 'Ordnen sie die Aktivität \"Technische Planung\" zur richtigen Phase zu.'),
(10, 1, 'Ordnen sie die Aktivität \"Lessonslearned\" zur richtigen Phase zu.'),
(11, 1, 'Ein Projekt hat immer einen definierten Zeitraum, auch dann, wenn ein Fertigstellungstermin aufgrund verschiedenster Ursachen sehr schwierig festzulegen ist.'),
(12, 2, 'Zusammensetzung & Auswahl der Projektmitglieder: Welche der folgenden Eigenschaften passen zur Fachkompetenz?');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `subcategory`
--

CREATE TABLE `subcategory` (
  `pk_subcategory_id` int(11) NOT NULL,
  `fk_pk_category_id` int(11) DEFAULT NULL,
  `subcategory_text` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `subcategory`
--

INSERT INTO `subcategory` (`pk_subcategory_id`, `fk_pk_category_id`, `subcategory_text`) VALUES
(1, 1, 'Projekt / Projekteigenschaften'),
(2, 1, 'Team / Teameigenschaften'),
(3, 2, 'OSPF'),
(4, 2, 'ACLs');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `answer`
--
ALTER TABLE `answer`
  ADD PRIMARY KEY (`pk_answer_id`),
  ADD KEY `fk_pk_question_id` (`fk_pk_question_id`);

--
-- Indizes für die Tabelle `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`pk_category_id`);

--
-- Indizes für die Tabelle `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`pk_question_id`),
  ADD KEY `fk_pk_subcategory_id` (`fk_pk_subcategory_id`);

--
-- Indizes für die Tabelle `subcategory`
--
ALTER TABLE `subcategory`
  ADD PRIMARY KEY (`pk_subcategory_id`),
  ADD KEY `fk_pk_category_id` (`fk_pk_category_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `answer`
--
ALTER TABLE `answer`
  MODIFY `pk_answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT für Tabelle `category`
--
ALTER TABLE `category`
  MODIFY `pk_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `question`
--
ALTER TABLE `question`
  MODIFY `pk_question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `subcategory`
--
ALTER TABLE `subcategory`
  MODIFY `pk_subcategory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `answer`
--
ALTER TABLE `answer`
  ADD CONSTRAINT `answer_ibfk_1` FOREIGN KEY (`fk_pk_question_id`) REFERENCES `question` (`pk_question_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`fk_pk_subcategory_id`) REFERENCES `subcategory` (`pk_subcategory_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `subcategory`
--
ALTER TABLE `subcategory`
  ADD CONSTRAINT `subcategory_ibfk_1` FOREIGN KEY (`fk_pk_category_id`) REFERENCES `category` (`pk_category_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
