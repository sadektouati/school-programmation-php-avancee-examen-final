-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2022 at 04:59 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `memo`
--
CREATE DATABASE IF NOT EXISTS `memo` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `memo`;

-- --------------------------------------------------------


  

--
-- Table structure for table `tache`
--

CREATE TABLE `tache` (
  `tac_id` int(10) UNSIGNED NOT NULL,
  `tac_texte` varchar(255) NOT NULL,
  `tac_accomplie` tinyint(1) NOT NULL DEFAULT 0,
  `tac_date_ajout` datetime NOT NULL,
  `tac_uti_id_ce` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `uti_id` int(10) UNSIGNED NOT NULL,
  `uti_nom` varchar(50) NOT NULL,
  `uti_courriel` varchar(100) NOT NULL,
  `uti_mdp` varchar(255) NOT NULL,
  `uti_date_ajout` date NOT NULL,
  `uti_confirmation` char(27) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tache`
--
ALTER TABLE `tache`
  ADD PRIMARY KEY (`tac_id`),
  ADD KEY `tac_uti_id_ce` (`tac_uti_id_ce`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`uti_id`),
  ADD UNIQUE KEY `uti_courriel` (`uti_courriel`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tache`
--
ALTER TABLE `tache`
  MODIFY `tac_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `uti_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tache`
--
ALTER TABLE `tache`
  ADD CONSTRAINT `tache_ibfk_1` FOREIGN KEY (`tac_uti_id_ce`) REFERENCES `utilisateur` (`uti_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
