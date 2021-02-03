-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Sam 25 Avril 2020 à 20:56
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `OT_BDD`
--
CREATE DATABASE IF NOT EXISTS `Chaufferie_BDD` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `Chaufferie_BDD`;

-- --------------------------------------------------------

--
-- Structure de la table `EQPT`
--

CREATE TABLE IF NOT EXISTS `EQPT` (
  `ID` int(255) NOT NULL,
  `BATIMENT` varchar(255) NOT NULL,
  `LOCAL` varchar(255) NOT NULL,
  `TYPE` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `MESURE`
--


CREATE TABLE IF NOT EXISTS `MESURE` (
  `ID` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `TYPE` varchar(255) NOT NULL,
  `VALEUR` float(24),
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `MESURE_PAR_EQPT`
--


CREATE TABLE IF NOT EXISTS `MESURE_PAR_EQPT` (
  `ID_MESURE` int(255) NOT NULL,
  `ID_EQPT` int(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- --------------------------------------------------------

--
-- Structure de la table `ANOMALIE`
--

CREATE TABLE IF NOT EXISTS `ANOMALIE` (
  `ID` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `ID_EQPT` int(255) unsigned NOT NULL,
  `ID_TYPE_ANOMALIE` int(255) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- --------------------------------------------------------

--
-- Structure de la table `TYPE_ANOMALIE`
--

CREATE TABLE IF NOT EXISTS `TYPE_ANOMALIE` (
  `ID` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `TYPE` varchar(255) NOT NULL,
  `SOLUTION` varchar(255) NOT NULL,
  `CRITICITE` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- --------------------------------------------------------

--
-- Structure de la table `MODELE_RONDE`
--

CREATE TABLE IF NOT EXISTS `MODELE_RONDE` (
  `ID` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `NOM` varchar(255) NOT NULL,
  `PERIODICITE` int(255),
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- --------------------------------------------------------

--
-- Structure de la table `EQPT_PAR_RONDE`
--

CREATE TABLE IF NOT EXISTS `EQPT_PAR_RONDE` (
  `ID_MODELE_RONDE` int(255) NOT NULL,
  `ID_EQPT` int(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
