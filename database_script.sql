-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2025 at 02:52 AM
-- Server version: 8.0.19
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ismoshare_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `annonce`
--

CREATE TABLE `annonce` (
  `ID_ANNONCE` int NOT NULL,
  `ID_USER` int NOT NULL,
  `NOM_ANNONCE` varchar(100) DEFAULT NULL,
  `TYPE_ANNONCE` char(50) DEFAULT NULL,
  `DATE_ANNONCE` datetime DEFAULT NULL,
  `CONTENU_ANNONCE` text,
  `ETAT_VALIDATION_A` varchar(20) DEFAULT 'en attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `annonce`
--

INSERT INTO `annonce` (`ID_ANNONCE`, `ID_USER`, `NOM_ANNONCE`, `TYPE_ANNONCE`, `DATE_ANNONCE`, `CONTENU_ANNONCE`, `ETAT_VALIDATION_A`) VALUES
(21, 2, 'wjjwejs', 'concours', '2025-06-12 20:58:00', 'terere', 'valide'),
(25, 2, 'dj', 'opportunités', '2025-12-12 23:29:00', 'wes', 'valide'),
(27, 2, 'wfwef', 'actualités', '2025-06-20 18:33:00', 'wefwefj', 'valide'),
(38, 2, 'concours embauche', 'opportunités', '2025-06-30 12:41:00', 'The 2025 Moroccan baccalaureate exams (session ordinaire) are scheduled for May 29, 30, and 31. The make-up exams (session de rattrapage) are scheduled for July 8 to 11. \r\nFor candidates in Terminale (final year of high school), the philosophy exam comes before the specialty exams. The philosophy exam is on June 11, and the specialty exams are June 12-13, according to Cap Mission. \r\nThe results for the normal session will be released on June 14, says 9rayti.Com. The results for the make-up session will be released on July 12. ', 'valide'),
(40, 21, 'exam js', 'opportunités', '2025-12-13 02:11:00', 'are you ready for the exams?', 'valide');

-- --------------------------------------------------------

--
-- Table structure for table `commentaireressource`
--

CREATE TABLE `commentaireressource` (
  `ID_COMMENT` int NOT NULL,
  `ID_USER` int NOT NULL,
  `ID_RESSOURCE` int NOT NULL,
  `CONTENU_COMMENT` varchar(255) DEFAULT NULL,
  `DATE_COMMENTAIRE` date DEFAULT NULL,
  `ETAT_VALIDATION_C` varchar(50) NOT NULL DEFAULT 'en attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `filiere`
--

CREATE TABLE `filiere` (
  `ID_FILIERE` int NOT NULL,
  `NOM_FILIERE` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `filiere`
--

INSERT INTO `filiere` (`ID_FILIERE`, `NOM_FILIERE`) VALUES
(1, 'Intelligence Artificielle'),
(2, 'Infographie Prépresse'),
(3, 'Infrastructure Digitale'),
(4, 'Développement Digital');

-- --------------------------------------------------------

--
-- Table structure for table `forum`
--

CREATE TABLE `forum` (
  `ID_FORUM` int NOT NULL,
  `ID_USER` int NOT NULL,
  `TITRE_FORUM` varchar(50) DEFAULT NULL,
  `THEME_FORUM` varchar(50) DEFAULT NULL,
  `CONTENU_SUJET` text,
  `DATE_PUB_FORUM` datetime DEFAULT NULL,
  `ETAT_VALIDATION_F` varchar(20) DEFAULT 'en attente',
  `CHEMIN_PHOTO_F` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `forum`
--

INSERT INTO `forum` (`ID_FORUM`, `ID_USER`, `TITRE_FORUM`, `THEME_FORUM`, `CONTENU_SUJET`, `DATE_PUB_FORUM`, `ETAT_VALIDATION_F`, `CHEMIN_PHOTO_F`) VALUES
(61, 2, 'sdmw', 'astuces', 'wjdwed', '2025-06-20 21:39:00', 'valide', 'photos/'),
(78, 21, 'pip install doesn&#039;t work?', 'autres', 'I have some basics of Python down. But a lot of functions for data analysis and visualisation depend on Python packages that use pip to install. No matter how I tried, I cannot install pip or use it to install packages – it shows a syntax error\r\n\r\nI&#039;ve tried reinstalling Python, setting up the path correctly, and everything.', '2025-12-13 02:24:00', 'valide', '	\n/ISMOSHARE/uploaded_files/image.png'),
(82, 21, 'error js syntax', 'questions', 'A &quot;SyntaxError&quot; in JavaScript indicates that the code is syntactically invalid and cannot be parsed or interpreted by the JavaScript engine. This type of error occurs when the code violates the established rules and structure of the JavaScript language.', '2025-12-13 02:26:00', 'valide', '/ISMOSHARE/uploaded_files/image.png'),
(88, 2, 'wej', 'entraide', 'whdwehd', '2025-12-12 23:24:00', 'valide', '/ISMOSHARE/uploaded_files/'),
(91, 1, 'Help with JavaScript Async/Await', 'questions', 'Hi everyone,\r\nI’m having trouble understanding async/await in JavaScript.\r\nCan someone explain when to use it instead of .then()?\r\nAlso, do you have good examples related to API calls?', '2025-12-13 02:19:00', 'valide', '/ISMOSHARE/uploaded_files/'),
(92, 1, 'Best Framework for Final Year Project?', 'questions', 'Hello,\r\nFor a web-based final year project, which framework do you recommend:\r\nReact, Angular, or Vue?\r\nOur project is a school management system.', '2025-12-13 02:20:00', 'valide', '/ISMOSHARE/uploaded_files/');

-- --------------------------------------------------------

--
-- Table structure for table `groupe`
--

CREATE TABLE `groupe` (
  `ID_GROUPE` int NOT NULL,
  `NOM_GROUPE` varchar(50) DEFAULT NULL,
  `DESCRIPTION` text,
  `DATE_CREATION` date DEFAULT NULL,
  `CREATEUR_GROUPE` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membre`
--

CREATE TABLE `membre` (
  `ID_USER` int NOT NULL,
  `ID_FILIERE` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `membre`
--

INSERT INTO `membre` (`ID_USER`, `ID_FILIERE`) VALUES
(1, 1),
(2, 1),
(21, 2),
(23, 4),
(24, 4);

-- --------------------------------------------------------

--
-- Table structure for table `membre_groupe`
--

CREATE TABLE `membre_groupe` (
  `ID_GROUPE` int NOT NULL,
  `ID_USER` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messageprv`
--

CREATE TABLE `messageprv` (
  `ID_USER_EXPEDITEUR` int NOT NULL,
  `ID_USER_RECEPTEUR` int NOT NULL,
  `CONTENU_MSG` varchar(255) DEFAULT NULL,
  `DATE_ENVOI` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messagerie`
--

CREATE TABLE `messagerie` (
  `ID_MESSAGE` int NOT NULL,
  `ID_GROUPE` int NOT NULL,
  `CONTENU_MESSAGE` text,
  `DATE_MESSAGE` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `ID_MODULE` int NOT NULL,
  `NOM_MODULE` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`ID_MODULE`, `NOM_MODULE`) VALUES
(1, 'Programmer en JavaScript'),
(2, 'Manipuler des bases de données'),
(3, 'Développer des sites web dynamiques'),
(4, 'Entrepreneuriat-PIE 1'),
(5, 'Gérer une infrastructure virtualisée'),
(6, 'Concevoir un réseau informatique');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `ID_NOTIFICATION` int NOT NULL,
  `ID_USER` int NOT NULL,
  `TEXTE_NOTIFICATION` text,
  `DATE_NOTIFICATION` date DEFAULT NULL,
  `EST_LUE_` tinyint(1) DEFAULT (false)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`ID_NOTIFICATION`, `ID_USER`, `TEXTE_NOTIFICATION`, `DATE_NOTIFICATION`, `EST_LUE_`) VALUES
(2, 2, '💬 Vous avez un nouveau commentaire sur votre forum', '2025-06-20', 1),
(3, 2, '🏆 Votre commentaire a été marqué comme le meilleur.', '2025-06-20', 1),
(4, 2, '🔔 Nouvelle  sujet forum à valider à valider.', '2025-06-20', 1),
(5, 2, '🔔 Nouvelle  sujet forum à valider à valider.', '2025-06-20', 1),
(6, 2, '🔔 Nouvelle Registration request à valider.', '2025-06-20', 1),
(7, 2, '🔔 Nouvelle  sujet forum à valider.', '2025-06-20', 1),
(8, 2, '💬 Nouvelle  commentaire à valider.', '2025-06-20', 1),
(9, 2, '🔔 Nouvelle  Ressource à valider.', '2025-06-20', 1),
(10, 1, '✔️​ Votre demande de publication d une ressource a ete acceptee ', '2025-06-20', 1),
(11, 1, '❌​​ Votre demande de publication d un forum a ete refusee ', '2025-06-20', 1),
(12, 2, '✔️​ Votre demande de publication d un sujet forum a ete acceptee ', '2025-06-20', 1),
(13, 1, '❌​​ Votre demande de publication d un commentaire a ete refusee ', '2025-06-20', 1),
(14, 1, '✔️​ Votre demande de publier un commentaire a ete acceptee ', '2025-06-20', 1),
(15, 1, '✔️​ Votre demande de publier un commentaire a ete acceptee ', '2025-06-20', 1),
(16, 1, '📅​ Une Nouvelle Annonce a ete publiee.', '2025-06-20', 1),
(17, 21, '📅​ Une Nouvelle Annonce a ete publiee.', '2025-06-20', 1),
(19, 23, '📅​ Une Nouvelle Annonce a ete publiee.', '2025-06-20', 0),
(20, 1, '📅​ Une Nouvelle Annonce a ete publiee par ismo', '2025-06-20', 1),
(21, 21, '📅​ Une Nouvelle Annonce a ete publiee par ismo', '2025-06-20', 1),
(23, 23, '📅​ Une Nouvelle Annonce a ete publiee par ismo', '2025-06-20', 0),
(24, 1, '📅​ Une Nouvelle Annonce a ete publiee par ismo admin', '2025-06-20', 1),
(25, 21, '📅​ Une Nouvelle Annonce a ete publiee par ismo admin', '2025-06-20', 1),
(27, 23, '📅​ Une Nouvelle Annonce a ete publiee par ismo admin', '2025-06-20', 0),
(28, 1, '📅​ Une Nouvelle Annonce a ete publiee par reda mfeddal', '2025-06-24', 1),
(29, 2, '📅​ Une Nouvelle Annonce a ete publiee par reda mfeddal', '2025-06-24', 1),
(31, 23, '📅​ Une Nouvelle Annonce a ete publiee par reda mfeddal', '2025-06-24', 0),
(32, 2, '💬 Vous avez un nouveau commentaire sur votre forum', '2025-06-24', 1),
(33, 21, '🏆 Votre commentaire a été marqué comme le meilleur par ismo admin.', '2025-06-24', 1),
(34, 1, '📅​ Une Nouvelle Annonce a ete publiee par reda mfeddal', '2025-06-24', 1),
(35, 2, '📅​ Une Nouvelle Annonce a ete publiee par reda mfeddal', '2025-06-24', 1),
(37, 23, '📅​ Une Nouvelle Annonce a ete publiee par reda mfeddal', '2025-06-24', 0),
(38, 21, '🚮 Votre annonce a été supprimee par ismo admin.', '2025-06-24', 1),
(39, 1, '🚮 Votre ressource a été supprimee par reda mfeddal.', '2025-06-24', 1),
(40, 1, '🚮 Votre commentaire a été supprimee par reda mfeddal.', '2025-06-24', 1),
(41, 2, '🚮 Votre forum a été supprimee par reda mfeddal.', '2025-06-24', 1),
(42, 2, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-24', 1),
(43, 21, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-24', 1),
(44, 2, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-25', 1),
(45, 21, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-25', 1),
(46, 2, '✍🏻 Abderahim EL HASSKOURI a demande de modifier son ressource', '2025-06-25', 1),
(47, 21, '✍🏻 Abderahim EL HASSKOURI a demande de modifier son ressource', '2025-06-25', 1),
(48, 2, '✍🏻 Abderahim EL HASSKOURI a demande de modifier son profile', '2025-06-25', 1),
(49, 2, '🔔 Nouvelle sujet forum à valider auteur (Abderahim EL HASSKOURI).', '2025-06-25', 1),
(50, 21, '🔔 Nouvelle sujet forum à valider auteur (Abderahim EL HASSKOURI).', '2025-06-25', 1),
(51, 2, '✍🏻 ABDERRAHIM EL HASSKOURI a demande de modifier son profile', '2025-06-25', 1),
(52, 2, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-25', 1),
(53, 21, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-25', 1),
(54, 23, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-25', 0),
(55, 1, '✔️​ Votre demande de publication d un sujet forum a ete acceptee ', '2025-06-25', 1),
(56, 1, '✔️​ Votre demande de publier un commentaire a ete acceptee ', '2025-06-25', 1),
(57, 1, '✔️​ Votre demande de publier un commentaire a ete acceptee ', '2025-06-25', 1),
(58, 1, '❤️ reda mfeddal a aime votre commentaire.', '2025-06-25', 1),
(59, 1, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-25', 1),
(60, 21, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-26', 1),
(61, 2, '⚠️ Nouvelle Registration request à valider.', '2025-06-26', 1),
(62, 24, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-26', 0),
(63, 2, '✍🏻 driss mjahad a demande de modifier son profile', '2025-06-26', 1),
(64, 2, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-27', 1),
(65, 21, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-27', 1),
(66, 23, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-27', 0),
(67, 2, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-27', 1),
(68, 21, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-27', 1),
(69, 23, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-27', 0),
(70, 2, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-27', 1),
(71, 21, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-27', 1),
(72, 23, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-27', 0),
(73, 2, '🔔 Nouvelle  Ressource à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-27', 1),
(74, 2, '💬 Nouvelle  commentaire à valider auteur(ABDERRAHIM EL HASSKOURI).', '2025-06-27', 1),
(75, 1, '❌​​ Votre demande de publication d un forum a ete refusee ', '2025-06-27', 1),
(76, 1, '✔️​ Votre demande de publier un commentaire a ete acceptee ', '2025-06-27', 1),
(77, 1, '❌​​ Votre demande de publication d un commentaire a ete refusee ', '2025-06-27', 1),
(78, 1, '❌​​ Votre demande de publication d un commentaire a ete refusee ', '2025-06-27', 1),
(79, 2, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-27', 1),
(80, 21, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-27', 1),
(81, 23, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-27', 0),
(82, 2, '🔔 Nouvelle  Ressource à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-27', 1),
(83, 2, '💬 Nouvelle  commentaire à valider auteur(ABDERRAHIM EL HASSKOURI).', '2025-06-27', 1),
(84, 1, '❌​​ Votre demande de publication d un forum a ete refusee ', '2025-06-27', 1),
(85, 1, '❌​​ Votre demande de publication d un commentaire a ete refusee ', '2025-06-27', 1),
(86, 2, '🔔 Nouvelle  Ressource à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-27', 1),
(87, 1, '✔️​ Votre demande de publication d une ressource a ete acceptee ', '2025-06-27', 1),
(88, 2, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-27', 1),
(89, 21, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-27', 1),
(90, 23, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-27', 0),
(91, 2, '🔔 Nouvelle  Ressource à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-27', 1),
(92, 1, '❌​​ Votre demande de publication d un forum a ete refusee ', '2025-06-27', 1),
(93, 2, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-30', 1),
(94, 21, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-30', 1),
(95, 23, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-30', 0),
(96, 2, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-30', 1),
(97, 21, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-30', 1),
(98, 23, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-30', 0),
(99, 2, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-30', 1),
(100, 21, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-30', 1),
(101, 23, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-30', 0),
(102, 1, '💬 Vous avez un nouveau commentaire sur votre forum auteur(ismo admin)', '2025-06-30', 1),
(103, 1, '✔️​ Votre demande de publier un commentaire a ete acceptee ', '2025-06-30', 1),
(104, 1, '✔️​ Votre demande de publier un commentaire a ete acceptee ', '2025-06-30', 1),
(105, 21, '💬 Vous avez un nouveau commentaire sur votre forum auteur(ismo admin)', '2025-06-30', 1),
(106, 1, '📅​ Une Nouvelle Annonce a ete publiee par ismo admin', '2025-06-30', 1),
(107, 21, '📅​ Une Nouvelle Annonce a ete publiee par ismo admin', '2025-06-30', 1),
(108, 23, '📅​ Une Nouvelle Annonce a ete publiee par ismo admin', '2025-06-30', 0),
(109, 24, '📅​ Une Nouvelle Annonce a ete publiee par ismo admin', '2025-06-30', 0),
(110, 1, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 1),
(111, 1, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 1),
(112, 21, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 1),
(113, 23, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 0),
(114, 2, '🔔 Nouvelle sujet forum à valider auteur (driss mjahadd).', '2025-06-30', 1),
(115, 2, '💬 Nouvelle  commentaire à valider. auteur(driss mjahadd)', '2025-06-30', 1),
(116, 2, '🔔 Nouvelle  Ressource à valider. auteur(driss mjahadd)', '2025-06-30', 1),
(117, 2, '🔔 Nouvelle  Ressource à valider. auteur(reda mfeddal)', '2025-06-30', 1),
(118, 2, '💬 Nouvelle  commentaire à valider. auteur(reda mfeddal)', '2025-06-30', 1),
(119, 2, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-06-30', 1),
(120, 2, '🔔 Nouvelle  Ressource à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-06-30', 1),
(121, 24, '❌​​ Votre demande de publication d un commentaire a ete refusee ', '2025-06-30', 0),
(122, 24, '❌​​ Votre demande de publication d une ressource a ete refusee ', '2025-06-30', 0),
(123, 1, '❌​​ Votre demande de publication d une ressource a ete refusee ', '2025-06-30', 1),
(124, 1, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 1),
(125, 1, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 1),
(126, 1, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 1),
(127, 1, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 1),
(128, 1, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 1),
(129, 1, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 1),
(130, 1, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 1),
(131, 23, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 0),
(132, 23, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 0),
(133, 21, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-06-30', 1),
(134, 2, '💬 Nouvelle  commentaire à valider. auteur(reda mfeddal)', '2025-06-30', 1),
(135, 2, '❤️ reda mfeddal a aime votre commentaire.', '2025-06-30', 1),
(136, 1, '❤️ reda mfeddal a aime votre commentaire.', '2025-06-30', 1),
(137, 2, '❤️ reda mfeddal a aime votre commentaire.', '2025-06-30', 1),
(138, 2, '💬 Nouvelle  commentaire à valider. auteur(reda mfeddal)', '2025-06-30', 1),
(139, 2, '💬 Nouvelle  commentaire à valider auteur(reda mfeddal).', '2025-06-30', 1),
(140, 2, '💬 Nouvelle  commentaire à valider auteur(reda mfeddal).', '2025-06-30', 1),
(141, 21, '✔️​ Votre demande de publier un commentaire a ete acceptee ', '2025-06-30', 1),
(142, 21, '✔️​ Votre demande de publier un commentaire a ete acceptee ', '2025-06-30', 1),
(143, 21, '✔️​ Votre demande de publier un commentaire a ete acceptee ', '2025-06-30', 1),
(144, 2, '⚠️ Nouvelle Registration request à valider.', '2025-12-12', 1),
(146, 2, '⚠️ Nouvelle Registration request à valider.', '2025-12-12', 1),
(148, 2, '⚠️ Nouvelle Registration request à valider.', '2025-12-12', 1),
(149, 2, '⚠️ Nouvelle Registration request à valider.', '2025-12-12', 1),
(150, 2, '⚠️ Nouvelle Registration request à valider.', '2025-12-12', 1),
(151, 2, '⚠️ Nouvelle Registration request à valider.', '2025-12-12', 1),
(152, 2, '⚠️ Nouvelle Registration request à valider.', '2025-12-12', 1),
(153, 2, '⚠️ Nouvelle Registration request à valider.', '2025-12-12', 1),
(154, 2, '⚠️ Nouvelle Registration request à valider.', '2025-12-12', 1),
(155, 2, '⚠️ Nouvelle Registration request à valider.', '2025-12-12', 1),
(156, 24, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-12-12', 0),
(157, 23, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-12-12', 0),
(158, 21, '✍🏻 ismo admin a modifier des infos sur votre profile', '2025-12-12', 1),
(159, 1, '🏆 Votre commentaire a été marqué comme le meilleur par ismo direction.', '2025-12-12', 1),
(160, 1, '❤️ ismo direction a aime votre commentaire.', '2025-12-12', 1),
(161, 1, '📅​ Une Nouvelle Annonce a ete publiee par ismo direction', '2025-12-12', 1),
(162, 21, '📅​ Une Nouvelle Annonce a ete publiee par ismo direction', '2025-12-12', 1),
(163, 23, '📅​ Une Nouvelle Annonce a ete publiee par ismo direction', '2025-12-12', 0),
(164, 24, '📅​ Une Nouvelle Annonce a ete publiee par ismo direction', '2025-12-12', 0),
(165, 1, '🚮 Votre forum a été supprimee par ismo direction.', '2025-12-12', 1),
(166, 1, '✔️​ Votre demande de publication d un sujet forum a ete acceptee ', '2025-12-13', 1),
(167, 24, '❌​​ Votre demande de publication d un forum a ete refusee ', '2025-12-13', 0),
(168, 2, '✍🏻 ABDERRAHIM EL HASSKOURI a demande de modifier son profile', '2025-12-13', 0),
(169, 2, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-12-13', 0),
(170, 1, '✔️​ Votre demande de publier un commentaire a ete acceptee ', '2025-12-13', 1),
(171, 2, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-12-13', 0),
(172, 1, '✔️​ Votre demande de publication d un sujet forum a ete acceptee ', '2025-12-13', 0),
(173, 2, '✍🏻 ABDERRAHIM EL HASSKOURI a demande de modifier son sujet forum', '2025-12-13', 0),
(174, 1, '✔️​ Votre demande de publication d un sujet forum a ete acceptee ', '2025-12-13', 0),
(175, 2, '💬 Nouvelle  commentaire à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-12-13', 0),
(176, 1, '✔️​ Votre demande de publier un commentaire a ete acceptee ', '2025-12-13', 0),
(177, 2, '🔔 Nouvelle  Ressource à valider. auteur(ABDERRAHIM EL HASSKOURI)', '2025-12-13', 0),
(178, 1, '✔️​ Votre demande de publication d une ressource a ete acceptee ', '2025-12-13', 0),
(179, 2, '✍🏻 ABDERRAHIM EL HASSKOURI a demande de modifier son ressource', '2025-12-13', 0),
(180, 1, '✔️​ Votre demande de publication d une ressource a ete acceptee ', '2025-12-13', 0),
(181, 21, '✍🏻 ismo direction a modifier des infos sur votre profile', '2025-12-13', 1),
(182, 2, '🚮 Votre forum a été supprimee par reda mfeddal.', '2025-12-13', 0),
(183, 1, '📅​ Une Nouvelle Annonce a ete publiee par reda mfeddal', '2025-12-13', 0),
(184, 2, '📅​ Une Nouvelle Annonce a ete publiee par reda mfeddal', '2025-12-13', 0),
(185, 23, '📅​ Une Nouvelle Annonce a ete publiee par reda mfeddal', '2025-12-13', 0),
(186, 24, '📅​ Une Nouvelle Annonce a ete publiee par reda mfeddal', '2025-12-13', 0),
(187, 2, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-12-13', 0),
(188, 21, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-12-13', 0),
(189, 2, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-12-13', 0),
(190, 21, '🔔 Nouvelle sujet forum à valider auteur (ABDERRAHIM EL HASSKOURI).', '2025-12-13', 0),
(191, 2, '🚮 Votre ressource a été supprimee par reda mfeddal.', '2025-12-13', 0),
(192, 1, '✔️​ Votre demande de publication d un sujet forum a ete acceptee ', '2025-12-13', 0),
(193, 1, '✔️​ Votre demande de publication d un sujet forum a ete acceptee ', '2025-12-13', 0),
(194, 2, '🚮 Votre forum a été supprimee par reda mfeddal.', '2025-12-13', 0),
(195, 2, '🚮 Votre forum a été supprimee par reda mfeddal.', '2025-12-13', 0),
(196, 1, '🚮 Votre forum a été supprimee par reda mfeddal.', '2025-12-13', 0),
(197, 2, '🚮 Votre forum a été supprimee par reda mfeddal.', '2025-12-13', 0),
(198, 2, '💬 Vous avez un nouveau commentaire sur votre forum auteur(reda mfeddal)', '2025-12-13', 0);

-- --------------------------------------------------------

--
-- Table structure for table `programme`
--

CREATE TABLE `programme` (
  `ID_FILIERE` int NOT NULL,
  `ID_MODULE` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `programme`
--

INSERT INTO `programme` (`ID_FILIERE`, `ID_MODULE`) VALUES
(4, 1),
(4, 2),
(4, 3),
(2, 4),
(3, 4),
(4, 4),
(3, 5),
(3, 6);

-- --------------------------------------------------------

--
-- Table structure for table `reaction`
--

CREATE TABLE `reaction` (
  `ID_USER` int NOT NULL,
  `ID_REPONSE` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reaction`
--

INSERT INTO `reaction` (`ID_USER`, `ID_REPONSE`) VALUES
(2, 43),
(21, 43),
(2, 47),
(21, 47),
(2, 52);

-- --------------------------------------------------------

--
-- Table structure for table `reponseforum`
--

CREATE TABLE `reponseforum` (
  `ID_REPONSE` int NOT NULL,
  `ID_USER` int NOT NULL,
  `ID_FORUM` int NOT NULL,
  `CONTENU_REPONSE` varchar(255) DEFAULT NULL,
  `DATE_REPONSE` date DEFAULT NULL,
  `ETAT_VALIDATION_C` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'en attente',
  `MEILLEUR_REPONSE` tinyint NOT NULL DEFAULT '0',
  `REACTION` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reponseforum`
--

INSERT INTO `reponseforum` (`ID_REPONSE`, `ID_USER`, `ID_FORUM`, `CONTENU_REPONSE`, `DATE_REPONSE`, `ETAT_VALIDATION_C`, `MEILLEUR_REPONSE`, `REACTION`) VALUES
(43, 1, 82, 'yes prof tu as raison', '2025-06-30', 'valide', 1, 2),
(44, 1, 78, 'up', '2025-06-30', 'valide', 0, 0),
(46, 2, 78, 'up', '2025-06-30', 'valide', 0, 0),
(47, 2, 82, 'up', '2025-06-30', 'valide', 0, 2),
(51, 21, 78, 'up', '2025-06-30', 'valide', 0, 0),
(52, 2, 88, 'yes', '2025-12-12', 'valide', 1, 1),
(55, 21, 88, 'oui', '2025-12-13', 'valide', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ressource`
--

CREATE TABLE `ressource` (
  `ID_RESSOURCE` int NOT NULL,
  `ID_MODULE` int NOT NULL,
  `ID_USER` int NOT NULL,
  `ID_FILIERE` int NOT NULL,
  `TITRE_RESSOURCE` varchar(50) DEFAULT NULL,
  `CHEMIN_FICHIER` varchar(255) DEFAULT NULL,
  `DATE_UPLOAD` date DEFAULT NULL,
  `ETAT_VALIDATION_R` varchar(20) DEFAULT 'en attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ressource`
--

INSERT INTO `ressource` (`ID_RESSOURCE`, `ID_MODULE`, `ID_USER`, `ID_FILIERE`, `TITRE_RESSOURCE`, `CHEMIN_FICHIER`, `DATE_UPLOAD`, `ETAT_VALIDATION_R`) VALUES
(41, 3, 21, 4, ' Concepts de cloud', '/ISMOSHARE/uploaded_files/M206-Ch01- Concepts de cloud computing.pdf', '2025-12-13', 'valide'),
(42, 3, 21, 4, 'python introduction', '/ISMOSHARE/uploaded_files/1-Introduction au language python .pdf', '2025-12-13', 'valide'),
(43, 3, 21, 4, 'php cc3', '/ISMOSHARE/uploaded_files/CC3_Projet_2025_102.pdf', '2025-12-13', 'valide');

-- --------------------------------------------------------

--
-- Table structure for table `telechargement`
--

CREATE TABLE `telechargement` (
  `ID_TELECHARGEMENT` int NOT NULL,
  `DATE_TEL` date NOT NULL,
  `ID_USER` int NOT NULL,
  `ID_RESSOURCE` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `telechargement`
--

INSERT INTO `telechargement` (`ID_TELECHARGEMENT`, `DATE_TEL`, `ID_USER`, `ID_RESSOURCE`) VALUES
(40, '2025-12-13', 21, 41),
(41, '2025-12-13', 21, 41),
(42, '2025-12-13', 21, 41),
(43, '2025-12-13', 21, 41),
(44, '2025-12-13', 21, 42),
(45, '2025-12-13', 21, 42);

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `ID_USER` int NOT NULL,
  `ID_MESSAGE` int DEFAULT NULL,
  `MATRICULE_OU_CEF` varchar(50) DEFAULT NULL,
  `NOM_USER` varchar(50) DEFAULT NULL,
  `PRENOM_USER` varchar(50) DEFAULT NULL,
  `EMAIL_INSTITUTIONNEL` varchar(100) DEFAULT NULL,
  `MOT_DE_PASSE` varchar(100) DEFAULT NULL,
  `ROLE` varchar(50) DEFAULT NULL,
  `ETAT_COMPTE` varchar(50) DEFAULT (_utf8mb4'en attente'),
  `CHEMIN_PHOTO` varchar(255) DEFAULT NULL,
  `inscription_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`ID_USER`, `ID_MESSAGE`, `MATRICULE_OU_CEF`, `NOM_USER`, `PRENOM_USER`, `EMAIL_INSTITUTIONNEL`, `MOT_DE_PASSE`, `ROLE`, `ETAT_COMPTE`, `CHEMIN_PHOTO`, `inscription_date`) VALUES
(1, NULL, 'SE231304', 'ABDERRAHIM', 'EL HASSKOURI', 'ABDERRAHIM.ELHASSKOURI@ismo.ma', '$argon2i$v=19$m=65536,t=4,p=1$emhaOFkxWUVoTkZwZzFlTQ$UuhWCiqm9nU6On6YABUW6YjCh37hRgUSgjVtHlnGaRQ', 'stagiaire', 'valide', '	\n/ISMOSHARE/uploaded_files/8a0b22e0-7726-4564-8345-de1c259452da.png', '2025-04-14'),
(2, NULL, 'DE234732', 'ismo', 'direction', 'direction@ismo.ma', '$argon2i$v=19$m=65536,t=4,p=1$WjdZUS5NSFVEZ0RIOXJ0Vg$69QB1d1u+upuR2bgRZPWec73TLwV7HiprrQz/AKQ1F0', 'admin', 'valide', '/ISMOSHARE/uploaded_files/20200713_170812.jpg', '2025-05-02'),
(21, NULL, '129120', 'reda', 'mfeddal', 'redamfeddal@ismo.ma', '$argon2i$v=19$m=65536,t=4,p=1$ZlhQOWZYQzlrODBhM0lveg$dyYSfm0jNcMdTRanjNJPo5eLM+53wPf/+zuIh++yLRA', 'formateur', 'valide', '/ISMOSHARE/uploaded_files/WhatsApp Image 2025-03-29 at 22.33.58.jpeg', '2025-05-02'),
(23, NULL, '102932', 'mohamed', 'elhass', 'mohamed-elhass@ismo.ma', '$argon2i$v=19$m=65536,t=4,p=1$clZiS3dtNlFFYjhOUlhrdg$jez7Mt9eGx+Md6RNBiFd9wKbYAkMr4CTWen56Prz94o', 'stagiaire', 'valide', '/ISMOSHARE/uploaded_files/photo_2025-11-27_20-23-34 (2).jpg', '2025-06-20'),
(24, NULL, '219291239', 'driss', 'mjahad', 'driss-mjahad@ismo.ma', '$argon2i$v=19$m=65536,t=4,p=1$ZTV0VEZMRDNFSzZYNkxzOA$1iQ7FFIaG6SdKBtCvgbcJ1T4HsM0OzgcCla8uwKI090', 'stagiaire', 'valide', '	\n/ISMOSHARE/uploaded_files/457695315_919288023565564_5633351088384214944_n.jpg', '2025-06-26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `annonce`
--
ALTER TABLE `annonce`
  ADD PRIMARY KEY (`ID_ANNONCE`),
  ADD KEY `FK_CREER` (`ID_USER`);

--
-- Indexes for table `commentaireressource`
--
ALTER TABLE `commentaireressource`
  ADD PRIMARY KEY (`ID_COMMENT`),
  ADD KEY `FK_AVOIR` (`ID_RESSOURCE`),
  ADD KEY `FK_REPONDRE` (`ID_USER`);

--
-- Indexes for table `filiere`
--
ALTER TABLE `filiere`
  ADD PRIMARY KEY (`ID_FILIERE`);

--
-- Indexes for table `forum`
--
ALTER TABLE `forum`
  ADD PRIMARY KEY (`ID_FORUM`),
  ADD KEY `FK_PUBLIER` (`ID_USER`);

--
-- Indexes for table `groupe`
--
ALTER TABLE `groupe`
  ADD PRIMARY KEY (`ID_GROUPE`);

--
-- Indexes for table `membre`
--
ALTER TABLE `membre`
  ADD PRIMARY KEY (`ID_USER`,`ID_FILIERE`),
  ADD KEY `FK_MEMBRE2` (`ID_FILIERE`);

--
-- Indexes for table `membre_groupe`
--
ALTER TABLE `membre_groupe`
  ADD PRIMARY KEY (`ID_GROUPE`,`ID_USER`),
  ADD KEY `FK_MEMBRE_GROUPE2` (`ID_USER`);

--
-- Indexes for table `messageprv`
--
ALTER TABLE `messageprv`
  ADD PRIMARY KEY (`ID_USER_EXPEDITEUR`,`ID_USER_RECEPTEUR`),
  ADD KEY `FK_MESSAGEPRV2` (`ID_USER_RECEPTEUR`);

--
-- Indexes for table `messagerie`
--
ALTER TABLE `messagerie`
  ADD PRIMARY KEY (`ID_MESSAGE`),
  ADD KEY `FK_ECHANGER` (`ID_GROUPE`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`ID_MODULE`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`ID_NOTIFICATION`),
  ADD KEY `FK_RECEVOIR` (`ID_USER`);

--
-- Indexes for table `programme`
--
ALTER TABLE `programme`
  ADD PRIMARY KEY (`ID_FILIERE`,`ID_MODULE`),
  ADD KEY `FK_PROGRAMME2` (`ID_MODULE`);

--
-- Indexes for table `reaction`
--
ALTER TABLE `reaction`
  ADD PRIMARY KEY (`ID_USER`,`ID_REPONSE`),
  ADD KEY `fk_idreponse` (`ID_REPONSE`);

--
-- Indexes for table `reponseforum`
--
ALTER TABLE `reponseforum`
  ADD PRIMARY KEY (`ID_REPONSE`),
  ADD KEY `FK_CONTENIR` (`ID_FORUM`),
  ADD KEY `FK_ECRIRE` (`ID_USER`);

--
-- Indexes for table `ressource`
--
ALTER TABLE `ressource`
  ADD PRIMARY KEY (`ID_RESSOURCE`),
  ADD KEY `FK_AJOUTER` (`ID_USER`),
  ADD KEY `FK_CONCERNER` (`ID_MODULE`),
  ADD KEY `FK_ID_FILIERE` (`ID_FILIERE`);

--
-- Indexes for table `telechargement`
--
ALTER TABLE `telechargement`
  ADD PRIMARY KEY (`ID_TELECHARGEMENT`),
  ADD KEY `fk_idr` (`ID_RESSOURCE`),
  ADD KEY `fk_idu` (`ID_USER`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`ID_USER`),
  ADD UNIQUE KEY `EMAIL_INSTITUTIONNEL` (`EMAIL_INSTITUTIONNEL`),
  ADD UNIQUE KEY `MATRICULE_OU_CEF` (`MATRICULE_OU_CEF`),
  ADD KEY `FK_ENVOYER` (`ID_MESSAGE`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `annonce`
--
ALTER TABLE `annonce`
  MODIFY `ID_ANNONCE` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `commentaireressource`
--
ALTER TABLE `commentaireressource`
  MODIFY `ID_COMMENT` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `filiere`
--
ALTER TABLE `filiere`
  MODIFY `ID_FILIERE` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `forum`
--
ALTER TABLE `forum`
  MODIFY `ID_FORUM` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `groupe`
--
ALTER TABLE `groupe`
  MODIFY `ID_GROUPE` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messagerie`
--
ALTER TABLE `messagerie`
  MODIFY `ID_MESSAGE` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `ID_MODULE` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `ID_NOTIFICATION` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `reponseforum`
--
ALTER TABLE `reponseforum`
  MODIFY `ID_REPONSE` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `ressource`
--
ALTER TABLE `ressource`
  MODIFY `ID_RESSOURCE` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `telechargement`
--
ALTER TABLE `telechargement`
  MODIFY `ID_TELECHARGEMENT` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `ID_USER` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `annonce`
--
ALTER TABLE `annonce`
  ADD CONSTRAINT `FK_CREER` FOREIGN KEY (`ID_USER`) REFERENCES `utilisateur` (`ID_USER`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `commentaireressource`
--
ALTER TABLE `commentaireressource`
  ADD CONSTRAINT `FK_AVOIR` FOREIGN KEY (`ID_RESSOURCE`) REFERENCES `ressource` (`ID_RESSOURCE`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_REPONDRE` FOREIGN KEY (`ID_USER`) REFERENCES `utilisateur` (`ID_USER`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `forum`
--
ALTER TABLE `forum`
  ADD CONSTRAINT `FK_PUBLIER` FOREIGN KEY (`ID_USER`) REFERENCES `utilisateur` (`ID_USER`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `membre`
--
ALTER TABLE `membre`
  ADD CONSTRAINT `FK_MEMBRE` FOREIGN KEY (`ID_USER`) REFERENCES `utilisateur` (`ID_USER`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_MEMBRE2` FOREIGN KEY (`ID_FILIERE`) REFERENCES `filiere` (`ID_FILIERE`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `membre_groupe`
--
ALTER TABLE `membre_groupe`
  ADD CONSTRAINT `FK_MEMBRE_GROUPE` FOREIGN KEY (`ID_GROUPE`) REFERENCES `groupe` (`ID_GROUPE`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_MEMBRE_GROUPE2` FOREIGN KEY (`ID_USER`) REFERENCES `utilisateur` (`ID_USER`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `messageprv`
--
ALTER TABLE `messageprv`
  ADD CONSTRAINT `FK_MESSAGEPRV` FOREIGN KEY (`ID_USER_EXPEDITEUR`) REFERENCES `utilisateur` (`ID_USER`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `FK_MESSAGEPRV2` FOREIGN KEY (`ID_USER_RECEPTEUR`) REFERENCES `utilisateur` (`ID_USER`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `messagerie`
--
ALTER TABLE `messagerie`
  ADD CONSTRAINT `FK_ECHANGER` FOREIGN KEY (`ID_GROUPE`) REFERENCES `groupe` (`ID_GROUPE`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `FK_RECEVOIR` FOREIGN KEY (`ID_USER`) REFERENCES `utilisateur` (`ID_USER`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `programme`
--
ALTER TABLE `programme`
  ADD CONSTRAINT `FK_PROGRAMME` FOREIGN KEY (`ID_FILIERE`) REFERENCES `filiere` (`ID_FILIERE`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_PROGRAMME2` FOREIGN KEY (`ID_MODULE`) REFERENCES `module` (`ID_MODULE`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reaction`
--
ALTER TABLE `reaction`
  ADD CONSTRAINT `fk_idreponse` FOREIGN KEY (`ID_REPONSE`) REFERENCES `reponseforum` (`ID_REPONSE`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_iduser` FOREIGN KEY (`ID_USER`) REFERENCES `utilisateur` (`ID_USER`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reponseforum`
--
ALTER TABLE `reponseforum`
  ADD CONSTRAINT `FK_CONTENIR` FOREIGN KEY (`ID_FORUM`) REFERENCES `forum` (`ID_FORUM`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ECRIRE` FOREIGN KEY (`ID_USER`) REFERENCES `utilisateur` (`ID_USER`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ressource`
--
ALTER TABLE `ressource`
  ADD CONSTRAINT `FK_AJOUTER` FOREIGN KEY (`ID_USER`) REFERENCES `utilisateur` (`ID_USER`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CONCERNER` FOREIGN KEY (`ID_MODULE`) REFERENCES `module` (`ID_MODULE`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ID_FILIERE` FOREIGN KEY (`ID_FILIERE`) REFERENCES `filiere` (`ID_FILIERE`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `telechargement`
--
ALTER TABLE `telechargement`
  ADD CONSTRAINT `fk_idr` FOREIGN KEY (`ID_RESSOURCE`) REFERENCES `ressource` (`ID_RESSOURCE`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_idu` FOREIGN KEY (`ID_USER`) REFERENCES `utilisateur` (`ID_USER`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `FK_ENVOYER` FOREIGN KEY (`ID_MESSAGE`) REFERENCES `messagerie` (`ID_MESSAGE`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
