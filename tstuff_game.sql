-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 09. Feb 2019 um 10:02
-- Server-Version: 10.1.19-MariaDB
-- PHP-Version: 5.6.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `tstuff_game`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bag`
--

CREATE TABLE `bag` (
  `id` int(11) UNSIGNED NOT NULL,
  `owner_type` int(11) UNSIGNED DEFAULT NULL,
  `owner_id` int(11) UNSIGNED DEFAULT NULL,
  `slots` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `bag`
--

INSERT INTO `bag` (`id`, `owner_type`, `owner_id`, `slots`) VALUES
(7, 3, 1, 25);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bagitems`
--

CREATE TABLE `bagitems` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED DEFAULT NULL,
  `amount` int(11) UNSIGNED DEFAULT NULL,
  `bag_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `charakter`
--

CREATE TABLE `charakter` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `money` double DEFAULT NULL,
  `honor` int(11) UNSIGNED DEFAULT NULL,
  `player_id` int(11) UNSIGNED DEFAULT NULL,
  `hero_class` int(11) UNSIGNED DEFAULT NULL,
  `current_mana` double DEFAULT NULL,
  `current_hp` double DEFAULT NULL,
  `level` int(11) UNSIGNED DEFAULT NULL,
  `xp` double DEFAULT NULL,
  `mapid` int(11) UNSIGNED DEFAULT NULL,
  `maptype` int(11) UNSIGNED DEFAULT NULL,
  `mappos` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attacklist` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `charequip`
--

CREATE TABLE `charequip` (
  `id` int(11) UNSIGNED NOT NULL,
  `head` int(11) UNSIGNED DEFAULT NULL,
  `breast` int(11) UNSIGNED DEFAULT NULL,
  `shoulder` int(11) UNSIGNED DEFAULT NULL,
  `legs` int(11) UNSIGNED DEFAULT NULL,
  `feet` int(11) UNSIGNED DEFAULT NULL,
  `hand` int(11) UNSIGNED DEFAULT NULL,
  `wapon` int(11) UNSIGNED DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `charstats`
--

CREATE TABLE `charstats` (
  `id` int(11) UNSIGNED NOT NULL,
  `player_id` int(11) UNSIGNED DEFAULT NULL,
  `strength` int(11) UNSIGNED DEFAULT NULL,
  `vitality` int(11) UNSIGNED DEFAULT NULL,
  `speed` int(11) UNSIGNED DEFAULT NULL,
  `armour` int(11) UNSIGNED DEFAULT NULL,
  `avoid` int(11) UNSIGNED DEFAULT NULL,
  `crit` int(11) UNSIGNED DEFAULT NULL,
  `inteligent` int(11) UNSIGNED DEFAULT NULL,
  `wisdom` int(11) UNSIGNED DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat`
--

CREATE TABLE `chat` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `channel` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `msg` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `createtime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fight`
--

CREATE TABLE `fight` (
  `id` int(11) UNSIGNED NOT NULL,
  `moblist` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `playerlist` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currentactor` int(11) UNSIGNED DEFAULT NULL,
  `order` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isfinished` int(11) UNSIGNED DEFAULT NULL,
  `speedvalue` double DEFAULT NULL,
  `lastaction` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fightattacks`
--

CREATE TABLE `fightattacks` (
  `id` int(11) UNSIGNED NOT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `targettype` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int(11) UNSIGNED DEFAULT NULL,
  `class` int(11) UNSIGNED DEFAULT NULL,
  `type` int(11) UNSIGNED DEFAULT NULL,
  `dmgtype` int(11) UNSIGNED DEFAULT NULL,
  `attackvalue` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `colorcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manacost` int(11) UNSIGNED DEFAULT NULL,
  `rounds` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `fightattacks`
--

INSERT INTO `fightattacks` (`id`, `key`, `targettype`, `name`, `description`, `level`, `class`, `type`, `dmgtype`, `attackvalue`, `colorcode`, `manacost`, `rounds`) VALUES
(1, 'hart_slap', 0, 'Harter Schlag', 'Ein harter schlag mit deiner Waffe. Der 50% mehr Schaden verursacht', 1, 0, 1, 0, '50', '#EDD560', 10, 0),
(3, 'small_heal', 0, 'Kleine Heilung', 'Ein Zauber der das Ziel um 25 Punkte heilt', 1, 0, 3, 1, '25', '#EDD560', 25, 0),
(4, 'small_heal_aoe', 1, 'Kleine Heilung (AOE)', 'Ein Zauber der die Gruppe des ziels um 10 Punkte heilt', 1, 0, 4, 1, '10', '#EDD560', 55, 0),
(5, 'fire_ball', 0, 'Feuerball', 'Ein Zauber der dem Ziel 50 Schaden zufügt', 1, 0, 2, 1, '50', '#EDD560', 15, 0),
(6, 'fire_ball_aoe', 1, 'Feuerball (AOE)', 'Ein Zauber der dem Ziel 25 Schaden zufügt', 1, 0, 6, 1, '25', '#EDD560', 35, 0),
(7, 'round_house', 1, 'Roundhouse Kick', 'Ein Tritt der allen Zielen 20% des waffenschaden zufügt', 1, 0, 5, 0, '-20', '#EDD560', 25, 0),
(8, 'round_heal', 0, 'Heilende Berührung', 'Eine Heilung die nach jedem Zug, 25 Leben heilt für 5 Runden', 1, 0, 7, 1, '25', '#EDD560', 20, 5),
(9, 'mean_curse', 0, 'Böser Fluch', 'Ein Fluch der dem Ziel jede Runde 25 Schaden verursacht für 3 Runden. ', 1, 0, 8, 1, '25', '#EDD560', 25, 3),
(10, 'send_sleep', 0, 'Einschläfern', 'Schläfert das Ziel für 2 Runden ein. ', 1, 0, 9, 1, '0;0', '#EDD560', 30, 2),
(11, 'armor_up', 0, 'Magische Rüstung', 'Erhöht die Rüstung des Ziels um 50% für 2 Runden', 1, 0, 9, 1, '6;50', '#EDD560', 15, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fightlog`
--

CREATE TABLE `fightlog` (
  `id` int(11) UNSIGNED NOT NULL,
  `msg` text COLLATE utf8mb4_unicode_ci,
  `ctime` int(11) UNSIGNED DEFAULT NULL,
  `actor` int(11) UNSIGNED DEFAULT NULL,
  `target` int(11) UNSIGNED DEFAULT NULL,
  `fight_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fightmember`
--

CREATE TABLE `fightmember` (
  `id` int(11) UNSIGNED NOT NULL,
  `actor_id` int(11) UNSIGNED DEFAULT NULL,
  `isuser` tinyint(1) UNSIGNED DEFAULT NULL,
  `stats` text COLLATE utf8mb4_unicode_ci,
  `currhp` double DEFAULT NULL,
  `currmana` double DEFAULT NULL,
  `attacks` text COLLATE utf8mb4_unicode_ci,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fight_id` int(11) UNSIGNED DEFAULT NULL,
  `speedvalue` double DEFAULT NULL,
  `aggro` double DEFAULT NULL,
  `effects` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `flags`
--

CREATE TABLE `flags` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `flag_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flag_value` int(11) UNSIGNED DEFAULT NULL,
  `modifiedon` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `items`
--

CREATE TABLE `items` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rarity` int(11) UNSIGNED DEFAULT NULL,
  `stats` text COLLATE utf8mb4_unicode_ci,
  `type` int(11) UNSIGNED DEFAULT NULL,
  `stack_size` int(11) UNSIGNED DEFAULT NULL,
  `sell_price` int(11) UNSIGNED DEFAULT NULL,
  `imagekey` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `items`
--

INSERT INTO `items` (`id`, `name`, `description`, `rarity`, `stats`, `type`, `stack_size`, `sell_price`, `imagekey`, `action`) VALUES
(2, 'Taschentuch', 'Ein sauberes Taschentuch', 0, '{"none":""}', 1, 10, 1, 'trash1', NULL),
(4, 'Kapuze der Weitsicht', 'Eine Kapuze mit löchern für die Augen', 2, '{"speed":-2,"armour":3}', 0, 1, 4, 'head1', NULL),
(6, 'Einfache Schultern', 'Waren das mal Putzlappen', 1, '{"armour":2}', 0, 1, 5, 'shoulder1', NULL),
(7, 'Einfache Handschuhe', 'Mammas alte Stulpen', 1, '{"armour":2}', 0, 1, 5, 'hand1', NULL),
(8, 'Einfache Beine', 'Helden in Strumpfhosen', 1, '{"armour":2}', 0, 1, 5, 'leg1', NULL),
(9, 'Einfache Schuhe', 'Könnten auch Socken sein', 1, '{"armour":2}', 0, 1, 5, 'feet1', NULL),
(10, 'Holzschwert', 'Erinnerungen an die Kindheit', 1, '{"mindmg":1,"maxdmg":4}', 0, 1, 5, 'sword1', NULL),
(11, 'Goldene Haarspange', 'Eine Goldene Haarspange, welch ein schönes ding.', 3, '{"none":""}', 3, 1, 0, 'quest1', NULL),
(12, 'Ein Bolle Eis', 'Echt ein bolle eis', 5, '{"none":""}', 1, 6, 1, 'trash1', NULL),
(14, 'Eisenschwert', 'Eisen statt Holz', 2, '{\r\n"mindmg":3,  \r\n"maxdmg":5,\r\n"avoid":2\r\n}', 0, 1, 30, 'sword1', NULL),
(15, 'Holzknüppel', 'Für den einfachen Haudrauf', 1, '{\n          \n    "mindmg":1,  \n    "maxdmg":7\n}', 0, 1, 10, 'sword1', NULL),
(16, 'Streitkolben', 'Für den richtigen Haudrauf', 2, ' {\r\n\r\n        "mindmg":2,\r\n        "maxdmg":5,\r\n        "strength":2\r\n        \r\n        }\r\n', 0, 1, 30, 'sword1', NULL),
(17, 'Dolch', 'Ein spitzes Stück Metall', 2, ' {\n\n        "mindmg":1,\n        "maxdmg":3,\n        "speed":1,\n        "crit":1\n        }', 0, 1, 24, 'sword1', NULL),
(18, 'Holzstab', 'Als Gehhilfe oder Waffe', 2, '  {\r\n\r\n        "mindmg":2,\r\n        "maxdmg":7,\r\n        "vitality":2\r\n        }', 0, 1, 32, 'sword1', NULL),
(19, 'Lanze', 'Für einen wahren Ritter', 2, '   {\r\n        "mindmg":1,\r\n        "maxdmg":10,\r\n        "vitality":1,\r\n        "crit":1,\r\n        "strength":1\r\n        }', 0, 1, 40, 'sword1', NULL),
(21, 'Fackel', 'Monster fürchten das Feuer', 1, '   {\n        "mindmg":2,\n        "maxdmg":5\n        }', 0, 1, 8, 'sword1', NULL),
(92, 'Einfache Stoffweste', 'Dreckiger Lappen aus Stoff', 1, '{"armour":"5"}', 0, 1, 6, 'breast1', NULL),
(93, 'Einfache Stoffrobe', 'Ein Umfunktioniertes Bettlaken', 1, '{"armour":"5"}', 0, 1, 6, 'breast1', NULL),
(94, 'Einfach Stoffrüstung', 'Selbstgenähte Rüstung aus alten Lumpen', 2, '{"vitality":"1","armour":"5"}', 0, 1, 10, 'breast1', NULL),
(95, 'Lederweste des Knappen', 'Geklaute Weste eines kümmerlichen Knappens', 2, '{"vitality":"1","speed":"1","armour":"8"}', 0, 1, 11, 'breast1', NULL),
(96, 'Dicke Stoffrobe des Lehrlings', 'Gibt warm und schützt Euch ein wenig', 2, '{"vitality":"1","speed":"1","inteligent":"1","wisdom":"1","armour":"3"}', 0, 1, 13, 'breast1', NULL),
(97, 'Dicke Lederweste des Bären', 'Die Haut des Bären ist dick und robust', 2, '{"vitality":"1","inteligent":"1","wisdom":"1","armour":"10"}', 0, 1, 13, 'breast1', NULL),
(98, 'Gefiederte Robe des Greifen', 'Nicht nur schön sondern auch schützend', 2, '{"vitality":"2","crit":"1","armour":"6"}', 0, 1, 18, 'breast1', NULL),
(99, 'Kettenhemd des Ritters', 'Kettenhemden sind robust und dennoch beweglich', 2, '{"strength":"1","vitality":"3","crit":"1","armour":"14"}', 0, 1, 20, 'breast1', NULL),
(100, 'Verschuppte Weste des Drachen', 'Drachenschuppen sind härter als jeder Diamant', 2, '{"strength":"1","vitality":"2","speed":"1","avoid":"1","wisdom":"1","armour":"22"}', 0, 1, 28, 'breast1', NULL),
(101, 'Verschuppte Robe des Drachen', 'Drachenschuppen schützen Euren Rücken vor hinterlistigen Feinden', 2, '{"vitality":"3","avoid":"2","armour":"24"}', 0, 1, 29, 'breast1', NULL),
(102, 'Verschuppte Rüstung des Drachen', 'Kein Schwert und kein Pfeil kann in Eure Brust eindringen', 2, '{"strength":"2","vitality":"3","crit":"1","armour":"30"}', 0, 1, 70, 'breast1', NULL),
(103, 'Einfache Stoffhose', 'Sehr bequem, jedoch ohne Schutz vor Waffen und Kälte', 1, '{"armour":"4"}', 0, 1, 5, 'leg1', NULL),
(104, 'Lederhose des Knappen', 'Der Knappe hat diese Hosen geliebt', 2, '{"speed":"2","avoid":"1","wisdom":"1","armour":"6"}', 0, 1, 10, 'leg1', NULL),
(105, 'Kettenhose des Ritters', 'Von der Taille abwärts, seid ihr hiermit geschützt', 2, '{"strength":"1","speed":"1","avoid":"1","wisdom":"1","armour":"11"}', 0, 1, 30, 'leg1', NULL),
(106, 'Plattenrüstung des Kriegers', 'Klingen prallen an der glatten Oberfläsche öfters ab', 2, '{"strength":"1","avoid":"3","wisdom":"1","armour":"15"}', 0, 1, 33, 'leg1', NULL),
(107, 'Hose aus der Haut des Drachen', 'Sehr unbequem, jedoch behaltet Ihr Eure Beine und Genitalien. Wenn Sie euch denn wichtig sind', 2, '{"strength":"2","vitality":"1","crit":"1","armour":"16"}', 0, 1, 37, 'leg1', NULL),
(108, 'Einfache Stoffhandschuhe', 'Handschuhe, gefertigt aus alten Unterhosen', 1, '{"armour":"4"}', 0, 1, 5, 'hand1', NULL),
(109, 'Fingerlinge aus Leder des Knappen', 'Der Knappe hat bald keine Kleidung mehr', 1, '{"armour":"6"}', 0, 1, 5, 'hand1', NULL),
(110, 'Kettenhandschuhe des Ritters', 'Kettenglieder schützen Eure Hände vor scharfen Klingen', 2, '{"strength":"1","vitality":"1","armour":"7"}', 0, 1, 13, 'hand1', NULL),
(111, 'Plattenhandschuhe des Kriegers', 'Unbewegliche , aber sehr robuste Handschuhe', 2, '{"strength":"1","vitality":"1","armour":"7"}', 0, 1, 15, 'hand1', NULL),
(112, 'Pfotenhandschuhe des Drachen', 'Ihr schlupft mit den Händen in die Pfoten des Drachen und eure Finger bleiben heile', 2, '{"strength":"2","speed":"2","crit":"1","armour":"8"}', 0, 1, 22, 'hand1', NULL),
(113, 'Einfache Ledersandalen', 'Sandalen wie der Sohn Gottes sie trug. Nicht schön und nicht schützend. Es sei denn gegen Kieselsteine', 2, '{"speed":"2","avoid":"2","armour":"5"}', 0, 1, 23, 'feet1', NULL),
(114, 'Lederstiefel des Knappen', 'Der Knappe hat sie in seinem Urin getränkt.  Sie passen Euch wie angegossen', 1, '{"speed":"3","avoid":"2","armour":"6"}', 0, 1, 6, 'feet1', NULL),
(115, 'Dicke Lederstiefel', 'Durch dieses Leder kommen nur sehr scharfe Klingen', 2, '{"speed":"2","avoid":"2","crit":"1","armour":"10"}', 0, 1, 12, 'feet1', NULL),
(116, 'Metallerne Stiefelüberzüge des Ritters', 'Kaltes, hartes Eisen auf euren dicken Lederstiefeln schützen Eure schönen Zehen', 2, '{"speed":"1","avoid":"1","crit":"1","inteligent":"2","armour":"12"}', 0, 1, 18, 'feet1', NULL),
(117, 'Pfotenstiefel des Drachen', 'Sie fallen etwas zu groß aus, dennoch schützen sie Eure Füße vor allem was euren Füßen schaden zufügen will', 2, '{"speed":"3","avoid":"3","crit":"1","inteligent":"1","wisdom":"1","armour":"16"}', 0, 1, 22, 'feet1', NULL),
(118, 'Stoffsack', 'Gut um Euer schändliches Gesicht zu verstecken. Für mehr jedoch nicht', 2, '{"armour":"6"}', 0, 1, 7, 'head1', NULL),
(119, 'Lederkappe', 'Die Menschen im Dorf lachen über euren Kopfschmuck. Immerhin schützt die Kappe ein wenig', 2, '{"speed":"1","avoid":"3","inteligent":"2","wisdom":"2","armour":"6"}', 0, 1, 15, 'head1', NULL),
(120, 'Des Henkers Kaputze', 'Euer Kopf sieht nun aus wie der eines Henkers. Euer Lappenkörper jedoch nicht.', 2, '{"strength":"1","vitality":"1","speed":"1","avoid":"1","armour":"7"}', 0, 1, 22, 'head1', NULL),
(121, 'Verstärkter Lederhelm', 'Die Menschen im Dorf lachen nicht mehr ganz so sehr über euren Kopfschmuck. ', 2, '{"vitality":"2","speed":"1","avoid":"1","armour":"12"}', 0, 1, 29, 'head1', NULL),
(122, 'Lederhelm mit Eisenplatten', 'Eisenplatten verschönern den Lederhelm zwar nicht, euer Kopf ist jedoch sicherer vor Klingen', 2, '{"strength":"1","vitality":"2","armour":"18"}', 0, 1, 14, 'head1', NULL),
(123, 'Kettenhelm', 'Ihr wollt wohl ein wahrer Ritter sein, nicht wahr? Geschützt wie einer , seid ihr auf jedenfall', 2, '{"vitality":"2","speed":"1","avoid":"1","crit":"2","inteligent":"2","wisdom":"2","armour":"22"}', 0, 1, 19, 'head1', NULL),
(124, 'Plattenhelm des Kriegers', 'Nur wahren Kriegern ist es bestimmt diesen Helm zu tragen. Krieger haben nicht nur Muskeln, sondern auch ein gehirn das geschützt werden muss', 2, '{"strength":"2","vitality":"2","speed":"1","avoid":"1","crit":"2","inteligent":"1","wisdom":"1","armour":"18"}', 0, 1, 25, 'head1', NULL),
(125, 'Der Kopf des Drachen', 'Der Kopf des Drachen ist wahrlich ein furchteinflösender und sehr schützender Helm. Nur leider riecht es etwas vermodert im Inneren. ', 2, '{"speed":"3","avoid":"3","crit":"1","inteligent":"2","wisdom":"2","armour":"27"}', 0, 1, 84, 'head1', NULL),
(126, 'Heil Stein', 'Ein stein der euch um 50 HP heilt', 2, '{}', 4, 20, 5, 'healpotion', '{"type":22,"cond":[],"key":"hp","value":50}'),
(127, 'Mana Stein', 'Ein stein der euer Mana um 50 Punkte auffüllt', 2, '{}', 4, 20, 5, 'manapotion', '{"type":23,"cond":[],"key":"mana","value":"50"}'),
(129, 'Horsts Zahn', 'Ein zahn der einst Horst gehörte. ', 0, '{"none":""}', 1, 10, 4, 'trash1', NULL),
(130, 'Helm der Eisenseite', 'Ein Helm aus einem der härtesten Metalle des Landes', 3, '{"vitality":"2","avoid":"3","strength":2,"crit":"3","armour":"50"}', 0, 1, 50, 'head1', '{"none":""}'),
(131, 'Schulter der Eisenseite', 'Diese Schultern halten auch dem stäksten stand', 3, '{"strength":"3","vitality":"1","avoid":"4","crit":"1","armour":"60"}', 0, 1, 50, 'shoulder1', '{"none":""}'),
(132, 'Brust der Eisenseite', 'todo', 3, '{"vitality":"6","avoid":"1","crit":"1","armour":"150"}', 0, 1, 80, 'breast1', '{"none":""}'),
(133, 'Handschuhe der Eisenseite', 'todo', 3, '{"vitality":"1","avoid":"4","crit":"4","armour":"40"}', 0, 1, 40, 'hand1', '{"none":""}'),
(134, 'Hose der Eisenseite', 'todo', 3, '{"strength":"3","vitality":"3","avoid":"2","crit":"2","armour":"55"}', 0, 1, 60, 'leg1', '{"none":""}'),
(135, 'Schuhe der Eisenseite', 'todo', 3, '{"vitality":"3","avoid":"5","crit":"1","armour":"30"}', 0, 1, 40, 'feet1', '{"none":""}'),
(136, 'Streitkolben der Eisenseite', 'todo', 3, '{"speed":"6","crit":"3","mindmg":8,"maxdmg":10}', 0, 1, 120, 'sword1', '{"none":""}'),
(137, 'Helm des Berserkers', 'todo', 3, '{"strength":"3","vitality":"1","speed":"1","avoid":"1","armour":"25"}', 0, 1, 50, 'head1', '{"none":""}'),
(138, 'Schulter des Berserkers', 'todo', 3, '{"strength":"3","vitality":"2","speed":"1","avoid":"1","armour":"20"}', 0, 1, 50, 'shoulder1', '{"none":""}'),
(139, 'Brust des Berserkers', 'todo', 3, '{"strength":"5","vitality":"3","speed":"2","armour":"35"}', 0, 1, 80, 'breast1', '{"none":""}'),
(140, 'Handschuhe des Berserkers', 'todo', 3, '{"strength":"3","vitality":"3","speed":"2","armour":"25"}', 0, 1, 40, 'hand1', '{"none":""}'),
(141, 'Hose des Berserkers', 'todo', 3, '{"strength":"4","vitality":"2","speed":"2","armour":"30"}', 0, 1, 60, 'leg1', '{"none":""}'),
(142, 'Schuhe des Berserkers', 'todo', 3, '{"strength":"3","vitality":"1","speed":"4","armour":"20"}', 0, 1, 40, 'feet1', '{"none":""}'),
(143, 'Schwert des Berserkers', 'todo', 3, '{"strength":"4","speed":"4","mindmg":4,"maxdmg":17}', 0, 1, 120, 'sword1', '{"none":""}');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `itemsequip`
--

CREATE TABLE `itemsequip` (
  `id` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED DEFAULT NULL,
  `slot` int(11) UNSIGNED DEFAULT NULL,
  `material` int(11) UNSIGNED DEFAULT NULL,
  `wapon_type` double DEFAULT NULL,
  `minlevel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `itemsequip`
--

INSERT INTO `itemsequip` (`id`, `item_id`, `slot`, `material`, `wapon_type`, `minlevel`) VALUES
(2, 4, 0, 0, -1, 0),
(4, 6, 1, 0, -1, 0),
(5, 7, 3, 0, -1, 0),
(6, 8, 4, 0, -1, 0),
(7, 9, 5, 0, -1, 0),
(8, 10, 6, 1, 0, 0),
(13, 17, 6, 4, 7, 2),
(14, 18, 6, 1, 2, 3),
(20, 14, 6, 4, 0, 1),
(21, 15, 6, 1, 4, 1),
(22, 16, 6, 4, 6, 1),
(25, 19, 6, 4, 6, 2),
(27, 21, 6, 1, 2, 1),
(98, 92, 2, 0, -1, 1),
(99, 93, 2, 0, -1, 1),
(100, 94, 2, 0, -1, 2),
(101, 95, 2, 2, -1, 1),
(102, 96, 2, 2, -1, 2),
(103, 97, 2, 2, -1, 1),
(104, 98, 2, 2, -1, 2),
(105, 99, 2, 3, -1, 1),
(106, 100, 2, 5, -1, 2),
(107, 101, 2, 5, -1, 3),
(108, 102, 2, 5, -1, 3),
(109, 103, 4, 0, -1, 1),
(110, 104, 4, 2, -1, 1),
(111, 105, 4, 3, -1, 2),
(112, 106, 4, 4, -1, 1),
(113, 107, 4, 5, -1, 1),
(114, 108, 3, 3, -1, 1),
(115, 109, 3, 2, -1, 1),
(116, 110, 3, 3, -1, 1),
(117, 111, 3, 4, -1, 3),
(118, 112, 3, 5, -1, 1),
(119, 113, 5, 0, -1, 1),
(120, 114, 5, 2, -1, 2),
(121, 115, 5, 2, -1, 1),
(122, 116, 5, 3, -1, 1),
(123, 117, 5, 5, -1, 1),
(124, 118, 0, 0, -1, 1),
(125, 119, 0, 2, -1, 2),
(126, 120, 0, 2, -1, 1),
(127, 121, 0, 2, -1, 1),
(128, 122, 0, 3, -1, 1),
(129, 123, 0, 3, -1, 1),
(130, 124, 0, 4, -1, 3),
(131, 125, 0, 5, -1, 3),
(132, 130, 0, 4, -1, 3),
(133, 131, 1, 4, -1, 3),
(134, 132, 2, 4, -1, 3),
(135, 133, 3, 4, -1, 3),
(136, 134, 4, 4, -1, 3),
(137, 135, 5, 4, -1, 3),
(138, 136, 6, 4, 4, 3),
(139, 137, 0, 4, -1, 3),
(140, 138, 1, 4, -1, 3),
(141, 139, 2, 4, -1, 3),
(142, 140, 3, 4, -1, 3),
(143, 141, 4, 4, -1, 3),
(144, 142, 5, 4, -1, 3),
(145, 143, 6, 4, 4, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `log`
--

CREATE TABLE `log` (
  `id` int(11) UNSIGNED NOT NULL,
  `channel` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `createtime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mob`
--

CREATE TABLE `mob` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `randomname` tinyint(1) UNSIGNED DEFAULT NULL,
  `type` int(11) UNSIGNED DEFAULT NULL,
  `fightstats` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int(11) UNSIGNED DEFAULT NULL,
  `rare` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `questmob` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobkey` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loot` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actions` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ki` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attacklist` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `mob`
--

INSERT INTO `mob` (`id`, `name`, `randomname`, `type`, `fightstats`, `level`, `rare`, `questmob`, `mobkey`, `loot`, `actions`, `ki`, `attacklist`) VALUES
(1, 'Verdorbene Ratte', 0, 0, '{"spell":10,"critMulti":1.3,"aggro":2.5,"maxHp":10,"maxMana":0,"crit":5,"avoid":5,"speed":1,"maxDmg":2,"minDmg":1,"armoreRed":0,"manaReg":1}', 1, '0', '1', 'verdob_rat', '[["xp",[8,17],105],["gold",[4,7],20],["item",[2,1],100]]', '[{"type":12,"cond":["queststatus","=",[2,0]],"key":"q02_rat_kill","value":true},\n{"type":19,"cond":[],"key":"q_rat_kill","value":1}\n]', 'rat', '[]'),
(2, 'Mutter Ratte', 0, 0, '{"spell":10,"critMulti":1.3,"aggro":2.5,"maxHp":25,"maxMana":0,"crit":5,"avoid":5,"speed":2,"maxDmg":2,"minDmg":1,"armoreRed":20,"manaReg":1}', 1, '0', '1', 'verdob_rat_mom', '[["xp",[6,22],105],["gold",[1,5],20],["item",[2,1],10]]', '[{"type":12,"cond":["queststatus","=",[2,0]],"key":"q02_rat_kill","value":true},\n{"type":19,"cond":[],"key":"q_rat_kill","value":1}\n]', 'rat', '[]'),
(3, 'Schtronk 1', 0, 1, '{"spell":10,"critMulti":1.3,"aggro":2.5,"maxHp":110,"maxMana":5,"crit":15,"avoid":5,"speed":6,"maxDmg":10,"minDmg":15,"armoreRed":20,"manaReg":1}', 2, '1', '0', 'm0_strong', '[["xp",["6","6"],"44"],["gold",["123","44"],"77"]]', '[]', 'strong', '[]'),
(4, 'Bobo der Chabbo', 0, 1, '{"spell":10,"critMulti":1.3,"aggro":2.5,"maxHp":11,"maxMana":0,"crit":2,"avoid":5,"speed":1,"maxDmg":4,"minDmg":1,"armoreRed":10,"manaReg":1}', 1, '0', '0', 'm02_bobo_chabbo', '[["xp",[7,23],105],["gold",[4,20],50]]', '[{"type":19,"cond":[],"key":"tower_m1","value":1}]', 'rat', '[]'),
(5, 'Kampfhorst', 0, 1, '{"spell":10,"critMulti":1.3,"aggro":2.5,"maxHp":31,"maxMana":17,"crit":2,"avoid":5,"speed":2,"maxDmg":12,"minDmg":5,"armoreRed":10,"manaReg":1}', 1, '0', '0', 'm03_kampf_hors', '[["xp",[10,26],105],["gold",[11,38],50],["item",[129,1],50]]', '[{"type":19,"cond":[],"key":"tower_m2","value":1}]', 'berserker', '[1]'),
(6, 'Swagger', 0, 1, '{"spell":10,"critMulti":1.3,"aggro":2.5,"maxHp":55,"maxMana":35,"crit":2,"avoid":5,"speed":3,"maxDmg":12,"minDmg":3,"armoreRed":15,"manaReg":5}', 1, '0', '0', 'm04_swagger', '[["xp",[8,22],100],["gold",[10,66],50]]', '[{"type":19,"cond":[],"key":"tower_m3","value":1}]', 'healer', '[3,8]'),
(7, 'Zwergenberserker', 0, 1, '{"spell":10,"critMulti":1.3,"aggro":2.5,"maxHp":75,"maxMana":31,"crit":5,"avoid":1,"speed":5,"maxDmg":37,"minDmg":11,"armoreRed":15,"manaReg":1}', 1, '0', '0', 'm05_swagger', '[["xp",[11,33],100],["gold",[11,36],50]]', '[{"type":19,"cond":[],"key":"tower_m4","value":1}]', 'berserker', '[1]'),
(9, 'Peter der Schreckliche', 0, 1, '{"spell":10,"critMulti":1.8,"aggro":2.5,"maxHp":950,"maxMana":120,"crit":15,"avoid":11,"speed":15,"maxDmg":46,"minDmg":19,"armoreRed":71,"manaReg":3}', 2, '1', '0', 'm06_peter', '[["xp",[110,320],100],["gold",[120,330],80]]', '[{"type":19,"cond":[],"key":"tower_m5","value":1}]', 'berserker', '[1]'),
(10, 'Schleimige Schlange', 0, 1, '{"spell":10,"critMulti":1.3,"aggro":2.5,"maxHp":150,"maxMana":0,"crit":4,"avoid":20,"speed":5,"maxDmg":15,"minDmg":8,"armoreRed":8,"manaReg":1}', 1, '0', '0', 'm07_snake', '[["xp",[55,111],100],["gold",[55,123],50]]', '[{"type":19,"cond":[],"key":"tower_m6","value":1}]', 'rat', '[]'),
(11, 'Kleiner Oger Wumbaba', 0, 1, '{"spell":10,"critMulti":1.3,"aggro":2.5,"maxHp":190,"maxMana":0,"crit":8,"avoid":2,"speed":3,"maxDmg":40,"minDmg":17,"armoreRed":25,"manaReg":1}', 1, '0', '0', 'm07_oger01', '[["xp",[55,155],100],["gold",[70,200],50]]', '[{"type":19,"cond":[],"key":"tower_m7","value":1}]', 'rat', '[]'),
(12, 'Fanpier', 0, 1, '{"spell":10,"critMulti":1.3,"aggro":2.5,"maxHp":220,"maxMana":0,"crit":25,"avoid":25,"speed":20,"maxDmg":80,"minDmg":65,"armoreRed":5,"manaReg":1}', 1, '0', '0', 'm08_vampyr', '[["xp",[60,160],100],["gold",[60,160],50]]', '[{"type":19,"cond":[],"key":"tower_m8","value":1}]', 'rat', '[]'),
(15, 'Grauer Wolf', NULL, 0, '{"armoreRed":"5","avoid":"25","crit":"1","minDmg":"2","maxDmg":"5","maxHp":"15","maxMana":"0","speed":"3","critMulti":"1.4","spell":"1","manaReg":"1"}', 1, '0', '1', 'm09_gray_wolf', '[["xp",["12","20"],"100"],["gold",["15","20"],"50"]]', '[{"type":"19","cond":[],"key":"gray_wolf","value":"1"}]', 'wolf', '[]'),
(16, 'Schwarzer Wolf', NULL, 0, '{"armoreRed":"7","avoid":"25","crit":"4","minDmg":"3","maxDmg":"7","maxHp":"17","maxMana":"0","speed":"4","critMulti":"1.2","spell":"1","manaReg":"1"}', 1, '0', '1', 'm10_black_wolf', '[["xp",["16","25"],"100"],["gold",["15","20"],"50"]]', '[{"type":"19","cond":[],"key":"black_wolf","value":"1"}]', 'wolf', '[]'),
(17, 'Alpha Wolf', NULL, 0, '{"armoreRed":"12","avoid":"25","crit":"5","minDmg":"5","maxDmg":"8","maxHp":"39","maxMana":"0","speed":"8","critMulti":"1.6","spell":"1","manaReg":"1"}', 2, '0', '0', 'm11_alpha_wolf', '[["xp",["20","30"],"100"],["gold",["15","20"],"50"]]', '[{"type":"19","cond":[],"key":"black_wolf","value":"1"}]', 'wolf', '[]');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `npc`
--

CREATE TABLE `npc` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` int(11) UNSIGNED DEFAULT NULL,
  `randomtext` text COLLATE utf8mb4_unicode_ci,
  `faction` int(11) UNSIGNED DEFAULT NULL,
  `guild` int(11) UNSIGNED DEFAULT NULL,
  `gender` int(11) UNSIGNED DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conditions` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `npc`
--

INSERT INTO `npc` (`id`, `name`, `type`, `randomtext`, `faction`, `guild`, `gender`, `description`, `conditions`) VALUES
(1, 'Händler', 3, '{"r1":"Ich begr\\u00fc\\u00dfe euch in meinem laden, werter Reisender","r2":"Habe ich euch nicht schoneinmal geshen?"}', 0, 0, 1, 'Noch nichts bekannt', '[]'),
(2, 'Hilda die Wilde', 2, '{\n"pq":"Wo ist sie nur... <br/> <i>Ihr hört ein schniefen hinter der Tavernen Türe</i>",\n"r1":"Hallo {name} schön das du wieder da bist. Na wie findest du die Haarspange in meinem Haar. Wie kann ich dir helfen?",\n"r2":"Willkommen zurück {name} was kann ich heute für dich tun?"\n}', 0, 0, 0, 'Hilda die Wilde, war irgendwas...  ', '[{"c":["queststatus","<",[1,2]],"t":"pq"}]'),
(5, 'Schmied', 4, '{"r1":"Willkommen in der Schmiede, wie kann ich euch helfen?"}', 0, 0, 1, 'Trainer für Stats', '[]');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `npcquest`
--

CREATE TABLE `npcquest` (
  `id` int(11) UNSIGNED NOT NULL,
  `npc_id` int(11) UNSIGNED DEFAULT NULL,
  `quests` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `npcquest`
--

INSERT INTO `npcquest` (`id`, `npc_id`, `quests`) VALUES
(1, 2, '[1,2,5]');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `npctrainer`
--

CREATE TABLE `npctrainer` (
  `id` int(11) UNSIGNED NOT NULL,
  `npc_id` int(11) UNSIGNED DEFAULT NULL,
  `trainer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `npctrainer`
--

INSERT INTO `npctrainer` (`id`, `npc_id`, `trainer`) VALUES
(1, 5, '{"strength":55,"vitality":55,"speed":55,"avoid":55,"crit":55,"inteligent":55,"wisdom":55,"level":5}');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `npcvendor`
--

CREATE TABLE `npcvendor` (
  `id` int(11) UNSIGNED NOT NULL,
  `npc_id` int(11) UNSIGNED DEFAULT NULL,
  `gold` double DEFAULT NULL,
  `mingold` int(11) UNSIGNED DEFAULT NULL,
  `maxgold` int(11) UNSIGNED DEFAULT NULL,
  `restocktime` int(11) UNSIGNED DEFAULT NULL,
  `lastrestock` int(11) UNSIGNED DEFAULT NULL,
  `itemconf` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `npcvendor`
--

INSERT INTO `npcvendor` (`id`, `npc_id`, `gold`, `mingold`, `maxgold`, `restocktime`, `lastrestock`, `itemconf`) VALUES
(1, 1, 24436, 200, 1100, 120, 1547202856, ' [[2,10,1], [4,10,1], [6,10,1], [7,10,1], [8,10,1], [9,10,1], [10,10,1], [11,10,1], [12,10,1], [14,10,1], [15,10,1], [16,10,1], [17,10,1], [18,10,1], [19,10,1], [21,10,1], [92,10,1], [93,10,1], [94,10,1], [95,10,1], [96,10,1], [97,10,1], [98,10,1], [99,10,1], [100,10,1], [101,10,1], [102,10,1], [103,10,1], [104,10,1], [105,10,1], [106,10,1], [107,10,1], [108,10,1], [109,10,1], [110,10,1], [111,10,1], [112,10,1], [113,10,1], [114,10,1], [115,10,1], [116,10,1], [117,10,1], [118,10,1], [119,10,1], [120,10,1], [121,10,1], [122,10,1], [123,10,1], [124,10,1], [125,10,1],\n\n[132,15,1],\n[133,15,1],\n[134,15,1],\n[135,15,1],\n[136,15,1],\n[137,15,1],\n[138,15,1],\n[139,15,1],\n[140,15,1],\n[141,15,1],\n[142,15,1],\n[143,15,1],\n[130,15,1],\n[131,15,1]\n\n\n ]');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `quest`
--

CREATE TABLE `quest` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pre_quest_id` int(11) UNSIGNED DEFAULT NULL,
  `startcond` text COLLATE utf8mb4_unicode_ci,
  `questtext` text COLLATE utf8mb4_unicode_ci,
  `reward` text COLLATE utf8mb4_unicode_ci,
  `goalcond` text COLLATE utf8mb4_unicode_ci,
  `objectiv` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `goalaction` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `quest`
--

INSERT INTO `quest` (`id`, `name`, `key`, `pre_quest_id`, `startcond`, `questtext`, `reward`, `goalcond`, `objectiv`, `goalaction`, `type`) VALUES
(1, 'Die goldene Brosche', 'q01_find_haarspange', 0, '["level",">",0]', '{"new":["Nein Nein!!! Ich lasse erst wieder jemanden hinein wenn ich meine Haarspange wieder habe!","Ich werde die Haarspange f\\u00fcr euch finden"],"active":["Habt ihr meine Haarspange bisher gefunden? *schnief*"],"solved":["Vielen danke das Ihr sie gefunden habt. Gebt ihr Sie mir?","Sicher, Bittesch\\u00f6n"]}', '[["xp",50]]', '["iteminbag","=",[11,1]]', 'Finde die Goldene Brosche', '[{"action":"remitem","key":11,"amount":1}]', 0),
(2, 'Die Ratte am Brunnen', 'q02_ratte_am_brunnen', 1, '["queststatus","=",[1,2]]', '{"new":["Ahhh da ist eine dieser verfluchten Ratten auf meinem Tresen! Die muss aus der Kirche gekommen sein..  Bitte macht was dagegen!","Annehmen","Zur\\u00fcck"],"active":["Schnell bevor sie noch alle Brezeln auffrisst!","Zur\\u00fcck"],"solved":["Ich danke euch vielmals, hier nehmt das Gold daf\\u00fcr.","Abgeben"]}', '[["xp",50],["gold",50]]', '["flag","=",["q02_rat_kill",1]]', 'Tötet die Ratte am Brunnen', '[{"action":"remflag","key":"q02_rat_kill"}]', 2),
(5, 'Der Kammerjäger', 'q03_der_kammer_jaeger', 0, '["queststatus","=",[1,2]]', '{"new":["Hm \\u00fcberall Ratten.. k\\u00f6nnt ihr nicht ein paar f\\u00fcr mich t\\u00f6ten?"],"active":["Habt ihr schon alle Ratten get\\u00f6tet?"],"solved":["Vielen dank. Es gibt hierf\\u00fcr auch eine kleinigkeit"]}', '[["xp",100],["gold",110]]', '["flag",">",["q_rat_kill",9]]', 'Tötet 10 Ratten', '[{"actioin":"remvar","key":"q_rat_kill"}]', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `questlog`
--

CREATE TABLE `questlog` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `quest_id` int(11) UNSIGNED DEFAULT NULL,
  `status` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `statistics`
--

CREATE TABLE `statistics` (
  `id` int(11) UNSIGNED NOT NULL,
  `statkey` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `value` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagekey` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`id`, `name`, `password`, `imagekey`) VALUES
(1, 'tino', 'test', ''),
(2, 'Info', 'aölskdfjaösldkfj asdfk jasdf asdfk jasdf askdfjl 32jj209 qwefasdifja sdfja', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `usergroup`
--

CREATE TABLE `usergroup` (
  `id` int(11) UNSIGNED NOT NULL,
  `leet_user_id` int(11) UNSIGNED DEFAULT NULL,
  `memberlist` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `groupkey` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `in_fight` tinyint(1) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `usergroup`
--

INSERT INTO `usergroup` (`id`, `leet_user_id`, `memberlist`, `groupkey`, `in_fight`) VALUES
(47, 1, '[]', '1_18278500', 0),
(48, 5, '[{"userid":5,"accepted":true}]', '5_60008482', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `worldfmap`
--

CREATE TABLE `worldfmap` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mapactions` text COLLATE utf8mb4_unicode_ci,
  `mapdata` text COLLATE utf8mb4_unicode_ci,
  `imgkey` varchar(155) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `worldfmap`
--

INSERT INTO `worldfmap` (`id`, `name`, `mapactions`, `mapdata`, `imgkey`) VALUES
(3, 'Tutorialien', '{"A":{"name":"openquest","id":1},"B":{"name":"opentrainer","id":1},"C":null,"D":{"name":"openvendor","id":1},"E":{"name":"openfmap","id":6},"F":{"name":"openspace","id":1},"G":{"name":"openspace","id":2}}', '[{"x":3,"y":0,"name":"Taverne","akey":"A"},{"x":3,"y":2,"name":"Der Schmied","akey":"B"},{"x":3,"y":3,"name":"Die Kirche","akey":"C"},{"x":4,"y":4,"name":"H\\u00e4ndler","akey":"D"},{"x":5,"y":3,"name":"Die Stra\\u00dfe","akey":"E"},{"x":4,"y":3,"name":"Der Brunnen","akey":"F"},{"x":1,"y":5,"name":"Der Turm","akey":"G"}]', 'village'),
(6, 'Der Wald', '{"A":null,"B":{"name":"openfmap","id":3}}', '[{"x":3,"y":3,"name":"Die Wolfsh\\u00f6le","akey":"A"},{"x":0,"y":4,"name":"In das Dorf","akey":"B"}]', 'forest');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `worldspace`
--

CREATE TABLE `worldspace` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_list` text COLLATE utf8mb4_unicode_ci,
  `startconditions` text COLLATE utf8mb4_unicode_ci,
  `actions` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `worldspace`
--

INSERT INTO `worldspace` (`id`, `name`, `text_list`, `startconditions`, `actions`) VALUES
(1, 'Der Brunnen', '{"r1":"Der Brunnen im Dorf. Nicht nur die Frischwasserquelle f\\u00fcr die einwohner. Auch ein Ort an dem sie ausspannen",\n"r2":"Ein sch\\u00f6ner Brunnen der mittem in diesem Dorf steht. Was sich hier wohl alles drum rum abspielt",\n"fb":"Ihr k\\u00f6nnt im gra\\u00df, welches um den brunnen herum w\\u00e4chst, etwas glizerndes sehen. Wollt ihr es nehmen?",\n"nn":"Nehmen",\n"rk":"Ihr seht eine rießige Ratte. Es scheint keine normale Ratte zu sein. Sie funkelt euch böse an.",\n"rkn":"Angreifen!",\n"hea":"Erfrischen",\n"heb":"Der Brunnen mit Frischem wasser. Ihr könnt euch hier kostenfrei erfrischen <small> Stellt 50 HP und MP her (max. Lvl. 3) </small>. "\n}', '[\n {"c":{"and":[["queststatus","=",[1,0]],["iteminbag","!=",[11,1]]]},"a":"fb","at":"nn","t":"fb"},\n \n {"c":{"and":[\n 		["queststatus","=",[2,0]],\n 		["flag","!=",["q02_rat_kill",true]]\n 	]},\n 	"a":"rk",\n 	"at":"rkn",\n 	"t":"rk"\n },\n{"c":["level","<",4],"a":"heal","at":"hea","t":"heb"}\n]', '{"fb":{"type":0,"cond":[],"key":11,"value":1},\n "rk":{"type":18,"cond":[],"key":1,"value":[1]},\n "heal":{"type":22,"cond":[],"key":"all","value":50}\n }\n\n'),
(2, 'Der Turm', '{"r0":"Der alte Turm. Was hier so alles haust!","aa":"Bek\\u00e4mpfe die Turm Etagen","aaa":"Etage 1","ab":"Bek\\u00e4mpfe die Turm Etagen","aba":"Etage 2","ac":"Bek\\u00e4mpfe die Turm Etagen","aca":"Etage 2 Hard","ad":"Bek\\u00e4mpfe die Turm Etagen","ada":"Etage 3","ae":"Bek\\u00e4mpfe die Turm Etagen","aea":"Etage 3 Hard","af":"Bek\\u00e4mpfe die Turm Etagen","afa":"Etage 4","ag":"Bek\\u00e4mpfe die Turm Etagen","aga":"Etage 4 Hard","ah":"Bek\\u00e4mpfe die Turm Etagen","aha":"Etage 5","ai":"Bek\\u00e4mpfe die Turm Etagen","aia":"Etage 5 Hard","aj":"Bek\\u00e4mpfe die Turm Etagen","aja":"Etage 6","ak":"Bek\\u00e4mpfe die Turm Etagen","aka":"Etage 6 Hard","al":"Bek\\u00e4mpfe die Turm Etagen","ala":"Etage 7","am":"Bek\\u00e4mpfe die Turm Etagen","ama":"Etage 7 Hard","an":"Bek\\u00e4mpfe die Turm Etagen","ana":"Etage 8","ao":"Bek\\u00e4mpfe die Turm Etagen","aoa":"Etage 8 Hard","ap":"Bek\\u00e4mpfe die Turm Etagen","apa":"Etage 9","aq":"Bek\\u00e4mpfe die Turm Etagen","aqa":"Etage 9 Hard"}', '[{"c":["level",">",0],"a":"aa","at":"aaa","t":"aa"},{"c":["flag",">",["tower_m1",9]],"a":"ab","at":"aba","t":"ab"},{"c":["flag",">",["tower_m1",49]],"a":"ac","at":"aca","t":"ac"},{"c":["flag",">",["tower_m2",9]],"a":"ad","at":"ada","t":"ad"},{"c":["flag",">",["tower_m2",49]],"a":"ae","at":"aea","t":"ae"},{"c":["flag",">",["tower_m3",9]],"a":"af","at":"afa","t":"af"},{"c":["flag",">",["tower_m3",49]],"a":"ag","at":"aga","t":"ag"},{"c":["flag",">",["tower_m4",9]],"a":"ah","at":"aha","t":"ah"},{"c":["flag",">",["tower_m4",49]],"a":"ai","at":"aia","t":"ai"},{"c":["flag",">",["tower_m5",9]],"a":"aj","at":"aja","t":"aj"},{"c":["flag",">",["tower_m5",49]],"a":"ak","at":"aka","t":"ak"},{"c":["flag",">",["tower_m6",9]],"a":"al","at":"ala","t":"al"},{"c":["flag",">",["tower_m6",49]],"a":"am","at":"ama","t":"am"},{"c":["flag",">",["tower_m7",9]],"a":"an","at":"ana","t":"an"},{"c":["flag",">",["tower_m7",49]],"a":"ao","at":"aoa","t":"ao"},{"c":["flag",">",["tower_m8",9]],"a":"ap","at":"apa","t":"ap"},{"c":["flag",">",["tower_m8",49]],"a":"aq","at":"aqa","t":"aq"}]', '{"aa":{"type":21,"key":0,"cond":[],"value":[1,4]},"ab":{"type":21,"key":0,"cond":[],"value":[1,1,4,4,5]},"ac":{"type":21,"key":0,"cond":[],"value":[1,1,4,4,5,1,4,4,5,1,4,4,5]},"ad":{"type":21,"key":0,"cond":[],"value":[1,4,5,5,6]},"ae":{"type":21,"key":0,"cond":[],"value":[1,4,5,5,6,4,5,5,6,4,5,5,6]},"af":{"type":21,"key":0,"cond":[],"value":[4,5,6,6,7]},"ag":{"type":21,"key":0,"cond":[],"value":[4,5,6,6,7,5,6,6,7,5,6,6,7]},"ah":{"type":21,"key":0,"cond":[],"value":[9,6,6]},"ai":{"type":21,"key":0,"cond":[],"value":[9,6,6,6,6,9,9,9,9]},"aj":{"type":21,"key":0,"cond":[],"value":[7,6,7,6,10]},"ak":{"type":21,"key":0,"cond":[],"value":[7,6,7,6,10,6,7,6,10,6,7,6,10]},"al":{"type":21,"key":0,"cond":[],"value":[7,7,10,10,11]},"am":{"type":21,"key":0,"cond":[],"value":[11,11,10,10,11,7,11,10,11,7,10,10,11]},"an":{"type":21,"key":0,"cond":[],"value":[10,10,11,11,12]},"ao":{"type":21,"key":0,"cond":[],"value":[10,12,11,11,12,10,12,12,12,12,11,11,12]},"ap":{"type":21,"key":0,"cond":[],"value":[7,10,11,12,12]},"aq":{"type":21,"key":0,"cond":[],"value":[7,10,11,12,12,10,11,12,12,10,11,12,12,12,12,12,9,9,9,9]}}');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `bag`
--
ALTER TABLE `bag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_bag_owner` (`owner_id`);

--
-- Indizes für die Tabelle `bagitems`
--
ALTER TABLE `bagitems`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_bagitems_item` (`item_id`),
  ADD KEY `index_foreignkey_bagitems_bag` (`bag_id`);

--
-- Indizes für die Tabelle `charakter`
--
ALTER TABLE `charakter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_charakter_player` (`player_id`);

--
-- Indizes für die Tabelle `charequip`
--
ALTER TABLE `charequip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_charequip_user` (`user_id`);

--
-- Indizes für die Tabelle `charstats`
--
ALTER TABLE `charstats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_charstats_player` (`player_id`);

--
-- Indizes für die Tabelle `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_chat_user` (`user_id`);

--
-- Indizes für die Tabelle `fight`
--
ALTER TABLE `fight`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `fightattacks`
--
ALTER TABLE `fightattacks`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `fightlog`
--
ALTER TABLE `fightlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_fightlog_fight` (`fight_id`);

--
-- Indizes für die Tabelle `fightmember`
--
ALTER TABLE `fightmember`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_fightmember_actor` (`actor_id`),
  ADD KEY `index_foreignkey_fightmember_fight` (`fight_id`);

--
-- Indizes für die Tabelle `flags`
--
ALTER TABLE `flags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_flags_user` (`user_id`);

--
-- Indizes für die Tabelle `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indizes für die Tabelle `itemsequip`
--
ALTER TABLE `itemsequip`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_id` (`item_id`),
  ADD KEY `index_foreignkey_itemsequip_item` (`item_id`);

--
-- Indizes für die Tabelle `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_log_user` (`user_id`);

--
-- Indizes für die Tabelle `mob`
--
ALTER TABLE `mob`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `npc`
--
ALTER TABLE `npc`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `npcquest`
--
ALTER TABLE `npcquest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_npcquest_npc` (`npc_id`);

--
-- Indizes für die Tabelle `npctrainer`
--
ALTER TABLE `npctrainer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_npctrainer_npc` (`npc_id`);

--
-- Indizes für die Tabelle `npcvendor`
--
ALTER TABLE `npcvendor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_npcvendor_npc` (`npc_id`);

--
-- Indizes für die Tabelle `quest`
--
ALTER TABLE `quest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_quest_pre_quest` (`pre_quest_id`);

--
-- Indizes für die Tabelle `questlog`
--
ALTER TABLE `questlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_questlog_user` (`user_id`),
  ADD KEY `index_foreignkey_questlog_quest` (`quest_id`);

--
-- Indizes für die Tabelle `statistics`
--
ALTER TABLE `statistics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_statistics_user` (`user_id`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `usergroup`
--
ALTER TABLE `usergroup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_foreignkey_usergroup_leet_user` (`leet_user_id`);

--
-- Indizes für die Tabelle `worldfmap`
--
ALTER TABLE `worldfmap`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `worldspace`
--
ALTER TABLE `worldspace`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `bag`
--
ALTER TABLE `bag`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT für Tabelle `bagitems`
--
ALTER TABLE `bagitems`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3207;
--
-- AUTO_INCREMENT für Tabelle `charakter`
--
ALTER TABLE `charakter`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT für Tabelle `charequip`
--
ALTER TABLE `charequip`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT für Tabelle `charstats`
--
ALTER TABLE `charstats`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
--
-- AUTO_INCREMENT für Tabelle `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `fight`
--
ALTER TABLE `fight`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=785;
--
-- AUTO_INCREMENT für Tabelle `fightattacks`
--
ALTER TABLE `fightattacks`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT für Tabelle `fightlog`
--
ALTER TABLE `fightlog`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `fightmember`
--
ALTER TABLE `fightmember`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `flags`
--
ALTER TABLE `flags`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;
--
-- AUTO_INCREMENT für Tabelle `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;
--
-- AUTO_INCREMENT für Tabelle `itemsequip`
--
ALTER TABLE `itemsequip`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;
--
-- AUTO_INCREMENT für Tabelle `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;
--
-- AUTO_INCREMENT für Tabelle `mob`
--
ALTER TABLE `mob`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT für Tabelle `npc`
--
ALTER TABLE `npc`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT für Tabelle `npcquest`
--
ALTER TABLE `npcquest`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT für Tabelle `npctrainer`
--
ALTER TABLE `npctrainer`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT für Tabelle `npcvendor`
--
ALTER TABLE `npcvendor`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT für Tabelle `quest`
--
ALTER TABLE `quest`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT für Tabelle `questlog`
--
ALTER TABLE `questlog`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `statistics`
--
ALTER TABLE `statistics`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT für Tabelle `usergroup`
--
ALTER TABLE `usergroup`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT für Tabelle `worldfmap`
--
ALTER TABLE `worldfmap`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT für Tabelle `worldspace`
--
ALTER TABLE `worldspace`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `bagitems`
--
ALTER TABLE `bagitems`
  ADD CONSTRAINT `c_fk_bagitems_bag_id` FOREIGN KEY (`bag_id`) REFERENCES `bag` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints der Tabelle `charequip`
--
ALTER TABLE `charequip`
  ADD CONSTRAINT `c_fk_charequip_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints der Tabelle `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `c_fk_chat_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints der Tabelle `fightlog`
--
ALTER TABLE `fightlog`
  ADD CONSTRAINT `c_fk_fightlog_fight_id` FOREIGN KEY (`fight_id`) REFERENCES `fight` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints der Tabelle `fightmember`
--
ALTER TABLE `fightmember`
  ADD CONSTRAINT `c_fk_fightmember_fight_id` FOREIGN KEY (`fight_id`) REFERENCES `fight` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints der Tabelle `flags`
--
ALTER TABLE `flags`
  ADD CONSTRAINT `c_fk_flags_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints der Tabelle `itemsequip`
--
ALTER TABLE `itemsequip`
  ADD CONSTRAINT `itemsequip_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `c_fk_log_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints der Tabelle `npcquest`
--
ALTER TABLE `npcquest`
  ADD CONSTRAINT `c_fk_npcquest_npc_id` FOREIGN KEY (`npc_id`) REFERENCES `npc` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints der Tabelle `npctrainer`
--
ALTER TABLE `npctrainer`
  ADD CONSTRAINT `c_fk_npctrainer_npc_id` FOREIGN KEY (`npc_id`) REFERENCES `npc` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints der Tabelle `npcvendor`
--
ALTER TABLE `npcvendor`
  ADD CONSTRAINT `c_fk_npcvendor_npc_id` FOREIGN KEY (`npc_id`) REFERENCES `npc` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

--
-- Constraints der Tabelle `questlog`
--
ALTER TABLE `questlog`
  ADD CONSTRAINT `c_fk_questlog_quest_id` FOREIGN KEY (`quest_id`) REFERENCES `quest` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `c_fk_questlog_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints der Tabelle `statistics`
--
ALTER TABLE `statistics`
  ADD CONSTRAINT `c_fk_statistics_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
